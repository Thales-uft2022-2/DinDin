<?php include_once __DIR__ . '/../_header.php'; // Inclui o topo ?>

<h1 class="mb-4">Lista de Transações</h1>

<div class="card shadow-sm mb-4">
    <div class="card-header bg-white">
        <h2 class="h5 mb-0"><i class="bi bi-funnel-fill me-2 text-primary"></i> Filtros de Busca</h2>
    </div>
    <div class="card-body">
        <form method="get" action="<?= BASE_URL ?>/transactions"> <div class="row g-3 mb-3 align-items-end">
                <div class="col-md-3">
                    <label for="filter-type" class="form-label">Tipo:</label>
                    <select name="type" id="filter-type" class="form-select">
                        <option value="">Todos</option>
                        <option value="income" <?= ($filters['type'] ?? '') === 'income' ? 'selected' : '' ?>>Receita</option>
                        <option value="expense" <?= ($filters['type'] ?? '') === 'expense' ? 'selected' : '' ?>>Despesa</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="filter-category" class="form-label">Categoria:</label>
                    <input type="text" name="category" id="filter-category" class="form-control"
                        list="categories-list"
                        value="<?= htmlspecialchars($filters['category'] ?? '') ?>"
                        placeholder="Digite ou selecione">
                    <datalist id="categories-list">
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= htmlspecialchars($cat) ?>">
                        <?php endforeach; ?>
                    </datalist>
                </div>
                 <div class="col-md-5">
                    <label for="filter-description" class="form-label">Descrição:</label>
                    <input type="text" name="description" id="filter-description" class="form-control"
                        value="<?= htmlspecialchars($filters['description'] ?? '') ?>"
                        placeholder="Buscar na descrição">
                </div>
            </div>
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="filter-start-date" class="form-label">Data Início:</label>
                    <input type="date" name="start_date" id="filter-start-date" class="form-control"
                        value="<?= htmlspecialchars($filters['start_date'] ?? '') ?>">
                </div>
                <div class="col-md-3">
                    <label for="filter-end-date" class="form-label">Data Fim:</label>
                    <input type="date" name="end_date" id="filter-end-date" class="form-control"
                        value="<?= htmlspecialchars($filters['end_date'] ?? '') ?>">
                </div>
                <div class="col-md-6 d-flex justify-content-end gap-2 mt-3 mt-md-0"> <button type="submit" class="btn btn-primary"><i class="bi bi-search me-1"></i> Filtrar</button>
                    <a href="<?= BASE_URL ?>/transactions" class="btn btn-outline-secondary"><i class="bi bi-eraser-fill me-1"></i> Limpar</a>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mb-3">
    <p class="mb-0 text-muted">Encontradas: <strong><?= count($transactions) ?></strong> transação(ões)</p>
    <a href="<?= BASE_URL ?>/transactions/create" class="btn btn-success">
      <i class="bi bi-plus-lg me-1"></i> Nova Transação
    </a>
</div>

<div class="table-responsive">
    <table class="table table-striped table-hover align-middle">
        <thead class="table-dark">
            <tr>
                <th scope="col">#</th>
                <th scope="col">Tipo</th>
                <th scope="col">Categoria</th>
                <th scope="col">Descrição</th>
                <th scope="col" class="text-end">Valor (R$)</th> <th scope="col" class="text-center">Data</th> <th scope="col" class="text-center">Ações</th> </tr>
        </thead>
        <tbody>
            <?php if (empty($transactions)): ?>
                <tr>
                    <td colspan="7" class="text-center text-muted fst-italic py-5"> Nenhuma transação encontrada para os filtros selecionados.
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($transactions as $t): ?>
                    <tr>
                        <td data-label="#"><?= htmlspecialchars($t['id']) ?></td>
                        <td data-label="Tipo">
                            <span class="badge <?= $t['type'] === 'income' ? 'text-bg-success' : 'text-bg-danger' ?>">
                                <?= $t['type'] === 'income' ? '<i class="bi bi-arrow-up-circle me-1"></i>Receita' : '<i class="bi bi-arrow-down-circle me-1"></i>Despesa' ?>
                            </span>
                        </td>
                        <td data-label="Categoria"><?= htmlspecialchars($t['category']) ?></td>
                        <td data-label="Descrição"><?= htmlspecialchars($t['description']) ?></td>
                        <td class="text-end <?= $t['type'] === 'income' ? 'text-income' : 'text-expense' ?> fw-bold" data-label="Valor (R$)">
                            <?= ($t['type'] === 'expense' ? '-' : '+') // Adiciona sinal ?>
                            R$ <?= number_format($t['amount'], 2, ',', '.') ?>
                        </td>
                        <td class="text-center" data-label="Data"><?= date('d/m/Y', strtotime($t['date'])) ?></td>
                        <td class="text-center table-actions" data-label="Ações">
                            <a href="<?= BASE_URL ?>/transactions/edit?id=<?= $t['id'] ?>" class="btn btn-sm btn-outline-primary" title="Editar">
                               <i class="bi bi-pencil-fill"></i>
                            </a>
                            <a href="<?= BASE_URL ?>/transactions/delete?id=<?= $t['id'] ?>"
                               class="btn btn-sm btn-outline-danger" title="Excluir"
                               onclick="return confirm('Tem certeza que deseja excluir esta transação? ID: <?= $t['id'] ?>')">
                               <i class="bi bi-trash3-fill"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include_once __DIR__ . '/../_footer.php'; // Inclui o rodapé ?>