<?php
require __DIR__ . '/database.php';

function findPlayerByEmail(string $email): array|false
{
    $pdo = database();
    $stmt = $pdo->prepare('SELECT id_user, pseudo, email, password FROM `user` WHERE email = :email LIMIT 1');
    $stmt->execute([':email' => $email]);
    return $stmt->fetch();
}

function findPlayerByPseudo(string $pseudo): array|false
{
    $pdo = database();
    $stmt = $pdo->prepare('SELECT id_user, pseudo, email, password FROM `user` WHERE pseudo = :pseudo LIMIT 1');
    $stmt->execute([':pseudo' => $pseudo]);
    return $stmt->fetch();
}

function findPlayerByEmailOrPseudo(string $email, string $pseudo): array|false
{
    $pdo = database();
    $stmt = $pdo->prepare('SELECT id_user, pseudo, email FROM `user` WHERE email = :email OR pseudo = :pseudo LIMIT 1');
    $stmt->execute([':email' => $email, ':pseudo' => $pseudo]);
    return $stmt->fetch();
}

function createPlayer(string $pseudo, string $email, string $plainPassword): bool
{
    $hash = password_hash($plainPassword, PASSWORD_DEFAULT);

    $pdo = database();
    $stmt = $pdo->prepare('INSERT INTO `user` (pseudo, email, password) VALUES (:pseudo, :email, :password)');
    return $stmt->execute([
        ':pseudo' => $pseudo,
        ':email' => $email,
        ':password' => $hash,
    ]);
}

function verifyPlayerPassword(string $email, string $plainPassword): array|false
{
    $player = findPlayerByEmail($email);
    if (!$player) {
        return false;
    }
    if (!password_verify($plainPassword, $player['password'])) {
        return false;
    }
    return $player;
}
