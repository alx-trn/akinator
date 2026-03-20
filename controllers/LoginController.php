<?php

require_once __DIR__ . '/../repositories/UserRepository.php';
require_once __DIR__ . '/../services/Csrf.php';
require_once __DIR__ . '/../services/RateLimit.php';

// function redirectTo(string $path): void { 
//     header('Location: ' . $path); exit; 
// }

if (!empty($_SESSION['player'])) {
    redirectTo('?page=account');
}

$errors = [];

if (!empty($_POST)) {
    csrfVerify();

    $waitSeconds = rateLimitCheck();
    if ($waitSeconds > 0) {
        $waitMinutes = (int)ceil($waitSeconds / 60);
        $errors[] = "Trop de tentatives. Veuillez patienter {$waitMinutes} minute(s) avant de réessayer.";
    } else {
        $email    = trim((string)($_POST['email'] ?? ''));
        $password = (string)($_POST['password'] ?? '');

        if ($email === '' || $password === '') {
            $errors[] = 'Veuillez remplir tous les champs.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email invalide.';
        } else {
            $player = verifyPlayerPassword($email, $password);
            if (!$player) {
                rateLimitFail();
                $data      = $_SESSION[_rateLimitKey()] ?? ['attempts' => LOGIN_MAX_ATTEMPTS];
                $remaining = max(0, LOGIN_MAX_ATTEMPTS - (int)$data['attempts']);
                $errors[]  = $remaining > 0
                    ? "Identifiants incorrects. Il vous reste {$remaining} tentative(s)."
                    : 'Trop de tentatives. Accès bloqué pendant 10 minutes.';
            } else {
                rateLimitReset();
                session_regenerate_id(true);
                $_SESSION['player'] = [
                    'id'     => (int)($player['id_user'] ?? 0),
                    'pseudo' => (string)($player['pseudo'] ?? ''),
                    'email'  => (string)($player['email'] ?? ''),
                ];
                redirectTo('?page=account');
            }
        }
    }
}

$title    = 'Connexion';
$template = __DIR__ . '/../views/login/login.phtml';

include __DIR__ . '/../views/layout.phtml';