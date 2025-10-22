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
     *
     * @param array $data Contém 'email', 'password', 'password_confirm', 'name' (opcional)
     * @return array ['success' => bool, 'errors' => array, 'message' => string, 'user' => array|null]
     */
    public function registerUser(array $data): array
    {
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
            // Poderia buscar o usuário recém-criado se necessário retornar mais dados
            $newUser = ['id' => $userId, 'name' => $name, 'email' => $email]; // Simplificado
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

    // --- Métodos de Login, Logout, Recuperar Senha virão aqui nas próximas tarefas ---

}