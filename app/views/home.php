<?php include_once __DIR__ . '/_header.php'; // Inclui o topo (com o novo menu de perfil) ?>

<div class="container mt-4">
    <div class="text-center mb-5">
        <img src="<?= BASE_URL ?>/images/DinDin_Logo_Option2.png" alt="Logo DinDin" class="dashboard-logo">
        <h1 class="display-4 fw-bold mb-3">Seu Dashboard Financeiro 📊</h1>
        <p class="lead"> Olá, <?= htmlspecialchars($_SESSION['user']['name'] ?? 'Utilizador') ?>!
            Aqui está o seu resumo financeiro para o mês de <span class="fw-bold"><?= date('m/Y') ?></span>.
        </p>
    </div>

    <!-- Resumo (Cards) -->
    <div class="row g-4 mb-5 text-center">
        <div class="col-md-4">
            <div class="card text-white bg-success shadow-sm h-100"> <div class="card-body d-flex flex-column justify-content-center p-4">
                    <h5 class="card-title mb-3">📈 Receitas (Mês)</h5>
                    <p class="card-text display-6 fw-bold">
                        R$ <?= number_format($monthlySummary['total_income'], 2, ',', '.') ?>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-danger shadow-sm h-100">
                <div class="card-body d-flex flex-column justify-content-center p-4">
                    <h5 class="card-title mb-3">📉 Despesas (Mês)</h5>
                    <p class="card-text display-6 fw-bold">
                        R$ <?= number_format($monthlySummary['total_expense'], 2, ',', '.') ?>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card <?= ($monthlySummary['balance'] >= 0) ? 'text-dark bg-info' : 'text-white bg-warning' ?> shadow-sm h-100">
                <div class="card-body d-flex flex-column justify-content-center p-4">
                    <h5 class="card-title mb-3">💰 Saldo (Mês)</h5>
                    <p class="card-text display-6 fw-bold">
                        R$ <?= number_format($monthlySummary['balance'], 2, ',', '.') ?>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- ================================== -->
    <!-- ==== SEÇÃO DE GRÁFICOS (CORRIGIDA) ==== -->
    <!-- ================================== -->
    <div class="row g-4 mb-5">
        
        <!-- US-Analytics-01: Gráfico de Despesas por Categoria -->
        <div class="col-lg-5">
            <div class="card shadow-sm h-100">
                <div class="card-header">
                    <h4 class="h5 mb-0"><i class="bi bi-pie-chart-fill me-2"></i>Despesas por Categoria (Mês)</h4>
                </div>
                <div class="card-body p-3">
                    
                    <!-- CORREÇÃO: Wrapper do gráfico agora começa com classe 'd-none' -->
                    <div class="chart-wrapper d-none" style="position: relative; height: 350px;">
                        <canvas id="expensesPieChart"></canvas>
                    </div>
                    
                    <!-- CORREÇÃO: Estado Vazio agora começa com 'd-flex' (visível) -->
                    <div id="expensesPieChartEmpty" class="text-center text-muted d-flex align-items-center justify-content-center" style="height: 350px;">
                        <div> <!-- Wrapper extra para centralização vertical -->
                            <i class="bi bi-emoji-frown fs-3"></i>
                            <p class="mt-2 mb-0">Nenhuma despesa no período.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- US-Analytics-02: Gráfico de Evolução Financeira -->
        <div class="col-lg-7">
            <div class="card shadow-sm h-100">
                <div class="card-header">
                    <h4 class="h5 mb-0"><i class="bi bi-graph-up me-2"></i>Evolução Financeira (Últimos 6 Meses)</h4>
                </div>
                <div class="card-body p-3">

                    <!-- CORREÇÃO: Wrapper do gráfico agora começa com classe 'd-none' -->
                    <div class="chart-wrapper d-none" style="position: relative; height: 350px;">
                        <canvas id="evolutionLineChart"></canvas>
                    </div>

                    <!-- CORREÇÃO: Estado Vazio agora começa com 'd-flex' (visível) -->
                    <div id="evolutionLineChartEmpty" class="text-center text-muted d-flex align-items-center justify-content-center" style="height: 350px;">
                        <div> <!-- Wrapper extra para centralização vertical -->
                            <i class="bi bi-clipboard-data fs-3"></i>
                            <p class="mt-2 mb-0">Dados insuficientes para exibir o gráfico.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <!-- ================================== -->
    <!-- ==== FIM DA SEÇÃO DE GRÁFICOS ==== -->
    <!-- ================================== -->


    <!-- Ações Rápidas (Botões) -->
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

</div>


