<?php
session_start();

require_once __DIR__ . '/../../config/config.php';       // Para BASE_URL etc.
require_once __DIR__ . '/../../config/google_config.php'; // Define $client (Google_Client)
require_once __DIR__ . '/../../app/models/UserModel.php';

try {
    if (!isset($_GET['code'])) {
        $_SESSION['erro'] = "Não foi possível autenticar com o Google (código ausente).";
        header("Location: " . BASE_URL . "/auth/login");
        exit;
    }

    // Troca o "code" por access token
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    if (isset($token['error'])) {
        $_SESSION['erro'] = "Erro ao autenticar com o Google: " . htmlspecialchars($token['error_description'] ?? $token['error']);
        header("Location: " . BASE_URL . "/auth/login");
        exit;
    }

    $client->setAccessToken($token);

    // Busca dados do usuário no Google
    $oauth = new Google_Service_Oauth2($client);
    $googleUser = $oauth->userinfo->get();

    $email       = $googleUser->email ?? null;
    $nome        = $googleUser->name  ?? '';
    $googleId    = $googleUser->id    ?? null;
    $fotoPerfil  = $googleUser->picture ?? null;

    if (!$email) {
        $_SESSION['erro'] = "Não foi possível obter o e-mail da sua conta Google.";
        header("Location: " . BASE_URL . "/auth/login");
        exit;
    }

    // Cria ou autentica o usuário
    $userModel = new UserModel();
    $user = $userModel->findByEmail($email);

    if (!$user) {
        // Primeiro login com Google → cria a conta
        $userModel->createFromGoogle($email, $nome, $googleId, $fotoPerfil);
        $user = $userModel->findByEmail($email);
    } else {
        // Já existe → atualiza dados do Google se estiverem vazios
        $userModel->updateGoogleData((int)$user['id'], $googleId, $fotoPerfil);
        $user = $userModel->findByEmail($email); // recarrega
    }

    // Autentica sessão
    $_SESSION['user'] = [
        'id'          => $user['id'],
        'nome'        => $user['nome'],
        'email'       => $user['email'],
        'foto_perfil' => $user['foto_perfil'] ?? null,
        'google_id'   => $user['google_id'] ?? null,
    ];

    // Redireciona para a home (ajuste a rota conforme seu router)
    header("Location: " . BASE_URL . "/home");
    exit;

} catch (Throwable $e) {
    // Falha inesperada
    $_SESSION['erro'] = "Falha no login com Google. Detalhe: " . $e->getMessage();
    header("Location: " . BASE_URL . "/auth/login");
    exit;
}