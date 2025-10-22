
<?php

class AuthService
{
    private $userModel;

    public function __construct()
    {
        // O AuthService depende do UserModel
        if (!class_exists('UserModel')) {
            require_once __DIR__ . '/../models/UserModel.php';
        }
        $this->userModel = new UserModel();
    }

    /**
     * Valida os dados e registra um novo usuário.
     * (TS-Auth-01)
     * ... (código do registerUser) ...
     */
    public function registerUser(array $data): array
    {
        // [CÓDIGO OMITIDO POR BREVIDADE - Mantenha o código existente aqui]
        // 1. Limpar e Validar Dados Essenciais
        $email = filter_var(trim($data['email'] ?? ''), FILTER_VALIDATE_EMAIL);
        $password = $data['password'] ?? '';
        $passwordConfirm = $data['password_confirm'] ?? '';
        $name = trim($data['name'] ?? ''); // Nome é opcional no registro inicial

        $errors = [];

        if (!$email) {
            $errors[] = 'E-mail inválido.';
        }
        if (strlen($password) < 8) {
            $errors[] = 'A senha deve ter no mínimo 8 caracteres.';
        }
        if ($password !== $passwordConfirm) {
            $errors[] = 'As senhas não coincidem.';
        }

        // 2. Critério de Aceite: Verificar se o e-mail já existe
        if ($email && $this->userModel->findByEmail($email)) {
            $errors[] = 'E-mail já cadastrado.';
        }

        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors, 'message' => 'Erro na validação.'];
        }

        // 3. Preparar dados para o Model (Nome padrão se não informado)
        if (empty($name)) {
             $name = ucfirst(explode('@', $email)[0]); // Usa a parte antes do @ como nome padrão
        }

        // 4. Chamar o Model para criar o usuário
        $userId = $this->userModel->create($name, $email, $password); // Supondo que create retorne o ID ou false

        if ($userId) {
            $newUser = ['id' => $userId, 'name' => $name, 'email' => $email];
            return [
                'success' => true,
                'message' => 'Usuário registrado com sucesso!',
                'user' => $newUser
            ];
        } else {
            return [
                'success' => false,
                'errors' => ['Ocorreu um erro inesperado ao registrar o usuário.'],
                'message' => 'Erro no servidor.'
            ];
        }
    }

    /**
     * Valida as credenciais e realiza o login do usuário.
     * (TS-Auth-02)
     * ... (código do loginUser) ...
     */
    public function loginUser(array $credentials): array
    {
        // [CÓDIGO OMITIDO POR BREVIDADE - Mantenha o código existente aqui]
        // 1. Validar dados de entrada
        $email = filter_var(trim($credentials['email'] ?? ''), FILTER_VALIDATE_EMAIL);
        $password = $credentials['password'] ?? '';

        if (!$email || empty($password)) {
            return ['success' => false, 'errors' => ['E-mail ou senha inválidos.'], 'message' => 'Falha na autenticação.'];
        }

        // 2. Chamar o Model para verificar as credenciais
        $user = $this->userModel->findByEmailAndPassword($email, $password);

        if ($user) {
            $userData = [
                'id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email']
            ];
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            session_regenerate_id(true);
            $_SESSION['user'] = $userData;

            return ['success' => true, 'message' => 'Login realizado com sucesso!', 'user' => $userData];
        } else {
            return ['success' => false, 'errors' => ['E-mail ou senha inválidos.'], 'message' => 'Falha na autenticação.'];
        }
    }

    /**
     * Realiza o logout do usuário destruindo a sessão.
     * (TS-Auth-03 - NOVO MÉTODO)
     *
     * @return array ['success' => bool, 'message' => string]
     */
    public function logoutUser(): array
    {
        // Garante que a sessão está iniciada antes de tentar destruí-la
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Limpa todas as variáveis de sessão
        $_SESSION = [];

        // Se estiver usando cookies de sessão, apaga o cookie também
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        // Finalmente, destrói a sessão
        session_destroy();

        return ['success' => true, 'message' => 'Logout realizado com sucesso!'];
    }

    // --- Método de Recuperar Senha virá aqui ---

}