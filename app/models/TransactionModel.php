<?php
class TransactionModel {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    // CREATE
    public function create(array $data) {
        $sql = "INSERT INTO transactions 
                   (user_id, type, category, description, amount, transaction_date) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            $data['user_id'] ?? null,
            $data['type'],
            $data['category'],
            $data['description'] ?? null,
            $data['amount'],
            $data['transaction_date']
        ]);
    }

    // READ (listar todas as transações de um usuário)
    public function all(?int $userId = null): array {
        if ($userId) {
            $sql = "SELECT * FROM transactions WHERE user_id = ? ORDER BY transaction_date DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$userId]);
        } else {
            $sql = "SELECT * FROM transactions ORDER BY transaction_date DESC";
            $stmt = $this->db->query($sql);
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // READ (buscar uma transação pelo id)
    public function find(int $id): ?array {
        $sql = "SELECT * FROM transactions WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    // UPDATE (editar uma transação existente)
    public function update(int $id, array $data): bool {
        $sql = "UPDATE transactions 
                   SET type = ?, category = ?, description = ?, amount = ?, transaction_date = ?, updated_at = NOW()
                 WHERE id = ?";
        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            $data['type'],
            $data['category'],
            $data['description'] ?? null,
            $data['amount'],
            $data['transaction_date'],
            $id
        ]);
    }
}
