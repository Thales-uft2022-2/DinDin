<?php

class CategoryModel {
    private $db;

    public function __construct() {
        if (!class_exists('Database')) {
            require_once __DIR__ . '/../core/Database.php';
        }
        $this->db = Database::getConnection();
    }

    /**
     * Cria uma nova categoria para um utilizador específico.
     * (US-Cat-01)
     */
    public function create(int $userId, string $name, string $type)
    {
        $sql = "INSERT INTO categories (user_id, name, type) VALUES (:user_id, :name, :type)";
        try {
            $stmt = $this->db->prepare($sql);
            $success = $stmt->execute([
                ':user_id' => $userId,
                ':name'    => trim($name),
                ':type'    => $type
            ]);
            if ($success) {
                return $this->db->lastInsertId();
            }
            return false;
        } catch (PDOException $e) {
            if ($e->getCode() == '23000' || $e->getCode() == 1062) {
                // Categoria duplicada para o mesmo usuário
                return false; 
            } else {
                error_log("CategoryModel::create Error: " . $e->getMessage());
                return false;
            }
        }
    }

    /**
     * Busca uma categoria pelo nome e tipo para um utilizador específico.
     * (US-Cat-01)
     */
    public function findByNameAndType(int $userId, string $name, string $type)
    {
        $sql = "SELECT * FROM categories WHERE user_id = :user_id AND name = :name AND type = :type";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':user_id' => $userId,
            ':name'    => trim($name),
            ':type'    => $type
        ]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Busca todas as categorias de um utilizador específico.
     * (US-Cat-02)
     */
    public function findAllByUserId(int $userId): array
    {
        $sql = "SELECT * FROM categories 
                WHERE user_id = :user_id 
                ORDER BY type, name";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':user_id' => $userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("CategoryModel::findAllByUserId Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Busca categorias de um tipo específico (income/expense).
     * Usado ao criar ou editar transações.
     */
    public function getCategoriesByType(int $userId, string $type): array
    {
        $sql = "SELECT id, name FROM categories 
                WHERE user_id = :user_id AND type = :type 
                ORDER BY name";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':user_id' => $userId,
                ':type'    => $type
            ]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("CategoryModel::getCategoriesByType Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Busca uma categoria específica pelo seu ID e ID do utilizador.
     * (US-Cat-03)
     */
    public function findById(int $categoryId, int $userId)
    {
        $sql = "SELECT * FROM categories WHERE id = :id AND user_id = :user_id";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':id'      => $categoryId,
                ':user_id' => $userId
            ]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("CategoryModel::findById Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Atualiza o nome e/ou tipo de uma categoria.
     * (US-Cat-03)
     */
    public function update(int $categoryId, int $userId, string $name, string $type): bool
    {
        $sql = "UPDATE categories 
                SET name = :name, type = :type, updated_at = CURRENT_TIMESTAMP 
                WHERE id = :id AND user_id = :user_id";
        
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':name'    => trim($name),
                ':type'    => $type,
                ':id'      => $categoryId,
                ':user_id' => $userId
            ]);
        } catch (PDOException $e) {
            if ($e->getCode() == '23000' || $e->getCode() == 1062) {
                // Categoria duplicada
                return false;
            } else {
                error_log("CategoryModel::update Error: " . $e->getMessage());
                return false;
            }
        }
    }

    /**
     * Exclui uma categoria específica do usuário logado.
     * (US-Cat-04)
     */
    public function delete(int $categoryId, int $userId): bool
    {
        $sql = "DELETE FROM categories WHERE id = :id AND user_id = :user_id";
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':id' => $categoryId,
                ':user_id' => $userId
            ]);
        } catch (PDOException $e) {
            error_log("CategoryModel::delete Error: " . $e->getMessage());
            return false;
        }
    }
}
