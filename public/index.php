<?php
declare(strict_types=1);
session_start();

// Caminhos físicos
if (!defined('BASE_PATH')) define('BASE_PATH', dirname(__DIR__));
if (!defined('APP_PATH'))  define('APP_PATH', BASE_PATH . '/app');

// Detecta BASE_URI automaticamente a partir do diretório do script
$scriptName = $_SERVER['SCRIPT_NAME'] ?? '/DinDin/public/index.php';
$publicUri  = rtrim(str_replace('\\','/', dirname($scriptName)), '/');
define('BASE_URI', $publicUri === '/' ? '/' : $publicUri);

// Autoload
require_once APP_PATH . '/core/Autoload.php';
Autoload::register();

// Config (não redefina BASE_URI aqui)
require_once BASE_PATH . '/config/config.php';

// Sobe o app e o roteador
require_once APP_PATH . '/core/App.php';
require_once APP_PATH . '/core/Router.php';

$app    = new App(BASE_URI);
$router = new Router($app);

// Carrega as rotas
require_once APP_PATH . '/routes/web.php';

// Despacha
$router->dispatch();
