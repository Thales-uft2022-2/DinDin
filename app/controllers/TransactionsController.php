<?php
require_once __DIR__ . '/../models/TransactionModel.php';
require_once __DIR__ . '/../models/CategoryModel.php';
require_once __DIR__ . '/../services/TransactionService.php'; // Adicionado por segurança

class TransactionsController
{
    private $transactionService;
    private $transactionModel;
    private $categoryModel;

    public function __construct()
    {
        $this->transactionService = new TransactionService();
        $this->transactionModel   = new TransactionModel();
        $this->categoryModel      = new CategoryModel();
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
    // ▼▼▼ MÉTODO 'create' ATUALIZADO (MAIS PROFISSIONAL) ▼▼▼
    // =======================================================
    public function create()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $userId = $_SESSION['user']['id'] ?? null;
        if (!$userId) {
            die("❌ Usuário não autenticado!");
        }

        // 1. Inclui o Header (HTML, CSS, etc.)
        include_once __DIR__ . '/../views/_header.php';

        // 2. Busca as categorias do usuário para o dropdown
        $categories = $this->categoryModel->findAllByUserId($userId);

        // 3. Define variáveis para o formulário
        $action = BASE_URL . '/transactions/store';
        $today  = date('Y-m-d');
        ?>

        <!-- Início do Formulário Profissional (Bootstrap 5) -->
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10">
                
                <div class="card shadow-sm border border-primary">
                    <div class="card-header bg-primary text-white">
                        <h1 class="h4 mb-0"><i class="bi bi-plus-circle-fill me-2"></i> Nova Transação</h1>
                    </div>
                    
                    <div class="card-body p-4 p-md-5">
                        <form method="post" action="<?= htmlspecialchars($action) ?>">
                            
                            <!-- Linha 1: Tipo e Categoria -->
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label for="type" class="form-label">Tipo:*</label>
                                    <select name="type" id="type" class="form-select form-select-lg" required>
                                        <option value="" selected disabled>-- Selecione o tipo --</option>
                                        <option value="income">Receita</option>
                                        <option value="expense">Despesa</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="category" class="form-label">Categoria:*</label>
                                    <select name="category" id="category" class="form-select form-select-lg" required disabled>
                                        <option value="">-- Selecione o tipo primeiro --</option>
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
                                </div>
                            </div>

                            <!-- Linha 2: Descrição -->
                            <div class="mb-3">
                                <label for="description" class="form-label">Descrição (Opcional):</label>
                                <input type="text" id="description" name="description" class="form-control form-control-lg" placeholder="Ex: Aluguel, Salário, Compras do mês">
                            </div>

                            <!-- Linha 3: Valor e Data -->
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label for="amount" class="form-label">Valor (R$):*</label>
                                    <div class="input-group input-group-lg">
                                        <span class="input-group-text">R$</span>
                                        <input type="number" step="0.01" id="amount" name="amount" class="form-control" required placeholder="0,00">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="date" class="form-label">Data:*</label>
                                    <input type="date" id="date" name="date" class="form-control form-control-lg" value="<?= htmlspecialchars($today) ?>" required>
                                </div>
                            </div>

                            <!-- Linha 4: Botões -->
                            <div class="d-flex justify-content-between align-items-center mt-4">
                                <a href="<?= BASE_URL ?>/home" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left me-1"></i> Cancelar
                                </a>
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="bi bi-check-lg me-1"></i> Salvar Transação
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- JavaScript para filtrar as categorias -->
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const typeSelect = document.getElementById('type');
                const categorySelect = document.getElementById('category');
                
                // Salva todas as opções de categoria originais
                const allOptions = Array.from(categorySelect.options).filter(opt => opt.value !== '');

                function filterCategories() {
                    const selectedType = typeSelect.value;
                    
                    // Guarda a categoria que estava selecionada (se houver)
                    const previouslySelected = categorySelect.value;
                    
                    // Limpa o select (mantendo a primeira opção "Selecione...")
                    categorySelect.innerHTML = '<option value="">-- Selecione uma categoria --</option>';

                    if (selectedType) {
                        // Adiciona apenas as opções do tipo correto
                        allOptions.forEach(opt => {
                            if (opt.dataset.type === selectedType) {
                                // Recria a opção para evitar problemas de DOM
                                const newOption = new Option(opt.text, opt.value);
                                newOption.dataset.type = opt.dataset.type;
                                
                                // Se era a que estava selecionada, mantém
                                if (opt.value === previouslySelected) {
                                    newOption.selected = true;
                                }
                                categorySelect.appendChild(newOption);
                            }
                        });
                        categorySelect.disabled = false;
                    } else {
                        // Se nenhum tipo for selecionado, desabilita e reseta
                        categorySelect.innerHTML = '<option value="">-- Selecione o tipo primeiro --</option>';
                        categorySelect.disabled = true;
                    }
                }

                // Adiciona o evento
                typeSelect.addEventListener('change', filterCategories);

                // Chama a função uma vez no início para configurar o estado inicial
                filterCategories();
            });
        </script>

        <?php
        // 4. Inclui o Footer (fecha o <body>, <html> e carrega scripts)
        include_once __DIR__ . '/../views/_footer.php';
    }
    // ▲▲▲ FIM DO MÉTODO 'create()' ▲▲▲


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

            function filterEditCategories() {
                const selectedType = typeSelect.value;
                const currentCategoryValue = "<?= htmlspecialchars($transaction['category']) ?>"; // Pega o valor atual
                
                categorySelect.innerHTML = '<option value="">Selecione uma categoria</option>';
                
                allOptions.forEach(opt => {
                    if (opt.dataset.type === selectedType) {
                        // Recria a opção para evitar problemas
                        const newOption = new Option(opt.text, opt.value);
                        newOption.dataset.type = opt.dataset.type;
                        
                        // Se for a categoria que já estava salva, seleciona ela
                        if (opt.value === currentCategoryValue) {
                            newOption.selected = true;
                        }
                        categorySelect.appendChild(newOption);
                    }
                });
            }

            typeSelect.addEventListener('change', filterEditCategories);

            // Atualiza automaticamente o filtro com base no tipo atual
            window.addEventListener('DOMContentLoaded', () => {
                filterEditCategories();
            });
        </script>

        <?php
        include_once __DIR__ . '/../views/_footer.php';
    }

}