<?php
class Autoload {
    public static function register(): void {
        spl_autoload_register(function ($class) {
            $base = __DIR__ . '/../';
            $paths = [
                $base . 'controllers/' . $class . '.php',
                $base . 'models/' . $class . '.php',
                __DIR__ . '/' . $class . '.php',
            ];
            foreach ($paths as $file) {
                if (is_file($file)) { require_once $file; return; }
            }
        });
    }
}
