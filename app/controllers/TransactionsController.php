<?php
class TransactionsController
{
    public function create()
    {
        $action = BASE_URL . '/transactions/store';
        $today  = date('Y-m-d');
        ?>
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
        <?php
    }

    public function store()
    {
        // Coleta e validação simples
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

        // Grava no banco
        $model = new TransactionModel();
        if ($model->create($data)) {
            echo '<h2>✅ Transação salva com sucesso!</h2>';
        } else {
            echo '<h2>❌ Erro ao salvar transação.</h2>';
        }

        echo '<p><a href="' . BASE_URL . '/transactions/create">Cadastrar outra</a></p>';
    }
}