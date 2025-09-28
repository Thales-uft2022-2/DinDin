<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../core/Database.php';

class UserModel
{
    /** @var PDO */
    private $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection(); // ← aqui o ajuste
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM usuarios WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user ?: null;
    }

    public function create(string $nome, string $email, string $senha): bool
    {
        $hash = password_hash($senha, PASSWORD_BCRYPT);
        $stmt = $this->pdo->prepare("
            INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)
        ");
        return $stmt->execute([$nome, $email, $hash]);
    }

    public function createFromGoogle(string $email, string $nome, ?string $googleId, ?string $fotoPerfil): bool
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO usuarios (nome, email, senha, google_id, foto_perfil)
            VALUES (?, ?, '', ?, ?)
        ");
        return $stmt->execute([$nome, $email, $googleId, $fotoPerfil]);
    }

    public function updateGoogleData(int $id, ?string $googleId, ?string $fotoPerfil): bool
    {
        $stmt = $this->pdo->prepare("
            UPDATE usuarios
               SET google_id = COALESCE(?, google_id),
                   foto_perfil = COALESCE(?, foto_perfil)
             WHERE id = ?
        ");
        return $stmt->execute([$googleId, $fotoPerfil, $id]);
    }
}