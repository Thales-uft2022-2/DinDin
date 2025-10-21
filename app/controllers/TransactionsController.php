<?php
// O autoloader (index.php) já deve estar cuidando dos 'requires'
// Mas por garantia, podemos manter o do Model que já estava
require_once __DIR__ . '/../models/TransactionModel.php';
// O service será carregado pelo autoloader

class TransactionsController
{
    private $transactionService;
    private $transactionModel; // Manteremos o model para os métodos não refatorados

    public function __construct()
    {
        // O Controller agora usa o Serviço
        $this->transactionService = new TransactionService();
        $this->transactionModel = new TransactionModel(); // Necessário para edit, update, delete
    }

    // =======================================================
    // MÉTODO 'index' (WEB) - REFATORADO (TS-Svc-02)
    // =======================================================
    public function index()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $userId = $_SESSION['user']['id'] ?? null;
        if (!$userId) {
            die("❌ Usuário não autenticado!");
        }

        // 1. Os filtros vêm do $_GET
        $filters = $_GET;

        // 2. Chamar o Serviço para buscar TODOS os dados
        $result = $this->transactionService->getTransactionsData($userId, $filters);

        // 3. "Desempacotar" os dados para a View
        // A view (index.php) espera variáveis chamadas $transactions, $categories, etc.
        $transactions = $result['transactions'];
        $categories   = $result['categories'];
        $summary      = $result['summary']; // A view pode usar isso agora!
        $filters      = $result['filters']; // Passa os filtros limpos para a view

