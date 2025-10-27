<?php include_once __DIR__ . '/../_header.php'; // Inclui o topo ?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8"> <div class="card shadow-sm">
                 <div class="card-header bg-primary text-white">
                    <h1 class="h4 mb-0"><i class="bi bi-pencil-square me-2"></i> Editar Transação #<?= htmlspecialchars($transaction['id']) ?></h1>
                </div>
                <div class="card-body p-4"> <form method="post" action="<?= htmlspecialchars(BASE_URL . '/transactions/update') ?>">
                        <input type="hidden" name="id" value="<?= htmlspecialchars($transaction['id']) ?>">

                        <div class="row g-3">
                            <div class="col-md-6 mb-3">
                                <label for="type" class="form-label">Tipo:</label>
                                <select name="type" id="type" class="form-select" required>
                                    <option value="income" <?= $transaction['type']==='income'?'selected':'' ?>>Receita</option>
                                    <option value="expense" <?= $transaction['type']==='expense'?'selected':'' ?>>Despesa</option>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="category" class="form-label">Categoria:</label>
                                <input type="text" id="category" name="category" class="form-control"
                                       value="<?= htmlspecialchars($transaction['category']) ?>" required
                                       placeholder="Ex: Salário, Supermercado">
                                </div>

                             <div class="col-12 mb-3">
                                <label for="description" class="form-label">Descrição:</label>
                                <input type="text" id="description" name="description" class="form-control"
                                       value="<?= htmlspecialchars($transaction['description']) ?>"
                                       placeholder="Ex: Compra do mês, Pagamento Adiantamento">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="amount" class="form-label">Valor (R$):</label>
                                <div class="input-group"> <span class="input-group-text">R$</span>
                                    <input type="number" step="0.01" id="amount" name="amount" class="form-control"
                                           value="<?= htmlspecialchars($transaction['amount']) ?>" required placeholder="0,00">
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="date" class="form-label">Data:</label>
                                <input type="date" id="date" name="date" class="form-control"
                                       value="<?= htmlspecialchars($transaction['date']) ?>" required>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="<?= BASE_URL ?>/transactions" class="btn btn-outline-secondary"> <i class="bi bi-arrow-left me-1"></i> Voltar para Lista
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-1"></i> Salvar Alterações
                            </button>
                        </div>
                    </form>
                </div> </div> </div> </div> </div> <?php include_once __DIR__ . '/../_footer.php'; // Inclui o rodapé ?>