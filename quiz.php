<?php
session_start();

require __DIR__ . '/repository/questionRepository.php';
require __DIR__ . '/repository/answerRepository.php';

function redirectTo(string $path): void { header('Location: ' . $path); exit; }

function clearQuizProgress(): void
{
    setcookie('question', '', time() - 3600);
    unset($_SESSION['result_id']);
}

function saveCurrentQuestionId(int $id): void
{
    setcookie('question', (string)$id, time() + 3600);
}

if (isset($_GET['restart'])) {
    clearQuizProgress();
    redirectTo('quiz.php');
}

$error = null;
$question = null;
$answers = [];

if (!empty($_POST['answer'])) {
    $answerId = (int)$_POST['answer'];
    $answer = fetchAnswerById($answerId);

    if (!$answer) {
        $error = 'Réponse inconnue. Recommencez le quiz.';
        clearQuizProgress();
    } elseif (!empty($answer['id_result'])) {

        $_SESSION['result_id'] = (int)$answer['id_result'];
        redirectTo('result.php');
    } else {

        $nextId = (int)($answer['id_next_question'] ?? 0);
        if ($nextId > 0) {
            saveCurrentQuestionId($nextId);
            $question = fetchQuestionById($nextId);
        }
        if (!$question) {
            $error = 'Chemin du quiz incomplet. Recommencez.';
            clearQuizProgress();
        }
    }
}

if (!$question) {
    $qid = !empty($_COOKIE['question']) ? (int)$_COOKIE['question'] : 0;
    if ($qid > 0) {
        $question = fetchQuestionById($qid);
    }

    if (!$question) {
        $question = fetchFirstQuestion();
        if ($question) {
            saveCurrentQuestionId((int)$question['id_question']);
        } else {
            $error = 'Aucune question de départ trouvée.';
        }
    }
}

if ($question && !empty($question['id_question'])) {
    $answers = fetchAnswersForQuestion((int)$question['id_question']);
}

if (!$question) {
    $question = ['content' => 'Erreur'];
}

$title = 'Quiz';
$template = 'template/quiz.phtml';
include 'template/layout.phtml';
