<?php
# Ce service gère la limitation de taux pour les tentatives de connexion
# Il utilise la session pour stocker le nombre de tentatives et le temps de verrouillage

# Les constantes LOGIN_MAX_ATTEMPTS et LOGIN_LOCKOUT_SECONDS définissent les règles de limitation

# La fonction `rateLimitCheck()` vérifie si l'adresse IP est actuellement verrouillée et retourne le temps restant de verrouillage
# La fonction `rateLimitFail()` incrémente le nombre de tentatives et verrouille l'adresse IP si le nombre maximum de tentatives est atteint   
# La fonction `rateLimitReset()` réinitialise les données de limitation pour l'adresse IP, généralement après une connexion réussie

# Note : Les fonctions de ce fichier sont utilisées par les contrôleurs, et ne sont pas destinées à être utilisées directement par les services ou les repositories.

declare(strict_types=1);

const LOGIN_MAX_ATTEMPTS      = 5;
const LOGIN_LOCKOUT_SECONDS   = 600;

function _rateLimitKey(): string {
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    return 'rl_' . md5($ip);
}

function rateLimitCheck(): int {
    $key  = _rateLimitKey();
    $data = $_SESSION[$key] ?? null;

    if (!$data) {
        return 0;
    }

    if ((int)$data['locked_until'] > 0 && time() >= (int)$data['locked_until']) {
        unset($_SESSION[$key]);
        return 0;
    }

    if ((int)$data['locked_until'] > 0) {
        return (int)$data['locked_until'] - time();
    }

    return 0;
}

function rateLimitFail(): void {
    $key  = _rateLimitKey();
    $data = $_SESSION[$key] ?? ['attempts' => 0, 'locked_until' => 0];

    $data['attempts']++;

    if ($data['attempts'] >= LOGIN_MAX_ATTEMPTS) {
        $data['locked_until'] = time() + LOGIN_LOCKOUT_SECONDS;
    }

    $_SESSION[$key] = $data;
}

function rateLimitReset(): void {
    unset($_SESSION[_rateLimitKey()]);
}