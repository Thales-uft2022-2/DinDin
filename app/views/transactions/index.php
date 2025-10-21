<?php include_once __DIR__ . '/../_header.php'; // Inclui o topo (<html>, <head>, <body>, logo) ?>

<h1>Lista de Transações</h1>

<div class="search-container" id="search-container">
    <h2>🔍 Filtros de Busca</h2>
    <form method="get" action="<?= BASE_URL ?>/transactions/index">
        <div class="filter-row">
            <label>
                Tipo:
                <select name="type">
                    <option value="">Todos os tipos</option>
                    <option value="income" <?= ($_GET['type'] ?? '') === 'income' ? 'selected' : '' ?>>📈 Receita</option>
                    <option value="expense" <?= ($_GET['type'] ?? '') === 'expense' ? 'selected' : '' ?>>📉 Despesa</option>
                </select>
            </label>
            
            <label>
                Categoria:
                <input type="text" name="category" list="categories" 
                    value="<?= htmlspecialchars($_GET['category'] ?? '') ?>"
                    placeholder="Digite uma categoria">
                <datalist id="categories">
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= htmlspecialchars($cat) ?>">
                    <?php endforeach; ?>
                </datalist>
            </label>
            
            <label>
                Descrição:
                <input type="text" name="description" 
                    value="<?= htmlspecialchars($_GET['description'] ?? '') ?>"
                    placeholder="Buscar na descrição">
            </label>
        </div>
        
        <div class="filter-row">
            <label>
                Data Início:
                <input type="date" name="start_date" 
                    value="<?= htmlspecialchars($_GET['start_date'] ?? '') ?>">
            </label>
            
            <label>
                Data Fim:
                <input type="date" name="end_date" 
                    value="<?= htmlspecialchars($_GET['end_date'] ?? '') ?>">
            </label>
            
            <div style="display: flex; gap: 10px; align-items: flex-end;">
                <button type="submit" class="btn primary">🔍 Filtrar</button>
                <a href="<?= BASE_URL ?>/transactions/index" class="btn secondary">🗑️ Limpar</a>
            </div>
        </div>
    </form>
</div>    
<div class="results-info">
    <p>Encontradas: <strong><?= count($transactions) ?></strong> transação(ões)</p>
</div>

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
        <?php if (empty($transactions)): ?>
            <tr>
                <td colspan="7" style="text-align: center;">Nenhuma transação encontrada</td>
            </tr>
        <?php else: ?>
            <?php foreach ($transactions as $t): ?>
                <tr>
                    <td><?= htmlspecialchars($t['id']) ?></td>
                    <td>
                        <span class="badge <?= $t['type'] === 'income' ? 'badge-income' : 'badge-expense' ?>">
                            <?= $t['type'] === 'income' ? '📈 Receita' : '📉 Despesa' ?>
                        </span>
                    </td>
                    <td><?= htmlspecialchars($t['category']) ?></td>
                    <td><?= htmlspecialchars($t['description']) ?></td>
                    <td class="<?= $t['type'] === 'income' ? 'text-income' : 'text-expense' ?>">
                        R$ <?= number_format($t['amount'], 2, ',', '.') ?>
                    </td>
                    <td><?= date('d/m/Y', strtotime($t['date'])) ?></td>
                    <td class="table-actions">
                        <a href="<?= BASE_URL ?>/transactions/edit?id=<?= $t['id'] ?>">Editar</a> |
                        <a href="<?= BASE_URL ?>/transactions/delete?id=<?= $t['id'] ?>" 
                           onclick="return confirm('Tem certeza que deseja excluir esta transação?')">Excluir</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<p><a href="<?= BASE_URL ?>/transactions/create">➕ Nova Transação</a></p>

<?php include_once __DIR__ . '/../_footer.php'; // Inclui o rodapé (</body>, </html>) ?>