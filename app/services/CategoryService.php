<?php

class CategoryService
{
    private $categoryModel;

    public function __construct()
    {
        if (!class_exists('CategoryModel')) {
            require_once __DIR__ . '/../models/CategoryModel.php';
        }
        $this->categoryModel = new CategoryModel();
    }

    public function createCategory(int $userId, array $data): array
    {
        $name = trim($data['name'] ?? '');
        $type = $data['type'] ?? '';
        $errors = [];

        if (empty($name)) {
            $errors[] = 'O nome da categoria é obrigatório.';
        } elseif (strlen($name) > 100) {
            $errors[] = 'O nome da categoria não pode exceder 100 caracteres.';
        }

        if (!in_array($type, ['income', 'expense'])) {
            $errors[] = 'O tipo da categoria deve ser "income" (Receita) ou "expense" (Despesa).';
        }

        if (empty($errors)) {
            if ($this->categoryModel->findByNameAndType($userId, $name, $type)) {
                $errors[] = 'Já existe uma categoria com este nome e tipo.';
            }
        }

        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors, 'message' => 'Erro na validação.'];
        }

        $categoryId = $this->categoryModel->create($userId, $name, $type);
        if ($categoryId) {
            return [
                'success' => true,
                'message' => 'Categoria criada com sucesso!',
                'category_id' => $categoryId
            ];
        } else {
            return [
                'success' => false,
                'errors' => ['Ocorreu um erro inesperado ao salvar a categoria.'],
                'message' => 'Erro no servidor.'
            ];
        }
    }

    public function getCategoriesByUser(int $userId): array
    {
        return $this->categoryModel->findAllByUserId($userId);
    }

    public function getCategoryById(int $categoryId, int $userId)
    {
        return $this->categoryModel->findById($categoryId, $userId);
    }

    public function updateCategory(int $categoryId, int $userId, array $data): array
    {
        $name = trim($data['name'] ?? '');
        $type = $data['type'] ?? '';
        $errors = [];

        if (empty($name)) {
            $errors[] = 'O nome da categoria é obrigatório.';
        } elseif (strlen($name) > 100) {
            $errors[] = 'O nome da categoria não pode exceder 100 caracteres.';
        }

        if (!in_array($type, ['income', 'expense'])) {
            $errors[] = 'O tipo da categoria deve ser "income" (Receita) ou "expense" (Despesa).';
        }

        if (empty($errors)) {
            $existingCategory = $this->categoryModel->findByNameAndType($userId, $name, $type);

            if ($existingCategory && $existingCategory['id'] != $categoryId) {
                $errors[] = 'Já existe uma categoria com este nome e tipo.';
            }
        }

        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors, 'message' => 'Erro na validação.'];
        }

        if ($this->categoryModel->update($categoryId, $userId, $name, $type)) {
            return [
                'success' => true,
                'message' => 'Categoria atualizada com sucesso!',
            ];
        } else {
            return [
                'success' => false,
                'errors' => ['Ocorreu um erro inesperado ao salvar. Verifique se o nome já existe.'],
                'message' => 'Erro no servidor.'
            ];
        }
    }

    /**
     * Exclui uma categoria do usuário.
     * (US-Cat-04)
     */
    public function deleteCategory(int $categoryId, int $userId): array
    {
        $category = $this->categoryModel->findById($categoryId, $userId);

        if (!$category) {
            return [
                'success' => false,
                'message' => 'Categoria não encontrada ou acesso negado.'
            ];
        }

        $deleted = $this->categoryModel->delete($categoryId, $userId);

        if ($deleted) {
            return [
                'success' => true,
                'message' => 'Categoria excluída com sucesso!'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Erro ao excluir categoria. Tente novamente.'
            ];
        }
    }
}