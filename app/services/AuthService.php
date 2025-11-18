<?php

// PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class AuthService
{
    private $userModel;

    public function __construct()
    {
        if (!class_exists('UserModel')) {
            require_once __DIR__ . '/../models/UserModel.php';
        }
        $this->userModel = new UserModel();
    }

    /**
     * Registrar usuário
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

        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors, 'message' => 'Erro na validação.'];
        }

        if (empty($name)) {
            $name = ucfirst(explode('@', $email)[0]);
        }

        $userId = $this->userModel->create($name, $email, $password);

        if ($userId) {
            $newUser = [
                'id' => $userId,
                'name' => $name,
                'email' => $email,
                'avatar' => null
            ];

            return ['success' => true, 'message' => 'Usuário registrado com sucesso!', 'user' => $newUser];
        }

        return [
            'success' => false,
            'errors' => ['Erro inesperado ao registrar usuário.'],
            'message' => 'Erro no servidor.'
        ];
    }

    /**
     * LOGIN DO USUÁRIO
     */
    public function loginUser(array $credentials): array
    {
        $email = filter_var(trim($credentials['email'] ?? ''), FILTER_VALIDATE_EMAIL);
        $password = $credentials['password'] ?? '';

        if (!$email || empty($password)) {
            return ['success' => false, 'errors' => ['E-mail ou senha inválidos.'], 'message' => 'Falha na autenticação.'];
        }

        // Busca o usuário
        $user = $this->userModel->findByEmailAndPassword($email, $password);

        // 🚫 BLOQUEAR LOGIN SE STATUS ≠ ACTIVE
        if ($user && $user['status'] !== 'active') {
            return [
                'success' => false,
                'errors' => ["Sua conta está bloqueada. Contate o administrador."],
                'message' => "Usuário bloqueado."
            ];
        }

        if ($user) {
            // Sessão com dados completos
            $userData = [
                'id'     => $user['id'],
                'name'   => $user['name'],
                'email'  => $user['email'],
                'avatar' => $user['avatar'],
                'role'   => $user['role'],   // IMPORTANTE
                'status' => $user['status'] // IMPORTANTE
            ];

            if (session_status() === PHP_SESSION_NONE) session_start();

            session_regenerate_id(true);
            $_SESSION['user'] = $userData;

            return ['success' => true, 'message' => 'Login realizado com sucesso!', 'user' => $userData];
        }

        return [
            'success' => false,
            'errors' => ['E-mail ou senha inválidos.'],
            'message' => 'Falha na autenticação.'
        ];
    }

    /**
     * LOGOUT
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

    // ========= RECUPERAÇÃO DE SENHA =========

    public function requestPasswordReset(string $email): array
    {
        $cleanEmail = filter_var(trim($email), FILTER_VALIDATE_EMAIL);

        if (!$cleanEmail) {
            return ['success' => true, 'message' => 'Se o e-mail estiver cadastrado, enviaremos o link.'];
        }

        $user = $this->userModel->findByEmail($cleanEmail);

        if ($user) {
            try {
                $token = bin2hex(random_bytes(32));
                $expires = date('Y-m-d H:i:s', time() + 3600);

                if ($this->userModel->saveResetToken($cleanEmail, $token, $expires)) {
                    $this->sendPasswordResetEmailInternal($cleanEmail, $token);
                }
            } catch (Exception $e) {
                error_log("Erro ao gerar token: " . $e->getMessage());
            }
        }

        return ['success' => true, 'message' => 'Se o e-mail estiver cadastrado, enviaremos o link.'];
    }

    public function verifyResetToken(string $token)
    {
        if (empty($token)) return false;

        $user = $this->userModel->findUserByResetToken($token);
        return $user ?: false;
    }

    public function resetPasswordWithToken(string $token, string $newPassword, string $passwordConfirm): array
    {
        $errors = [];

        if (empty($newPassword)) {
            $errors[] = 'A nova senha não pode estar em branco.';
        } elseif (strlen($newPassword) < 8) {
            $errors[] = 'A nova senha deve ter pelo menos 8 caracteres.';
        } elseif ($newPassword !== $passwordConfirm) {
            $errors[] = 'As senhas não coincidem.';
        }

        $user = $this->verifyResetToken($token);

        if (!$user) {
            $errors[] = 'Token inválido ou expirado.';
        }

        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        if ($this->userModel->updatePassword($user['id'], $newPassword)) {
            return ['success' => true, 'message' => 'Senha redefinida com sucesso!'];
        }

        return ['success' => false, 'errors' => ['Erro ao atualizar a senha.']];
    }

    /**
     * Enviar e-mail de recuperação
     */
    private function sendPasswordResetEmailInternal($email, $token)
    {
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
            $mail->Subject = 'Redefinição de Senha - DinDin';
            $mail->Body    =
                "Olá,<br><br>Para redefinir sua senha, clique no link abaixo (válido por 1 hora):<br><br>"
                . "<a href='{$resetLink}'>Redefinir Minha Senha</a><br><br>"
                . "Se você não solicitou, ignore este e-mail.";

            $mail->AltBody = "Use este link para redefinir sua senha: {$resetLink}";
            $mail->send();
        } catch (Exception $e) {
            error_log("Erro ao enviar e-mail: {$mail->ErrorInfo}");
        }
    }
}