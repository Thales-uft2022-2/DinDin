<?php

class AdminController
{
    private $adminService;

    public function __construct()
    {
        // Obtém conexão igual os outros controllers fazem
        $pdo = Database::getConnection();
        $this->adminService = new AdminService($pdo);
    }

    public function index()
    {
        // Verificar permissão
        if (!isset($_SESSION['user'])) {
            header('Location: /auth/login');
            exit;
        }

        if ($_SESSION['user']['role'] !== 'admin') {
            echo "<h1>Acesso negado</h1>";
            exit;
        }

        $users = $this->adminService->listUsers();
        require __DIR__ . '/../views/admin/users.php';
    }

    public function update()
    {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            echo "Acesso negado";
            exit;
        }

        $id = $_POST['id'];
        $role = $_POST['role'];
        $status = $_POST['status'];

        $this->adminService->changeRole($id, $role);
        $this->adminService->changeStatus($id, $status);

        header("Location: /admin");
        exit;
    }
}