<?php
require_once __DIR__ . '/../config/config.php';

// Autoload simples das classes (Controllers, Models, Core)
spl_autoload_register(function ($class) {
    $paths = [
        __DIR__ . '/../app/controllers/',
        __DIR__ . '/../app/models/',
        __DIR__ . '/../app/core/'
    ];

    foreach ($paths as $path) {
        $file = $path . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Inicia sessão (se não estiver ativa)
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Captura a rota atual
$uri  = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$base = trim(parse_url(BASE_URL, PHP_URL_PATH), '/');
$path = ltrim(substr($uri, strlen($base)), '/');

// Tabela de rotas explícitas
$routes = [
    // Página inicial / Home
    'home'                 => ['HomeController', 'index'],

    // Autenticação
    'auth/login'           => ['AuthController', 'login'],
    'auth/logout'          => ['AuthController', 'logout'],   // ✅ Logout configurado
    'auth/register'        => ['AuthController', 'register'],

    // Transações
    'transactions'         => ['TransactionsController', 'index'],
    'transactions/create'  => ['TransactionsController', 'create'],
    'transactions/store'   => ['TransactionsController', 'store'],
    'transactions/edit'    => ['TransactionsController', 'edit'],
    'transactions/update'  => ['TransactionsController', 'update'],
    'transactions/delete'  => ['TransactionsController', 'delete'],
];

// Define rota padrão (raiz = login)
if ($path === '') {
    [$controllerName, $action] = $routes['auth/login'];
} elseif (isset($routes[$path])) {
    [$controllerName, $action] = $routes[$path];
} else {
    // Fallback dinâmico: /controller/action
    [$ctrl, $action] = array_pad(explode('/', $path, 2), 2, 'index');
    $controllerName  = ucfirst($ctrl) . 'Controller';
}

// Despacha a rota
if (class_exists($controllerName) && method_exists($controllerName, $action)) {
    (new $controllerName)->$action();
} else {
    http_response_code(404);
    echo "<h1>404 - Página não encontrada</h1>";
}
