<?php

class UserController {
    private $userModel;

    public function __construct() {
        $this->userModel = new UserModel();
    }
    
    /**
     * Exibe o formulário de registro/login (Rota: /user/register)
     */
    public function register() {
        // Se já estiver logado, redireciona para a home
        if (isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/home');
            exit;
        }

        // Pega as mensagens de sucesso ou erro da sessão e as limpa
        $error = $_SESSION['error'] ?? null;
        $success = $_SESSION['success'] ?? null;
        unset($_SESSION['error'], $_SESSION['success']);
        
        // Cria o diretório se não existir (para a View)
        $viewDir = __DIR__ . '/../views/auth';
        if (!is_dir($viewDir)) {
            mkdir($viewDir, 0777, true);
        }

        // Inclui a view de registro/login
        include __DIR__ . '/../views/auth/register.php';
    }

    /**
     * Processa o envio do formulário de registro (Rota: /user/store)
     */
    public function store() {
        // ... (código do store inalterado, pois já estava funcionando) ...

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        
        // 1. Validação de Dados
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = '⚠️ E-mail inválido!';
        } elseif (strlen($password) < 8) {
            $_SESSION['error'] = '⚠️ A senha deve ter no mínimo 8 caracteres.';
        }
        
        if (isset($_SESSION['error'])) {
            header('Location: ' . BASE_URL . '/user/register');
            exit;
        }
        
        // 2. Critério de Aceite: Verificar se o e-mail já existe
        if ($this->userModel->findByEmail($email)) {
            $_SESSION['error'] = '❌ E-mail já cadastrado.';
            header('Location: ' . BASE_URL . '/user/register');
            exit;
        }
        
        // 3. Criação do Usuário
        if ($this->userModel->create($email, $password)) {
            $_SESSION['success'] = '✅ Cadastro realizado com sucesso! Agora você pode fazer o login com seu e-mail e senha.';
        } else {
            $_SESSION['error'] = '❌ Erro ao tentar cadastrar o usuário. Por favor, tente novamente.';
        }
        
        header('Location: ' . BASE_URL . '/user/register');
        exit;
    }

    /**
     * Processa o envio do formulário de login (Nova Rota: /user/authenticate)
     */
    public function authenticate() {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            $_SESSION['error'] = '⚠️ Por favor, informe e-mail e senha.';
            header('Location: ' . BASE_URL . '/user/register');
            exit;
        }

        $user = $this->userModel->findByEmailAndPassword($email, $password);

        if ($user) {
            // Sucesso: Autenticação bem-sucedida
            $_SESSION['user_id'] = $user['id']; // CRUCIAL para as Transações!
            $_SESSION['user_name'] = $user['name'];
            
            // Redireciona para a página inicial (home.php)
            header('Location: ' . BASE_URL . '/home');
            exit;

        } else {
            // Falha na autenticação
            $_SESSION['error'] = '❌ E-mail ou senha incorretos.';
            header('Location: ' . BASE_URL . '/user/register');
            exit;
        }
    }
    
    /**
     * Finaliza a sessão do usuário (Nova Rota: /user/logout)
     */
    public function logout() {
        session_destroy();
        $_SESSION = [];
        header('Location: ' . BASE_URL . '/user/register');
        exit;
    }

    /**
     * Exibe a página de perfil do usuário.
     */
    public function profile() {
        // Protege a página: precisa estar logado
        if (empty($_SESSION['user']['id'])) {
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }

        $userId = $_SESSION['user']['id'];
        $user = $this->userModel->findById($userId);

        // Pega mensagens de sucesso ou erro da sessão e as limpa
        $success = $_SESSION['success'] ?? null;
        $error = $_SESSION['error'] ?? null;
        unset($_SESSION['success'], $_SESSION['error']);

        // Cria o diretório se não existir (para a View)
        $viewDir = __DIR__ . '/../views/user';
        if (!is_dir($viewDir)) {
            mkdir($viewDir, 0777, true);
        }

        include __DIR__ . '/../views/user/profile.php';
    }

    /**
     * Processa a atualização dos dados do perfil.
     */
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_SESSION['user']['id'])) {
            header('Location: ' . BASE_URL . '/home');
            exit;
        }
        
        $userId = $_SESSION['user']['id'];
        $name = trim($_POST['name'] ?? '');
        $password = $_POST['password'] ?? '';
        $password_confirm = $_POST['password_confirm'] ?? '';

        // 1. Validação dos dados
        if (empty($name)) {
            $_SESSION['error'] = 'O nome não pode ficar em branco.';
            header('Location: ' . BASE_URL . '/user/profile');
            exit;
        }

        if (!empty($password)) {
            if (strlen($password) < 8) {
                $_SESSION['error'] = 'A nova senha deve ter no mínimo 8 caracteres.';
                header('Location: ' . BASE_URL . '/user/profile');
                exit;
            }
            if ($password !== $password_confirm) {
                $_SESSION['error'] = 'As senhas não coincidem.';
                header('Location: ' . BASE_URL . '/user/profile');
                exit;
            }
        }

        // 2. Atualizar no banco
        if ($this->userModel->updateProfile($userId, $name, $password)) {
            $_SESSION['success'] = 'Perfil atualizado com sucesso!';
            // Atualiza o nome na sessão para refletir imediatamente no cabeçalho
            $_SESSION['user']['name'] = $name;
        } else {
            $_SESSION['error'] = 'Ocorreu um erro ao atualizar o perfil. Tente novamente.';
        }

        header('Location: ' . BASE_URL . '/user/profile');
        exit;
    }
}
