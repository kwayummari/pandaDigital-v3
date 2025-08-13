<?php
require_once __DIR__ . "/../middleware/AuthMiddleware.php";
require_once __DIR__ . "/../models/Quiz.php";
require_once __DIR__ . "/../models/Course.php";

$auth = new AuthMiddleware();
$auth->requireRole('user');

$currentUser = $auth->getCurrentUser();
$quizModel = new Quiz();
$courseModel = new Course();

// Set content type to JSON
header('Content-Type: application/json');

try {
    // Get POST data
    $videoId = $_POST['videoId'] ?? null;
    $courseId = $_POST['courseId'] ?? null;

    if (!$videoId || !$courseId) {
        throw new Exception('Missing required parameters');
    }

    // Validate that user is enrolled in this course
    $isEnrolled = $courseModel->isUserEnrolled($currentUser['id'], $courseId);
    if (!$isEnrolled) {
        throw new Exception('User not enrolled in this course');
    }

    // Get questions for this video
    $questions = $quizModel->getQuestionsByVideo($videoId);
    if (empty($questions)) {
        throw new Exception('No questions found for this video');
    }

    // Collect answers from form
    $answers = [];
    foreach ($questions as $question) {
        $answerKey = 'question_' . $question['id'];
        if (isset($_POST[$answerKey])) {
            $answers[$question['id']] = $_POST[$answerKey];
        }
    }

    // Check if all questions were answered
    if (count($answers) !== count($questions)) {
        throw new Exception('Please answer all questions');
    }

    // Submit quiz answers
    $result = $quizModel->submitQuizAnswers($currentUser['id'], $videoId, $answers);

    if ($result) {
        // Track course view (this counts as completing the lesson)
        $courseModel->trackCourseView($currentUser['id'], $courseId);

        echo json_encode([
            'success' => true,
            'message' => 'Quiz submitted successfully',
            'questionsAnswered' => count($answers),
            'totalQuestions' => count($questions)
        ]);
    } else {
        throw new Exception('Failed to submit quiz answers');
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}


