<?php

class DashboardService
{
    private $transactionModel;

    public function __construct()
    {
        // O DashboardService depende do TransactionModel para buscar as transações
        if (!class_exists('TransactionModel')) {
            require_once __DIR__ . '/../models/TransactionModel.php';
        }
        $this->transactionModel = new TransactionModel();
    }

    /**
     * Calcula o resumo financeiro (receitas, despesas, saldo)
     * para o mês corrente de um utilizador específico.
     * (US-Dash-01)
     *
     * @param int $userId ID do utilizador autenticado.
     * @return array ['total_income' => float, 'total_expense' => float, 'balance' => float, 'month' => string, 'year' => string, 'startDate' => string, 'endDate' => string]
     */
    public function getMonthlySummary(int $userId): array
    {
        // 1. Definir o período do mês corrente
        $currentYear = date('Y');
        $currentMonth = date('m');
        $startDate = "{$currentYear}-{$currentMonth}-01";
        $endDate = date('Y-m-t', strtotime($startDate)); // 't' pega o último dia do mês

        // 2. Montar filtros para buscar transações APENAS do mês corrente
        $filters = [
            'start_date' => $startDate,
            'end_date'   => $endDate,
            // Não precisamos filtrar por tipo aqui, vamos buscar tudo do mês
        ];

        // 3. Usar o TransactionModel para buscar as transações filtradas
        $transactions = $this->transactionModel->findWithFilters($filters, $userId);

        // 4. Calcular os totais (Receitas e Despesas)
        $totalIncome = 0.0;
        $totalExpense = 0.0;

        foreach ($transactions as $transaction) {
            if ($transaction['type'] === 'income') {
                $totalIncome += (float) $transaction['amount'];
            } elseif ($transaction['type'] === 'expense') {
                $totalExpense += (float) $transaction['amount'];
            }
        }

        // 5. Calcular o balanço
        $balance = $totalIncome - $totalExpense;

        // 6. Retornar os dados formatados
        return [
            'total_income'  => $totalIncome,
            'total_expense' => $totalExpense,
            'balance'       => $balance,
            'month'         => $currentMonth, // Pode ser útil na view
            'year'          => $currentYear,   // Pode ser útil na view
            'startDate'     => $startDate,   // (NOVO) Retorna para usar no gráfico de pizza
            'endDate'       => $endDate     // (NOVO) Retorna para usar no gráfico de pizza
        ];
    }

    // =======================================================
    // NOVO MÉTODO (US-Analytics-01)
    // =======================================================
    /**
     * Busca e prepara os dados para o gráfico de despesas por categoria.
     * @param int $userId
     * @param string $startDate
     * @param string $endDate
     * @return array ['labels' => [], 'data' => []]
     */
    public function getExpensesByCategoryData(int $userId, string $startDate, string $endDate): array
    {
        $rawData = $this->transactionModel->getExpensesByCategory($userId, $startDate, $endDate);
        
        $labels = [];
        $data = [];
        
        foreach ($rawData as $row) {
            $labels[] = $row['category'];
            $data[] = (float) $row['total'];
        }
        
        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    // =======================================================
    // NOVO MÉTODO (US-Analytics-02)
    // =======================================================
    /**
     * Busca e prepara os dados para o gráfico de evolução financeira.
     * @param int $userId
     * @return array ['labels' => [], 'incomes' => [], 'expenses' => []]
     */
    public function getFinancialEvolutionData(int $userId): array
    {
        $rawData = $this->transactionModel->getFinancialEvolution($userId);
        
        $labels = [];
        $incomes = [];
        $expenses = [];

        // Mapeamento de meses (Português)
        $monthMap = [
            '01' => 'Jan', '02' => 'Fev', '03' => 'Mar', '04' => 'Abr', 
            '05' => 'Mai', '06' => 'Jun', '07' => 'Jul', '08' => 'Ago', 
            '09' => 'Set', '10' => 'Out', '11' => 'Nov', '12' => 'Dez'
        ];

        // Precisamos garantir 6 meses, mesmo que não haja dados.
        $currentDate = new DateTime('now');
        $monthsToProcess = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = new DateTime("first day of -$i months");
            $monthKey = $date->format('Y-m'); // '2025-11'
            $monthLabel = $monthMap[$date->format('m')] . '/' . $date->format('y'); // 'Nov/25'
            
            $monthsToProcess[$monthKey] = [
                'label' => $monthLabel,
                'income' => 0.0,
                'expense' => 0.0
            ];
        }

        // Preenche os dados do banco
        foreach ($rawData as $row) {
            $monthKey = $row['month_year']; // '2025-10'
            if (isset($monthsToProcess[$monthKey])) {
                $monthsToProcess[$monthKey]['income'] = (float) $row['total_income'];
                $monthsToProcess[$monthKey]['expense'] = (float) $row['total_expense'];
            }
        }
        
        // Separa nos arrays finais
        foreach ($monthsToProcess as $monthData) {
            $labels[] = $monthData['label'];
            $incomes[] = $monthData['income'];
            $expenses[] = $monthData['expense'];
        }

        return [
            'labels' => $labels,    // ['Jun/25', 'Jul/25', ..., 'Nov/25']
            'incomes' => $incomes,  // [5000, 5100, ...]
            'expenses' => $expenses // [1200, 1300, ...]
        ];
    }
}