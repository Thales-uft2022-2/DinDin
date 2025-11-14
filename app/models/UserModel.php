<?php

class UserModel {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function findByEmail(string $email): ?array {
        // Seleciona o avatar também
        $stmt = $this->db->prepare('SELECT id, email, password, name, avatar FROM users WHERE email = :email');
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public function findByEmailAndPassword(string $email, string $password): ?array {
        // Seleciona o avatar também
        $stmt = $this->db->prepare('SELECT id, email, password, name, avatar FROM users WHERE email = :email AND provider = "email"');
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();
        if ($user && password_verify($password, $user['password'])) {
            unset($user['password']);
            return $user;
        }
        return null;
    }

    public function create(string $name, string $email, string $password)
    {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        try {
            $stmt = $this->db->prepare(
                'INSERT INTO users (name, email, password, provider) VALUES (:name, :email, :password, "email")'
            );
            $success = $stmt->execute([
                'name' => $name,
                'email' => $email,
                'password' => $hashedPassword,
            ]);
            if ($success) {
                return $this->db->lastInsertId();
            }
            return false;
        } catch (PDOException $e) {
            error_log("Erro ao criar usuário: " . $e->getMessage());
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

    public function updateName(int $userId, string $name): bool
    {
        $sql = "UPDATE users SET name = :name, updated_at = CURRENT_TIMESTAMP WHERE id = :id";
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':name' => $name,
                ':id' => $userId
            ]);
        } catch (PDOException $e) {
            error_log("UserModel::updateName Error: " . $e->getMessage());
            return false;
        }
    }

    // ▼▼▼ MÉTODO ATUALIZADO (aceita null) ▼▼▼
    public function updateAvatar(int $userId, ?string $avatarPath): bool
    {
        $sql = "UPDATE users SET avatar = :avatar, updated_at = CURRENT_TIMESTAMP WHERE id = :id";
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':avatar' => $avatarPath, // Pode ser null
                ':id' => $userId
            ]);
        } catch (PDOException $e) {
            error_log("UserModel::updateAvatar Error: " . $e->getMessage());
            return false;
        }
    }

    public function getPasswordHash(int $userId): ?string
    {
        $sql = "SELECT password FROM users WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $userId]);
        $result = $stmt->fetch();
        return $result['password'] ?? null;
    }

    public function updatePasswordById(int $userId, string $newHashedPassword): bool
    {
        $sql = "UPDATE users SET password = :password, updated_at = CURRENT_TIMESTAMP WHERE id = :id";
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':password' => $newHashedPassword,
                ':id' => $userId
            ]);
        } catch (PDOException $e) {
            error_log("UserModel::updatePasswordById Error: " . $e->getMessage());
            return false;
        }
    }
}