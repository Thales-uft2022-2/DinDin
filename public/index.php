
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

// Tabela de rotas explícitas (COM TODAS AS ROTAS RESOLVIDAS + AUTH REGISTER + AUTH LOGIN + AUTH LOGOUT)
$routes = [
    'home'                   => ['HomeController', 'index'],
    'auth/login'             => ['AuthController', 'login'],             // Rota WEB Login
    'auth/logout'            => ['AuthController', 'logout'],            // Rota WEB Logout
    'auth/register'          => ['AuthController', 'register'],          // Rota WEB Registro

    'auth/forgot-password'   => ['AuthController', 'forgotPassword'],
    'auth/send-reset-link'   => ['AuthController', 'sendResetLink'],
    'auth/reset-password'    => ['AuthController', 'resetPassword'],
    'auth/update-password'   => ['AuthController', 'updatePassword'],

    'transactions'           => ['TransactionsController', 'index'],
    'transactions/create'    => ['TransactionsController', 'create'],
    'transactions/store'     => ['TransactionsController', 'store'],
    'transactions/edit'      => ['TransactionsController', 'edit'],
    'transactions/update'    => ['TransactionsController', 'update'],
    'transactions/delete'    => ['TransactionsController', 'delete'],

    // --- APIs de Transações ---
    'api/transactions/create' => ['TransactionsController', 'apiCreate'], // TS-Svc-01
    'api/transactions'        => ['TransactionsController', 'apiIndex'],  // TS-Svc-02
    'api/transactions/update' => ['TransactionsController', 'apiUpdate'], // TS-Svc-03
    'api/transactions/delete' => ['TransactionsController', 'apiDelete'], // TS-Svc-04

    // --- APIs de Autenticação ---
    'api/auth/register'       => ['AuthController', 'apiRegister'],       // TS-Auth-01
    'api/auth/login'          => ['AuthController', 'apiLogin'],          // TS-Auth-02
    'api/auth/logout'         => ['AuthController', 'apiLogout'],         // TS-Auth-03 (NOVA)
];

// Rota padrão (somente a raiz vai para login)
if ($path === '') {
    [$controllerName, $action] = $routes['auth/login'];
} elseif (isset($routes[$path])) {
    [$controllerName, $action] = $routes[$path];
} else {
    // fallback dinâmico: /controller/action
    [$ctrl, $action] = array_pad(explode('/', $path, 2), 2, 'index');
    $controllerName  = ucfirst($ctrl) . 'Controller';
}

// Dispara a action
if (class_exists($controllerName) && method_exists($controllerName, $action)) {
    (new $controllerName)->$action();
} else {
    http_response_code(404);
    echo "<h1>404 - Página não encontrada</h1>";
}