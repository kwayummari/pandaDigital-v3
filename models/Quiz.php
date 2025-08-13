<?php
require_once __DIR__ . "/../config/database.php";

class Quiz
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    /**
     * Submit quiz answers for a video
     */
    public function submitQuizAnswers($userId, $videoId, $answers)
    {
        try {
            $conn = $this->db->getConnection();

            // Start transaction
            $conn->beginTransaction();

            // Delete existing answers for this user and video
            $stmt = $conn->prepare("
                DELETE a FROM algorithm a
                JOIN questions q ON a.qn_id = q.id
                WHERE a.user_id = ? AND q.video_id = ?
            ");
            $stmt->execute([$userId, $videoId]);

            // Insert new answers
            $stmt = $conn->prepare("
                INSERT INTO algorithm (qn_id, ans_id, user_id, date_created) 
                VALUES (?, ?, ?, NOW())
            ");

            foreach ($answers as $questionId => $answerId) {
                $stmt->execute([$questionId, $answerId, $userId]);
            }

            // Commit transaction
            $conn->commit();

            return true;
        } catch (PDOException $e) {
            // Rollback transaction
            $conn->rollBack();
            error_log("Error submitting quiz answers: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get quiz results for a video
     */
    public function getQuizResults($userId, $videoId)
    {
        try {
            $conn = $this->db->getConnection();

            $stmt = $conn->prepare("
                SELECT 
                    q.id as question_id,
                    q.name as question_text,
                    a.id as answer_id,
                    a.name as answer_text,
                    a.status as is_correct,
                    ua.ans_id as user_answer_id,
                    ua.date_created as answered_at
                FROM questions q
                LEFT JOIN answers a ON a.qn_id = q.id
                LEFT JOIN algorithm ua ON ua.qn_id = q.id AND ua.user_id = ?
                WHERE q.video_id = ?
                ORDER BY q.id ASC, a.id ASC
            ");

            $stmt->execute([$userId, $videoId]);
            $results = $stmt->fetchAll();

            // Process results
            $questions = [];
            $totalQuestions = 0;
            $correctAnswers = 0;

            foreach ($results as $result) {
                $questionId = $result['question_id'];

                if (!isset($questions[$questionId])) {
                    $questions[$questionId] = [
                        'id' => $questionId,
                        'text' => $result['question_text'],
                        'answers' => [],
                        'user_answer' => null,
                        'is_correct' => false
                    ];
                    $totalQuestions++;
                }

                $questions[$questionId]['answers'][] = [
                    'id' => $result['answer_id'],
                    'text' => $result['answer_text'],
                    'is_correct' => $result['is_correct'] === 'true'
                ];

                if ($result['user_answer_id'] == $result['answer_id']) {
                    $questions[$questionId]['user_answer'] = $result['answer_id'];
                    if ($result['is_correct'] === 'true') {
                        $questions[$questionId]['is_correct'] = true;
                        $correctAnswers++;
                    }
                }
            }

            $scorePercentage = $totalQuestions > 0 ? ($correctAnswers / $totalQuestions) * 100 : 0;

            return [
                'questions' => array_values($questions),
                'total_questions' => $totalQuestions,
                'correct_answers' => $correctAnswers,
                'score_percentage' => $scorePercentage,
                'grade' => $this->calculateGrade($scorePercentage)
            ];
        } catch (PDOException $e) {
            error_log("Error getting quiz results: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Check if user has completed quiz for a video
     */
    public function hasCompletedQuiz($userId, $videoId)
    {
        try {
            $conn = $this->db->getConnection();

            $stmt = $conn->prepare("
                SELECT COUNT(*) as completed
                FROM algorithm a 
                JOIN questions q ON q.id = a.qn_id 
                WHERE q.video_id = ? AND a.user_id = ?
            ");

            $stmt->execute([$videoId, $userId]);
            $result = $stmt->fetch();

            return $result['completed'] > 0;
        } catch (PDOException $e) {
            error_log("Error checking quiz completion: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get user's quiz statistics
     */
    public function getUserQuizStats($userId)
    {
        try {
            $conn = $this->db->getConnection();

            $stmt = $conn->prepare("
                SELECT 
                    COUNT(DISTINCT a.qn_id) as total_questions_answered,
                    COUNT(DISTINCT CASE WHEN ans.status = 'true' THEN a.qn_id END) as correct_answers,
                    COUNT(DISTINCT q.video_id) as videos_completed,
                    AVG(CASE WHEN ans.status = 'true' THEN 100 ELSE 0 END) as average_score
                FROM algorithm a
                JOIN questions q ON q.id = a.qn_id
                JOIN answers ans ON ans.id = a.ans_id
                WHERE a.user_id = ?
            ");

            $stmt->execute([$userId]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error getting user quiz stats: " . $e->getMessage());
            return [
                'total_questions_answered' => 0,
                'correct_answers' => 0,
                'videos_completed' => 0,
                'average_score' => 0
            ];
        }
    }

    /**
     * Get user's quiz statistics for a specific course
     */
    public function getUserQuizStatsForCourse($userId, $courseId)
    {
        try {
            $conn = $this->db->getConnection();

            $stmt = $conn->prepare("
                SELECT 
                    COUNT(DISTINCT q.id) as total_questions,
                    COUNT(DISTINCT a.qn_id) as questions_answered,
                    AVG(CASE WHEN ans.status = 'true' THEN 100 ELSE 0 END) as average_score
                FROM course c
                LEFT JOIN video v ON v.course_id = c.id
                LEFT JOIN questions q ON q.video_id = v.id
                LEFT JOIN algorithm a ON a.qn_id = q.id AND a.user_id = ?
                LEFT JOIN answers ans ON ans.id = a.ans_id
                WHERE c.id = ?
            ");

            $stmt->execute([$userId, $courseId]);
            $result = $stmt->fetch();

            return $result ?: [
                'total_questions' => 0,
                'questions_answered' => 0,
                'average_score' => 0
            ];
        } catch (PDOException $e) {
            error_log("Error getting user quiz stats for course: " . $e->getMessage());
            return [
                'total_questions' => 0,
                'questions_answered' => 0,
                'average_score' => 0
            ];
        }
    }

    /**
     * Get quiz statistics for a course
     */
    public function getCourseQuizStats($courseId)
    {
        try {
            $conn = $this->db->getConnection();

            $stmt = $conn->prepare("
                SELECT 
                    COUNT(DISTINCT q.id) as total_questions,
                    COUNT(DISTINCT v.id) as total_videos,
                    COUNT(DISTINCT e.user_id) as total_students,
                    AVG(CASE WHEN ans.status = 'true' THEN 100 ELSE 0 END) as average_score
                FROM course c
                LEFT JOIN enrolled e ON e.course_id = c.id
                LEFT JOIN video v ON v.course_id = c.id
                LEFT JOIN questions q ON q.video_id = v.id
                LEFT JOIN algorithm a ON a.qn_id = q.id
                LEFT JOIN answers ans ON ans.id = a.ans_id
                WHERE c.id = ?
            ");

            $stmt->execute([$courseId]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error getting course quiz stats: " . $e->getMessage());
            return [
                'total_questions' => 0,
                'total_videos' => 0,
                'total_students' => 0,
                'average_score' => 0
            ];
        }
    }

    /**
     * Record quiz attempt
     */
    public function recordQuizAttempt($userId, $videoId, $courseId, $totalQuestions, $correctAnswers, $scorePercentage)
    {
        try {
            $conn = $this->db->getConnection();

            $stmt = $conn->prepare("
                INSERT INTO quiz_attempts (user_id, video_id, course_id, total_questions, correct_answers, score_percentage, date_created)
                VALUES (?, ?, ?, ?, ?, ?, NOW())
            ");

            return $stmt->execute([
                $userId,
                $videoId,
                $courseId,
                $totalQuestions,
                $correctAnswers,
                $scorePercentage
            ]);
        } catch (PDOException $e) {
            error_log("Error recording quiz attempt: " . $e->getMessage());
            return false;
        }
    }



    /**
     * Get user's quiz attempts
     */
    public function getUserQuizAttempts($userId, $limit = 50)
    {
        try {
            $conn = $this->db->getConnection();

            $stmt = $conn->prepare("
                SELECT qa.*, v.description as video_title, c.name as course_name
                FROM quiz_attempts qa
                JOIN video v ON v.id = qa.video_id
                JOIN course c ON c.id = qa.course_id
                WHERE qa.user_id = ?
                ORDER BY qa.date_created DESC
                LIMIT ?
            ");

            $stmt->execute([$userId, $limit]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting user quiz attempts: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get questions for a specific video
     */
    public function getQuestionsByVideo($videoId)
    {
        try {
            $conn = $this->db->getConnection();

            $stmt = $conn->prepare("
                SELECT 
                    q.id,
                    q.name as question,
                    q.options,
                    q.correct_answer,
                    q.video_id
                FROM questions q
                WHERE q.video_id = ?
                ORDER BY q.id ASC
            ");

            $stmt->execute([$videoId]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting questions by video: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Calculate grade based on score percentage
     */
    private function calculateGrade($scorePercentage)
    {
        if ($scorePercentage >= 90) return 'A+';
        if ($scorePercentage >= 80) return 'A';
        if ($scorePercentage >= 70) return 'B+';
        if ($scorePercentage >= 60) return 'B';
        if ($scorePercentage >= 50) return 'C+';
        if ($scorePercentage >= 40) return 'C';
        if ($scorePercentage >= 30) return 'D';
        return 'F';
    }
}
