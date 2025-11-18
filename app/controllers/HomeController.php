<?php

// Inclui o novo DashboardService (Necessário)
require_once __DIR__ . '/../services/DashboardService.php';

class HomeController
{
    private $dashboardService; // Adiciona a dependência do serviço

    public function __construct()
    {
        // Garante que a sessão está ativa ANTES de verificar o utilizador
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Protege a home: precisa estar logado
        if (empty($_SESSION['user']['id'])) { // Verifica o ID do utilizador na sessão
            header("Location: " . BASE_URL . "/auth/login");
            exit;
        }

        // Instancia o serviço necessário
        $this->dashboardService = new DashboardService();
    }

    public function index()
    {
        // 1. Pega o ID do utilizador logado (já garantido pelo constructor)
        $userId = $_SESSION['user']['id'];

        // 2. Busca os dados do resumo mensal usando o DashboardService
        // $monthlySummary agora também contém 'startDate' e 'endDate'
        $monthlySummary = $this->dashboardService->getMonthlySummary($userId);

        // 3. (NOVO) Busca dados para o gráfico de pizza
        $expensesByCategoryData = $this->dashboardService->getExpensesByCategoryData(
            $userId, 
            $monthlySummary['startDate'], 
            $monthlySummary['endDate']
        );
        
        // 4. (NOVO) Busca dados para o gráfico de linha
        $financialEvolutionData = $this->dashboardService->getFinancialEvolutionData($userId);

        // 5. Renderiza a view home.php, passando todos os dados
        // A view terá acesso a:
        // - $monthlySummary
        // - $expensesByCategoryData
        // - $financialEvolutionData
        include __DIR__ . '/../views/home.php';
    }
}