<?php
class TransactionModel {
    /** @var PDO */
    private $db;

    public function __construct() {
        $this->db = Database::getConnection(); // deve retornar um PDO
    }

    // CREATE
    public function create(array $data): bool {
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
            $sql = "SELECT * FROM transactions WHERE user_id = ? ORDER BY transaction_date DESC, id DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$userId]);
        } else {
            $sql = "SELECT * FROM transactions ORDER BY transaction_date DESC, id DESC";
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

    /**
     * Busca com filtros + paginação.
     * @param array     $filt  keys: q, type, date_from, date_to, amount_min, amount_max
     * @param int|null  $userId filtra por usuário (opcional)
     * @param int       $page   página 1..N
     * @param int       $per    itens por página
     * @return array    ['rows'=>[], 'total'=>int, 'page'=>int, 'perPage'=>int]
     */
    public function search(array $filt = [], ?int $userId = null, int $page = 1, int $per = 20): array
    {
        $where = [];
        $bind  = [];

        if ($userId) {
            $where[] = 'user_id = ?';
            $bind[]  = $userId;
        }
        if (!empty($filt['q'])) {
            $where[] = '(category LIKE ? OR description LIKE ?)';
            $like = '%' . $filt['q'] . '%';
            $bind[] = $like; 
            $bind[] = $like;
        }
        if (!empty($filt['type']) && in_array($filt['type'], ['Receita','Despesa'], true)) {
            $where[] = 'type = ?';
            $bind[]  = $filt['type'];
        }
        if (!empty($filt['date_from'])) {
            $where[] = 'transaction_date >= ?';
            $bind[]  = $filt['date_from'];
        }
        if (!empty($filt['date_to'])) {
            $where[] = 'transaction_date <= ?';
            $bind[]  = $filt['date_to'];
        }
        if (isset($filt['amount_min']) && $filt['amount_min'] !== '') {
            $where[] = 'amount >= ?';
            $bind[]  = (float)$filt['amount_min'];
        }
        if (isset($filt['amount_max']) && $filt['amount_max'] !== '') {
            $where[] = 'amount <= ?';
            $bind[]  = (float)$filt['amount_max'];
        }

        $sqlWhere = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

        // total
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM transactions $sqlWhere");
        $stmt->execute($bind);
        $total = (int)$stmt->fetchColumn();

        // paginação com clamp
        $per         = max(1, (int)$per);
        $totalPages  = max(1, (int)ceil($total / $per));
        $page        = max(1, min((int)$page, $totalPages));
        $offset      = ($page - 1) * $per;

        // rows
        $stmt = $this->db->prepare("
            SELECT * FROM transactions
            $sqlWhere
            ORDER BY transaction_date DESC, id DESC
            LIMIT $per OFFSET $offset
        ");
        $stmt->execute($bind);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return ['rows'=>$rows, 'total'=>$total, 'page'=>$page, 'perPage'=>$per];
    }
}
