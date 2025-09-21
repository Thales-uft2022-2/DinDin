<?php
class TransactionModel {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    // Criar transação (já tinha, ajustado para gerar ID manual)
    public function create($data) {
        $sql = "SELECT MAX(id) as max_id FROM transactions";
        $stmt = $this->db->query($sql);
        $row = $stmt->fetch();
        $newId = ($row['max_id'] ?? 0) + 1;

        $sql = "INSERT INTO transactions (id, type, category, description, amount, date) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $newId, 
            $data['type'],
            $data['category'],
            $data['description'],
            $data['amount'],
            $data['date']
        ]);
    }

    // Buscar todas
    public function findAll() {
        $sql = "SELECT * FROM transactions ORDER BY date DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    // Buscar por ID
    public function findById($id) {
        $sql = "SELECT * FROM transactions WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // Atualizar
    public function update($id, $data) {
        $sql = "UPDATE transactions 
                   SET type = ?, category = ?, description = ?, amount = ?, date = ? 
                 WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['type'],
            $data['category'],
            $data['description'],
            $data['amount'],
            $data['date'],
            $id
        ]);
    }

    // <<< ADICIONADO: excluir transação por ID
public function delete($id) {
    $sql = "DELETE FROM transactions WHERE id = ?";
    $stmt = $this->db->prepare($sql);
    return $stmt->execute([$id]);
}

}
