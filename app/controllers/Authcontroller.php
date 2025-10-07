<?php
class AuthController {
    private $userModel;

    public function __construct() {
        require_once __DIR__ . '/../models/User.php';
        $pdo = Database::getConnection();
        $this->userModel = new User($pdo);

        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    // Tela de login + autenticação
    public function login() {
        // Se já estiver logado, redireciona para a home
        if (!empty($_SESSION['user'])) {
            header("Location: " . BASE_URL . "/home");
            exit;
        }

        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            $user = $this->userModel->findByEmail($email);

            if ($user && password_verify($password, $user['password'])) {
                // Salva dados essenciais do usuário na sessão
                $_SESSION['user'] = [
                    'id'    => $user['id'],
                    'name'  => $user['name'],
                    'email' => $user['email']
                ];

                header("Location: " . BASE_URL . "/home");
                exit;
            } else {
                $error = "E-mail ou senha inválidos!";
            }
        }

        include __DIR__ . '/../views/auth/login.php';
    }

    // Tela de registro
    public function register() {
        include __DIR__ . '/../views/auth/register.php';
    }

    // ✅ Logout do sistema
    public function logout() {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        // Limpa todos os dados da sessão
        $_SESSION = [];

        // Destroi a sessão
        session_destroy();

        // Redireciona para tela de login
        header("Location: " . BASE_URL . "/auth/login");
        exit;
    }
}
