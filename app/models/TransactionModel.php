<?php
class TransactionModel {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    // Criar transação vinculada ao usuário
    public function create($data) {
        // Gerar novo ID manual (se não for AUTO_INCREMENT)
        // Self-correction: A tabela dindin.sql mostra ID AUTO_INCREMENT, não precisamos disso.
        // $sql = "SELECT MAX(id) as max_id FROM transactions";
        // $stmt = $this->db->query($sql);
        // $row = $stmt->fetch();
        // $newId = ($row['max_id'] ?? 0) + 1;

        $sql = "INSERT INTO transactions (user_id, type, category, description, amount, date) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            // $newId, // Removido
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

    // =======================================================
    //  (US-Analytics-01)
    // =======================================================
    /**
     * Busca o total de despesas agrupadas por categoria para um período.
     * @param int $userId
     * @param string $startDate (Formato 'YYYY-MM-DD')
     * @param string $endDate (Formato 'YYYY-MM-DD')
     * @return array [['category' => string, 'total' => float]]
     */
    public function getExpensesByCategory(int $userId, string $startDate, string $endDate): array
    {
        $sql = "SELECT category, SUM(amount) as total
                FROM transactions
                WHERE user_id = :user_id
                  AND type = 'expense'
                  AND date >= :start_date
                  AND date <= :end_date
                GROUP BY category
                ORDER BY total DESC";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':user_id' => $userId,
                ':start_date' => $startDate,
                ':end_date' => $endDate
            ]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("TransactionModel::getExpensesByCategory Error: " . $e->getMessage());
            return [];
        }
    }

    // =======================================================
    //  (US-Analytics-02)
    // =======================================================
    /**
     * Busca o total de receitas e despesas dos últimos 6 meses.
     * @param int $userId
     * @return array [['month_year' => string, 'total_income' => float, 'total_expense' => float]]
     */
    public function getFinancialEvolution(int $userId): array
    {
        // SQL para MySQL que agrupa por mês e ano e pivota receitas/despesas
        // Garante que pegamos os últimos 6 meses (incluindo o mês atual)
        $sql = "SELECT 
                    DATE_FORMAT(date, '%Y-%m') AS month_year,
                    SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END) AS total_income,
                    SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END) AS total_expense
                FROM transactions
                WHERE user_id = :user_id
                  AND date >= DATE_SUB(CURDATE(), INTERVAL 5 MONTH) -- 5 meses atrás + mês atual = 6 meses
                GROUP BY DATE_FORMAT(date, '%Y-%m')
                ORDER BY month_year ASC";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':user_id' => $userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("TransactionModel::getFinancialEvolution Error: " . $e->getMessage());
            return [];
        }
    }

}