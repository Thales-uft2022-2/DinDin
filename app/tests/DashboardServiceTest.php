<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../app/services/DashboardService.php';

class DashboardServiceTest extends TestCase
{
    private $dashboard;

    protected function setUp(): void
    {
        $this->dashboard = new DashboardService();
    }

    // TS-COV-04-A: cálculo de saldo geral
    public function testCalculoDeSaldoGeral()
    {
        $resultado = $this->dashboard->calcularSaldo('2024-08');
        $this->assertIsNumeric($resultado);
    }

    // TS-COV-04-B: mês sem movimentação (saldo 0)
    public function testMesSemMovimentacao()
    {
        $saldo = $this->dashboard->calcularSaldo('2024-07');
        $this->assertSame(0.0, (float)$saldo);
    }

    // TS-COV-04-C: mês só com despesas (saldo <= 0)
    public function testMesApenasComDespesas()
    {
        $saldo = $this->dashboard->calcularSaldo('2024-06');
        $this->assertLessThanOrEqual(0, $saldo);
    }

    // TS-COV-04-D: filtros de data
    public function testFiltroPorPeriodo()
    {
        $dados = $this->dashboard->filtrarPorPeriodo('2024-01', '2024-12');
        $this->assertIsArray($dados);
        $this->assertNotEmpty($dados);
    }

    // TS-COV-04-E: integridade dos campos
    public function testIntegridadeDosDados()
    {
        $dados = $this->dashboard->filtrarPorPeriodo('2024-05', '2024-06');
        foreach ($dados as $r) {
            $this->assertArrayHasKey('mes', $r);
            $this->assertArrayHasKey('receitas', $r);
            $this->assertArrayHasKey('despesas', $r);
            $this->assertArrayHasKey('saldo', $r);
        }
    }

    // TS-COV-04-F: range realmente aplicado
    public function testAplicacaoCorretaDoFiltroDeData()
    {
        $dados = $this->dashboard->filtrarPorPeriodo('2024-03', '2024-05');
        foreach ($dados as $r) {
            $this->assertTrue($r['mes'] >= '2024-03' && $r['mes'] <= '2024-05');
        }
    }
}