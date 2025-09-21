<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Lista de Transações</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">
</head>
<body>
    <h1>Lista de Transações</h1>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Tipo</th>
                <th>Categoria</th>
                <th>Descrição</th>
                <th>Valor (R$)</th>
                <th>Data</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($transactions as $t): ?>
                <tr>
                    <td><?= htmlspecialchars($t['id']) ?></td>
                    <td><?= htmlspecialchars($t['type']) ?></td>
                    <td><?= htmlspecialchars($t['category']) ?></td>
                    <td><?= htmlspecialchars($t['description']) ?></td>
                    <td><?= number_format($t['amount'], 2, ',', '.') ?></td>
                    <td><?= htmlspecialchars($t['date']) ?></td>
                    <td class="table-actions">
                        <a href="<?= BASE_URL ?>/transactions/edit?id=<?= $t['id'] ?>">Editar</a> |
                        <a href="<?= BASE_URL ?>/transactions/delete?id=<?= $t['id'] ?>" 
                           onclick="return confirm('Tem certeza que deseja excluir esta transação?')">Excluir</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <p><a href="<?= BASE_URL ?>/transactions/create">➕ Nova Transação</a></p>
</body>
</html>
