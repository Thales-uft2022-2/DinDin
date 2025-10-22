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
        $stmt = $this->db->prepare('SELECT id, email, password, name FROM users WHERE email = :email'); // Adicionado name
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
     * @param string $name <<< MUDANÇA: Nome vem primeiro agora
     * @param string $email
     * @param string $password Senha pura (será hasheada aqui)
     * @return int|false Retorna o ID do usuário criado ou false em caso de erro. <<< MUDANÇA: Retorna ID
     */
    public function create(string $name, string $email, string $password) // <<< ORDEM MUDOU
    {
        // Gera o hash seguro da senha
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        try {
            $stmt = $this->db->prepare(
                'INSERT INTO users (name, email, password, provider) VALUES (:name, :email, :password, "email")' // <<< ORDEM MUDOU AQUI TAMBÉM
            );
            $success = $stmt->execute([
                'name' => $name, // <<< MUDANÇA
                'email' => $email,
                'password' => $hashedPassword,
            ]);

            if ($success) {
                return $this->db->lastInsertId(); // <<< MUDANÇA: Retorna o ID
            }
            return false;

        } catch (PDOException $e) {
            error_log("Erro ao criar usuário: " . $e->getMessage()); // É bom logar o erro
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

}