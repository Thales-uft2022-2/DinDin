<?php

class AdminService
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function listUsers()
    {
        $sql = "SELECT id, name, email, role, status FROM users ORDER BY id DESC";
        return $this->pdo->query($sql)->fetchAll();
    }

    public function changeRole($id, $role)
    {
        $stmt = $this->pdo->prepare("UPDATE users SET role = :r WHERE id = :id");
        return $stmt->execute([':r' => $role, ':id' => $id]);
    }

    public function changeStatus($id, $status)
    {
        $stmt = $this->pdo->prepare("UPDATE users SET status = :s WHERE id = :id");
        return $stmt->execute([':s' => $status, ':id' => $id]);
    }
}