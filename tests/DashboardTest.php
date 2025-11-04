<?php
session_start(); $_SESSION['user_id'] = 1;

require_once __DIR__ . '/../app/services/DashboardService.php';

$svc = new DashboardService();
$res = $svc->saldoMensal(1, (int)date('Y'), (int)date('n'));

if (!isset($res['entradas'],$res['saidas'],$res['saldo'])) {
  exit("[TS-Test-01] FALHA: resposta inválida\n");
}
echo "[TS-Test-01] OK: ", json_encode($res, JSON_UNESCAPED_UNICODE), PHP_EOL;