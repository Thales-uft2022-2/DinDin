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
}