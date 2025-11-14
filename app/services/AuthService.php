<?php

// Precisa do PHPMailer para enviar e-mail
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class AuthService
{
    private $userModel;

    public function __construct()
    {
        // O AuthService depende do UserModel
        if (!class_exists('UserModel')) {
            require_once __DIR__ . '/../models/UserModel.php';
        }
        $this->userModel = new UserModel();
    }

    /**
     * Valida os dados e registra um novo usuário.
     */
    public function registerUser(array $data): array
    {
        $email = filter_var(trim($data['email'] ?? ''), FILTER_VALIDATE_EMAIL);
        $password = $data['password'] ?? '';
        $passwordConfirm = $data['password_confirm'] ?? '';
        $name = trim($data['name'] ?? '');
        $errors = [];
        if (!$email) $errors[] = 'E-mail inválido.';
        if (strlen($password) < 8) $errors[] = 'A senha deve ter no mínimo 8 caracteres.';
        if ($password !== $passwordConfirm) $errors[] = 'As senhas não coincidem.';
        if ($email && $this->userModel->findByEmail($email)) $errors[] = 'E-mail já cadastrado.';
        if (!empty($errors)) return ['success' => false, 'errors' => $errors, 'message' => 'Erro na validação.'];
        if (empty($name)) $name = ucfirst(explode('@', $email)[0]);
        
        $userId = $this->userModel->create($name, $email, $password);
        
        if ($userId) {
            // ▼▼▼ CORREÇÃO AQUI ▼▼▼
            // Adiciona 'avatar' (como null) ao novo usuário
            $newUser = ['id' => $userId, 'name' => $name, 'email' => $email, 'avatar' => null];
            // ▲▲▲ FIM DA CORREÇÃO ▲▲▲
            return ['success' => true, 'message' => 'Usuário registrado com sucesso!', 'user' => $newUser];
        } else {
            return ['success' => false, 'errors' => ['Ocorreu um erro inesperado ao registrar o usuário.'], 'message' => 'Erro no servidor.'];
        }
    }

    /**
     * Valida as credenciais e realiza o login do usuário.
     */
    public function loginUser(array $credentials): array
    {
        $email = filter_var(trim($credentials['email'] ?? ''), FILTER_VALIDATE_EMAIL);
        $password = $credentials['password'] ?? '';
        if (!$email || empty($password)) return ['success' => false, 'errors' => ['E-mail ou senha inválidos.'], 'message' => 'Falha na autenticação.'];
        
        // O findByEmailAndPassword já retorna o avatar
        $user = $this->userModel->findByEmailAndPassword($email, $password);
        
        if ($user) {
            // ▼▼▼ CORREÇÃO IMPORTANTE AQUI ▼▼▼
            // Agora estamos incluindo o 'avatar' na sessão
            $userData = [
                'id' => $user['id'], 
                'name' => $user['name'], 
                'email' => $user['email'],
                'avatar' => $user['avatar'] // <--- ESTA LINHA CONSERTA O BUG
            ];
            // ▲▲▲ FIM DA CORREÇÃO ▲▲▲

            if (session_status() === PHP_SESSION_NONE) session_start();
            session_regenerate_id(true);
            $_SESSION['user'] = $userData; // A sessão agora tem o avatar
            
            return ['success' => true, 'message' => 'Login realizado com sucesso!', 'user' => $userData];
        } else {
            return ['success' => false, 'errors' => ['E-mail ou senha inválidos.'], 'message' => 'Falha na autenticação.'];
        }
    }

    /**
     * Realiza o logout do usuário destruindo a sessão.
     */
    public function logoutUser(): array
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
        }
        session_destroy();
        return ['success' => true, 'message' => 'Logout realizado com sucesso!'];
    }

    // --- MÉTODOS PARA RECUPERAÇÃO DE SENHA ---

    public function requestPasswordReset(string $email): array
    {
        $cleanEmail = filter_var(trim($email), FILTER_VALIDATE_EMAIL);

        if (!$cleanEmail) {
            return ['success' => true, 'message' => 'Se o e-mail estiver cadastrado, um link de redefinição será enviado.'];
        }
        $user = $this->userModel->findByEmail($cleanEmail);
        if ($user) {
            try {
                $token = bin2hex(random_bytes(32)); 
                $expires = date('Y-m-d H:i:s', time() + 3600);
                if ($this->userModel->saveResetToken($cleanEmail, $token, $expires)) {
                    $this->sendPasswordResetEmailInternal($cleanEmail, $token);
                } else {
                    error_log("AuthService: Falha ao salvar reset token para {$cleanEmail}");
                }
            } catch (Exception $e) {
                error_log("AuthService: Erro ao gerar reset token: " . $e->getMessage());
            }
        }
        return ['success' => true, 'message' => 'Se o e-mail estiver cadastrado, um link de redefinição será enviado.'];
    }

    public function verifyResetToken(string $token)
    {
        if (empty($token)) {
            return false;
        }
        $user = $this->userModel->findUserByResetToken($token);
        return $user ?: false; 
    }

    public function resetPasswordWithToken(string $token, string $newPassword, string $passwordConfirm): array
    {
        $errors = [];
        if (empty($newPassword)) {
            $errors[] = 'A nova senha não pode estar em branco.';
        } elseif (strlen($newPassword) < 8) {
            $errors[] = 'A nova senha deve ter no mínimo 8 caracteres.';
        } elseif ($newPassword !== $passwordConfirm) {
            $errors[] = 'As senhas não coincidem.';
        }
        $user = $this->verifyResetToken($token);
        if (!$user) {
            $errors[] = 'Token de redefinição inválido ou expirado.';
        }
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors, 'message' => 'Erro na validação.'];
        }
        if ($this->userModel->updatePassword($user['id'], $newPassword)) {
            return ['success' => true, 'message' => 'Senha redefinida com sucesso!'];
        } else {
            return ['success' => false, 'errors' => ['Ocorreu um erro ao atualizar a senha.'], 'message' => 'Erro no servidor.'];
        }
    }

    private function sendPasswordResetEmailInternal($email, $token) {
        $mail = new PHPMailer(true);
        $resetLink = BASE_URL . '/auth/reset-password?token=' . $token;
        try {
            $mail->CharSet = 'UTF-8';
            $mail->isSMTP();
            $mail->Host       = SMTP_HOST;
            $mail->SMTPAuth   = true;
            $mail->Username   = SMTP_USER;
            $mail->Password   = SMTP_PASS;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; 
            $mail->Port       = SMTP_PORT; 
            $mail->setFrom(MAIL_FROM_ADDRESS, MAIL_FROM_NAME);
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'Redefinicao de Senha - DinDin';
            $mail->Body    = "Olá,<br><br>Você solicitou a redefinição de sua senha. Clique no link abaixo para criar uma nova senha (válido por 1 hora):<br><br>"
                           . "<a href='{$resetLink}'>Redefinir Minha Senha</a><br><br>"
                           . "Se você não solicitou isso, por favor ignore este e-mail.<br><br>"
                           . "Atenciosamente,<br>Equipe DinDin";
            $mail->AltBody = "Para redefinir sua senha, copie e cole este link no seu navegador (válido por 1 hora): {$resetLink}";
            $mail->send();
        } catch (Exception $e) {
            error_log("AuthService: Falha ao enviar e-mail de reset para {$email}. Mailer Error: {$mail->ErrorInfo}");
        }
    }
}