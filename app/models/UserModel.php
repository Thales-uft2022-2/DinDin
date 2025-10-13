<?php

class UserModel {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Verifica se um usuário com o email fornecido já existe.
     * @param string $email
     * @return array|null Retorna os dados do usuário se encontrado, ou null.
     */
    public function findByEmail(string $email): ?array {
        $stmt = $this->db->prepare('SELECT id, email, password, name FROM users WHERE email = :email');
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();
        
        return $user ?: null;
    }
    
    /**
     * Tenta encontrar um usuário pelo email e verifica a senha.
     * @param string $email
     * @param string $password
     * @return array|null Retorna os dados do usuário (id, email, name) se a autenticação for bem-sucedida, ou null.
     */
    public function findByEmailAndPassword(string $email, string $password): ?array {
        $stmt = $this->db->prepare('SELECT id, email, password, name FROM users WHERE email = :email AND provider = "email"');
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        // Se o usuário for encontrado e a senha corresponder ao hash salvo
        if ($user && password_verify($password, $user['password'])) {
            // Remove o hash da senha antes de retornar (segurança)
            unset($user['password']); 
            return $user;
        }
        
        return null;
    }


    /**
     * Cria um novo usuário no banco de dados.
     * @param string $email
     * @param string $password
     * @return bool Retorna true em caso de sucesso.
     */
    public function create(string $email, string $password): bool {
        // Gera o hash seguro da senha
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        // Define o nome inicial como a parte inicial do e-mail
        $name = ucfirst(explode('@', $email)[0]); 

        try {
            $stmt = $this->db->prepare(
                'INSERT INTO users (email, password, name, provider) VALUES (:email, :password, :name, "email")'
            );
            return $stmt->execute([
                'email' => $email,
                'password' => $hashedPassword,
                'name' => $name,
            ]);
        } catch (PDOException $e) {
            // Se ocorrer um erro (ex: falha de conexão, erro na query), retorna false
            return false;
        }
    }


    /**
     * Salva o token de redefinição de senha para um usuário.
     * @param string $email
     * @param string $token
     * @param string $expires
     * @return bool
     */
    public function saveResetToken(string $email, string $token, string $expires): bool {
        $sql = "UPDATE users SET reset_token = :token, reset_token_expires = :expires WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'token' => $token,
            'expires' => $expires,
            'email' => $email
        ]);
    }

    /**
     * Encontra um usuário por um token de redefinição válido.
     * @param string $token
     * @return array|null
     */
    public function findUserByResetToken(string $token): ?array {
        $sql = "SELECT id, email FROM users WHERE reset_token = :token AND reset_token_expires > NOW()";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['token' => $token]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    /**
     * Atualiza a senha do usuário e limpa o token de redefinição.
     * @param int $userId
     * @param string $newPassword
     * @return bool
     */
    public function updatePassword(int $userId, string $newPassword): bool {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET password = :password, reset_token = NULL, reset_token_expires = NULL WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'password' => $hashedPassword,
            'id' => $userId
        ]);
    }

    /**
     * Busca os dados de um usuário pelo seu ID.
     * @param int $userId
     * @return array|null
     */
    public function findById(int $userId): ?array {
        $stmt = $this->db->prepare('SELECT id, name, email FROM users WHERE id = :id');
        $stmt->execute(['id' => $userId]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    /**
     * Atualiza o nome e, opcionalmente, a senha de um usuário.
     * @param int    $userId
     * @param string $name
     * @param string|null $newPassword
     * @return bool
     */
    public function updateProfile(int $userId, string $name, ?string $newPassword): bool {
        $params = ['name' => $name, 'id' => $userId];
        $sql = "UPDATE users SET name = :name";

        // Se uma nova senha foi fornecida, adiciona à query
        if (!empty($newPassword)) {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $sql .= ", password = :password";
            $params['password'] = $hashedPassword;
        }

        $sql .= " WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

}
