<?php
session_start();

require __DIR__ . '/repository/userRepository.php';

function redirectTo(string $path): void { header('Location: ' . $path); exit; }

if (!empty($_SESSION['player'])) {
    redirectTo('account.php');
}

$errors = [];

if (!empty($_POST)) {
    $email = trim((string)($_POST['email'] ?? ''));
    $password = (string)($_POST['password'] ?? '');

    if ($email === '' || $password === '') {
        $errors[] = 'Veuillez remplir tous les champs.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Email invalide.';
    } else {
        $player = verifyPlayerPassword($email, $password);
        if (!$player) {
            $errors[] = 'Identifiants incorrects.';
        } else {

            session_regenerate_id(true);
            $_SESSION['player'] = [
                'id' => (int)($player['id_user'] ?? 0),
                'pseudo' => (string)($player['pseudo'] ?? ''),
                'email' => (string)($player['email'] ?? ''),
            ];
            redirectTo('account.php');
        }
    }
}

$title = 'Connexion';
$template = 'template/login.phtml';
include 'template/layout.phtml';
