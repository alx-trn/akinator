<?php
require __DIR__ . '/database.php';

function addGameForPlayer(int $playerId, int $resultId): bool
{
    $pdo = database();

    $stmt = $pdo->prepare(
        'INSERT INTO game (`date`, id_user, id_result)
         VALUES (CURDATE(), :user_id, :result_id)'
    );

    return $stmt->execute([
        ':user_id' => $playerId,
        ':result_id' => $resultId,
    ]);
}

function fetchGamesByPlayerId(int $playerId): array
{
    $pdo = database();

    $sql = 'SELECT
                g.`date` AS played_at,
                r.name AS result_name
            FROM game AS g
            JOIN result AS r ON r.id_result = g.id_result
            WHERE g.id_user = :user_id
            ORDER BY g.`date` DESC';

    $stmt = $pdo->prepare($sql);
    $stmt->execute([':user_id' => $playerId]);
    return $stmt->fetchAll();
}
