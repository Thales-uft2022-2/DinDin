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
     * @return array ['total_income' => float, 'total_expense' => float, 'balance' => float, 'month' => string, 'year' => string]
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
            'year'          => $currentYear   // Pode ser útil na view
        ];
    }

    // Futuramente, métodos para gráficos, etc., podem vir aqui...
}