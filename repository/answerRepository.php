<?php
require __DIR__ . '/database.php';

function fetchAnswersForQuestion(int $questionId): array
{
    $pdo = database();

    $sql = 'SELECT
                a.id_answer,
                a.content,
                a.id_next_question,
                a.id_result
            FROM answer AS a
            WHERE a.id_question = :question_id
            ORDER BY a.id_answer ASC';

    $stmt = $pdo->prepare($sql);
    $stmt->execute([':question_id' => $questionId]);
    return $stmt->fetchAll();
}

function fetchAnswerById(int $answerId): array|false
{
    $pdo = database();

    $stmt = $pdo->prepare(
        'SELECT
            id_answer,
            id_question,
            content,
            id_next_question,
            id_result
        FROM answer
        WHERE id_answer = :answer_id
        LIMIT 1'
    );

    $stmt->execute([':answer_id' => $answerId]);
    return $stmt->fetch();
}
