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

// 1. Rota de limpeza
$uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$base = trim(parse_url(BASE_URL, PHP_URL_PATH), '/');
$path = ltrim(substr($uri, strlen($base)), '/');

// 2. Lógica de Roteamento (Define Controller e Action)
// ROTA PADRÃO (PÁGINA INICIAL) -> REGISTRO (UserController->register)
if ($path === '' || $path === 'home') {
    $ctrl = 'user';
    $action = 'register';
} else {
    // Rota normal: /controller/action
    [$ctrl, $action] = array_pad(explode('/', $path, 2), 2, 'index');
}

$controller = ucfirst($ctrl) . 'Controller';

// 3. Execução do Controller
// verifica se controller e action existem
if (class_exists($controller) && method_exists($controller, $action)) {
    // Cria a instância do Controller e chama o método (Action)
    (new $controller)->$action();
} else {
    http_response_code(404);
    echo "<h1>404 - Página não encontrada</h1>";
}