<!-- ================================== -->
<!-- ==== LÓGICA JS (CORRIGIDA) ==== -->
<!-- ================================== -->
<script>
    // Passa os dados do PHP para o JS
    const expensesData = <?= json_encode($expensesByCategoryData ?? ['labels' => [], 'data' => []]) ?>;
    const evolutionData = <?= json_encode($financialEvolutionData ?? ['labels' => [], 'incomes' => [], 'expenses' => []]) ?>;

    // Aguarda o DOM estar pronto
    document.addEventListener('DOMContentLoaded', function () {
        
        // Pega a preferência de tema (claro/escuro) para os gráficos
        const isDarkMode = document.documentElement.getAttribute('data-bs-theme') === 'dark';
        const gridColor = isDarkMode ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.1)';
        const textColor = isDarkMode ? '#F8F9FA' : '#212529';
        
        // --- 1. Inicializa Gráfico de Pizza (US-Analytics-01) ---
        const pieChartCtx = document.getElementById('expensesPieChart');
        const pieChartEmpty = document.getElementById('expensesPieChartEmpty');
        // CORREÇÃO: Agora o wrapper é o elemento irmão (anterior) do estado vazio
        const pieChartWrapper = pieChartEmpty.previousElementSibling; 

        if (expensesData && expensesData.data && expensesData.data.length > 0) {
            
            // CORREÇÃO: Usa classes Bootstrap para alternar a visibilidade
            pieChartEmpty.classList.add('d-none'); // Esconde o estado vazio
            pieChartEmpty.classList.remove('d-flex'); // Remove o flex
            pieChartWrapper.classList.remove('d-none'); // Mostra o wrapper do gráfico
            
            new Chart(pieChartCtx, {
                type: 'doughnut',
                data: {
                    labels: expensesData.labels,
                    datasets: [{
                        label: 'Despesas',
                        data: expensesData.data,
                        backgroundColor: [
                            '#ef4444', '#f97316', '#eab308', '#22c55e', '#06b6d4',
                            '#3b82f6', '#8b5cf6', '#ec4899', '#78716c', '#84cc16'
                        ],
                        borderColor: isDarkMode ? '#343a40' : '#ffffff',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false, 
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { color: textColor }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.raw || 0;
                                    const total = context.chart.getDatasetMeta(0).total;
                                    const percentage = ((value / total) * 100).toFixed(1);
                                    return `${label}: R$ ${value.toFixed(2)} (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });
        }
        // Se não houver dados, não faz nada (o estado vazio já está visível por padrão)
       

        // --- 2. Inicializa Gráfico de Linha (US-Analytics-02) ---
        const lineChartCtx = document.getElementById('evolutionLineChart');
        const lineChartEmpty = document.getElementById('evolutionLineChartEmpty');
        // CORREÇÃO: Agora o wrapper é o elemento irmão (anterior) do estado vazio
        const lineChartWrapper = lineChartEmpty.previousElementSibling;

        // Verifica se há dados
        const hasEvolutionData = evolutionData && 
                                 (evolutionData.incomes.some(v => v > 0) || evolutionData.expenses.some(v => v > 0));

        if (hasEvolutionData) {
            
            // CORREÇÃO: Usa classes Bootstrap para alternar a visibilidade
            lineChartEmpty.classList.add('d-none'); // Esconde o estado vazio
            lineChartEmpty.classList.remove('d-flex'); // Remove o flex
            lineChartWrapper.classList.remove('d-none'); // Mostra o wrapper do gráfico

            new Chart(lineChartCtx, {
                type: 'line',
                data: {
                    labels: evolutionData.labels,
                    datasets: [
                        {
                            label: 'Receitas',
                            data: evolutionData.incomes,
                            borderColor: '#22c55e', // Verde
                            backgroundColor: 'rgba(34, 197, 94, 0.1)',
                            fill: true,
                            tension: 0.3
                        },
                        {
                            label: 'Despesas',
                            data: evolutionData.expenses,
                            borderColor: '#ef4444', // Vermelho
                            backgroundColor: 'rgba(239, 68, 68, 0.1)',
                            fill: true,
                            tension: 0.3
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false, 
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: { color: textColor }
                        }
                    },
                    scales: {
                        x: {
                            grid: { color: gridColor },
                            ticks: { color: textColor }
                        },
                        y: {
                            grid: { color: gridColor },
                            ticks: { 
                                color: textColor,
                                callback: function(value) { return 'R$ ' + value; }
                            },
                            beginAtZero: true
                        }
                    }
                }
            });

        }
        // Se não houver dados, não faz nada (o estado vazio já está visível por padrão)

    });
</script>
<!-- ================================== -->
<!-- ==== FIM DO SCRIPT DOS GRÁFICOS ==== -->
<!-- ================================== -->


<?php include_once __DIR__ . '/_footer.php'; ?>