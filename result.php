<?php
session_start();

require __DIR__ . '/repository/resultRepository.php';
require __DIR__ . '/repository/gameRepository.php';

function redirectTo(string $path): void { header('Location: ' . $path); exit; }

function resetQuizCookie(): void
{
    setcookie('question', '', time() - 3600);
    unset($_SESSION['result_id']);
}

$resultId = (int)($_SESSION['result_id'] ?? 0);
if ($resultId <= 0) { redirectTo('quiz.php'); }

$result = fetchResultById($resultId);
if (!$result) {
    redirectTo('quiz.php?restart=1');
}

if (!empty($_SESSION['player'])) {
    $alreadySaved = (int)($_SESSION['last_game_saved'] ?? 0) === $resultId;
    if (!$alreadySaved) {
        addGameForPlayer((int)$_SESSION['player']['id'], $resultId);
        $_SESSION['last_game_saved'] = $resultId;
    }
}

resetQuizCookie();

$title = 'Résultat';
$template = 'template/result.phtml';
include 'template/layout.phtml';
