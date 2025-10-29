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
     *
     * @param int $userId ID do utilizador dono da categoria.
     * @param string $name Nome da categoria.
     * @param string $type Tipo ('income' ou 'expense').
     * @return int|false Retorna o ID da nova categoria criada ou false em caso de erro/duplicidade.
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
            // Verifica se o erro é de violação de chave única (código 23000 ou 1062 no MySQL)
            if ($e->getCode() == '23000' || $e->getCode() == 1062) {
                // Erro de duplicidade (nome + tipo já existe para este utilizador)
                return false; // Indicamos falha, o Service tratará a mensagem de erro.
            } else {
                // Outro erro de base de dados
                error_log("CategoryModel::create Error: " . $e->getMessage());
                return false;
            }
        }
    }

    /**
     * Busca uma categoria pelo nome e tipo para um utilizador específico.
     * Útil para verificar se uma categoria já existe antes de criar.
     * (US-Cat-01)
     *
     * @param int $userId
     * @param string $name
     * @param string $type
     * @return array|false Retorna os dados da categoria se encontrada, senão false.
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
     *
     * @param int $userId ID do utilizador.
     * @return array Retorna uma lista de categorias.
     */
    public function findAllByUserId(int $userId): array
    {
        // Ordena por tipo (para agrupar receitas/despesas) e depois por nome
        $sql = "SELECT * FROM categories 
                WHERE user_id = :user_id 
                ORDER BY type, name";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':user_id' => $userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("CategoryModel::findAllByUserId Error: " . $e->getMessage());
            return []; // Retorna lista vazia em caso de erro
        }
    }

}