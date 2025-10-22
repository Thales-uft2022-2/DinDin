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
        $this->transactionModel = new TransactionModel(); // Necessário para edit
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
        $filters = $_GET;
        $result = $this->transactionService->getTransactionsData($userId, $filters);
        $transactions = $result['transactions'];
        $categories   = $result['categories'];
        $summary      = $result['summary'];
        $filters      = $result['filters'];
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
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            http_response_code(405); // Method Not Allowed
            echo json_encode(['error' => 'Método não permitido, use GET']);
            return;
        }
        $filters = $_GET;
        $data = $this->transactionService->getTransactionsData($userId, $filters);
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
        $data = $_POST; // O Serviço já lida com os campos esperados
        $result = $this->transactionService->createTransaction($data, $userId);

        if ($result['success']) {
            $msg  = "✅ " . $result['message'];
            $type = "success";
        } else {
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
            http_response_code(401);
            echo json_encode(['error' => 'Usuário não autenticado']);
            return;
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Método não permitido, use POST']);
            return;
        }
        $data = json_decode(file_get_contents('php://input'), true);
        if (!is_array($data)) {
            http_response_code(400);
            echo json_encode(['error' => 'JSON inválido']);
            return;
        }
        $result = $this->transactionService->createTransaction($data, $userId);
        if ($result['success']) {
            http_response_code(201);
            echo json_encode(['message' => $result['message']]);
        } else {
            http_response_code(422);
            echo json_encode(['errors' => $result['errors']]);
        }
    }

    // =======================================================
    // MÉTODO 'update' (WEB) - REFATORADO (TS-Svc-03)
    // =======================================================
    public function update()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $userId = $_SESSION['user']['id'] ?? null;
        if (!$userId) {
            die("❌ Usuário não autenticado!");
        }

        // 1. Pegar dados do POST
        $transactionId = (int) ($_POST['id'] ?? 0);
        $data = $_POST; // O Serviço vai extrair o que precisa

        if (!$transactionId) {
            echo "<p>ID não informado.</p>"; // Poderia ser uma view de erro
            return;
        }

        // 2. Chamar o Serviço
        $result = $this->transactionService->updateTransaction($transactionId, $userId, $data);

        // 3. Tratar a resposta
        if ($result['success']) {
            $msg = "✅ " . $result['message'];
            include __DIR__ . '/../views/transactions/message.php';
        } else {
            // Se falhar, mostra os erros (assim como o código antigo fazia)
            include_once __DIR__ . '/../views/_header.php';
            echo '<div class="form-container">';
            echo '<h1>❌ Erro ao atualizar</h1>';
            foreach ($result['errors'] as $e) {
                echo '<p style="color:red;">' . htmlspecialchars($e) . '</p>';
            }
            echo '<p><a href="' . BASE_URL . '/transactions/edit?id=' . $transactionId . '">Voltar para a edição</a></p>';
            echo '</div>';
            include_once __DIR__ . '/../views/_footer.php';
        }
    }

    // =======================================================
    // MÉTODO 'apiUpdate' (API) - NOVO (TS-Svc-03)
    // =======================================================
    public function apiUpdate()
    {
        header('Content-Type: application/json; charset=utf-8');
        if (session_status() === PHP_SESSION_NONE) session_start();
        $userId = $_SESSION['user']['id'] ?? null;
        if (!$userId) {
            http_response_code(401); // Unauthorized
            echo json_encode(['error' => 'Usuário não autenticado']);
            return;
        }

        // 1. Validar Método
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405); // Method Not Allowed
            echo json_encode(['error' => 'Método não permitido, use POST']);
            return;
        }

        // 2. Pegar dados do JSON
        $data = json_decode(file_get_contents('php://input'), true);
        if (!is_array($data)) {
            http_response_code(400); // Bad Request
            echo json_encode(['error' => 'JSON inválido']);
            return;
        }

        // 3. Pegar o ID da transação
        $transactionId = (int) ($data['id'] ?? 0);
        if (!$transactionId) {
            http_response_code(400); // Bad Request
            echo json_encode(['error' => 'O campo "id" da transação é obrigatório.']);
            return;
        }

        // 4. Chamar o Serviço
        $result = $this->transactionService->updateTransaction($transactionId, $userId, $data);

        // 5. Retornar a resposta JSON
        http_response_code($result['status_code']); // Usa o código (200, 403, 404, 422) vindo do serviço
        if ($result['success']) {
            echo json_encode(['message' => $result['message']]);
        } else {
            echo json_encode(['errors' => $result['errors']]);
        }
    }

    // =======================================================
    // MÉTODO 'delete' (WEB) - REFATORADO (TS-Svc-04)
    // =======================================================
    public function delete()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $userId = $_SESSION['user']['id'] ?? null;
        if (!$userId) {
            die("❌ Usuário não autenticado!");
        }

        // 1. Pegar ID do GET
        $transactionId = (int) ($_GET['id'] ?? 0);
        if (!$transactionId) {
            $msg  = "❌ ID não informado.";
            $type = "error";
            include __DIR__ . '/../views/transactions/message.php';
            return;
        }

        // 2. Chamar o Serviço (que tem a lógica de segurança)
        $result = $this->transactionService->deleteTransaction($transactionId, $userId);

        // 3. Mostrar o resultado
        if ($result['success']) {
            $msg  = "✅ " . $result['message'];
            $type = "success";
        } else {
            $msg  = "❌ Erro ao excluir: " . implode(', ', $result['errors']);
            $type = "error";
        }
        include __DIR__ . '/../views/transactions/message.php';
    }

    // =======================================================
    // MÉTODO 'apiDelete' (API) - NOVO (TS-Svc-04)
    // =======================================================
    public function apiDelete()
    {
        header('Content-Type: application/json; charset=utf-8');
        if (session_status() === PHP_SESSION_NONE) session_start();
        $userId = $_SESSION['user']['id'] ?? null;
        if (!$userId) {
            http_response_code(401); // Unauthorized
            echo json_encode(['error' => 'Usuário não autenticado']);
            return;
        }

        // 1. Validar Método
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405); // Method Not Allowed
            echo json_encode(['error' => 'Método não permitido, use POST']);
            return;
        }

        // 2. Pegar dados do JSON
        $data = json_decode(file_get_contents('php://input'), true);
        if (!is_array($data)) {
            http_response_code(400); // Bad Request
            echo json_encode(['error' => 'JSON inválido']);
            return;
        }

        // 3. Pegar o ID da transação
        $transactionId = (int) ($data['id'] ?? 0);
        if (!$transactionId) {
            http_response_code(400); // Bad Request
            echo json_encode(['error' => 'O campo "id" da transação é obrigatório.']);
            return;
        }

        // 4. Chamar o Serviço
        $result = $this->transactionService->deleteTransaction($transactionId, $userId);

        // 5. Retornar a resposta JSON
        http_response_code($result['status_code']);
        if ($result['success']) {
            echo json_encode(['message' => $result['message']]);
        } else {
            echo json_encode(['errors' => $result['errors']]);
        }
    }

    // =======================================================
    // MÉTODOS VISUAIS (create, edit)
    // =======================================================
    public function create()
    {
        include_once __DIR__ . '/../views/_header.php';
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
        include_once __DIR__ . '/../views/_footer.php';
    }

    public function edit()
    {
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

        // Garante que o usuário só pode editar sua própria transação
        $userId = $_SESSION['user']['id'] ?? null;
        if ($transaction['user_id'] != $userId) {
            echo "<h1>Acesso Negado</h1><p>Você não tem permissão para editar esta transação.</p>";
            include_once __DIR__ . '/../views/_footer.php';
            return;
        }

        // O arquivo edit.php precisa existir e não deve conter <html>, <body>
        include __DIR__ . '/../views/transactions/edit.php';

        include_once __DIR__ . '/../views/_footer.php';
    }
}