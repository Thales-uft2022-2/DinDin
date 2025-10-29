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
    /**
     * Exibe o formulário para editar uma categoria.
     * Rota: GET /categories/edit
     * (US-Cat-03)
     */
    public function edit()
    {
        // 1. Pegar ID do GET e ID do utilizador
        $categoryId = (int) ($_GET['id'] ?? 0);
        $userId = $_SESSION['user']['id'];
        
        if (!$categoryId) {
            // Se não tem ID, redireciona para a lista
            header('Location: ' . BASE_URL . '/categories');
            exit;
        }

        // 2. Buscar a categoria no serviço (que verifica a posse)
        $category = $this->categoryService->getCategoryById($categoryId, $userId);

        // 3. Verificar se a categoria existe e pertence ao utilizador
        if (!$category) {
            // Se não encontrou ou não pertence, define erro e vai para a lista
            $_SESSION['flash_message'] = [
                'type' => 'error',
                'message' => 'Categoria não encontrada ou acesso negado.'
            ];
            header('Location: ' . BASE_URL . '/categories');
            exit;
        }
        
        // 4. Carrega a view do formulário de edição, passando os dados
        // Usamos $oldData para que a view de edição possa reutilizar a mesma lógica
        // da view de criação ('create.php') caso dê erro no 'update'
        $oldData = $category; 
        include __DIR__ . '/../views/categories/edit.php';
    }

    /**
     * Processa o envio do formulário de edição de categoria.
     * Rota: POST /categories/update
     * (US-Cat-03)
     */
    public function update()
    {
        // Garante que é um POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/categories');
            exit;
        }
        // 1. Pega os dados do POST e o ID do utilizador
        $userId = $_SESSION['user']['id'];

        $categoryId = (int) ($_POST['id'] ?? 0);
        $data = [
            'name' => $_POST['name'] ?? '',
            'type' => $_POST['type'] ?? ''
        ];

        // 2. Chama o Serviço para ATUALIZAR a categoria
        $result = $this->categoryService->updateCategory($categoryId, $userId, $data);

        // 3. Trata o resultado
        if ($result['success']) {
            // Sucesso: Define mensagem flash e redireciona para a lista (Critério Aceite 1)
            $_SESSION['flash_message'] = [
                'type' => 'success',
                'message' => $result['message']
            ];
            header('Location: ' . BASE_URL . '/categories');
            exit;
        } else {
            // Erro: Guarda os erros, os dados antigos (com o ID) e recarrega a view de EDIÇÃO (Critério Aceite 2)
            $errors = $result['errors'];
            $oldData = $data;
            $oldData['id'] = $categoryId; // Garante que o ID está presente para o form
            $_SESSION['flash_message'] = [ // Adiciona mensagem de erro flash
                'type' => 'error',
                'message' => 'Erro ao atualizar categoria. Verifique os campos.'
            ];
            include __DIR__ . '/../views/categories/edit.php';
        }
    }
}
