<?php
class TransactionsController
{
    public function index()
    {
        $model = new TransactionModel();
        $userId = $_SESSION['user_id'] ?? null; // se quiser filtrar por usuário
        $transactions = $model->all($userId);

        $base = BASE_URL;
        include APP_PATH . '/views/transactions/index.php';
    }

    public function create()
    {
        $action = BASE_URL . '/transactions/store';
        $today  = date('Y-m-d');

        // dados vazios para o form
        $data = [
            'id'               => null,
            'type'             => 'income',
            'category'         => '',
            'description'      => '',
            'amount'           => '',
            'transaction_date' => $today,
        ];
        $title = 'Nova Transação';
        include APP_PATH . '/views/transactions/form.php';
    }

    public function store()
    {
        // coleta
        $type        = $_POST['type']        ?? '';
        $category    = trim($_POST['category'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $amountRaw   = trim($_POST['amount']   ?? '');
        $dateRaw     = $_POST['date']         ?? date('Y-m-d');

        // normaliza valor (aceita 1.234,56)
        $amountNorm = str_replace(['.', ','], ['', '.'], $amountRaw);
        if (!is_numeric($amountNorm)) { $amountNorm = -1; }
        $amount = (float)$amountNorm;

        // valida data
        $transactionDate = $dateRaw;
        $validDate = false;
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $transactionDate)) {
            [$Y,$m,$d] = array_map('intval', explode('-', $transactionDate));
            $validDate = checkdate($m,$d,$Y);
        }

        $userId = $_SESSION['user_id'] ?? 1; // provisório

        $errors = [];
        if (!in_array($type, ['income','expense'], true)) $errors[] = 'Tipo inválido';
        if ($amount <= 0) $errors[] = 'Valor deve ser maior que zero';
        if ($category === '') $errors[] = 'Categoria é obrigatória';
        if (!$validDate) $errors[] = 'Data inválida (YYYY-MM-DD)';

        if ($errors) {
            $action = BASE_URL . '/transactions/store';
            $title  = 'Nova Transação';
            $data = [
                'id'               => null,
                'type'             => $type,
                'category'         => $category,
                'description'      => $description,
                'amount'           => $amountRaw,
                'transaction_date' => $transactionDate,
            ];
            $formErrors = $errors;
            include APP_PATH . '/views/transactions/form.php';
            return;
        }

        $payload = [
            'user_id'          => $userId,
            'type'             => $type,
            'category'         => $category,
            'description'      => $description ?: null,
            'amount'           => $amount,
            'transaction_date' => $transactionDate,
        ];

        try {
            $model = new TransactionModel();
            $ok = $model->create($payload);
            header('Location: ' . BASE_URL . '/transactions');
        } catch (Throwable $e) {
            $action = BASE_URL . '/transactions/store';
            $title  = 'Nova Transação';
            $data = [
                'id'               => null,
                'type'             => $type,
                'category'         => $category,
                'description'      => $description,
                'amount'           => $amountRaw,
                'transaction_date' => $transactionDate,
            ];
            $formErrors = ['Erro ao salvar: ' . $e->getMessage()];
            include APP_PATH . '/views/transactions/form.php';
        }
    }

    public function edit()
    {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($id <= 0) { http_response_code(400); echo 'ID inválido'; return; }

        $model = new TransactionModel();
        $row = $model->find($id);
        if (!$row) { http_response_code(404); echo 'Transação não encontrada'; return; }

        $title  = 'Editar Transação #' . $id;
        $action = BASE_URL . '/transactions/update';
        $data = [
            'id'               => $row['id'],
            'type'             => $row['type'],
            'category'         => $row['category'],
            'description'      => $row['description'],
            'amount'           => (string)$row['amount'],
            'transaction_date' => $row['transaction_date'],
        ];
        include APP_PATH . '/views/transactions/form.php';
    }

    public function update()
    {
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        if ($id <= 0) { http_response_code(400); echo 'ID inválido'; return; }

        $type        = $_POST['type']        ?? '';
        $category    = trim($_POST['category'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $amountRaw   = trim($_POST['amount']   ?? '');
        $dateRaw     = $_POST['date']         ?? date('Y-m-d');

        $amountNorm = str_replace(['.', ','], ['', '.'], $amountRaw);
        if (!is_numeric($amountNorm)) { $amountNorm = -1; }
        $amount = (float)$amountNorm;

        $transactionDate = $dateRaw;
        $validDate = false;
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $transactionDate)) {
            [$Y,$m,$d] = array_map('intval', explode('-', $transactionDate));
            $validDate = checkdate($m,$d,$Y);
        }

        $errors = [];
        if (!in_array($type, ['income','expense'], true)) $errors[] = 'Tipo inválido';
        if ($amount <= 0) $errors[] = 'Valor deve ser maior que zero';
        if ($category === '') $errors[] = 'Categoria é obrigatória';
        if (!$validDate) $errors[] = 'Data inválida (YYYY-MM-DD)';

        if ($errors) {
            $title  = 'Editar Transação #' . $id;
            $action = BASE_URL . '/transactions/update';
            $data = [
                'id'               => $id,
                'type'             => $type,
                'category'         => $category,
                'description'      => $description,
                'amount'           => $amountRaw,
                'transaction_date' => $transactionDate,
            ];
            $formErrors = $errors;
            include APP_PATH . '/views/transactions/form.php';
            return;
        }

        try {
            $model = new TransactionModel();
            $ok = $model->update($id, [
                'type'             => $type,
                'category'         => $category,
                'description'      => $description ?: null,
                'amount'           => $amount,
                'transaction_date' => $transactionDate,
            ]);
            header('Location: ' . BASE_URL . '/transactions');
        } catch (Throwable $e) {
            $title  = 'Editar Transação #' . $id;
            $action = BASE_URL . '/transactions/update';
            $data = [
                'id'               => $id,
                'type'             => $type,
                'category'         => $category,
                'description'      => $description,
                'amount'           => $amountRaw,
                'transaction_date' => $transactionDate,
            ];
            $formErrors = ['Erro ao atualizar: ' . $e->getMessage()];
            include APP_PATH . '/views/transactions/form.php';
        }
    }
}
