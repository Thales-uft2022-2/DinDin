<?php
class TransactionsController
{
    public function index()
    {
        // coleta filtros da querystring
        $q          = trim($_GET['q'] ?? '');
        $type       = $_GET['type'] ?? '';
        $date_from  = $_GET['date_from'] ?? '';
        $date_to    = $_GET['date_to'] ?? '';
        $amin_raw   = trim($_GET['amount_min'] ?? '');
        $amax_raw   = trim($_GET['amount_max'] ?? '');
        $page       = isset($_GET['page']) ? (int)$_GET['page'] : 1;

        // normaliza valores (aceita "1.234,56")
        $normalizeMoney = function($s) {
            if ($s === '' || $s === null) return '';
            $n = str_replace(['.',','],['','.' ], $s);
            return is_numeric($n) ? $n : '';
        };
        $amount_min = $normalizeMoney($amin_raw);
        $amount_max = $normalizeMoney($amax_raw);

        // valida datas simples (YYYY-MM-DD)
        $isValidDate = function($d) {
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $d)) return false;
            [$Y,$m,$dd] = array_map('intval', explode('-', $d));
            return checkdate($m,$dd,$Y);
        };
        if ($date_from && !$isValidDate($date_from)) $date_from = '';
        if ($date_to   && !$isValidDate($date_to))   $date_to   = '';

        $filters = [
            'q'           => $q ?: null,
            'type'        => in_array($type, ['Receita','Despesa'], true) ? $type : null,
            'date_from'   => $date_from ?: null,
            'date_to'     => $date_to ?: null,
            'amount_min'  => $amount_min,
            'amount_max'  => $amount_max,
        ];

        $model  = new TransactionModel();
        $userId = null; // filtre por usuário se quiser
        $per    = 10;

        $result = $model->search($filters, $userId, $page, $per);

        $transactions = $result['rows'];
        $total        = $result['total'];
        $perPage      = $result['perPage'];
        $currentPage  = $result['page'];
        $base         = BASE_URL;

        // manter filtros na view
        $persist = [
            'q'          => $q,
            'type'       => $type,
            'date_from'  => $date_from,
            'date_to'    => $date_to,
            'amount_min' => $amin_raw,
            'amount_max' => $amax_raw,
        ];

        include APP_PATH . '/views/transactions/index.php';
    }


    public function create()
    {
        $action = BASE_URL . '/transactions/store';
        $today  = date('Y-m-d');

        // dados vazios para o form
        $data = [
            'id'               => null,
            'type'             => 'Receita',
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
        if (!in_array($type, ['Receita','Despesa'], true)) $errors[] = 'Tipo inválido';
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
        if (!in_array($type, ['Receita','Despesa'], true)) $errors[] = 'Tipo inválido';
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
