<?php
require_once __DIR__ . '/../models/TransactionModel.php';

class TransactionsController
{
    // =========================
    // FORMULÁRIO WEB (SEU CÓDIGO)
    // =========================
    public function create()
    {
        $action = BASE_URL . '/transactions/store';
        $today  = date('Y-m-d');
        ?>
        <!-- link do CSS -->
        <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">

        <div class="form-container">
            <h1>Formulário de Nova Transação</h1>

            <form method="post" action="<?= htmlspecialchars($action) ?>">
                <label>Tipo:
                    <select name="type" required>
                        <option value="income">Receita</option>
                        <option value="expense">Despesa</option>
                    </select>
                </label><br><br>

                <label>Categoria:
                    <input type="text" name="category" required>
                </label><br><br>

                <label>Descrição:
                    <input type="text" name="description">
                </label><br><br>

                <label>Valor (R$):
                    <input type="number" step="0.01" name="amount" required>
                </label><br><br>

                <label>Data:
                    <input type="date" name="date" value="<?= htmlspecialchars($today) ?>">
                </label><br><br>

                <button type="submit">Salvar</button>
            </form>
        </div>
        <?php
    }

    // =========================
    // SALVAR PELO FORMULÁRIO (SEU CÓDIGO)
    // =========================
    public function store()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();

        $userId = $_SESSION['user']['id'] ?? null;
        if (!$userId) {
            die("❌ Usuário não autenticado!");
        }

        $data = [
            'user_id'     => $userId,  // passa o ID do usuário
            'type'        => $_POST['type'] ?? '',
            'category'    => trim($_POST['category'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'amount'      => (float) ($_POST['amount'] ?? 0),
            'date'        => $_POST['date'] ?: date('Y-m-d'),
        ];

        $errors = [];
        if (!in_array($data['type'], ['income', 'expense'], true)) {
            $errors[] = 'Tipo inválido';
        }
        if ($data['amount'] <= 0) {
            $errors[] = 'Valor deve ser maior que zero';
        }
        if ($data['category'] === '') {
            $errors[] = 'Categoria é obrigatória';
        }

        if ($errors) {
            foreach ($errors as $e) {
                echo '<p style="color:red;">' . htmlspecialchars($e) . '</p>';
            }
            echo '<p><a href="' . BASE_URL . '/transactions/create">Voltar</a></p>';
            return;
        }

        $model = new TransactionModel();
        if ($model->create($data)) {
            $msg  = "✅ Transação salva com sucesso!";
            $type = "success";
        } else {
            $msg  = "❌ Erro ao salvar transação.";
            $type = "error";
        }

        include __DIR__ . '/../views/transactions/message.php';
    }

    // =========================
    // LISTAGEM (SEU CÓDIGO)
    // =========================
    public function index()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();

        $model = new TransactionModel();

        $userId = $_SESSION['user']['id'] ?? null;
        if (!$userId) {
            die("❌ Usuário não autenticado!");
        }

        $filters = [
            'type'        => $_GET['type'] ?? '',
            'category'    => $_GET['category'] ?? '',
            'description' => $_GET['description'] ?? '',
            'start_date'  => $_GET['start_date'] ?? '',
            'end_date'    => $_GET['end_date'] ?? ''
        ];

        $transactions = $model->findWithFilters($filters, $userId);
        $categories   = $model->getUniqueCategories($userId);

        include __DIR__ . '/../views/transactions/index.php';
    }

    // =========================
    // EDITAR (SEU CÓDIGO)
    // =========================
    public function edit()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            echo "<p>ID não informado.</p>";
            return;
        }

        $model = new TransactionModel();
        $transaction = $model->findById($id);

        if (!$transaction) {
            echo "<p>Transação não encontrada.</p>";
            return;
        }

        include __DIR__ . '/../views/transactions/edit.php';
    }

    // =========================
    // ATUALIZAR (SEU CÓDIGO)
    // =========================
    public function update()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();

        $id = $_POST['id'] ?? null;
        if (!$id) {
            echo "<p>ID não informado.</p>";
            return;
        }

        $userId = $_SESSION['user']['id'] ?? null;
        if (!$userId) {
            die("❌ Usuário não autenticado!");
        }

        $data = [
            'user_id'     => $userId,
            'type'        => $_POST['type'] ?? '',
            'category'    => trim($_POST['category'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'amount'      => (float) ($_POST['amount'] ?? 0),
            'date'        => $_POST['date'] ?: date('Y-m-d'),
        ];

        $errors = [];
        if (!in_array($data['type'], ['income', 'expense'], true)) {
            $errors[] = 'Tipo inválido';
        }
        if ($data['amount'] <= 0) {
            $errors[] = 'Valor deve ser maior que zero';
        }
        if ($data['category'] === '') {
            $errors[] = 'Categoria é obrigatória';
        }

        if ($errors) {
            foreach ($errors as $e) {
                echo '<p style="color:red;">' . htmlspecialchars($e) . '</p>';
            }
            echo '<p><a href="' . BASE_URL . '/transactions/edit?id=' . $id . '">Voltar</a></p>';
            return;
        }

        $model = new TransactionModel();
        if ($model->update($id, $data)) {
            $msg = "✅ Transação atualizada com sucesso!";
        } else {
            $msg = "❌ Erro ao atualizar transação.";
        }

        include __DIR__ . '/../views/transactions/message.php';
    }

    // =========================
    // EXCLUIR (SEU CÓDIGO)
    // =========================
    public function delete()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $msg  = "❌ ID não informado.";
            $type = "error";
            include __DIR__ . '/../views/transactions/message.php';
            return;
        }

        $model = new TransactionModel();
        if ($model->delete($id)) {
            $msg  = "✅ Transação excluída com sucesso!";
            $type = "success";
        } else {
            $msg  = "❌ Erro ao excluir transação.";
            $type = "error";
        }

        include __DIR__ . '/../views/transactions/message.php';
    }

    // =========================
    // API JSON (ACRESCENTADO) — CADASTRAR TRANSAÇÃO
    // =========================
    public function apiCreate()
    {
        header('Content-Type: application/json; charset=utf-8');
        if (session_status() === PHP_SESSION_NONE) session_start();

        $userId = $_SESSION['user']['id'] ?? null;
        if (!$userId) {
            http_response_code(401);
            echo json_encode(['error' => 'Usuário não autenticado']);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Método não permitido']);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        if (!is_array($data)) {
            http_response_code(400);
            echo json_encode(['error' => 'JSON inválido']);
            return;
        }

        // validação mínima
        if (empty($data['type']) || empty($data['amount']) || empty($data['date'])) {
            http_response_code(422);
            echo json_encode(['error' => 'Campos obrigatórios: type, amount, date']);
            return;
        }

        $model = new TransactionModel();
        $ok = $model->create([
            'user_id'     => $userId,
            'type'        => $data['type'],
            'category'    => $data['category'] ?? null,
            'description' => $data['description'] ?? null,
            'amount'      => $data['amount'],
            'date'        => $data['date']
        ]);

        if ($ok) {
            http_response_code(201);
            echo json_encode(['message' => 'Transação cadastrada com sucesso!']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Erro ao cadastrar transação']);
        }
    }
}