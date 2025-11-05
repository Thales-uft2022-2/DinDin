<?php include_once __DIR__ . '/../_header.php'; ?>

<div class="container my-4">

  <h1 class="fw-bold mb-4 text-light-emphasis">Lista de Transações</h1>

  <div class="card shadow-sm mb-4" 
       style="background-color: var(--bs-body-bg); color: var(--bs-body-color); border: 1px solid var(--bs-border-color-translucent);">
    <div class="card-body">
      <h5 class="card-title fw-semibold mb-3 text-body">Filtros de Busca</h5>

      <form method="GET" action="<?= BASE_URL ?>/transactions" class="row g-3 align-items-end">

        <div class="col-md-3">
          <label class="form-label fw-semibold">Tipo:</label>
          <select name="type" class="form-select">
            <option value="">Todos</option>
            <option value="income" <?= ($filters['type'] ?? '') === 'income' ? 'selected' : '' ?>>Receita</option>
            <option value="expense" <?= ($filters['type'] ?? '') === 'expense' ? 'selected' : '' ?>>Despesa</option>
          </select>
        </div>

        <div class="col-md-3">
          <label class="form-label fw-semibold">Categoria:</label>
          <input type="text" name="category" class="form-control" placeholder="Digite ou selecione"
                 value="<?= htmlspecialchars($filters['category'] ?? '') ?>">
        </div>

        <div class="col-md-3">
          <label class="form-label fw-semibold">Descrição:</label>
          <input type="text" name="description" class="form-control" placeholder="Buscar na descrição"
                 value="<?= htmlspecialchars($filters['description'] ?? '') ?>">
        </div>

        <div class="col-md-3 text-end">
          <button type="submit" class="btn btn-primary">
            <i class="bi bi-search"></i> Filtrar
          </button>
          <a href="<?= BASE_URL ?>/transactions" class="btn btn-secondary">
            <i class="bi bi-eraser"></i> Limpar
          </a>
        </div>

        <div class="col-md-3">
          <label class="form-label fw-semibold">Data Início:</label>
          <input type="date" name="start_date" class="form-control"
                 value="<?= htmlspecialchars($filters['start_date'] ?? '') ?>">
        </div>

        <div class="col-md-3">
          <label class="form-label fw-semibold">Data Fim:</label>
          <input type="date" name="end_date" class="form-control"
                 value="<?= htmlspecialchars($filters['end_date'] ?? '') ?>">
        </div>

      </form>
    </div>
  </div>

  <p class="text-secondary mb-3">
    Encontradas: <?= count($transactions) ?> transação(ões)
  </p>

  <div class="d-flex justify-content-end mb-3">
    <a href="<?= BASE_URL ?>/transactions/create" class="btn btn-success">
      <i class="bi bi-plus-circle"></i> Nova Transação
    </a>
  </div>

  <div class="table-responsive shadow-sm rounded">
    <table class="table table-hover align-middle"
           style="background-color: var(--bs-body-bg); color: var(--bs-body-color); border: 1px solid var(--bs-border-color-translucent);">
      <thead class="table-dark text-center">
        <tr>
          <th>#</th>
          <th>Tipo</th>
          <th>Categoria</th>
          <th>Descrição</th>
          <th>Valor (R$)</th>
          <th>Data</th>
          <th>Ações</th>
        </tr>
      </thead>
      <tbody class="text-center">
        <?php if (empty($transactions)): ?>
          <tr>
            <td colspan="7" class="text-muted py-3">Nenhuma transação encontrada.</td>
          </tr>
        <?php else: ?>
          <?php foreach ($transactions as $t): ?>
            <tr>
              <td><?= htmlspecialchars($t['id']) ?></td>
              <td>
                <?php if ($t['type'] === 'income'): ?>
                  <span class="badge bg-success"><i class="bi bi-arrow-up-circle"></i> Receita</span>
                <?php else: ?>
                  <span class="badge bg-danger"><i class="bi bi-arrow-down-circle"></i> Despesa</span>
                <?php endif; ?>
              </td>
              <td><?= htmlspecialchars($t['category']) ?></td>
              <td><?= htmlspecialchars($t['description']) ?></td>
              <td class="<?= $t['type'] === 'income' ? 'text-success' : 'text-danger' ?>">
                <?= $t['type'] === 'income' ? '+ R$ ' : '- R$ ' ?><?= number_format($t['amount'], 2, ',', '.') ?>
              </td>
              <td><?= date('d/m/Y', strtotime($t['date'])) ?></td>
              <td>
                <a href="<?= BASE_URL ?>/transactions/edit?id=<?= $t['id'] ?>" class="btn btn-sm btn-primary">
                  <i class="bi bi-pencil-square"></i>
                </a>
                <a href="<?= BASE_URL ?>/transactions/delete?id=<?= $t['id'] ?>" 
                   class="btn btn-sm btn-danger" 
                   onclick="return confirm('Tem certeza que deseja excluir esta transação?');">
                  <i class="bi bi-trash"></i>
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include_once __DIR__ . '/../_footer.php'; ?>
