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
        $stmt = $this->db->prepare('SELECT id, email, password FROM users WHERE email = :email');
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
}
