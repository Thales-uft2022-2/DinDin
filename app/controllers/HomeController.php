<?php
class HomeController {
    public function __construct() {
        // Protege a home: precisa estar logado
        if (empty($_SESSION['user'])) {
            header("Location: " . BASE_URL . "/auth/login");
            exit;
        }
    }

    public function index() {
        // Renderiza sua view existente de home
        include __DIR__ . '/../views/home.php';
    }
}