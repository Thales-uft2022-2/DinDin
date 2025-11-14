<?php

class UserController {
    private $userModel;

    public function __construct() {
        $this->userModel = new UserModel();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Exibe a página principal de "Meu Perfil" (Nome e Avatar)
     * (US-Profile-01 - GET)
     */
    public function profile()
    {
        if (empty($_SESSION['user']['id'])) {
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }

        $user = $_SESSION['user'];

        $flashMessage = $_SESSION['flash_message'] ?? null;
        if ($flashMessage) {
            unset($_SESSION['flash_message']);
        }
        
        $validationErrors = $_SESSION['validation_errors'] ?? [];
        if ($validationErrors) {
            unset($_SESSION['validation_errors']);
        }

        // Passa $user, $flashMessage, e $validationErrors para a view
        include __DIR__ . '/../views/user/profile.php';
    }

    /**
     * Exibe a página separada "Alterar Senha"
     * (US-Profile-02 - GET)
     */
    public function showChangePasswordForm()
    {
        if (empty($_SESSION['user']['id'])) {
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }

        $user = $_SESSION['user']; // A view pode precisar
        
        $flashMessage = $_SESSION['flash_message'] ?? null;
        if ($flashMessage) {
            unset($_SESSION['flash_message']);
        }
        
        $validationErrors = $_SESSION['validation_errors'] ?? [];
        if ($validationErrors) {
            unset($_SESSION['validation_errors']);
        }

        // Carrega a view de senha
        include __DIR__ . '/../views/user/change-password.php';
    }

    /**
     * Processa a atualização de NOME
     * (US-Profile-01 - POST)
     */
    public function updateProfile()
    {
        if (empty($_SESSION['user']['id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/profile');
            exit;
        }

        $userId = $_SESSION['user']['id'];
        $newName = trim($_POST['name'] ?? '');

        if (empty($newName)) {
            $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'O nome não pode ficar em branco.'];
            header('Location: ' . BASE_URL . '/profile');
            exit;
        }

        if ($this->userModel->updateName($userId, $newName)) {
            $_SESSION['user']['name'] = $newName;
            $_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Nome atualizado com sucesso!'];
        } else {
            $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'Erro ao atualizar o nome.'];
        }

        header('Location: ' . BASE_URL . '/profile');
        exit;
    }

    /**
     * Processa a atualização de SENHA
     * (US-Profile-02 - POST)
     */
    public function changePassword()
    {
        if (empty($_SESSION['user']['id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/profile/password');
            exit;
        }

        $userId = $_SESSION['user']['id'];
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        $errors = [];

        // Validações
        if (strlen($newPassword) < 8) {
            $errors['new_password'] = 'A nova senha deve ter no mínimo 8 caracteres.';
        }
        if ($newPassword !== $confirmPassword) {
            $errors['confirm_password'] = 'As senhas não coincidem.';
        }

        // Verificar senha atual
        $hash = $this->userModel->getPasswordHash($userId);
        if (!$hash || !password_verify($currentPassword, $hash)) {
            $errors['current_password'] = 'Senha atual incorreta.';
        }

        // Se houver erros, voltar
        if (!empty($errors)) {
            $_SESSION['validation_errors'] = $errors;
            $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'Não foi possível alterar a senha. Verifique os erros.'];
            header('Location: ' . BASE_URL . '/profile/password'); // Redireciona para pág. de senha
            exit;
        }

        // Sucesso: Atualizar senha
        $newHashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        if ($this->userModel->updatePasswordById($userId, $newHashedPassword)) {
            $_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Senha alterada com sucesso!'];
        } else {
            $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'Erro ao atualizar a senha.'];
        }

