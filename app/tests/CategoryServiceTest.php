<?php

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../app/core/Database.php';
require_once __DIR__ . '/../../app/models/CategoryModel.php';
require_once __DIR__ . '/../../app/services/CategoryService.php';

require_once __DIR__ . '/../../config/config.php';
 // ajusta o caminho conforme sua estrutura

use PHPUnit\Framework\TestCase;

class CategoryServiceTest extends TestCase
{
    private $db;
    private $categoryModel;

    protected function setUp(): void
    {
        // Conexão de teste (usa o mesmo banco, mas você pode apontar para outro se quiser)
        $this->db = Database::getConnection();
        $this->categoryModel = new CategoryModel();

        // Limpa tabela antes de cada teste (isolamento total)
        $this->db->exec("DELETE FROM categories");
    }

    /** @test */
    public function deve_criar_categoria_para_usuario()
    {
        $id = $this->categoryModel->create(1, 'Alimentação', 'despesa');
        $this->assertIsNumeric($id, 'O ID retornado deve ser numérico.');
    }

    /** @test */
    public function nao_deve_permitir_nomes_duplicados_para_mesmo_usuario()
    {
        $this->categoryModel->create(1, 'Transporte', 'despesa');
        $duplicada = $this->categoryModel->create(1, 'Transporte', 'despesa');
        $this->assertFalse($duplicada, 'Não deve permitir nome duplicado para o mesmo usuário.');
    }

    /** @test */
    public function deve_permitir_nomes_iguais_para_usuarios_diferentes()
    {
        $id1 = $this->categoryModel->create(1, 'Saúde', 'despesa');
        $id2 = $this->categoryModel->create(2, 'Saúde', 'despesa');
        $this->assertNotFalse($id1);
        $this->assertNotFalse($id2);
        $this->assertNotEquals($id1, $id2);
    }

    /** @test */
    public function deve_buscar_categorias_do_usuario()
    {
        $this->categoryModel->create(1, 'Educação', 'despesa');
        $this->categoryModel->create(1, 'Lazer', 'despesa');
        $categorias = $this->categoryModel->findAllByUserId(1);
        $this->assertCount(2, $categorias);
    }

    /** @test */
    public function deve_editar_categoria_existente()
    {
        $id = $this->categoryModel->create(1, 'Transporte', 'despesa');
        $resultado = $this->categoryModel->update($id, 1, 'Transporte Público', 'despesa');
        $this->assertTrue($resultado);
    }

    /** @test */
    public function deve_excluir_categoria_do_usuario()
    {
        $id = $this->categoryModel->create(1, 'Roupas', 'despesa');
        $excluido = $this->categoryModel->delete($id, 1);
        $this->assertTrue($excluido);
    }

}