        // 4. Carregar a view, que agora tem acesso a todas essas variáveis
        include __DIR__ . '/../views/transactions/index.php';
    }

    // =======================================================
    // MÉTODO 'apiIndex' (API) - NOVO (TS-Svc-02)
    // =======================================================
    public function apiIndex()
    {
        header('Content-Type: application/json; charset=utf-8');
        if (session_status() === PHP_SESSION_NONE) session_start();
        $userId = $_SESSION['user']['id'] ?? null;

        if (!$userId) {
            http_response_code(401); // Unauthorized
            echo json_encode(['error' => 'Usuário não autenticado']);
            return;
        }

        // 1. Validar o método
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            http_response_code(405); // Method Not Allowed
            echo json_encode(['error' => 'Método não permitido, use GET']);
            return;
        }

        // 2. Os filtros vêm da URL (query parameters)
        $filters = $_GET;

        // 3. Chamar o Serviço
        $data = $this->transactionService->getTransactionsData($userId, $filters);

        // 4. Retornar os dados como JSON
        http_response_code(200); // OK
        echo json_encode($data);
    }


    // =======================================================
    // MÉTODO 'store' (WEB) - REFATORADO (TS-Svc-01)
    // =======================================================
    public function store()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $userId = $_SESSION['user']['id'] ?? null;
        if (!$userId) {
            die("❌ Usuário não autenticado!");
        }

        // 1. Pegar os dados do POST
        $data = [
            'type'        => $_POST['type'] ?? '',
            'category'    => $_POST['category'] ?? '',
            'description' => $_POST['description'] ?? '',
            'amount'      => $_POST['amount'] ?? 0,
            'date'        => $_POST['date'] ?? '',
        ];

        // 2. Chamar o Serviço
        $result = $this->transactionService->createTransaction($data, $userId);

        // 3. Mostrar a resposta (HTML)
        if ($result['success']) {
            $msg  = "✅ " . $result['message'];
            $type = "success";
        } else {
            // Se falhar, mostra os erros
            $msg  = "❌ Erro ao salvar:<br>" . implode('<br>', $result['errors']);
            $type = "error";
        }

        include __DIR__ . '/../views/transactions/message.php';
    }


    // =======================================================
    // MÉTODO 'apiCreate' (API) - REFATORADO (TS-Svc-01)
    // =======================================================
    public function apiCreate()
    {
        header('Content-Type: application/json; charset=utf-8');
        if (session_status() === PHP_SESSION_NONE) session_start();
        $userId = $_SESSION['user']['id'] ?? null;

        if (!$userId) {
            http_response_code(401); // Unauthorized
            echo json_encode(['error' => 'Usuário não autenticado']);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405); // Method Not Allowed
            echo json_encode(['error' => 'Método não permitido, use POST']);
            return;
        }

        // 1. Pegar os dados do JSON
        $data = json_decode(file_get_contents('php://input'), true);
        if (!is_array($data)) {
            http_response_code(400); // Bad Request
            echo json_encode(['error' => 'JSON inválido']);
            return;
        }

        // 2. Chamar o Serviço
        $result = $this->transactionService->createTransaction($data, $userId);

        // 3. Mostrar a resposta (JSON)
        if ($result['success']) {
            http_response_code(201); // Created
            echo json_encode(['message' => $result['message']]);
        } else {
            http_response_code(422); // Unprocessable Entity (Erro de validação)
            echo json_encode(['errors' => $result['errors']]);
        }
    }


    // =======================================================
    // MÉTODO 'create' (FORMULÁRIO WEB) - ATUALIZADO COM HEADER/FOOTER
    // =======================================================
    public function create()
    {
        // 1. INCLUI O NOVO HEADER
        include_once __DIR__ . '/../views/_header.php'; 

        // 2. O HTML do formulário
        $action = BASE_URL . '/transactions/store';
        $today  = date('Y-m-d');
        ?>
        
        <div class="form-container">
            <h1>Formulário de Nova Transação</h1>
            <form method="post" action="<?= htmlspecialchars($action) ?>">
                <label>Tipo:
                    <select name="type" required>
                        <option value="income">Receita</option>
                        <option value="expense">Despesa</option>
                    </select>
                </label><br><br>
                <label>Categoria:
                    <input type="text" name="category" required>
                </label><br><br>
                <label>Descrição:
                    <input type="text" name="description">
                </label><br><br>
                <label>Valor (R$):
                    <input type="number" step="0.01" name="amount" required>
                </label><br><br>
                <label>Data:
                    <input type="date" name="date" value="<?= htmlspecialchars($today) ?>">
                </label><br><br>
                <button type="submit">Salvar</button>
            </form>
        </div>
        
        <?php
        // 3. INCLUI O NOVO FOOTER
        include_once __DIR__ . '/../views/_footer.php'; 
    }

    // =======================================================
    // MÉTODOS ANTIGOS (edit, update, delete)
    // =======================================================

    public function edit()
    {
        // NOTA: Esta página também deveria incluir o header/footer
        include_once __DIR__ . '/../views/_header.php';

        $id = $_GET['id'] ?? null;
        if (!$id) {
            echo "<p>ID não informado.</p>";
            include_once __DIR__ . '/../views/_footer.php';
            return;
        }
        $transaction = $this->transactionModel->findById($id);
        if (!$transaction) {
            echo "<p>Transação não encontrada.</p>";
            include_once __DIR__ . '/../views/_footer.php';
            return;
        }

        // O arquivo edit.php precisa existir e também não deve conter <html>, <body>, etc.
        include __DIR__ . '/../views/transactions/edit.php'; 
        
        include_once __DIR__ . '/../views/_footer.php';
    }

    public function update()
    {
        // ... (Este será o próximo a ser refatorado) ...
        if (session_status() === PHP_SESSION_NONE) session_start();
        $id = $_POST['id'] ?? null;
        if (!$id) {
            echo "<p>ID não informado.</p>";
            return;
        }
        $userId = $_SESSION['user']['id'] ?? null;
        if (!$userId) {
            die("❌ Usuário não autenticado!");
        }
        $data = [
            'user_id'     => $userId,
            'type'        => $_POST['type'] ?? '',
            'category'    => trim($_POST['category'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'amount'      => (float) ($_POST['amount'] ?? 0),
            'date'        => $_POST['date'] ?: date('Y-m-d'),
        ];
        $errors = [];
        if (!in_array($data['type'], ['income', 'expense'], true)) {
            $errors[] = 'Tipo inválido';
        }
        if ($data['amount'] <= 0) {
            $errors[] = 'Valor deve ser maior que zero';
        }
        if ($data['category'] === '') {
            $errors[] = 'Categoria é obrigatória';
        }
        if ($errors) {
            // NOTA: Esta resposta de erro também deveria usar a view de header/footer
            include_once __DIR__ . '/../views/_header.php';
            foreach ($errors as $e) {
                echo '<p style="color:red;">' . htmlspecialchars($e) . '</p>';
            }
            echo '<p><a href="' . BASE_URL . '/transactions/edit?id=' . $id . '">Voltar</a></p>';
            include_once __DIR__ . '/../views/_footer.php';
            return;
        }
        
        if ($this->transactionModel->update($id, $data)) {
            $msg = "✅ Transação atualizada com sucesso!";
        } else {
            $msg = "❌ Erro ao atualizar transação.";
        }
        include __DIR__ . '/../views/transactions/message.php';
    }

    public function delete()
    {
        // ... (Este também será refatorado) ...
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $msg  = "❌ ID não informado.";
            $type = "error";
            include __DIR__ . '/../views/transactions/message.php';
            return;
        }
        if ($this->transactionModel->delete($id)) {
            $msg  = "✅ Transação excluída com sucesso!";
            $type = "success";
        } else {
            $msg  = "❌ Erro ao excluir transação.";
            $type = "error";
        }
        include __DIR__ . '/../views/transactions/message.php';
    }
}