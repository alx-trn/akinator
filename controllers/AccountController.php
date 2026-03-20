<?php

require_once __DIR__ . '/../repositories/GameRepository.php';

function redirectTo(string $path): void
{
    header('Location: ' . $path);
    exit;
}

if (empty($_SESSION['player'])) {
    redirectTo('?page=login');
}

$player = $_SESSION['player'];
$games = fetchGamesByPlayerId((int)$player['id']);

$title = 'Mon compte';
$template = __DIR__ . '/../views/account/account.phtml';

include __DIR__ . '/../views/layout.phtml';