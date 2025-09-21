<?php
class TransactionModel {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function create($data) {
        $sql = "INSERT INTO transactions (type, category, description, amount, date) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['type'],
            $data['category'],
            $data['description'],
            $data['amount'],
            $data['date']
        ]);
    }

    // ===== NOVOS MÉTODOS PARA EDITAR TRANSAÇÃO ===== //

    // Buscar transação pelo ID
    public function findById($id) {
        $sql = "SELECT * FROM transactions WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(); // retorna array associativo ou false
    }

    // Atualizar transação existente
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
}
