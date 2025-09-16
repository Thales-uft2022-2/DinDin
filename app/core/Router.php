<?php
class Router {
    private array $routes = []; // ['GET'][] = ['path'=>'/login', 'handler'=>[Ctrl, 'method']]

    public function __construct(private App $app) {}

    public function get(string $path, array $handler): void {
        $this->routes['GET'][] = ['path' => $this->norm($path), 'handler' => $handler];
    }
    public function post(string $path, array $handler): void {
        $this->routes['POST'][] = ['path' => $this->norm($path), 'handler' => $handler];
    }
    public function any(string $path, array $handler): void {
        foreach (['GET','POST','PUT','PATCH','DELETE'] as $m) {
            $this->routes[$m][] = ['path' => $this->norm($path), 'handler' => $handler];
        }
    }

    public function dispatch(): void {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $path   = $this->app->path();
        $list   = $this->routes[$method] ?? [];

        foreach ($list as $r) {
            if ($r['path'] === $path) {
                [$ctrl, $action] = $r['handler'];
                if (!class_exists($ctrl)) { http_response_code(500); echo "Controller $ctrl não encontrado"; return; }
                $instance = new $ctrl();
                if (!method_exists($instance, $action)) { http_response_code(500); echo "Ação $action não encontrada em $ctrl"; return; }
                $instance->$action();
                return;
            }
        }
        http_response_code(404);
        echo "<h1>404 - Rota não encontrada</h1><p>{$method} {$path}</p>";
    }

    private function norm(string $path): string {
        $path = '/' . ltrim($path, '/');
        return rtrim($path,'/') === '' ? '/' : rtrim($path,'/');
    }
}
