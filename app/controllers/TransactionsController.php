<?php
class TransactionsController
{
    public function create()
{
    $action = BASE_URL . '/transactions/store';
    $today  = date('Y-m-d');
    ?>
    <!-- <<< ADICIONADO: link para CSS -->
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


    public function store()
    {
        $data = [
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

    // Lista todas as transações com botão de editar
    public function index()
    {
        $model = new TransactionModel();
        $transactions = $model->findAll();

        include __DIR__ . '/../views/transactions/index.php';
    }

    // Mostra formulário de edição
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

    // Salva alterações
    public function update()
    {
        $id = $_POST['id'] ?? null;
        if (!$id) {
            echo "<p>ID não informado.</p>";
            return;
        }

        $data = [
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

    // <<< ADICIONADO: excluir transação
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
