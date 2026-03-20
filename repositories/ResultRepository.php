<?php

require_once __DIR__ . '/../services/Database.php';

function fetchResultById(int $resultId): array|false
{
    $pdo = database();
    $stmt = $pdo->prepare('SELECT id_result, name, description, image FROM result WHERE id_result = :rid');
    $stmt->execute(['rid' => $resultId]);
    return $stmt->fetch();
}