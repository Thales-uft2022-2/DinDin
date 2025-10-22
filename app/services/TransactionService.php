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
     * (TS-Svc-01)
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
     * (TS-Svc-02)
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

    /**
     * Valida e ATUALIZA uma transação existente.
     * (TS-Svc-03)
     *
     * @param int $transactionId ID da transação a ser atualizada
     * @param int $userId ID do usuário autenticado (para segurança)
     * @param array $data Novos dados (type, category, description, amount, date)
     * @return array ['success' => bool, 'errors' => array, 'message' => string, 'status_code' => int]
     */
    public function updateTransaction(int $transactionId, int $userId, array $data): array
    {
        // 1. Verificar se a transação existe e se pertence ao usuário
        $transaction = $this->transactionModel->findById($transactionId);

        if (!$transaction) {
            return ['success' => false, 'errors' => ['Transação não encontrada.'], 'status_code' => 404]; // 404 Not Found
        }

        if ($transaction['user_id'] != $userId) {
            return ['success' => false, 'errors' => ['Acesso negado. Você não pode editar esta transação.'], 'status_code' => 403]; // 403 Forbidden
        }

        // 2. Limpar e validar os novos dados (lógica idêntica ao createTransaction)
        $validatedData = [
            'user_id'     => $userId, // Importante para a query do Model
            'type'        => $data['type'] ?? '',
            'category'    => trim($data['category'] ?? 'Outros'),
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
        $d = DateTime::createFromFormat('Y-m-d', $validatedData['date']);
        if (!$d || $d->format('Y-m-d') !== $validatedData['date']) {
             $errors[] = 'Formato de data inválido. Use AAAA-MM-DD.';
        }

        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors, 'status_code' => 422]; // 422 Unprocessable Entity
        }

        // 3. Chamar o Model para atualizar
        if ($this->transactionModel->update($transactionId, $validatedData)) {
            return ['success' => true, 'message' => 'Transação atualizada com sucesso!', 'status_code' => 200]; // 200 OK
        } else {
            return ['success' => false, 'errors' => ['Erro desconhecido ao atualizar no banco.'], 'status_code' => 500]; // 500 Internal Server Error
        }
    }

    /**
     * Valida e EXCLUI uma transação existente.
     *
     * @param int $transactionId ID da transação a ser excluída
     * @param int $userId ID do usuário autenticado (para segurança)
     * @return array ['success' => bool, 'errors' => array, 'message' => string, 'status_code' => int]
     */
    public function deleteTransaction(int $transactionId, int $userId): array
    {
        // 1. Verificar se a transação existe e se pertence ao usuário (VERIFICAÇÃO DE SEGURANÇA)
        $transaction = $this->transactionModel->findById($transactionId);

        if (!$transaction) {
            return ['success' => false, 'errors' => ['Transação não encontrada.'], 'status_code' => 404]; // 404 Not Found
        }

        if ($transaction['user_id'] != $userId) {
            return ['success' => false, 'errors' => ['Acesso negado. Você não pode excluir esta transação.'], 'status_code' => 403]; // 403 Forbidden
        }

        // 2. Chamar o Model para excluir
        if ($this->transactionModel->delete($transactionId)) {
            return ['success' => true, 'message' => 'Transação excluída com sucesso!', 'status_code' => 200]; // 200 OK
        } else {
            return ['success' => false, 'errors' => ['Erro desconhecido ao excluir do banco.'], 'status_code' => 500]; // 500 Internal Server Error
        }
    }

}



