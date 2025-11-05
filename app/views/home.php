<?php include_once __DIR__ . '/_header.php'; // Inclui o topo (<html>, <head>, <body>, logo) ?>

<div class="container mt-4">
    <div class="text-center mb-5">
        <img src="<?= BASE_URL ?>/images/DinDin_Logo_Option2.png" alt="Logo DinDin" class="dashboard-logo">
        <h1 class="display-4 fw-bold mb-3">Seu Dashboard Financeiro 📊</h1>
        <p class="lead"> Olá, <?= htmlspecialchars($_SESSION['user']['name'] ?? 'Utilizador') ?>!
            Aqui está o seu resumo financeiro para o mês de <span class="fw-bold"><?= date('m/Y') ?></span>.
        </p>
    </div>

    <div class="row g-4 mb-5 text-center">
        <div class="col-md-4">
            <div class="card text-white bg-success shadow-sm h-100"> <div class="card-body d-flex flex-column justify-content-center p-4">
                    <h5 class="card-title mb-3">📈 Receitas (Mês)</h5>
                    <p class="card-text">
                        R$ <?= number_format($monthlySummary['total_income'], 2, ',', '.') ?>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-danger shadow-sm h-100">
                <div class="card-body d-flex flex-column justify-content-center p-4">
                    <h5 class="card-title mb-3">📉 Despesas (Mês)</h5>
                    <p class="card-text">
                        R$ <?= number_format($monthlySummary['total_expense'], 2, ',', '.') ?>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card <?= ($monthlySummary['balance'] >= 0) ? 'text-dark bg-info' : 'text-white bg-warning' ?> shadow-sm h-100">
                <div class="card-body d-flex flex-column justify-content-center p-4">
                    <h5 class="card-title mb-3">💰 Saldo (Mês)</h5>
                    <p class="card-text">
                        R$ <?= number_format($monthlySummary['balance'], 2, ',', '.') ?>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 justify-content-center mb-4">
        <div class="col-md-5">
             <div class="d-grid">
                <a href="<?= BASE_URL ?>/transactions/create" class="btn btn-primary btn-lg py-3">
                    <i class="bi bi-plus-circle-fill me-2"></i> Adicionar Transação
                </a>
             </div>
        </div>
        <div class="col-md-5">
             <div class="d-grid">
                <a href="<?= BASE_URL ?>/transactions" class="btn btn-secondary btn-lg py-3">
                    <i class="bi bi-list-ul me-2"></i> Ver Histórico
                </a>
             </div>
        </div>
         <div class="col-md-5 mt-3">
             <div class="d-grid">
                <a href="<?= BASE_URL ?>/categories" class="btn btn-warning btn-lg py-3 text-dark"> 
                    <i class="bi bi-tags-fill me-2"></i> Gerir Categorias
                </a>
             </div>
        </div>
    </div>

    <div class="text-center mt-5">
        <a href="<?= BASE_URL ?>/auth/logout" class="btn btn-outline-danger px-4 py-2">
           <i class="bi bi-box-arrow-right me-2"></i> Sair
        </a>
    </div>

</div>

<?php include_once __DIR__ . '/_footer.php'; // Inclui o rodapé (</body>, </html>) ?>