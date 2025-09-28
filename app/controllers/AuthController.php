<?php
require_once __DIR__ . '/../models/UserModel.php';

class AuthController
{
    public function login()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        if (isset($_SESSION['user'])) {
            header("Location: " . BASE_URL . "/home");
            exit;
        }
        require __DIR__ . '/../views/auth/login.php';
    }

    // === REGISTRO (GET) ===
    public function register()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        if (isset($_SESSION['user'])) {
            header("Location: " . BASE_URL . "/home");
            exit;
        }
        require __DIR__ . '/../views/auth/register.php';
    }

    // === REGISTRO (POST) ===
    public function store()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();

        $nome  = trim($_POST['nome'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $senha = $_POST['password'] ?? '';

        $erros = [];

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $erros[] = "E-mail inválido.";
        if (strlen($senha) < 8)                        $erros[] = "A senha deve ter no mínimo 8 caracteres.";

        $userModel = new UserModel();
        if ($email && $userModel->findByEmail($email)) $erros[] = "E-mail já cadastrado.";

        if ($erros) {
            $_SESSION['erro'] = implode(' | ', $erros);
            header("Location: " . BASE_URL . "/auth/register");
            exit;
        }

        if (!$nome) $nome = 'Usuário';

        // cria usuário (UserModel::create já faz o hash)
        if (!$userModel->create($nome, $email, $senha)) {
            $_SESSION['erro'] = "Falha ao criar usuário.";
            header("Location: " . BASE_URL . "/auth/register");
            exit;
        }

        // autentica e vai para home
        $user = $userModel->findByEmail($email);
        $_SESSION['user'] = $user;
        header("Location: " . BASE_URL . "/home");
        exit;
    }

    public function authenticate()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        $email = $_POST['email'] ?? '';
        $senha = $_POST['senha'] ?? '';

        $userModel = new UserModel();
        $user = $userModel->findByEmail($email);

        if ($user && !empty($user['senha']) && password_verify($senha, $user['senha'])) {
            $_SESSION['user'] = $user;
            header("Location: " . BASE_URL . "/home");
            exit;
        }

        $_SESSION['erro'] = "E-mail ou senha inválidos.";
        header("Location: " . BASE_URL . "/auth/login");
        exit;
    }

    public function logout()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        session_destroy();
        header("Location: " . BASE_URL . "/auth/login");
        exit;
    }
}