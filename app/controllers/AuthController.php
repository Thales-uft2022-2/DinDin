<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
class AuthController {
    private $userModel;

    public function __construct() {
        // Vamos padronizar e usar o UserModel, que é mais completo
        require_once __DIR__ . '/../models/UserModel.php';
        $this->userModel = new UserModel();
    }

    // Tela e processamento do login
   public function login() {
    // Se já estiver logado, manda pra home
    if (!empty($_SESSION['user'])) {
        header("Location: " . BASE_URL . "/home");
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $user = $this->userModel->findByEmail($email);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user'] = [
                'id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email']
            ];
            header("Location: " . BASE_URL . "/home");
            exit;
        } else {
            $error = "E-mail ou senha inválidos!";
            include __DIR__ . '/../views/auth/login.php';
        }
    } else {
        include __DIR__ . '/../views/auth/login.php';
    }
}

    // Logout do sistema
    public function logout() {
        session_destroy();
        header("Location: " . BASE_URL . "/auth/login");
        exit;
    }

    // Cadastro de usuários (caso queira manter aqui também)
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name     = trim($_POST['name'] ?? '');
            $email    = trim($_POST['email'] ?? '');
            $password = password_hash($_POST['password'] ?? '', PASSWORD_DEFAULT);

            $this->userModel->create($name, $email, $password);

            header("Location: " . BASE_URL . "/auth/login");
            exit;
        } else {
            include __DIR__ . '/../views/auth/register.php';
        }
    }
    // ... (métodos login, logout, register existentes) ...

    // ===== NOVOS MÉTODOS PARA RECUPERAÇÃO DE SENHA =====

    /**
     * Exibe o formulário para solicitar a redefinição de senha.
     * Rota: /auth/forgot-password
     */
    public function forgotPassword() {
        include __DIR__ . '/../views/auth/forgot-password.php';
    }

    /**
     * Processa a solicitação de redefinição de senha e envia o e-mail.
     * Rota: /auth/send-reset-link
     */
    public function sendResetLink() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $user = $this->userModel->findByEmail($email);

            if ($user) {
                // Gerar token seguro
                $token = bin2hex(random_bytes(50));
                // Definir tempo de expiração (ex: 1 hora)
                $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

                if ($this->userModel->saveResetToken($email, $token, $expires)) {
                    // Enviar o e-mail
                    $this->sendPasswordResetEmail($email, $token);
                }
            }
            // Critério de Aceite: Sempre mostrar a mesma mensagem
            // para não vazar se um e-mail existe ou não. 
            include __DIR__ . '/../views/auth/reset-link-sent.php';
        } else {
            header("Location: " . BASE_URL . "/auth/forgot-password");
            exit;
        }
    }
    
    /**
     * Exibe o formulário para o usuário criar uma nova senha.
     * Rota: /auth/reset-password
     */
    public function resetPassword() {
        $token = $_GET['token'] ?? '';
        $user = $this->userModel->findUserByResetToken($token);

        if (!$user) {
            // Token inválido ou expirado
            die("Token de redefinição inválido ou expirado. Por favor, solicite um novo link.");
        }

        // Passa o token para a view
        include __DIR__ . '/../views/auth/reset-password.php';
    }

    /**
     * Processa o formulário de nova senha e atualiza no banco.
     * Rota: /auth/update-password
     */
    public function updatePassword() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['token'] ?? '';
            $password = $_POST['password'] ?? '';
            $password_confirm = $_POST['password_confirm'] ?? '';

            // 1. Validar se as senhas coincidem e não estão vazias
            if (empty($password) || $password !== $password_confirm) {
                die("As senhas não coincidem ou estão em branco. Por favor, tente novamente.");
            }
            if (strlen($password) < 8) {
                die("A senha deve ter no mínimo 8 caracteres.");
            }

            // 2. Verificar o token novamente por segurança
            $user = $this->userModel->findUserByResetToken($token);
            if (!$user) {
                die("Token inválido ou expirado. Ação não permitida.");
            }

            // 3. Atualizar a senha
            if ($this->userModel->updatePassword($user['id'], $password)) {
                // Senha atualizada com sucesso
                include __DIR__ . '/../views/auth/password-reset-success.php';
            } else {
                die("Ocorreu um erro ao atualizar sua senha. Tente novamente.");
            }
        } else {
             header("Location: " . BASE_URL . "/auth/login");
             exit;
        }
    }


    /**
     * Função auxiliar para enviar o e-mail com PHPMailer.
     */
    private function sendPasswordResetEmail($email, $token) {
        $mail = new PHPMailer(true);
        $resetLink = BASE_URL . '/auth/reset-password?token=' . $token;

        try {
            // HABILITE O DEBUG AQUI!
            //$mail->SMTPDebug = SMTP::DEBUG_SERVER; // Mostra toda a comunicação com o servidor
            $mail->CharSet = 'UTF-8'; // Garante a codificação correta para acentos

            // Configurações do servidor (usando as constantes do config.php)
            $mail->isSMTP();
            $mail->Host       = SMTP_HOST;
            $mail->SMTPAuth   = true;
            $mail->Username   = SMTP_USER;
            $mail->Password   = SMTP_PASS;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = SMTP_PORT;

            // Destinatários
            $mail->setFrom(MAIL_FROM_ADDRESS, MAIL_FROM_NAME);
            $mail->addAddress($email);

            // Conteúdo do e-mail
            $mail->isHTML(true);
            $mail->Subject = 'Redefinicao de Senha - DinDin';
            $mail->Body    = "Olá,<br><br>Você solicitou a redefinição de sua senha. Clique no link abaixo para criar uma nova senha:<br><br>"
                           . "<a href='{$resetLink}'>Redefinir Minha Senha</a><br><br>"
                           . "Se você não solicitou isso, por favor ignore este e-mail.<br><br>"
                           . "Atenciosamente,<br>Equipe DinDin";
            $mail->AltBody = "Para redefinir sua senha, copie e cole este link no seu navegador: {$resetLink}";

            $mail->send();
        } catch (Exception $e) {
            // Não exibir o erro para o usuário, mas pode ser útil logar
            error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
        }
    }
}
