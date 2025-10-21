<?php include_once __DIR__ . '/_header.php'; // Inclui o topo (<html>, <head>, <body>, logo) ?>

<div class="home">
    <div class="card">
        <div class="logo">
            <img src="<?= BASE_URL ?>/images/DinDin_Logo_Option2.jpg" alt="Logo DinDin">
        </div>

        <h1>Bem-vindo ao DinDin 💰</h1>
        <p class="description">
            O DinDin é um sistema simples e prático para gerenciar suas finanças pessoais.  
            Aqui você pode registrar suas receitas e despesas, e acompanhar seu histórico de transações de forma rápida.
        </p>

        <div class="menu">
            <a href="<?= BASE_URL ?>/transactions/create" class="btn primary">
                ➕ Criar Nova Transação
            </a>
            <a href="<?= BASE_URL ?>/transactions/index" class="btn secondary">
                📜 Histórico de Transações
            </a>

        <div style="text-align: right; padding: 15px;">
    <a href="<?= BASE_URL ?>/auth/logout" class="btn btn-danger">Sair</a>
</div>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/_footer.php'; // Inclui o rodapé (</body>, </html>) ?>