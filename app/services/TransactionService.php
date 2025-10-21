<?php

class TransactionService
{
    private $transactionModel;

    public function __construct()
    {
        // O serviço depende do Model para acessar o banco
        // O Model já deve estar sendo carregado pelo autoloader ou require no Controller
        if (!class_exists('TransactionModel')) {
             require_once __DIR__ . '/../models/TransactionModel.php';
        }
        $this->transactionModel = new TransactionModel();
    }

    /**
     * Valida e cria uma nova transação para um usuário.
     *
     * @param array $data (type, category, description, amount, date)
     * @param int $userId ID do usuário autenticado
     * @return array ['success' => bool, 'errors' => array, 'message' => string]
     */
    public function createTransaction(array $data, int $userId): array
    {
        // 1. Limpar e validar os dados
        $validatedData = [
            'user_id'     => $userId,
            'type'        => $data['type'] ?? '',
            'category'    => trim($data['category'] ?? 'Outros'), // Categoria padrão
            'description' => trim($data['description'] ?? ''),
            'amount'      => (float) ($data['amount'] ?? 0),
            'date'        => $data['date'] ?? date('Y-m-d'),
        ];

        $errors = [];
        if (!in_array($validatedData['type'], ['income', 'expense'], true)) {
            $errors[] = 'Tipo inválido. Deve ser "income" ou "expense".';
        }
        if ($validatedData['amount'] <= 0) {
            $errors[] = 'Valor deve ser maior que zero.';
        }
        if (empty($validatedData['category'])) {
            $errors[] = 'Categoria é obrigatória.';
        }
        
        // Validação da data (simples)
        $d = DateTime::createFromFormat('Y-m-d', $validatedData['date']);
        if (!$d || $d->format('Y-m-d') !== $validatedData['date']) {
             $errors[] = 'Formato de data inválido. Use AAAA-MM-DD.';
        }


        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        // 2. Chamar o Model para salvar
        if ($this->transactionModel->create($validatedData)) {
            return ['success' => true, 'message' => 'Transação salva com sucesso!'];
        } else {
            return ['success' => false, 'errors' => ['Erro desconhecido ao salvar no banco.']];
        }
    }

    /**
     * Busca dados de transações, filtros, categorias e resumo para um usuário.
     * (MÉTODO NOVO - TS-Svc-02)
     *
     * @param int $userId ID do usuário autenticado
     * @param array $filters Filtros (type, category, start_date, end_date, etc.)
     * @return array
     */
    public function getTransactionsData(int $userId, array $filters = []): array
    {
        // 1. Limpar e padronizar os filtros
        $cleanFilters = [
            'type'        => $filters['type'] ?? '',
            'category'    => $filters['category'] ?? '',
            'description' => $filters['description'] ?? '',
            'start_date'  => $filters['start_date'] ?? '',
            'end_date'    => $filters['end_date'] ?? ''
        ];

        // 2. Buscar dados do Model
        $transactions = $this->transactionModel->findWithFilters($cleanFilters, $userId);
        $categories   = $this->transactionModel->getUniqueCategories($userId);

        // 3. Calcular o resumo
        $totalIncome  = 0;
        $totalExpense = 0;
        
        // Vamos calcular o resumo com base nas transações *filtradas*
        foreach ($transactions as $tx) {
            if ($tx['type'] == 'income') {
                $totalIncome += $tx['amount'];
            } else {
                $totalExpense += $tx['amount'];
            }
        }
        
        $balance = $totalIncome - $totalExpense;

        // 4. Retornar um pacote de dados completo
        return [
            'transactions' => $transactions,
            'categories'   => $categories,
            'summary'      => [
                'total_income'  => $totalIncome,
                'total_expense' => $totalExpense,
                'balance'       => $balance
            ],
            'filters'      => $cleanFilters // Retorna os filtros aplicados
        ];
    }
}