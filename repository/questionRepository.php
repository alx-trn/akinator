<?php
require __DIR__ . '/database.php';

function fetchQuestionById(int $id): array|false
{
    $pdo = database();

    $stmt = $pdo->prepare('SELECT q.id_question, q.content FROM question AS q WHERE q.id_question = :qid LIMIT 1');
    $stmt->execute([':qid' => $id]);
    return $stmt->fetch();
}

function fetchFirstQuestion(): array|false
{
    $pdo = database();

    // On récupère LA question de départ
    $stmt = $pdo->query('SELECT id_question, content FROM question WHERE first_question = 1 ORDER BY id_question ASC LIMIT 1');
    return $stmt->fetch();
}
