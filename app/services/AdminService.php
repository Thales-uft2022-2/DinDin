<?php

class AdminService
{
    private $userModel;

    public function __construct(PDO $pdo)
    {
        $this->userModel = new UserModel($pdo);
    }

    public function listUsers()
    {
        return $this->userModel->getAllUsers();
    }

    public function changeRole($id, $role)
    {
        return $this->userModel->updateRole($id, $role);
    }

    public function changeStatus($id, $status)
    {
        return $this->userModel->updateStatus($id, $status);
    }
}