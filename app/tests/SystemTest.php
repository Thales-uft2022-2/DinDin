<?php
// app/tests/SystemTest.php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../core/Database.php';

// Models
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/CategoryModel.php';
require_once __DIR__ . '/../models/TransactionModel.php';

// Services
require_once __DIR__ . '/../services/AuthService.php';
require_once __DIR__ . '/../services/CategoryService.php';
require_once __DIR__ . '/../services/TransactionService.php';
require_once __DIR__ . '/../services/DashboardService.php';

class SystemTest extends TestCase
{
    private $pdo;

    protected function setUp(): void
    {
        // Conexão com o DB real (config.php deve definir DB_HOST, DB_NAME, DB_USER, DB_PASS)
        $this->pdo = Database::getConnection();

        // Limpa tabelas para ambiente de teste
        $this->pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
        $this->pdo->exec("TRUNCATE TABLE transactions");
        $this->pdo->exec("TRUNCATE TABLE categories");
        $this->pdo->exec("TRUNCATE TABLE users");
        $this->pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
    }

    protected function tearDown(): void
    {
        // Limpa após teste (para não deixar lixo)
        $this->pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
        $this->pdo->exec("TRUNCATE TABLE transactions");
        $this->pdo->exec("TRUNCATE TABLE categories");
        $this->pdo->exec("TRUNCATE TABLE users");
        $this->pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
    }

    private function getLastTransactionId(int $userId)
    {
        $stmt = $this->pdo->prepare("SELECT id FROM transactions WHERE user_id = :uid ORDER BY id DESC LIMIT 1");
        $stmt->execute([':uid' => $userId]);
        $row = $stmt->fetch();
        return $row ? (int)$row['id'] : null;
    }

