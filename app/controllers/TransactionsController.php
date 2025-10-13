<?php
class TransactionsController
{
    public function create()
    {
        $action = BASE_URL . '/transactions/store';
        $today  = date('Y-m-d');
        ?>
        <?php include __DIR__ . '/../views/partials/header.php'; ?>
        <script src="<?= BASE_URL ?>/js/theme-switcher.js"></script>

        <!-- <<< ADICIONADO: link para CSS -->
        <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">
        <a href="<?= BASE_URL ?>/home" class="home-logo-link">
            <img src="<?= BASE_URL ?>/images/DinDin_Logo_Option2.jpg" alt="Voltar para a Home">
        </a>
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

    public function store()
    {
        // <<< ADICIONADO: pegar usuário logado
        $userId = $_SESSION['user']['id'] ?? null;
        if (!$userId) {
            die("❌ Usuário não autenticado!");
        }

        $data = [
            'user_id'     => $userId,  // <<< passa o ID do usuário
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

    // =====    PARTE DA FEATURE DE (edição) =====

    public function index()
    {
        $model = new TransactionModel();

        // <<< ADICIONADO: filtrar pelo usuário logado
        $userId = $_SESSION['user']['id'] ?? null;
        if (!$userId) {
            die("❌ Usuário não autenticado!");
        }

        // Capturar parâmetros de filtro
        $filters = [
            'type'       => $_GET['type'] ?? '',
            'category'   => $_GET['category'] ?? '',
            'description'=> $_GET['description'] ?? '',
            'start_date' => $_GET['start_date'] ?? '',
            'end_date'   => $_GET['end_date'] ?? ''
        ];

        // Buscar transações SOMENTE do usuário logado
        $transactions = $model->findWithFilters($filters, $userId);

        // Buscar categorias únicas do usuário logado
        $categories = $model->getUniqueCategories($userId);

        include __DIR__ . '/../views/transactions/index.php';
    }

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

    public function update()
    {
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

    public function delete() {
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
}