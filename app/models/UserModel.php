<?php
// app/models/UserModel.php

class UserModel {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function findByEmail(string $email): ?array {
        $stmt = $this->db->prepare('SELECT id, email, password, name FROM users WHERE email = :email');
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();
        
        return $user ?: null;
    }
    
    public function findByEmailAndPassword(string $email, string $password): ?array {
        $stmt = $this->db->prepare('SELECT id, email, password, name FROM users WHERE email = :email AND provider = "email"');
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            unset($user['password']); 
            return $user;
        }
        
        return null;
    }

    public function create(string $email, string $password): bool {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
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
            return false;
        }
    }

    public function saveResetToken(string $email, string $token, string $expires): bool {
        $sql = "UPDATE users SET reset_token = :token, reset_token_expires = :expires WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'token' => $token,
            'expires' => $expires,
            'email' => $email
        ]);
    }

    public function findUserByResetToken(string $token): ?array {
        $sql = "SELECT id, email FROM users WHERE reset_token = :token AND reset_token_expires > NOW()";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['token' => $token]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public function updatePassword(int $userId, string $newPassword): bool {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET password = :password, reset_token = NULL, reset_token_expires = NULL WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'password' => $hashedPassword,
            'id' => $userId
        ]);
    }

    public function findById(int $userId): ?array {
        $stmt = $this->db->prepare('SELECT id, name, email FROM users WHERE id = :id');
        $stmt->execute(['id' => $userId]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    /**
     * NOVO: Busca os dados COMPLETO de um usuário pelo seu ID (INCLUI SENHA para Confirmação).
     */
    public function findByIdWithPassword(int $userId): ?array {
        $stmt = $this->db->prepare('SELECT id, name, email, password FROM users WHERE id = :id');
        $stmt->execute(['id' => $userId]);
        $user = $stmt->fetch();
        return $user ?: null;
    }
    
    public function updateProfile(int $userId, string $name, ?string $newPassword): bool {
        $params = ['name' => $name, 'id' => $userId];
        $sql = "UPDATE users SET name = :name";

        if (!empty($newPassword)) {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $sql .= ", password = :password";
            $params['password'] = $hashedPassword;
        }

        $sql .= " WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }
    
    /**
     * NOVO: Lista as contas associadas (busca todos os usuários).
     */
    public function findAssociatedAccounts(int $currentUserId): array {
        $stmt = $this->db->prepare('SELECT id, name, email FROM users ORDER BY name');
        $stmt->execute();
        $allAccounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $accounts = [];
        
        // Coloca a conta atual primeiro
        foreach ($allAccounts as $account) {
            if ((int)$account['id'] === $currentUserId) {
                $account['current'] = true;
                array_unshift($accounts, $account);
            } else {
                $accounts[] = $account;
            }
        }
        
        return $accounts;
    }
}