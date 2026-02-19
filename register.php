<?php
session_start();

require __DIR__ . '/repository/userRepository.php';

function redirectTo(string $path): void { header('Location: ' . $path); exit; }

if (!empty($_SESSION['player'])) {
    redirectTo('account.php');
}

$errors = [];
$success = null;

if (!empty($_POST)) {
    
    $pseudo = trim((string)($_POST['pseudo'] ?? ''));
    $email = trim((string)($_POST['email'] ?? ''));
    $password = (string)($_POST['password'] ?? '');

    if ($pseudo === '' || $email === '' || $password === '') {
        $errors[] = 'Tous les champs sont obligatoires.';
    }
    if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Email invalide.';
    }
    if ($password !== '' && strlen($password) < 6) {
        $errors[] = 'Mot de passe trop court (min. 6 caractères).';
    }

    if (!$errors) {
        $existing = findPlayerByEmailOrPseudo($email, $pseudo);
        if ($existing) {
            if (strcasecmp($existing['email'], $email) === 0) {
                $errors[] = 'Cet email est déjà utilisé.';
            }
            if (strcasecmp($existing['pseudo'], $pseudo) === 0) {
                $errors[] = 'Ce pseudo est déjà pris.';
            }
        }
    }

    if (!$errors) {
        try {
            if (createPlayer($pseudo, $email, $password)) {
                $success = 'Compte créé ! Vous pouvez maintenant vous connecter.';
            } else {
                $errors[] = 'Impossible de créer le compte.';
            }
        } catch (Throwable $e) {

            $errors[] = 'Erreur lors de la création du compte (base de données).';
        }
    }
}

$title = 'Inscription';
$template = 'template/register.phtml';
include 'template/layout.phtml';
