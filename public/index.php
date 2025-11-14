<?php

// ===== DEBUG MODE =====
ini_set('display_errors', 1);
error_reporting(E_ALL);
// ======================

// Configurações e autoload
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../vendor/autoload.php';

// Autoload customizado
spl_autoload_register(function ($class) {
    $paths = [
        __DIR__ . '/../app/controllers/',
        __DIR__ . '/../app/models/',
        __DIR__ . '/../app/core/',
        __DIR__ . '/../app/services/'
    ];

    foreach ($paths as $path) {
        $file = $path . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Inicia a sessão (caso ainda não tenha sido iniciada)
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Captura e trata a URL atual
$uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$base = trim(parse_url(BASE_URL, PHP_URL_PATH), '/');
$path = ltrim(substr($uri, strlen($base)), '/');

// ============================================================
// 🔗 DEFINIÇÃO DAS ROTAS DO SISTEMA
// ============================================================
$routes = [

    // --------- TELA INICIAL ---------
    '' => ['HomeController', 'index'],
    'home' => ['HomeController', 'index'],

    // --------- AUTENTICAÇÃO ---------
    'auth/login' => ['AuthController', 'login'],
    'auth/register' => ['AuthController', 'register'],
    'auth/logout' => ['AuthController', 'logout'],
    'auth/forgot-password' => ['AuthController', 'forgotPassword'],
    'auth/send-reset-link' => ['AuthController', 'sendResetLink'],
    'auth/reset-password' => ['AuthController', 'resetPassword'],
    'auth/update-password' => ['AuthController', 'updatePassword'],

    // --------- USUÁRIO E PERFIL ---------
    'user/store' => ['UserController', 'store'],
    'profile' => ['UserController', 'profile'],                  // (US-Profile-01) GET - Mostrar página de perfil
    'profile/update' => ['UserController', 'updateProfile'],        // (US-Profile-01) POST - Atualizar nome
    'profile/update-avatar' => ['UserController', 'updateAvatar'],    // (US-Profile-01) POST - Atualizar foto
    
    // ▼▼▼ NOVA ROTA PARA APAGAR AVATAR ▼▼▼
    'profile/delete-avatar' => ['UserController', 'deleteAvatar'],    // (US-Profile-01) POST - Apagar foto
    
    'profile/password' => ['UserController', 'showChangePasswordForm'], // (US-Profile-02) GET - Mostrar pág. de senha
    'profile/change-password' => ['UserController', 'changePassword'],  // (US-Profile-02) POST - Atualizar senha

    // --------- TRANSAÇÕES ---------
    'transactions' => ['TransactionsController', 'index'],
    'transactions/create' => ['TransactionsController', 'create'],
    'transactions/store' => ['TransactionsController', 'store'],
    'transactions/edit' => ['TransactionsController', 'edit'],
    'transactions/update' => ['TransactionsController', 'update'],
    'transactions/delete' => ['TransactionsController', 'delete'],

    // --------- CATEGORIAS ---------
    'categories' => ['CategoryController', 'index'],
    'categories/create' => ['CategoryController', 'create'],
    'categories/store' => ['CategoryController', 'store'],
    'categories/edit' => ['CategoryController', 'edit'],
    'categories/update' => ['CategoryController', 'update'],
    'categories/delete' => ['CategoryController', 'delete'],

    // --------- API (Seu código original) ---------
    'api/transactions' => ['TransactionsController', 'apiIndex'],
    'api/transactions/create' => ['TransactionsController', 'apiCreate'],
    'api/transactions/update' => ['TransactionsController', 'apiUpdate'],
    'api/transactions/delete' => ['TransactionsController', 'apiDelete'],
    'api/auth/register' => ['AuthController', 'apiRegister'],
    'api/auth/login' => ['AuthController', 'apiLogin'],
    'api/auth/logout' => ['AuthController', 'apiLogout'],
    'api/auth/forgot-password' => ['AuthController', 'apiForgotPassword'],
    'api/auth/reset-password' => ['AuthController', 'apiResetPassword'],
    'api/categories' => ['CategoryController', 'apiIndex'],
    'api/categories/store' => ['CategoryController', 'apiStore'],
    'api/categories/update' => ['CategoryController', 'apiUpdate'],
    'api/categories/delete' => ['CategoryController', 'apiDelete'],
];

// ============================================================
// 🚀 RESOLUÇÃO DAS ROTAS
// ============================================================
if ($path === '') {
    [$controllerName, $action] = $routes[''];
} elseif (isset($routes[$path])) {
    [$controllerName, $action] = $routes[$path];
} else {
    // fallback dinâmico /controller/action
    $parts = explode('/', $path, 2);
    $ctrl = $parts[0];
    $action = $parts[1] ?? 'index';
    $controllerName = ucfirst($ctrl) . 'Controller';

    if (!class_exists($controllerName) || !method_exists($controllerName, $action)) {
        http_response_code(404);
        echo "<h1>404 - Página não encontrada</h1>";
        echo "<p>Rota: /{$path}</p>";
        exit;
    }
}

// ============================================================
// ⚙️ EXECUÇÃO DO CONTROLLER
// ============================================================
try {
    $controllerInstance = new $controllerName();
    $controllerInstance->$action();
} catch (Throwable $e) {
    error_log("Erro na rota '/{$path}': " . $e->getMessage());
    http_response_code(500);
    echo "<h1>Erro interno (500)</h1>";
    echo "<pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
}