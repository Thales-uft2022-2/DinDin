<?php

require_once __DIR__ . '/../services/CategoryService.php';

class CategoryController
{
    private $categoryService;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (empty($_SESSION['user']['id'])) {
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }

        $this->categoryService = new CategoryService();
    }

    public function index()
    {
        $userId = $_SESSION['user']['id'];
        $categories = $this->categoryService->getCategoriesByUser($userId);
        include __DIR__ . '/../views/categories/index.php';
    }

    public function create()
    {
        include __DIR__ . '/../views/categories/create.php';
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/categories/create');
            exit;
        }

        $userId = $_SESSION['user']['id'];
        $data = [
            'name' => $_POST['name'] ?? '',
            'type' => $_POST['type'] ?? ''
        ];

        $result = $this->categoryService->createCategory($userId, $data);

        if ($result['success']) {
            $_SESSION['flash_message'] = [
                'type' => 'success',
                'message' => $result['message']
            ];
            header('Location: ' . BASE_URL . '/categories');
            exit;
        } else {
            $errors = $result['errors'];
            $oldData = $data;
            $_SESSION['flash_message'] = [
                'type' => 'error',
                'message' => 'Erro ao criar categoria.'
            ];
            include __DIR__ . '/../views/categories/create.php';
        }
    }

    public function edit()
    {
        $categoryId = (int) ($_GET['id'] ?? 0);
        $userId = $_SESSION['user']['id'];

        if (!$categoryId) {
            header('Location: ' . BASE_URL . '/categories');
            exit;
        }

        $category = $this->categoryService->getCategoryById($categoryId, $userId);

        if (!$category) {
            $_SESSION['flash_message'] = [
                'type' => 'error',
                'message' => 'Categoria não encontrada ou acesso negado.'
            ];
            header('Location: ' . BASE_URL . '/categories');
            exit;
        }

        $oldData = $category;
        include __DIR__ . '/../views/categories/edit.php';
    }

    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/categories');
            exit;
        }

        $userId = $_SESSION['user']['id'];
        $categoryId = (int) ($_POST['id'] ?? 0);
        $data = [
            'name' => $_POST['name'] ?? '',
            'type' => $_POST['type'] ?? ''
        ];

        $result = $this->categoryService->updateCategory($categoryId, $userId, $data);

        if ($result['success']) {
            $_SESSION['flash_message'] = [
                'type' => 'success',
                'message' => $result['message']
            ];
            header('Location: ' . BASE_URL . '/categories');
            exit;
        } else {
            $errors = $result['errors'];
            $oldData = $data;
            $oldData['id'] = $categoryId;
            $_SESSION['flash_message'] = [
                'type' => 'error',
                'message' => 'Erro ao atualizar categoria.'
            ];
            include __DIR__ . '/../views/categories/edit.php';
        }
    }

    /**
     * Exclui uma categoria.
     * (US-Cat-04)
     */
    public function delete()
    {
        $categoryId = (int) ($_GET['id'] ?? 0);
        $userId = $_SESSION['user']['id'];

        if (!$categoryId) {
            $_SESSION['flash_message'] = [
                'type' => 'error',
                'message' => 'ID de categoria inválido.'
            ];
            header('Location: ' . BASE_URL . '/categories');
            exit;
        }

        $result = $this->categoryService->deleteCategory($categoryId, $userId);

        $_SESSION['flash_message'] = [
            'type' => $result['success'] ? 'success' : 'error',
            'message' => $result['message']
        ];

        header('Location: ' . BASE_URL . '/categories');
        exit;
    }
}