<link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">

<div class="home">
    <div class="card">
        <!-- Logo -->
        <div class="logo">
            <img src="<?= BASE_URL ?>/images/DinDin_Logo_Option2.jpg" alt="Logo DinDin">
        </div>

        <!-- Título e descrição -->
        <h1>Bem-vindo ao DinDin 💰</h1>
        <p class="description">
            O DinDin é um sistema simples e prático para gerenciar suas finanças pessoais.  
            Aqui você pode registrar suas receitas e despesas, e acompanhar seu histórico de transações de forma rápida.
        </p>

        <!-- Menu de ações -->
        <div class="menu">
            <a href="<?= BASE_URL ?>/transactions/create" class="btn primary">
                ➕ Criar Nova Transação
            </a>
            <a href="<?= BASE_URL ?>/transactions/index" class="btn secondary">
                📜 Histórico de Transações
            </a>

        <!-- Barra superior com botão de logout -->
<div style="text-align: right; padding: 15px;">
    <a href="<?= BASE_URL ?>/auth/logout" class="btn btn-danger">Sair</a>
</div>
        </div>
    </div>
</div>
