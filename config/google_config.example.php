<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/../vendor/autoload.php';

$client = new Google_Client();
$client->setClientId("SUA_CLIENT_ID_AQUI");
$client->setClientSecret("SUA_CLIENT_SECRET_AQUI");

// monta o redirect a partir do BASE_URL (que já termina com /public)
$client->setRedirectUri(rtrim(BASE_URL, '/') . '/auth/google_callback.php');

$client->addScope('email');
$client->addScope('profile');