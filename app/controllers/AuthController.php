<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Inclui o AuthService
require_once __DIR__ . '/../services/AuthService.php';

class AuthController {

    private $authService;

    public function __construct() {
        $this->authService = new AuthService();
    }

    // =======================================================
    // REGISTER (WEB)
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
    // REGISTER (API)
    // =======================================================
    public function apiRegister() {
        header('Content-Type: application/json; charset=utf-8');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Método não permitido']);
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
    // LOGIN (WEB)
    // =======================================================
    public function login() {
        if (session_status() === PHP_SESSION_NONE) session_start();

        // Se já estiver logado
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
                $error = $result['errors'][0] ?? 'Erro desconhecido.';
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
    // LOGIN (API)
    // =======================================================
    public function apiLogin() {
        header('Content-Type: application/json; charset=utf-8');
        if (session_status() === PHP_SESSION_NONE) session_start();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Método não permitido']);
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
    // LOGOUT (WEB)
    // =======================================================
    public function logout() {
        $this->authService->logoutUser();
        header("Location: " . BASE_URL . "/auth/login");
        exit;
    }

    // =======================================================
    // LOGOUT (API)
    // =======================================================
    public function apiLogout() {
        header('Content-Type: application/json; charset=utf-8');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Método não permitido']);
            return;
        }

        $result = $this->authService->logoutUser();
        echo json_encode(['message' => $result['message']]);
    }

    // =======================================================
    // FORGOT PASSWORD
    // =======================================================
    public function forgotPassword() {
        include __DIR__ . '/../views/auth/forgot-password.php';
    }

    public function sendResetLink() {
        $message = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $result = $this->authService->requestPasswordReset($email);
            $message = $result['message'];
        }

        include __DIR__ . '/../views/auth/reset-link-sent.php';
    }

    // =======================================================
    // RESET PASSWORD
    // =======================================================
    public function resetPassword() {
        $token = $_GET['token'] ?? '';
        $user = $this->authService->verifyResetToken($token);

        if (!$user) {
            include_once __DIR__ . '/../views/_header.php';
            echo '<div class="form-container"><h1>Erro</h1><p>Token inválido ou expirado.</p><p><a href="' . BASE_URL . '/auth/forgot-password">Solicitar novo link</a></p></div>';
            include_once __DIR__ . '/../views/_footer.php';
            exit;
        }

        include __DIR__ . '/../views/auth/reset-password.php';
    }

    public function updatePassword() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['token'] ?? '';
            $password = $_POST['password'] ?? '';
            $password_confirm = $_POST['password_confirm'] ?? '';

            $result = $this->authService->resetPasswordWithToken($token, $password, $password_confirm);

            if ($result['success']) {
                include __DIR__ . '/../views/auth/password-reset-success.php';
            } else {
                $errors = $result['errors'];
                include __DIR__ . '/../views/auth/reset-password.php';
            }
        } else {
            header("Location: " . BASE_URL . "/auth/login");
            exit;
        }
    }

    // =======================================================
    // API RESET PASSWORD
    // =======================================================
    public function apiForgotPassword() {
        header('Content-Type: application/json; charset=utf-8');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Método não permitido']);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);

        if (!is_array($data) || !isset($data['email'])) {
            http_response_code(400);
            echo json_encode(['error' => 'JSON inválido ou campo email ausente']);
            return;
        }

        $result = $this->authService->requestPasswordReset($data['email']);
        echo json_encode(['message' => $result['message']]);
    }

    public function apiResetPassword() {
        header('Content-Type: application/json; charset=utf-8');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Método não permitido']);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);

        if (!is_array($data)) {
            http_response_code(400);
            echo json_encode(['error' => 'JSON inválido']);
            return;
        }

        $result = $this->authService->resetPasswordWithToken(
            $data['token'] ?? '',
            $data['password'] ?? '',
            $data['password_confirm'] ?? ''
        );

        if ($result['success']) {
            echo json_encode(['message' => $result['message']]);
        } else {
            http_response_code(400);
            echo json_encode(['errors' => $result['errors']]);
        }
    }
}