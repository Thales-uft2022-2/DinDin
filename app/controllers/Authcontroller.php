<?php
class AuthController {
    private $userModel;

    public function __construct() {
        require_once __DIR__ . '/../models/User.php';
        // Usa a conexão do Database.php
        $pdo = Database::getConnection();
        $this->userModel = new User($pdo);
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
}
