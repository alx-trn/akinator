<?php
session_start();

require __DIR__ . '/repository/gameRepository.php';

function redirectTo(string $path): void
{
    header('Location: ' . $path);
    exit;
}

if (empty($_SESSION['player'])) {
    redirectTo('login.php');
}

$player = $_SESSION['player'];
$games = fetchGamesByPlayerId((int)$player['id']);

$title = 'Mon compte';
$template = 'template/account.phtml';
include 'template/layout.phtml';