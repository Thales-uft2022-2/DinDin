<?php

class AdminController
{
    private $adminService;

    public function __construct()
    {
        $pdo = Database::getConnection();
        $this->adminService = new AdminService($pdo);

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function index()
    {
        if (!isset($_SESSION['user'])) {
            header("Location: " . BASE_URL . "/auth/login");
            exit;
        }

        if (($_SESSION['user']['role'] ?? '') !== 'admin') {
            echo "<h1>Acesso negado</h1>";
            exit;
        }

        $users = $this->adminService->listUsers();
        require __DIR__ . '/../views/admin/user.php';
    }

    public function update()
    {
        if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'admin') {
            echo "Acesso negado";
            exit;
        }

        $id     = $_POST['id'] ?? null;
        $role   = $_POST['role'] ?? null;
        $status = $_POST['status'] ?? null;

        if ($id && $role && $status) {
            $this->adminService->changeRole($id, $role);
            $this->adminService->changeStatus($id, $status);
        }

        header("Location: " . BASE_URL . "/admin");
        exit;
    }
}