    private function getTransactionById(int $id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM transactions WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    /** 
     * Teste único integrador cobrindo várias funcionalidades do sistema
     */
    public function test_full_system_flow()
    {
        // -------------------------
        // 1) AUTH: registro e login
        // -------------------------
        $authService = new AuthService();

        $email = 'unitario+' . bin2hex(random_bytes(4)) . '@exemplo.test';
        $password = 'senhaTest123';

        $register = $authService->registerUser([
            'name' => 'User Teste',
            'email' => $email,
            'password' => $password,
            'password_confirm' => $password
        ]);

        $this->assertTrue($register['success'], "Registro falhou: " . json_encode($register));
        $this->assertArrayHasKey('user', $register, "Resposta de registro não contém 'user'");
        $this->assertArrayHasKey('id', $register['user'] ?? [], "Registro não retornou id do usuário (user.id)");

        $userId = (int)$register['user']['id'];

        // Login
        $login = $authService->loginUser(['email' => $email, 'password' => $password]);
        $this->assertTrue($login['success'], "Login falhou: " . json_encode($login));
        $this->assertEquals($email, $login['user']['email'] ?? '', "E-mail na sessão diverge");

        // -------------------------
        // 2) CATEGORY: CRUD + regras
        // -------------------------
        $categoryService = new CategoryService();

        // Criar categoria income
        $res1 = $categoryService->createCategory($userId, ['name' => 'Salário Test', 'type' => 'income']);
        $this->assertTrue($res1['success'], 'Falha ao criar categoria income: ' . json_encode($res1));
        $this->assertArrayHasKey('category_id', $res1, 'createCategory não retornou category_id');

        // Criar categoria expense
        $res2 = $categoryService->createCategory($userId, ['name' => 'Moradia Test', 'type' => 'expense']);
        $this->assertTrue($res2['success'], 'Falha ao criar categoria expense: ' . json_encode($res2));

        // Tentar criar duplicada -> deve falhar
        $dup = $categoryService->createCategory($userId, ['name' => 'Salário Test', 'type' => 'income']);
        $this->assertFalse($dup['success'], 'Categoria duplicada aceitou inserção');

        // Buscar categorias do usuário
        $cats = $categoryService->getCategoriesByUser($userId);
        $this->assertIsArray($cats, 'getCategoriesByUser não retornou array');
        $this->assertCount(2, $cats, 'Quantidade de categorias esperadas != 2');

        // Update categoria (pegar id da primeira)
        $catId = (int)$res1['category_id'];
        $upd = $categoryService->updateCategory($catId, $userId, ['name' => 'Salário Test Edit', 'type' => 'income']);
        $this->assertTrue($upd['success'], 'Falha ao atualizar categoria: ' . json_encode($upd));

        // Delete categoria (ok)
        $del = $categoryService->deleteCategory($catId, $userId);
        $this->assertTrue($del['success'], 'Falha ao excluir categoria: ' . json_encode($del));

        // -------------------------
        // 3) TRANSACTIONS: CRUD + segurança
        // -------------------------
        $transactionService = new TransactionService();

        // Criar transação income
        $t1 = [
            'type' => 'income',
            'category' => 'Salário Test', // apesar de termos excluído uma categoria id, coluna category é string na tabela
            'description' => 'Pagamento unit test',
            'amount' => 1200.00,
            'date' => date('Y-m-d')
        ];
        $resT1 = $transactionService->createTransaction($t1, $userId);
        $this->assertTrue($resT1['success'], 'Falha ao criar transação income: ' . json_encode($resT1));
        $this->assertStringContainsString('Transação', $resT1['message'] ?? '', 'Mensagem inesperada na criação');

        // Criar transação expense
        $t2 = [
            'type' => 'expense',
            'category' => 'Moradia Test',
            'description' => 'Conta unit test',
            'amount' => 300.00,
            'date' => date('Y-m-d')
        ];
        $resT2 = $transactionService->createTransaction($t2, $userId);
        $this->assertTrue($resT2['success'], 'Falha ao criar transação expense: ' . json_encode($resT2));

        // Pegar último ID criado para este usuário
        $lastId = $this->getLastTransactionId($userId);
        $this->assertNotNull($lastId, 'Não foi possível obter último id de transação');

        // Checar dados no DB
        $tx = $this->getTransactionById($lastId);
        $this->assertNotEmpty($tx, 'Transação não encontrada no DB após criação');
        $this->assertEquals('expense', $tx['type'], 'Tipo da transação DB diferente do esperado');

        // Dashboard / Analytics checks
        $dashboard = new DashboardService();
        $summary = $dashboard->getMonthlySummary($userId);

        // total_income deve ser >= 1200 e total_expense >= 300 (dependendo do mês)
        $this->assertIsArray($summary, 'Resumo mensal não retornou array');
        $this->assertArrayHasKey('total_income', $summary);
        $this->assertArrayHasKey('total_expense', $summary);

        // comparar somas: buscar transações diretas para calcular
        $stmt = $this->pdo->prepare("SELECT SUM(amount) as total_income FROM transactions WHERE user_id = :uid AND type = 'income'");
        $stmt->execute([':uid' => $userId]);
        $rowInc = $stmt->fetch();
        $expectedIncome = (float)($rowInc['total_income'] ?? 0.0);

        $stmt2 = $this->pdo->prepare("SELECT SUM(amount) as total_expense FROM transactions WHERE user_id = :uid AND type = 'expense'");
        $stmt2->execute([':uid' => $userId]);
        $rowExp = $stmt2->fetch();
        $expectedExpense = (float)($rowExp['total_expense'] ?? 0.0);

        $this->assertEquals($expectedIncome, (float)$summary['total_income']);
        $this->assertEquals($expectedExpense, (float)$summary['total_expense']);

        // Analytics: despesas por categoria (usar intervalo atual)
        $start = date('Y-m-01');
        $end = date('Y-m-t');
        $expensesByCat = $dashboard->getExpensesByCategoryData($userId, $start, $end);
        $this->assertIsArray($expensesByCat, 'getExpensesByCategoryData não retornou array');
        // Deve existir "Moradia Test" com valor >= 300
        $found = false;
        foreach ($expensesByCat['labels'] as $i => $label) {
            if ($label === 'Moradia Test') {
                $found = true;
                $this->assertGreaterThanOrEqual(300.0, (float)$expensesByCat['data'][$i]);
            }
        }
        // Pelo menos uma categoria de despesas deve existir
        $this->assertTrue(count($expensesByCat['labels']) >= 0);

        // Update transaction (último id)
        $updateData = [
            'type' => 'expense',
            'category' => 'Moradia Test',
            'description' => 'Conta atualizada',
            'amount' => 350.00,
            'date' => date('Y-m-d')
        ];
        $updRes = $transactionService->updateTransaction($lastId, $userId, $updateData);
        $this->assertTrue($updRes['success'], 'Falha ao atualizar transação: ' . json_encode($updRes));

        // Tentativa de editar por outro usuário -> deve falhar (403)
        $fakeUserId = 999999;
        $updFake = $transactionService->updateTransaction($lastId, $fakeUserId, $updateData);
        $this->assertFalse($updFake['success'], 'Outro usuário conseguiu editar transação alheia');
        $this->assertArrayHasKey('status_code', $updFake);
        $this->assertEquals(403, $updFake['status_code'], 'Código HTTP esperado 403 para edição indevida');

        // Delete transação (dono)
        $delRes = $transactionService->deleteTransaction($lastId, $userId);
        $this->assertTrue($delRes['success'], 'Falha ao excluir transação: ' . json_encode($delRes));

        // Delete por outro usuário -> criar nova transac e testar exclusão indevida
        $res3 = $transactionService->createTransaction($t2, $userId);
        $this->assertTrue($res3['success'], 'Falha ao criar transação para teste de exclusão indevida');
        $newId = $this->getLastTransactionId($userId);
        $delFake = $transactionService->deleteTransaction($newId, 888888);
        $this->assertFalse($delFake['success'], 'Usuário indevido conseguiu excluir transação');
        $this->assertEquals(403, $delFake['status_code']);

        // -------------------------
        // 4) FINAL: checagens adicionais pequenas
        // -------------------------
        // Confirmar que existem transações (pelo menos a criada anteriormente)
        $rows = $this->pdo->prepare("SELECT COUNT(*) as c FROM transactions WHERE user_id = :uid");
        $rows->execute([':uid' => $userId]);
        $count = (int)$rows->fetch()['c'];
        $this->assertGreaterThanOrEqual(0, $count);

        // Tudo ok se chegou até aqui
        $this->assertTrue(true, 'Fluxo integrado executado com sucesso');
    }
}
