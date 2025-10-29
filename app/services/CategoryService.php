<?php

class CategoryService
{
    private $categoryModel;
public function __construct()
    {
        // O CategoryService depende do CategoryModel
        if (!class_exists('CategoryModel')) {
            require_once __DIR__ .
'/../models/CategoryModel.php';
        }
        $this->categoryModel = new CategoryModel();
}

    /**
     * Valida os dados e cria uma nova categoria para um utilizador.
* (US-Cat-01)
     *
     * @param int $userId ID do utilizador dono da categoria.
* @param array $data Dados da categoria (espera 'name' e 'type').
* @return array ['success' => bool, 'errors' => array, 'message' => string, 'category_id' => int|null]
     */
    public function createCategory(int $userId, array $data): array
    {
        // 1. Limpar e Validar Dados de Entrada
        $name = trim($data['name'] ?? '');
$type = $data['type'] ?? ''; // Tipo (income/expense)

        $errors = [];
if (empty($name)) {
            $errors[] = 'O nome da categoria é obrigatório.';
} elseif (strlen($name) > 100) {
             $errors[] = 'O nome da categoria não pode exceder 100 caracteres.';
}

        if (!in_array($type, ['income', 'expense'])) {
            $errors[] = 'O tipo da categoria deve ser "income" (Receita) ou "expense" (Despesa).';
}

        // 2. Verificar Duplicidade (Critério de Aceite)
        // O utilizador não pode ter duas categorias com o mesmo nome E mesmo tipo.
if (empty($errors)) { // Só verifica duplicidade se os dados básicos são válidos
            if ($this->categoryModel->findByNameAndType($userId, $name, $type)) {
                $errors[] = 'Já existe uma categoria com este nome e tipo.';
}
        }

        // 3. Retornar erros se houver
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors, 'message' => 'Erro na validação.'];
}

        // 4. Chamar o Model para criar a categoria
        $categoryId = $this->categoryModel->create($userId, $name, $type);
if ($categoryId) {
            return [
                'success' => true,
                'message' => 'Categoria criada com sucesso!',
                'category_id' => $categoryId // Retorna o ID da nova categoria
            ];
} else {
            // Pode ser um erro de BD inesperado (além da duplicidade já tratada)
             error_log("CategoryService: Erro do Model ao tentar criar categoria '{$name}' para user {$userId}.");
return [
                'success' => false,
                'errors' => ['Ocorreu um erro inesperado ao salvar a categoria.'],
                'message' => 'Erro no servidor.'
];
        }
    }

    /**
     * Busca todas as categorias de um utilizador.
     * (US-Cat-02)
     *
     * @param int $userId ID do utilizador.
     * @return array Lista de categorias.
     */
    public function getCategoriesByUser(int $userId): array
    {
        return $this->categoryModel->findAllByUserId($userId);
    }

    // =======================================================
    // MÉTODOS NOVOS (US-Cat-03) - VERIFIQUE SE ESTES ESTÃO AQUI
    // =======================================================

    /**
     * Busca uma categoria específica para edição, verificando a posse.
     * (US-Cat-03)
     *
     * @param int $categoryId ID da categoria.
     * @param int $userId ID do utilizador.
     * @return array|false Retorna a categoria ou false.
     */
    public function getCategoryById(int $categoryId, int $userId)
    {
        return $this->categoryModel->findById($categoryId, $userId);
    }

    /**
     * Valida os dados e atualiza uma categoria.
     * (US-Cat-03)
     *
     * @param int $categoryId ID da categoria a ser atualizada.
     * @param int $userId ID do utilizador dono.
     * @param array $data Dados (espera 'name' e 'type').
     * @return array ['success' => bool, 'errors' => array, 'message' => string]
     */
    public function updateCategory(int $categoryId, int $userId, array $data): array
    {
        // 1. Validar Dados de Entrada
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

        // 2. Verificar Duplicidade
        if (empty($errors)) {
            $existingCategory = $this->categoryModel->findByNameAndType($userId, $name, $type);

            if ($existingCategory && $existingCategory['id'] != $categoryId) {
                $errors[] = 'Já existe uma categoria com este nome e tipo.';
            }
        }

        // 3. Retornar erros se houver
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors, 'message' => 'Erro na validação.'];
}

        // 4. Chamar o Model para atualizar
        if ($this->categoryModel->update($categoryId, $userId, $name, $type)) {
            return [
                'success' => true,
                'message' => 'Categoria atualizada com sucesso!',
            ];
        } else {
             error_log("CategoryService: Erro do Model ao tentar ATUALIZAR categoria '{$name}' (ID: {$categoryId}) para user {$userId}.");
             return [
                'success' => false,
                'errors' => ['Ocorreu um erro inesperado ao salvar. Verifique se o nome já existe.'],
                'message' => 'Erro no servidor.'
             ];
        }
    }
}