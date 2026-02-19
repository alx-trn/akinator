<?php
declare(strict_types=1);

if (!function_exists('database')) {
    function database(): PDO
    {
        $host = 'db.3wa.io';
        $dbname = 'alextiron_akinator';
        $user = 'alextiron';
        $pass = '3b543eb05d8625f110dfa424ca3179bb';
        $charset = 'utf8mb4';

        $dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";

        return new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }
}
