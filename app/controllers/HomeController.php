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
        $monthlySummary = $this->dashboardService->getMonthlySummary($userId);

        // 3. Renderiza a view home.php, passando os dados do resumo
        // A view terá acesso à variável $monthlySummary
        include __DIR__ . '/../views/home.php';
    }
}