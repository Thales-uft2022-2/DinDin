<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Inclui o novo AuthService
require_once __DIR__ . '/../services/AuthService.php';

class AuthController {
    private $userModel; // Mantemos para os métodos não refatorados ainda
    private $authService; // Nova dependência

    public function __construct() {
        // Vamos padronizar e usar o UserModel, que é mais completo
        require_once __DIR__ . '/../models/UserModel.php';
        $this->userModel = new UserModel();
        $this->authService = new AuthService(); // Instancia o serviço
    }

    // =======================================================
    // MÉTODO 'register' (WEB) - REFATORADO (TS-Auth-01)
    // =======================================================
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // 1. Pegar dados do POST
            $data = [
                'name'             => $_POST['name'] ?? '', // Formulário pode ou não ter nome
                'email'            => $_POST['email'] ?? '',
                'password'         => $_POST['password'] ?? '',
                'password_confirm' => $_POST['password_confirm'] ?? '' // Precisa adicionar no form HTML
            ];

            // 2. Chamar o Serviço
            $result = $this->authService->registerUser($data);

            // 3. Tratar o resultado
            if ($result['success']) {
                // Sucesso: Redireciona para login com mensagem flash (se tiver sistema de msg)
                // Ou faz login automático e redireciona para home
                // Por simplicidade, vamos redirecionar para login
                if (session_status() === PHP_SESSION_NONE) session_start();
                $_SESSION['success_message'] = $result['message']; // Mensagem flash simples
                header("Location: " . BASE_URL . "/auth/login");
                exit;
            } else {
                // Erro: Guarda os erros e recarrega a view de registro
                $errors = $result['errors'];
                $oldData = $data; // Para repopular o formulário
                include __DIR__ . '/../views/auth/register.php';
            }
        } else {
            // Método GET: Apenas exibe o formulário
            // Limpa mensagens flash antigas
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

        // 1. Validar Método
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405); // Method Not Allowed
            echo json_encode(['error' => 'Método não permitido, use POST']);
            return;
        }

        // 2. Pegar dados do JSON
        $data = json_decode(file_get_contents('php://input'), true);
        if (!is_array($data)) {
            http_response_code(400); // Bad Request
            echo json_encode(['error' => 'JSON inválido']);
            return;
        }

        // 3. Chamar o Serviço
        $result = $this->authService->registerUser($data);

        // 4. Retornar a resposta JSON
        if ($result['success']) {
            http_response_code(201); // Created
            echo json_encode(['message' => $result['message'], 'user' => $result['user']]);
        } else {
            http_response_code(422); // Unprocessable Entity (Erro de validação)
            echo json_encode(['errors' => $result['errors']]);
        }
    }


    // --- MÉTODOS ANTIGOS (login, logout, recuperação) ---
    // Eles ainda não foram refatorados, mas continuam funcionando
    // Serão refatorados nas próximas tarefas (TS-Auth-02, 03, 04)

    public function login() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!empty($_SESSION['user'])) {
            header("Location: " . BASE_URL . "/home");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            // TODO: Refatorar para usar AuthService->loginUser() na TS-Auth-02
            $user = $this->userModel->findByEmail($email);

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user'] = [
                    'id' => $user['id'],
                    'name' => $user['name'] ?? explode('@', $user['email'])[0], // Garante nome
                    'email' => $user['email']
                ];
                header("Location: " . BASE_URL . "/home");
                exit;
            } else {
                $error = "E-mail ou senha inválidos!";
                include __DIR__ . '/../views/auth/login.php';
            }
        } else {
             // Recupera e limpa mensagem flash de sucesso do registro
            $success_message = $_SESSION['success_message'] ?? null;
            unset($_SESSION['success_message']);
            include __DIR__ . '/../views/auth/login.php';
        }
    }

    public function logout() {
        // TODO: Refatorar para usar AuthService->logoutUser() na TS-Auth-03
        if (session_status() === PHP_SESSION_NONE) session_start();
        session_destroy();
        header("Location: " . BASE_URL . "/auth/login");
        exit;
    }

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