<?php
// app/controllers/UserController.php

class UserController {
    private $userModel;

    public function __construct() {
        $this->userModel = new UserModel(); 
        
        $caller = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1]['function'] ?? '';

        if (empty($_SESSION['user']['id']) && !in_array($caller, ['register', 'store'])) {
            header("Location: " . BASE_URL . "/auth/login");
            exit;
        }
    }
    
    public function register() {
        if (!empty($_SESSION['user']['id'])) {
            header('Location: ' . BASE_URL . '/home');
            exit;
        }

        $error = $_SESSION['error'] ?? null;
        $success = $_SESSION['success'] ?? null;
        unset($_SESSION['error'], $_SESSION['success']);
        
        include __DIR__ . '/../views/auth/register.php';
    }

    public function store() {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        
        if ($this->userModel->findByEmail($email)) {
            $_SESSION['error'] = '❌ E-mail já cadastrado.';
            header('Location: ' . BASE_URL . '/user/register');
            exit;
        }

        if ($this->userModel->create($email, $password)) {
            $user = $this->userModel->findByEmail($email);
            if ($user) {
                $_SESSION['user'] = [
                    'id' => $user['id'], 
                    'name' => $user['name'],
                    'email' => $user['email']
                ];
                header('Location: ' . BASE_URL . '/home');
                exit;
            } else {
                $_SESSION['success'] = '✅ Cadastro realizado com sucesso! Faça login.';
            }
        } else {
            $_SESSION['error'] = '❌ Erro ao tentar cadastrar o usuário.';
        }
        
        header('Location: ' . BASE_URL . '/user/register');
        exit;
    }

    public function profile() {
        $userId = $_SESSION['user']['id'];
        $user = $this->userModel->findById($userId);
        
        $success = $_SESSION['success'] ?? null;
        $error = $_SESSION['error'] ?? null;
        unset($_SESSION['success'], $_SESSION['error']);
        
        include __DIR__ . '/../views/user/profile.php';
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/home');
            exit;
        }
        
        $userId = $_SESSION['user']['id'];
        $name = trim($_POST['name'] ?? '');
        $password = $_POST['password'] ?? '';
        $password_confirm = $_POST['password_confirm'] ?? '';
        
        if ($this->userModel->updateProfile($userId, $name, $password)) {
            $_SESSION['success'] = 'Perfil atualizado com sucesso!';
            $_SESSION['user']['name'] = $name; 
        } else {
            $_SESSION['error'] = 'Ocorreu um erro ao atualizar o perfil. Tente novamente.';
        }

        header('Location: ' . BASE_URL . '/user/profile');
        exit;
    }
    
    /**
     * US-PROF-03: Exibe a lista de contas disponíveis para troca.
     */
    public function switchAccounts() {
        $currentUserId = $_SESSION['user']['id'];
        
        $associatedAccounts = $this->userModel->findAssociatedAccounts($currentUserId); 
        
        $success = $_SESSION['switch_success'] ?? null;
        $error = $_SESSION['error'] ?? null;
        unset($_SESSION['switch_success'], $_SESSION['error']);
        
        include __DIR__ . '/../views/user/switch-accounts.php';
    }
    
    /**
     * US-PROF-03: Exibe o formulário para CONFIRMAR A SENHA antes de trocar de conta.
     */
    public function confirmSwitch() {
        $targetUserId = $_GET['id'] ?? null;
        
        if (empty($targetUserId)) {
            $_SESSION['error'] = 'ID de usuário para troca não fornecido.';
            header("Location: " . BASE_URL . "/user/switch-accounts");
            exit;
        }

        $targetUser = $this->userModel->findById((int)$targetUserId);

        if (!$targetUser) {
            $_SESSION['error'] = 'Conta de destino não encontrada.';
            header("Location: " . BASE_URL . "/user/switch-accounts");
            exit;
        }
        
        $targetName = $targetUser['name'];
        $error = $_SESSION['error'] ?? null;
        unset($_SESSION['error']);
        
        // Inclui a nova view de confirmação
        include __DIR__ . '/../views/user/confirm-switch.php';
    }
    
    /**
     * US-PROF-03: Processa a troca de conta APÓS a confirmação da senha.
     */
    public function doSwitch() {
        $targetUserId = $_POST['id'] ?? null;
        $password = $_POST['password'] ?? '';
        
        if (empty($targetUserId) || empty($password)) {
            $_SESSION['error'] = 'Senha e ID são obrigatórios.';
            header("Location: " . BASE_URL . "/user/confirm-switch?id={$targetUserId}");
            exit;
        }

        // 1. Busca os dados COMPLETO do usuário alvo (incluindo hash da senha)
        $targetUser = $this->userModel->findByIdWithPassword((int)$targetUserId); 

        // 2. Valida a senha (CRITÉRIO DE SEGURANÇA)
        if (!$targetUser || !password_verify($password, $targetUser['password'])) {
            $_SESSION['error'] = 'Senha inválida. Tente novamente.';
            header("Location: " . BASE_URL . "/user/confirm-switch?id={$targetUserId}");
            exit;
        }

        // 3. Sucesso na troca: define o novo usuário na sessão
        $_SESSION['user'] = [
            'id' => $targetUser['id'], 
            'name' => $targetUser['name'],
            'email' => $targetUser['email']
        ];
        
        $_SESSION['switch_success'] = '✅ Sessão trocada com sucesso para ' . htmlspecialchars($targetUser['name']) . '!';
        
        header("Location: " . BASE_URL . "/home");
        exit;
    }
}