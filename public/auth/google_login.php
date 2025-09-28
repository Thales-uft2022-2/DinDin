<?php
session_start();
require_once __DIR__ . '/../../config/google_config.php';

// Gera a URL do Google OAuth
$googleAuthUrl = $client->createAuthUrl();

// Redireciona para o Google
header("Location: " . $googleAuthUrl);
exit;