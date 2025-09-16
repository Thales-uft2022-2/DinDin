<?php
// carrega as configs
require_once __DIR__ . '/../config/config.php';

// autoload simples para controllers, models e core
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

session_start();

// rota simples (ex.: /transactions/create)
$uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$base = trim(parse_url(BASE_URL, PHP_URL_PATH), '/');
$path = ltrim(substr($uri, strlen($base)), '/');

// rota padrão -> home
if ($path === '' || $path === 'home') {
    echo "<h1>Bem-vindo ao DinDin 💰</h1>";
    exit;
}

[$ctrl, $action] = array_pad(explode('/', $path, 2), 2, 'index');
$controller = ucfirst($ctrl) . 'Controller';

// verifica se controller e action existem
if (class_exists($controller) && method_exists($controller, $action)) {
    (new $controller)->$action();
} else {
    http_response_code(404);
    echo "<h1>404 - Página não encontrada</h1>";
}