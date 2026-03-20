<?php
# Ce fichier fournit des fonctions permettant de générer et de vérifier des jetons CSRF pour les soumissions de formulaires.

# La fonction `csrfToken()` génère un jeton et l'enregistre dans la session s'il n'y en a pas déjà un.
# La fonction `csrfVerify()` compare le jeton soumis à celui enregistré dans la session et le régénère après vérification afin d'empêcher toute réutilisation.

# Ces fonctions sont utilisées par les contrôleurs pour protéger les actions sensibles contre les attaques CSRF.
# Note : Les fonctions de ce fichier sont utilisées par les contrôleurs, et ne sont pas destinées à être utilisées directement par les services ou les repositories.

declare(strict_types=1);

function csrfToken(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrfVerify(): void {
    $submitted = (string)($_POST['csrf_token'] ?? '');
    $expected  = (string)($_SESSION['csrf_token'] ?? '');

    if ($submitted === '' || !hash_equals($expected, $submitted)) {
        http_response_code(403);
        die('Requête invalide (token CSRF manquant ou incorrect).');
    }

    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}