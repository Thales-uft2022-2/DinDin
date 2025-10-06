<?php
class TransactionModel {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    // Criar transação vinculada ao usuário
    public function create($data) {
        // Gerar novo ID manual (se não for AUTO_INCREMENT)
        $sql = "SELECT MAX(id) as max_id FROM transactions";
        $stmt = $this->db->query($sql);
        $row = $stmt->fetch();
        $newId = ($row['max_id'] ?? 0) + 1;

        $sql = "INSERT INTO transactions (id, user_id, type, category, description, amount, date) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $newId,
            $data['user_id'],   // <<< ID do usuário logado
            $data['type'],
            $data['category'],
            $data['description'],
            $data['amount'],
            $data['date']
        ]);
    }

    // Buscar todas as transações de um usuário
    public function findAll($userId) {
        $sql = "SELECT * FROM transactions WHERE user_id = ? ORDER BY date DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    // Buscar transação por ID (respeitando o usuário)
    public function findById($id) {
        $sql = "SELECT * FROM transactions WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // Atualizar transação
    public function update($id, $data) {
        $sql = "UPDATE transactions 
                   SET type = ?, category = ?, description = ?, amount = ?, date = ? 
                 WHERE id = ? AND user_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['type'],
            $data['category'],
            $data['description'],
            $data['amount'],
            $data['date'],
            $id,
            $data['user_id']   // garante que só atualiza se for do dono
        ]);
    }

    // Excluir transação
    public function delete($id) {
        $sql = "DELETE FROM transactions WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    // Buscar transações com filtros (somente do usuário)
    public function findWithFilters($filters, $userId) {
        $sql = "SELECT * FROM transactions WHERE user_id = ?";
        $params = [$userId];
        
        if (!empty($filters['type'])) {
            $sql .= " AND type = ?";
            $params[] = $filters['type'];
        }
        if (!empty($filters['category'])) {
            $sql .= " AND category LIKE ?";
            $params[] = '%' . $filters['category'] . '%';
        }
        if (!empty($filters['description'])) {
            $sql .= " AND description LIKE ?";
            $params[] = '%' . $filters['description'] . '%';
        }
        if (!empty($filters['start_date'])) {
            $sql .= " AND date >= ?";
            $params[] = $filters['start_date'];
        }
        if (!empty($filters['end_date'])) {
            $sql .= " AND date <= ?";
            $params[] = $filters['end_date'];
        }

        $sql .= " ORDER BY date DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    // Buscar categorias únicas do usuário
    public function getUniqueCategories($userId) {
        $sql = "SELECT DISTINCT category FROM transactions WHERE user_id = ? ORDER BY category";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}