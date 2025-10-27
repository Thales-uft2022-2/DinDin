<?php include_once __DIR__ . '/_header.php'; // Inclui o topo (<html>, <head>, <body>, logo) ?>

<div class="container mt-4">
    <div class="text-center mb-5">
        <img src="<?= BASE_URL ?>/images/DinDin_Logo_Option2.jpg" alt="Logo DinDin" class="dashboard-logo">
        <h1 class="display-4 fw-bold mb-3">Seu Dashboard Financeiro 📊</h1>
        <p class="lead text-muted">
            Olá, <?= htmlspecialchars($_SESSION['user']['name'] ?? 'Utilizador') ?>!
            Aqui está o seu resumo financeiro para o mês de <span class="fw-bold"><?= date('m/Y') ?></span>.
        </p>
    </div>

    <div class="row g-4 mb-5 text-center">
        <div class="col-md-4">
            <div class="card text-white bg-success shadow-lg h-100 border-0">
                <div class="card-body d-flex flex-column justify-content-center p-4">
                    <h5 class="card-title mb-3 fs-5">📈 Receitas (Mês)</h5>
                    <p class="card-text fs-2 fw-bold">
                        R$ <?= number_format($monthlySummary['total_income'], 2, ',', '.') ?>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-danger shadow-lg h-100 border-0">
                <div class="card-body d-flex flex-column justify-content-center p-4">
                    <h5 class="card-title mb-3 fs-5">📉 Despesas (Mês)</h5>
                    <p class="card-text fs-2 fw-bold">
                        R$ <?= number_format($monthlySummary['total_expense'], 2, ',', '.') ?>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card <?= ($monthlySummary['balance'] >= 0) ? 'text-dark bg-info' : 'text-white bg-warning' ?> shadow-lg h-100 border-0">
                <div class="card-body d-flex flex-column justify-content-center p-4">
                    <h5 class="card-title mb-3 fs-5">💰 Saldo (Mês)</h5>
                    <p class="card-text fs-2 fw-bold">
                        R$ <?= number_format($monthlySummary['balance'], 2, ',', '.') ?>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 justify-content-center mb-4">
        <div class="col-md-5">
             <div class="d-grid">
                <a href="<?= BASE_URL ?>/transactions/create" class="btn btn-primary btn-lg py-3 shadow-sm">
                    ➕ Adicionar Nova Transação
                </a>
             </div>
        </div>
        <div class="col-md-5">
             <div class="d-grid">
                <a href="<?= BASE_URL ?>/transactions" class="btn btn-secondary btn-lg py-3 shadow-sm">
                    📜 Ver Histórico Completo
                </a>
             </div>
        </div>
    </div>

    <div class="text-center mt-5">
        <a href="<?= BASE_URL ?>/auth/logout" class="btn btn-outline-danger px-4 py-2">Sair</a>
    </div>

</div>

<?php include_once __DIR__ . '/_footer.php'; // Inclui o rodapé (</body>, </html>) ?>