<?php

// ===== ADICIONE ESTAS DUAS LINHAS PARA DEBUG =====
ini_set('display_errors', 1);
error_reporting(E_ALL);
// =================================================

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../vendor/autoload.php';
spl_autoload_register(function ($class) {
    $paths = [
        __DIR__ . '/../app/controllers/',
        __DIR__ . '/../app/models/',
        __DIR__ . '/../app/core/',
        __DIR__ . '/../app/services/' // Garante que o autoloader do Service está aqui
    ];

    foreach ($paths as $path) {
        $file = $path . $class . '.php';
        if (file_exists($file)) { require_once $file; return; }
    }
});

// Inicia sessão (se não estiver ativa)
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Rota atual (sem a BASE_URL)
$uri  = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$base = trim(parse_url(BASE_URL, PHP_URL_PATH), '/');
$path = ltrim(substr($uri, strlen($base)), '/');

// Tabela de rotas explícitas (COM ROTAS DE CATEGORIA ADICIONADAS)
$routes = [
    // --- Rotas Principais ---
    'home'                   => ['HomeController', 'index'],

    // --- Rotas WEB de Autenticação ---
    'auth/login'             => ['AuthController', 'login'],
    'auth/logout'            => ['AuthController', 'logout'],
    'auth/register'          => ['AuthController', 'register'],
    'auth/forgot-password'   => ['AuthController', 'forgotPassword'],
    'auth/send-reset-link'   => ['AuthController', 'sendResetLink'],
    'auth/reset-password'    => ['AuthController', 'resetPassword'],
    'auth/update-password'   => ['AuthController', 'updatePassword'],

    // --- Rotas WEB de Transações ---
    'transactions'           => ['TransactionsController', 'index'],
    'transactions/create'    => ['TransactionsController', 'create'],
    'transactions/store'     => ['TransactionsController', 'store'],
    'transactions/edit'      => ['TransactionsController', 'edit'],
    'transactions/update'    => ['TransactionsController', 'update'],
    'transactions/delete'    => ['TransactionsController', 'delete'],

    // --- Rotas WEB de Categorias (NOVAS - US-Cat-01) ---
    'categories'             => ['CategoryController', 'index'], //<-- (NOVA ROTA US-Cat-02) Lista as categorias
    'categories/create'      => ['CategoryController', 'create'], // Mostra o formulário
    'categories/store'       => ['CategoryController', 'store'],  // Processa o formulário

    // --- APIs de Transações ---
    'api/transactions/create' => ['TransactionsController', 'apiCreate'], // TS-Svc-01
    'api/transactions'        => ['TransactionsController', 'apiIndex'],  // TS-Svc-02
    'api/transactions/update' => ['TransactionsController', 'apiUpdate'], // TS-Svc-03
    'api/transactions/delete' => ['TransactionsController', 'apiDelete'], // TS-Svc-04

    // --- APIs de Autenticação ---
    'api/auth/register'       => ['AuthController', 'apiRegister'],       // TS-Auth-01
    'api/auth/login'          => ['AuthController', 'apiLogin'],          // TS-Auth-02
    'api/auth/logout'         => ['AuthController', 'apiLogout'],         // TS-Auth-03
    'api/auth/forgot-password'=> ['AuthController', 'apiForgotPassword'], // TS-Auth-04
    'api/auth/reset-password' => ['AuthController', 'apiResetPassword'],  // TS-Auth-04

    // --- APIs de Categorias (VIRÃO NAS PRÓXIMAS TAREFAS) ---
    // 'api/categories'       => ['CategoryController', 'apiIndex'], // Ex: Listar
    // 'api/categories/store'   => ['CategoryController', 'apiStore'], // Ex: Criar via API
    // ... etc ...
];

// Rota padrão (somente a raiz vai para login)
if ($path === '') {
    [$controllerName, $action] = $routes['auth/login'];
} elseif (isset($routes[$path])) {
    [$controllerName, $action] = $routes[$path];
} else {
    // fallback dinâmico: /controller/action
    // Tenta encontrar /controller/action (ex: /categories/index chamaria CategoryController->index())
    $parts = explode('/', $path, 2);
    $ctrl = $parts[0];
    $action = $parts[1] ?? 'index'; // Ação padrão é 'index' se não for especificada
    $controllerName  = ucfirst($ctrl) . 'Controller';

    // Verifica se o controller/action dinâmico existe ANTES de dar 404
    if (!class_exists($controllerName) || !method_exists($controllerName, $action)) {
         // Se não encontrar nem na tabela $routes nem dinamicamente, dá 404
         http_response_code(404);
         echo "<h1>404 - Rota não encontrada</h1><p>A rota solicitada '/{$path}' não foi encontrada.</p>";
         exit; // Termina a execução
    }
}

// Dispara a action do controller correspondente
try {
    // Cria a instância do controller e chama a ação
    $controllerInstance = new $controllerName();
    $controllerInstance->$action();
} catch (Throwable $e) {
    // Captura erros gerais durante a execução do controller/action
    error_log("Erro ao executar rota '/{$path}': " . $e->getMessage() . "\n" . $e->getTraceAsString());
    http_response_code(500); // Erro interno do servidor
    // Em produção, mostrar uma página de erro genérica. Em dev, pode mostrar detalhes.
    echo "<h1>Erro 500 - Erro Interno do Servidor</h1>";
    if (ini_get('display_errors')) { // Mostra detalhes apenas se display_errors estiver ativo
        echo "<pre>Erro: " . htmlspecialchars($e->getMessage()) . "\n" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    } else {
        echo "<p>Ocorreu um erro inesperado. Tente novamente mais tarde.</p>";
    }
}