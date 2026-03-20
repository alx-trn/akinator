<?php
# Ce service fournit une fonction `database()` qui établit une connexion à la base de données MySQL en utilisant les paramètres définis dans le fichier .env
# La connexion est mise en cache pour éviter de créer plusieurs instances de PDO lors d'une même requête
# La fonction `database()` est utilisée par les fonctions d'accès aux données dans les repositories pour interagir avec la base de données

# Le fichier .env doit être présent à la racine du projet, et doit contenir les variables d'environnement suivantes :
# DB_HOST=localhost
# DB_NAME=nom_de_la_base
# DB_USER=utilisateur
# DB_PASSWORD=mot_de_passe
# Si le fichier .env est manquant ou mal configuré, le script s'arrêtera avec un message d'erreur approprié

# Le service utilise PDO pour la connexion à la base de données, avec les options suivantes :
# - ERRMODE_EXCEPTION : pour lancer des exceptions en cas d'erreur de base de données
# - DEFAULT_FETCH_MODE : pour retourner les résultats sous forme de tableaux associatifs    

declare(strict_types=1);

function database(): PDO
{
    static $pdo = null;

    if ($pdo !== null) {
        return $pdo;
    }

    $envFile = dirname(__DIR__) . DIRECTORY_SEPARATOR . '.env';

    if (!file_exists($envFile)) {
        die('Fichier .env introuvable. Copiez .env.example en .env et remplissez vos identifiants.');
    }

    $env = parse_ini_file($envFile);

    if ($env === false) {
        die('Impossible de lire le fichier .env. Vérifiez son contenu (pas de guillemets, pas de parenthèses).');
    }

    $host     = $env['DB_HOST']     ?? 'localhost';
    $dbname   = $env['DB_NAME']     ?? '';
    $user     = $env['DB_USER']     ?? '';
    $password = $env['DB_PASSWORD'] ?? '';

    try {
        $pdo = new PDO(
            "mysql:host={$host};dbname={$dbname};charset=utf8mb4",
            $user,
            $password
        );
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die('Erreur de connexion à la base de données : ' . $e->getMessage());
    }

    return $pdo;
}
