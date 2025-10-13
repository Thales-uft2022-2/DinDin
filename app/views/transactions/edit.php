<link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">
<script src="<?= BASE_URL ?>/js/theme-switcher.js"></script>

<?php include __DIR__ . '/../partials/header.php'; ?>


<div class="form-container">
    <h1>Editar Transação</h1>

    <form method="post" action="<?= htmlspecialchars(BASE_URL . '/transactions/update') ?>">
        <input type="hidden" name="id" value="<?= htmlspecialchars($transaction['id']) ?>">

        <label>Tipo:
            <select name="type" required>
                <option value="income" <?= $transaction['type']==='income'?'selected':'' ?>>Receita</option>
                <option value="expense" <?= $transaction['type']==='expense'?'selected':'' ?>>Despesa</option>
            </select>
        </label><br><br>

        <label>Categoria:
            <input type="text" name="category" value="<?= htmlspecialchars($transaction['category']) ?>" required>
        </label><br><br>

        <label>Descrição:
            <input type="text" name="description" value="<?= htmlspecialchars($transaction['description']) ?>">
        </label><br><br>

        <label>Valor (R$):
            <input type="number" step="0.01" name="amount" value="<?= htmlspecialchars($transaction['amount']) ?>" required>
        </label><br><br>

        <label>Data:
            <input type="date" name="date" value="<?= htmlspecialchars($transaction['date']) ?>">
        </label><br><br>

        <button type="submit">Salvar Alterações</button>
        <button type="button" onclick="window.location.href='<?= BASE_URL ?>/transactions/index'">Voltar</button>
    </form>
</div>