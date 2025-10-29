<?php

// Inclui o CategoryService
require_once __DIR__ . '/../services/CategoryService.php';

class CategoryController
{
    private $categoryService;

    public function __construct()
    {
        // Garante que a sessão está ativa e que o utilizador está logado
        // (Assumindo que apenas utilizadores logados podem gerir categorias)
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (empty($_SESSION['user']['id'])) {
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }

        // Instancia o serviço
        $this->categoryService = new CategoryService();
    }
    
    /**
     * Exibe a lista de categorias do utilizador.
     * Rota: GET /categories
     * (US-Cat-02)
     */
    public function index()
    {
        // 1. Pega o ID do utilizador logado
        $userId = $_SESSION['user']['id'];

        // 2. Busca as categorias usando o serviço
        $categories = $this->categoryService->getCategoriesByUser($userId);

        // 3. Carrega a view da lista, passando as categorias
        include __DIR__ . '/../views/categories/index.php';
    }

    /**
     * Exibe o formulário para criar uma nova categoria.
     * Rota: GET /categories/create
     * (US-Cat-01)
     */
    public function create()
    {
        // Apenas carrega a view do formulário
        include __DIR__ . '/../views/categories/create.php';
    }

    /**
     * Processa o envio do formulário de criação de categoria.
     * Rota: POST /categories/store
     * (US-Cat-01)
     */
    public function store()
    {
        // Garante que é um POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            // Se não for POST, redireciona de volta para o formulário
            header('Location: ' . BASE_URL . '/categories/create');
            exit;
        }

        // 1. Pega o ID do utilizador logado
        $userId = $_SESSION['user']['id'];

        // 2. Pega os dados do formulário
        $data = [
            'name' => $_POST['name'] ?? '',
            'type' => $_POST['type'] ?? '' // Precisa de um campo 'type' no formulário
        ];

        // 3. Chama o Serviço para criar a categoria
        $result = $this->categoryService->createCategory($userId, $data);

        // 4. Trata o resultado
        if ($result['success']) {
            // Sucesso: Define uma mensagem flash e redireciona para a lista de categorias
            $_SESSION['flash_message'] = [
                'type' => 'success',
                'message' => $result['message']
            ];
            header('Location: ' . BASE_URL . '/categories');
            exit;
        } else {
            // Erro: Guarda os erros, os dados antigos, e recarrega a view do formulário
            $errors = $result['errors'];
            $oldData = $data;
            $_SESSION['flash_message'] = [ // Adiciona mensagem de erro flash também
                'type' => 'error',
                'message' => 'Erro ao criar categoria. Verifique os campos.'
            ];
            include __DIR__ . '/../views/categories/create.php';
        }
    }

// --- Métodos edit, update, delete virão aqui (US-Cat-03, 04) ---
}