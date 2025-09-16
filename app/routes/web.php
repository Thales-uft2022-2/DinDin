<?php
// Definição de rotas do DinDin
$router->get('/',               [TesteController::class, 'index']);            // home
$router->get('/home',           [TesteController::class, 'index']);            // alias

// Transações
$router->get('/transactions',          [TransactionsController::class, 'index']);
$router->get('/transactions/create',   [TransactionsController::class, 'create']);
$router->post('/transactions/store',   [TransactionsController::class, 'store']);
$router->get('/transactions/edit',     [TransactionsController::class, 'edit']);   // ?id=123
$router->post('/transactions/update',  [TransactionsController::class, 'update']);

// Rota de saúde (útil em deploys)
$router->get('/health', [TesteController::class, 'index']);