        header('Location: ' . BASE_URL . '/profile/password'); // Redireciona para pág. de senha
        exit;
    }

    /**
     * Processa a atualização de AVATAR (Foto de Perfil)
     * (US-Profile-01 - POST)
     */
    public function updateAvatar()
    {
        if (empty($_SESSION['user']['id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/profile');
            exit;
        }
        
        $userId = $_SESSION['user']['id'];
        
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['avatar'];
            
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            if (!in_array($file['type'], $allowedTypes)) {
                $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'Erro: Apenas arquivos JPG, PNG, GIF ou WebP.'];
                header('Location: ' . BASE_URL . '/profile');
                exit;
            }
            
            if ($file['size'] > 2 * 1024 * 1024) { // 2MB Max
                $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'Erro: O arquivo é muito grande (máx 2MB).'];
                header('Location: ' . BASE_URL . '/profile');
                exit;
            }
            
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = 'user_' . $userId . '_' . uniqid() . '.' . $extension;
            $uploadDir = 'uploads/avatars/'; 
            $uploadPath = $uploadDir . $filename;
            
            $destination = __DIR__ . '/../../public/' . $uploadPath;

            if (!is_dir(dirname($destination))) {
                mkdir(dirname($destination), 0777, true);
            }
            
            // Apaga a foto antiga, se existir
            $oldAvatar = $_SESSION['user']['avatar'] ?? null;
            if ($oldAvatar && file_exists(__DIR__ . '/../../public/' . $oldAvatar)) {
                unlink(__DIR__ . '/../../public/' . $oldAvatar);
            }

            if (move_uploaded_file($file['tmp_name'], $destination)) {
                if ($this->userModel->updateAvatar($userId, $uploadPath)) {
                    $_SESSION['user']['avatar'] = $uploadPath;
                    $_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Foto de perfil atualizada!'];
                } else {
                    $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'Erro ao salvar o caminho no banco.'];
                }
            } else {
                $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'Erro ao mover o arquivo.'];
            }
        } else {
            $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'Nenhum arquivo enviado ou erro no upload.'];
        }
        
        header('Location: ' . BASE_URL . '/profile');
        exit;
    }

    /**
     * Processa a exclusão do AVATAR (Foto de Perfil)
     */
    public function deleteAvatar()
    {
        if (empty($_SESSION['user']['id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/profile');
            exit;
        }

        $userId = $_SESSION['user']['id'];
        $oldAvatar = $_SESSION['user']['avatar'] ?? null;

        // 1. Apaga o arquivo físico, se existir
        if ($oldAvatar && file_exists(__DIR__ . '/../../public/' . $oldAvatar)) {
            unlink(__DIR__ . '/../../public/' . $oldAvatar);
        }

        // 2. Remove do banco de dados (seta como NULL)
        if ($this->userModel->updateAvatar($userId, null)) {
            $_SESSION['user']['avatar'] = null;
            $_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Foto de perfil removida.'];
        } else {
            $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'Erro ao remover a foto.'];
        }

        header('Location: ' . BASE_URL . '/profile');
        exit;
    }
    
    // --- MÉTODOS ANTIGOS (Mantidos por segurança) ---
    public function register() {
        header('Location: ' . BASE_URL . '/auth/register'); exit;
    }

    // ▼▼▼ FUNÇÃO 'STORE' CORRIGIDA ▼▼▼
    public function store() {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $name = trim($_POST['name'] ?? '');
        if(empty($name)) { $name = explode('@', $email)[0]; }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { $_SESSION['error'] = '⚠️ E-mail inválido!'; }
        elseif (strlen($password) < 8) { $_SESSION['error'] = '⚠️ A senha deve ter no mínimo 8 caracteres.'; }
        if (isset($_SESSION['error'])) { header('Location: ' . BASE_URL . '/auth/register'); exit; }
        if ($this->userModel->findByEmail($email)) {
            $_SESSION['error'] = '❌ E-mail já cadastrado.';
            header('Location: ' . BASE_URL . '/auth/register'); exit;
        }
        if ($this->userModel->create($name, $email, $password)) {
            $_SESSION['success_message'] = '✅ Cadastro realizado com sucesso! Faça o login.';
        } else { $_SESSION['error'] = '❌ Erro ao tentar cadastrar o usuário.'; }
        header('Location: ' . BASE_URL . '/auth/register'); exit;
    }

    public function authenticate() {
       header('Location: ' . BASE_URL . '/auth/login'); exit;
    }
    
    public function logout() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        session_destroy();
        $_SESSION = [];
        header('Location: ' . BASE_URL . '/auth/login');
        exit;
    }
}