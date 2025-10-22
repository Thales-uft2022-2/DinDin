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
     * (TS-Auth-01)
     * ... (código do registerUser - MANTENHA O CÓDIGO EXISTENTE) ...
     */
    public function registerUser(array $data): array
    {
        // [CÓDIGO OMITIDO POR BREVIDADE - Mantenha o código existente aqui]
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
            $newUser = ['id' => $userId, 'name' => $name, 'email' => $email];
            return ['success' => true, 'message' => 'Usuário registrado com sucesso!', 'user' => $newUser];
        } else {
            return ['success' => false, 'errors' => ['Ocorreu um erro inesperado ao registrar o usuário.'], 'message' => 'Erro no servidor.'];
        }
    }

    /**
     * Valida as credenciais e realiza o login do usuário.
     * (TS-Auth-02)
     * ... (código do loginUser - MANTENHA O CÓDIGO EXISTENTE) ...
     */
    public function loginUser(array $credentials): array
    {
        // [CÓDIGO OMITIDO POR BREVIDADE - Mantenha o código existente aqui]
        $email = filter_var(trim($credentials['email'] ?? ''), FILTER_VALIDATE_EMAIL);
        $password = $credentials['password'] ?? '';
        if (!$email || empty($password)) return ['success' => false, 'errors' => ['E-mail ou senha inválidos.'], 'message' => 'Falha na autenticação.'];
        $user = $this->userModel->findByEmailAndPassword($email, $password);
        if ($user) {
            $userData = ['id' => $user['id'], 'name' => $user['name'], 'email' => $user['email']];
            if (session_status() === PHP_SESSION_NONE) session_start();
            session_regenerate_id(true);
            $_SESSION['user'] = $userData;
            return ['success' => true, 'message' => 'Login realizado com sucesso!', 'user' => $userData];
        } else {
            return ['success' => false, 'errors' => ['E-mail ou senha inválidos.'], 'message' => 'Falha na autenticação.'];
        }
    }

    /**
     * Realiza o logout do usuário destruindo a sessão.
     * (TS-Auth-03)
     * ... (código do logoutUser - MANTENHA O CÓDIGO EXISTENTE) ...
     */
    public function logoutUser(): array
    {
        // [CÓDIGO OMITIDO POR BREVIDADE - Mantenha o código existente aqui]
        if (session_status() === PHP_SESSION_NONE) session_start();
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
        }
        session_destroy();
        return ['success' => true, 'message' => 'Logout realizado com sucesso!'];
    }

    // --- MÉTODOS PARA RECUPERAÇÃO DE SENHA (TS-Auth-04 - NOVOS) ---

    /**
     * Inicia o processo de recuperação de senha para um e-mail.
     * Gera um token, salva no banco e dispara o envio do e-mail.
     *
     * @param string $email
     * @return array ['success' => bool, 'message' => string]
     */
    public function requestPasswordReset(string $email): array
    {
        $cleanEmail = filter_var(trim($email), FILTER_VALIDATE_EMAIL);

        if (!$cleanEmail) {
            // Mesmo com e-mail inválido, retornamos a mensagem genérica por segurança
            return ['success' => true, 'message' => 'Se o e-mail estiver cadastrado, um link de redefinição será enviado.'];
        }

        $user = $this->userModel->findByEmail($cleanEmail);

        if ($user) {
            try {
                // Gerar token seguro
                $token = bin2hex(random_bytes(32)); // Token mais longo
                // Definir tempo de expiração (ex: 1 hora a partir de agora)
                $expires = date('Y-m-d H:i:s', time() + 3600);

                // Salvar token e expiração no banco de dados para o usuário
                if ($this->userModel->saveResetToken($cleanEmail, $token, $expires)) {
                    // Enviar o e-mail de redefinição
                    $this->sendPasswordResetEmailInternal($cleanEmail, $token);
                } else {
                    // Logar erro, mas não informar o usuário
                    error_log("AuthService: Falha ao salvar reset token para {$cleanEmail}");
                }
            } catch (Exception $e) {
                // Captura erro na geração do token (muito raro)
                error_log("AuthService: Erro ao gerar reset token: " . $e->getMessage());
            }
        }

        // Critério de Aceite: Sempre retornar a mesma mensagem para não vazar informações
        return ['success' => true, 'message' => 'Se o e-mail estiver cadastrado, um link de redefinição será enviado.'];
    }

    /**
     * Verifica se um token de redefinição é válido (existe e não expirou).
     *
     * @param string $token
     * @return array|false Retorna os dados do usuário associado ao token ou false se inválido/expirado.
     */
    public function verifyResetToken(string $token)
    {
        if (empty($token)) {
            return false;
        }
        // O UserModel já faz a checagem da expiração na query
        $user = $this->userModel->findUserByResetToken($token);
        return $user ?: false; // Retorna os dados do usuário (id, email) ou false
    }

    /**
     * Redefine a senha do usuário usando um token válido.
     *
     * @param string $token Token recebido (geralmente da URL)
     * @param string $newPassword Nova senha
     * @param string $passwordConfirm Confirmação da nova senha
     * @return array ['success' => bool, 'errors' => array, 'message' => string]
     */
    public function resetPasswordWithToken(string $token, string $newPassword, string $passwordConfirm): array
    {
        // 1. Validar a nova senha
        $errors = [];
        if (empty($newPassword)) {
            $errors[] = 'A nova senha não pode estar em branco.';
        } elseif (strlen($newPassword) < 8) {
            $errors[] = 'A nova senha deve ter no mínimo 8 caracteres.';
        } elseif ($newPassword !== $passwordConfirm) {
            $errors[] = 'As senhas não coincidem.';
        }

        // 2. Verificar o token (reutiliza o método verify)
        $user = $this->verifyResetToken($token);
        if (!$user) {
            $errors[] = 'Token de redefinição inválido ou expirado.';
        }

        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors, 'message' => 'Erro na validação.'];
        }

        // 3. Chamar o Model para atualizar a senha e limpar o token
        if ($this->userModel->updatePassword($user['id'], $newPassword)) {
            return ['success' => true, 'message' => 'Senha redefinida com sucesso!'];
        } else {
            return ['success' => false, 'errors' => ['Ocorreu um erro ao atualizar a senha.'], 'message' => 'Erro no servidor.'];
        }
    }


    /**
     * Função auxiliar INTERNA para enviar o e-mail de redefinição.
     * Reutiliza a lógica que estava no AuthController.
     */
    private function sendPasswordResetEmailInternal($email, $token) {
        $mail = new PHPMailer(true);
        // Gera o link que o usuário vai clicar (aponta para a rota WEB)
        $resetLink = BASE_URL . '/auth/reset-password?token=' . $token;
        try {
            // Configurações do PHPMailer (usando constantes do config.php)
            $mail->CharSet = 'UTF-8';
            $mail->isSMTP();
            $mail->Host       = SMTP_HOST;
            $mail->SMTPAuth   = true;
            $mail->Username   = SMTP_USER;
            $mail->Password   = SMTP_PASS;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Ou PHPMailer::ENCRYPTION_SMTPS
            $mail->Port       = SMTP_PORT;                     // Porta correta para TLS/SMTPS

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
            // Logar o erro detalhado para o desenvolvedor
            error_log("AuthService: Falha ao enviar e-mail de reset para {$email}. Mailer Error: {$mail->ErrorInfo}");
            // Não relançar a exceção para não expor detalhes ao usuário
        }
    }

}
