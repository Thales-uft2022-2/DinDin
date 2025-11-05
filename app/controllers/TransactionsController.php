<?php
require_once __DIR__ . '/../models/TransactionModel.php';
require_once __DIR__ . '/../models/CategoryModel.php'; // ✅ novo require

class TransactionsController
{
    private $transactionService;
    private $transactionModel;
    private $categoryModel; // ✅ novo atributo

    public function __construct()
    {
        $this->transactionService = new TransactionService();
        $this->transactionModel   = new TransactionModel();
        $this->categoryModel      = new CategoryModel(); // ✅ instanciado
    }

    // =======================================================
    // MÉTODO 'index' (WEB)
    // =======================================================
    public function index()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $userId = $_SESSION['user']['id'] ?? null;
        if (!$userId) die("❌ Usuário não autenticado!");

        $filters = $_GET;
        $result = $this->transactionService->getTransactionsData($userId, $filters);
        $transactions = $result['transactions'];
        $categories   = $result['categories'];
        $summary      = $result['summary'];
        $filters      = $result['filters'];
        include __DIR__ . '/../views/transactions/index.php';
    }

    // =======================================================
    // MÉTODO 'store'
    // =======================================================
    public function store()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $userId = $_SESSION['user']['id'] ?? null;
        if (!$userId) die("❌ Usuário não autenticado!");

        $data = $_POST;
        $result = $this->transactionService->createTransaction($data, $userId);

        $msg  = $result['success']
            ? "✅ " . $result['message']
            : "❌ Erro ao salvar:<br>" . implode('<br>', $result['errors']);
        include __DIR__ . '/../views/transactions/message.php';
    }

    // =======================================================
    // MÉTODO 'update'
    // =======================================================
    public function update()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $userId = $_SESSION['user']['id'] ?? null;
        if (!$userId) die("❌ Usuário não autenticado!");

        $transactionId = (int) ($_POST['id'] ?? 0);
        $data = $_POST;

        if (!$transactionId) {
            echo "<p>ID não informado.</p>";
            return;
        }

        $result = $this->transactionService->updateTransaction($transactionId, $userId, $data);

        if ($result['success']) {
            $msg = "✅ " . $result['message'];
            include __DIR__ . '/../views/transactions/message.php';
        } else {
            include_once __DIR__ . '/../views/_header.php';
            echo '<div class="form-container">';
            echo '<h1>❌ Erro ao atualizar</h1>';
            foreach ($result['errors'] as $e) {
                echo '<p style="color:red;">' . htmlspecialchars($e) . '</p>';
            }
            echo '<p><a href="' . BASE_URL . '/transactions/edit?id=' . $transactionId . '">Voltar</a></p>';
            echo '</div>';
            include_once __DIR__ . '/../views/_footer.php';
        }
    }

    // =======================================================
    // MÉTODO 'delete'
    // =======================================================
    public function delete()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $userId = $_SESSION['user']['id'] ?? null;
        if (!$userId) die("❌ Usuário não autenticado!");

        $transactionId = (int) ($_GET['id'] ?? 0);
        if (!$transactionId) {
            $msg = "❌ ID não informado.";
            include __DIR__ . '/../views/transactions/message.php';
            return;
        }

        $result = $this->transactionService->deleteTransaction($transactionId, $userId);

        $msg = $result['success']
            ? "✅ " . $result['message']
            : "❌ Erro ao excluir: " . implode(', ', $result['errors']);
        include __DIR__ . '/../views/transactions/message.php';
    }

    // =======================================================
    // MÉTODO 'create' (com integração de categorias)
    // =======================================================
    public function create()
{
    if (session_status() === PHP_SESSION_NONE) session_start();
    $userId = $_SESSION['user']['id'] ?? null;
    if (!$userId) {
        die("❌ Usuário não autenticado!");
    }

    include_once __DIR__ . '/../views/_header.php';

    // Instancia o model de categorias
    $categoryModel = new CategoryModel();
    $categories = $categoryModel->findAllByUserId($userId); // ✅ Corrigido o nome do método

    $action = BASE_URL . '/transactions/store';
    $today  = date('Y-m-d');
    ?>
    <div class="form-container">
        <h1>Formulário de Nova Transação</h1>
        <form method="post" action="<?= htmlspecialchars($action) ?>">
            <label>Tipo:
                <select name="type" id="type" required>
                    <option value="income">Receita</option>
                    <option value="expense">Despesa</option>
                </select>
            </label><br><br>

            <label>Categoria:
                <select name="category" id="category" required>
                    <option value="">Selecione uma categoria</option>
                    <?php
                    if (!empty($categories)) {
                        foreach ($categories as $cat) {
                            echo '<option value="' . htmlspecialchars($cat['name']) . '" data-type="' . htmlspecialchars($cat['type']) . '">'
                                . htmlspecialchars($cat['name'])
                                . ' (' . ($cat['type'] === 'income' ? 'Receita' : 'Despesa') . ')</option>';
                        }
                    }
                    ?>
                </select>
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

    <script>
        // Filtra categorias automaticamente conforme o tipo escolhido
        const typeSelect = document.getElementById('type');
        const categorySelect = document.getElementById('category');
        const allOptions = Array.from(categorySelect.options);

        typeSelect.addEventListener('change', function () {
            const selectedType = this.value;
            categorySelect.innerHTML = '<option value="">Selecione uma categoria</option>';
            allOptions.forEach(opt => {
                if (opt.dataset.type === selectedType) {
                    categorySelect.appendChild(opt);
                }
            });
        });
    </script>

    <?php
    include_once __DIR__ . '/../views/_footer.php';
}


    // =======================================================
    // MÉTODO 'edit' (com categorias personalizadas)
    // =======================================================
    public function edit()
{
    if (session_status() === PHP_SESSION_NONE) session_start();
    $userId = $_SESSION['user']['id'] ?? null;
    if (!$userId) {
        die("❌ Usuário não autenticado!");
    }

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
    if ($transaction['user_id'] != $userId) {
        echo "<h1>Acesso Negado</h1><p>Você não tem permissão para editar esta transação.</p>";
        include_once __DIR__ . '/../views/_footer.php';
        return;
    }

    // 🔧 Corrigido: busca as categorias do usuário
    $categoryModel = new CategoryModel();
    $categories = $categoryModel->findAllByUserId($userId);

    $action = BASE_URL . '/transactions/update';
    ?>
    <div class="form-container">
        <h1>Editar Transação</h1>
        <form method="post" action="<?= htmlspecialchars($action) ?>">
            <input type="hidden" name="id" value="<?= htmlspecialchars($transaction['id']) ?>">

            <label>Tipo:
                <select name="type" id="type" required>
                    <option value="income" <?= $transaction['type'] === 'income' ? 'selected' : '' ?>>Receita</option>
                    <option value="expense" <?= $transaction['type'] === 'expense' ? 'selected' : '' ?>>Despesa</option>
                </select>
            </label><br><br>

            <label>Categoria:
                <select name="category" id="category" required>
                    <option value="">Selecione uma categoria</option>
                    <?php
                    if (!empty($categories)) {
                        foreach ($categories as $cat) {
                            $selected = ($cat['name'] === $transaction['category']) ? 'selected' : '';
                            echo '<option value="' . htmlspecialchars($cat['name']) . '" data-type="' . htmlspecialchars($cat['type']) . '" ' . $selected . '>'
                                . htmlspecialchars($cat['name'])
                                . ' (' . ($cat['type'] === 'income' ? 'Receita' : 'Despesa') . ')</option>';
                        }
                    }
                    ?>
                </select>
            </label><br><br>

            <label>Descrição:
                <input type="text" name="description" value="<?= htmlspecialchars($transaction['description']) ?>">
            </label><br><br>

            <label>Valor (R$):
                <input type="number" step="0.01" name="amount" value="<?= htmlspecialchars($transaction['amount']) ?>" required>
            </label><br><br>

            <label>Data:
                <input type="date" name="date" value="<?= htmlspecialchars($transaction['date']) ?>">
            </label><br><br>

            <button type="submit">Atualizar</button>
        </form>
    </div>

    <script>
        // Filtra categorias conforme o tipo escolhido
        const typeSelect = document.getElementById('type');
        const categorySelect = document.getElementById('category');
        const allOptions = Array.from(categorySelect.options);

        typeSelect.addEventListener('change', function () {
            const selectedType = this.value;
            categorySelect.innerHTML = '<option value="">Selecione uma categoria</option>';
            allOptions.forEach(opt => {
                if (opt.dataset.type === selectedType) {
                    categorySelect.appendChild(opt);
                }
            });
        });

        // Atualiza automaticamente o filtro com base no tipo atual
        window.addEventListener('DOMContentLoaded', () => {
            const event = new Event('change');
            typeSelect.dispatchEvent(event);
        });
    </script>

    <?php
    include_once __DIR__ . '/../views/_footer.php';
}

}
