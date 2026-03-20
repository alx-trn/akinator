<?php

session_start();

function redirectTo(string $path): void { 
    header('Location: ' . $path); exit; 
}

$page = $_GET['page'] ?? 'home';

require __DIR__ . '/configs/settings.php';

if (!array_key_exists($page, ROUTES)) {
    http_response_code(404);
    die('Page introuvable.');
}

require ROUTES[$page];