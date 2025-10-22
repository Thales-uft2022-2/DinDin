
<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Inclui o AuthService
require_once __DIR__ . '/../services/AuthService.php';

class AuthController {
    private $userModel; // Mantemos para os métodos não refatorados ainda
    private $authService; // Dependência do serviço

    public function __construct() {
        require_once __DIR__ . '/../models/UserModel.php';
        $this->userModel = new UserModel();
        $this->authService = new AuthService(); // Instancia o serviço
    }

    // =======================================================
    // MÉTODO 'register' (WEB) - REFATORADO (TS-Auth-01)
    // =======================================================
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $_POST;
            $result = $this->authService->registerUser($data);
            if ($result['success']) {
                if (session_status() === PHP_SESSION_NONE) session_start();
                $_SESSION['success_message'] = $result['message'];
                header("Location: " . BASE_URL . "/auth/login");
                exit;
            } else {
                $errors = $result['errors'];
                $oldData = $data;
                include __DIR__ . '/../views/auth/register.php';
            }
        } else {
            if (session_status() === PHP_SESSION_NONE) session_start();
            unset($_SESSION['success_message']);
            include __DIR__ . '/../views/auth/register.php';
        }
    }

    // =======================================================
    // MÉTODO 'apiRegister' (API) - NOVO (TS-Auth-01)
    // =======================================================
    public function apiRegister() {
        header('Content-Type: application/json; charset=utf-8');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Método não permitido, use POST']);
            return;
        }
        $data = json_decode(file_get_contents('php://input'), true);
        if (!is_array($data)) {
            http_response_code(400);
            echo json_encode(['error' => 'JSON inválido']);
            return;
        }
        $result = $this->authService->registerUser($data);
        if ($result['success']) {
            http_response_code(201);
            echo json_encode(['message' => $result['message'], 'user' => $result['user']]);
        } else {
            http_response_code(422);
            echo json_encode(['errors' => $result['errors']]);
        }
    }

    // =======================================================
    // MÉTODO 'login' (WEB) - REFATORADO (TS-Auth-02)
    // =======================================================
    public function login() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        // Se já estiver logado (sessão ativa), redireciona para home
        if (!empty($_SESSION['user'])) {
            header("Location: " . BASE_URL . "/home");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $credentials = $_POST;
            $result = $this->authService->loginUser($credentials);
            if ($result['success']) {
                header("Location: " . BASE_URL . "/home");
                exit;
            } else {
                $error = $result['errors'][0] ?? 'Erro desconhecido no login.';
                $success_message = $_SESSION['success_message'] ?? null;
                unset($_SESSION['success_message']);
                include __DIR__ . '/../views/auth/login.php';
            }
        } else {
            $success_message = $_SESSION['success_message'] ?? null;
            unset($_SESSION['success_message']);
            include __DIR__ . '/../views/auth/login.php';
        }
    }

    // =======================================================
    // MÉTODO 'apiLogin' (API) - NOVO (TS-Auth-02)
    // =======================================================
    public function apiLogin() {
        header('Content-Type: application/json; charset=utf-8');
        if (session_status() === PHP_SESSION_NONE) session_start();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Método não permitido, use POST']);
            return;
        }
        $credentials = json_decode(file_get_contents('php://input'), true);
        if (!is_array($credentials)) {
            http_response_code(400);
            echo json_encode(['error' => 'JSON inválido']);
            return;
        }
        $result = $this->authService->loginUser($credentials);
        if ($result['success']) {
            http_response_code(200);
            echo json_encode([
                'message' => $result['message'],
                'user' => $result['user'],
                'session_id' => session_id()
             ]);
        } else {
            http_response_code(401);
            echo json_encode(['errors' => $result['errors']]);
        }
    }

    // =======================================================
    // MÉTODO 'logout' (WEB) - REFATORADO (TS-Auth-03)
    // =======================================================
    public function logout() {
        // Chama o serviço para destruir a sessão
        $this->authService->logoutUser();
        // Redireciona para a página de login
        header("Location: " . BASE_URL . "/auth/login");
        exit;
    }

    // =======================================================
    // MÉTODO 'apiLogout' (API) - NOVO (TS-Auth-03)
    // =======================================================
    public function apiLogout() {
        header('Content-Type: application/json; charset=utf-8');
        // Chama o serviço para destruir a sessão
        $result = $this->authService->logoutUser();

        // Retorna a resposta JSON (sempre sucesso, idealmente)
        // O método POST é usado por convenção para ações que alteram estado (destruir sessão)
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
             http_response_code(405); // Method Not Allowed
             echo json_encode(['error' => 'Método não permitido, use POST']);
             return;
        }

        http_response_code(200); // OK
        echo json_encode(['message' => $result['message']]);
    }


    // --- MÉTODOS ANTIGOS (recuperação) ---
    // Eles ainda não foram refatorados

    public function forgotPassword() {
        // TODO: Refatorar para usar AuthService na TS-Auth-04
        include __DIR__ . '/../views/auth/forgot-password.php';
    }

    public function sendResetLink() {
        // TODO: Refatorar para usar AuthService na TS-Auth-04
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $user = $this->userModel->findByEmail($email); // Ainda usa o model

            if ($user) {
                $token = bin2hex(random_bytes(50));
                $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

                if ($this->userModel->saveResetToken($email, $token, $expires)) { // Ainda usa o model
                    $this->sendPasswordResetEmail($email, $token); // Função auxiliar
                }
            }
            include __DIR__ . '/../views/auth/reset-link-sent.php';
        } else {
            header("Location: " . BASE_URL . "/auth/forgot-password");
            exit;
        }
    }

    public function resetPassword() {
        // TODO: Refatorar para usar AuthService na TS-Auth-04
        $token = $_GET['token'] ?? '';
        $user = $this->userModel->findUserByResetToken($token); // Ainda usa o model

        if (!$user) {
            die("Token de redefinição inválido ou expirado. Por favor, solicite um novo link.");
        }
        include __DIR__ . '/../views/auth/reset-password.php';
    }

    public function updatePassword() {
        // TODO: Refatorar para usar AuthService na TS-Auth-04
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['token'] ?? '';
            $password = $_POST['password'] ?? '';
            $password_confirm = $_POST['password_confirm'] ?? '';

            if (empty($password) || $password !== $password_confirm) {
                die("As senhas não coincidem ou estão em branco. Por favor, tente novamente.");
            }
            if (strlen($password) < 8) {
                die("A senha deve ter no mínimo 8 caracteres.");
            }

            $user = $this->userModel->findUserByResetToken($token); // Ainda usa o model
            if (!$user) {
                die("Token inválido ou expirado. Ação não permitida.");
            }

            if ($this->userModel->updatePassword($user['id'], $password)) { // Ainda usa o model
                include __DIR__ . '/../views/auth/password-reset-success.php';
            } else {
                die("Ocorreu um erro ao atualizar sua senha. Tente novamente.");
            }
        } else {
             header("Location: " . BASE_URL . "/auth/login");
             exit;
        }
    }

    // Função auxiliar - pode ser movida para um Service/Helper depois
    private function sendPasswordResetEmail($email, $token) {
        $mail = new PHPMailer(true);
        $resetLink = BASE_URL . '/auth/reset-password?token=' . $token;
        try {
            // Configurações do PHPMailer... (seu código aqui)
            $mail->CharSet = 'UTF-8';
            $mail->isSMTP();
            $mail->Host       = SMTP_HOST; // Constantes do config.php
            $mail->SMTPAuth   = true;
            $mail->Username   = SMTP_USER;
            $mail->Password   = SMTP_PASS;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = SMTP_PORT;
            $mail->setFrom(MAIL_FROM_ADDRESS, MAIL_FROM_NAME);
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'Redefinicao de Senha - DinDin';
            $mail->Body    = "Olá,<br><br>Você solicitou a redefinição de sua senha. Clique no link abaixo para criar uma nova senha:<br><br>"
                           . "<a href='{$resetLink}'>Redefinir Minha Senha</a><br><br>"
                           . "Se você não solicitou isso, por favor ignore este e-mail.<br><br>"
                           . "Atenciosamente,<br>Equipe DinDin";
            $mail->AltBody = "Para redefinir sua senha, copie e cole este link no seu navegador: {$resetLink}";
            $mail->send();
        } catch (Exception $e) {
            error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
        }
    }
}