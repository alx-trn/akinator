<?php

require_once __DIR__ . '/../repositories/ResultRepository.php';
require_once __DIR__ . '/../repositories/GameRepository.php';

// function redirectTo(string $path): void { 
//     header('Location: ' . $path); exit; 
// }

function resetQuizCookie(): void
{
    setcookie('question', '', time() - 3600);
    unset($_SESSION['result_id']);
}

$resultId = (int)($_SESSION['result_id'] ?? 0);
if ($resultId <= 0) { redirectTo('?page=quiz'); }

$result = fetchResultById($resultId);
if (!$result) {
    redirectTo('?page=quiz&restart=1');
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
$template = __DIR__ . '/../views/result/result.phtml';

include __DIR__ . '/../views/layout.phtml';