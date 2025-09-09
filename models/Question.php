<?php
require_once __DIR__ . "/../config/database.php";

class Question
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getAllQuestionsForAdmin()
    {
        try {
            $conn = $this->db->getConnection();

            $stmt = $conn->prepare("
                SELECT 
                    q.id, q.name as question_text, q.video_id,
                    v.name as video_title, c.name as course_name
                FROM questions q
                LEFT JOIN video v ON q.video_id = v.id
                LEFT JOIN course c ON v.course_id = c.id
                ORDER BY q.id DESC
            ");

            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting questions for admin: " . $e->getMessage());
            return [];
        }
    }

    public function getOverallQuestionStats()
    {
        try {
            $conn = $this->db->getConnection();

            // Get total questions
            $stmt = $conn->prepare("SELECT COUNT(*) as total_questions FROM questions");
            $stmt->execute();
            $totalQuestions = $stmt->fetch()['total_questions'];

            // Get total videos
            $stmt = $conn->prepare("SELECT COUNT(*) as total_videos FROM video");
            $stmt->execute();
            $totalVideos = $stmt->fetch()['total_videos'];

            // Get total courses
            $stmt = $conn->prepare("SELECT COUNT(*) as total_courses FROM course");
            $stmt->execute();
            $totalCourses = $stmt->fetch()['total_courses'];

            // Get questions this month
            $stmt = $conn->prepare("
                SELECT COUNT(*) as this_month 
                FROM questions 
                WHERE MONTH(date_created) = MONTH(CURRENT_DATE()) 
                AND YEAR(date_created) = YEAR(CURRENT_DATE())
            ");
            $stmt->execute();
            $thisMonth = $stmt->fetch()['this_month'];

            // Get questions last month
            $stmt = $conn->prepare("
                SELECT COUNT(*) as last_month 
                FROM questions 
                WHERE MONTH(date_created) = MONTH(DATE_SUB(CURRENT_DATE(), INTERVAL 1 MONTH)) 
                AND YEAR(date_created) = YEAR(DATE_SUB(CURRENT_DATE(), INTERVAL 1 MONTH))
            ");
            $stmt->execute();
            $lastMonth = $stmt->fetch()['last_month'];

            return [
                'total_questions' => $totalQuestions,
                'total_videos' => $totalVideos,
                'total_courses' => $totalCourses,
                'this_month' => $thisMonth,
                'last_month' => $lastMonth
            ];
        } catch (PDOException $e) {
            error_log("Error getting question stats: " . $e->getMessage());
            return [
                'total_questions' => 0,
                'total_videos' => 0,
                'total_courses' => 0,
                'this_month' => 0,
                'last_month' => 0
            ];
        }
    }

    public function getQuestionById($id)
    {
        try {
            $conn = $this->db->getConnection();

            $stmt = $conn->prepare("
                SELECT 
                    q.id, q.name as question_text, q.video_id,
                    v.name as video_title, c.name as course_name
                FROM questions q
                LEFT JOIN video v ON q.video_id = v.id
                LEFT JOIN course c ON v.course_id = c.id
                WHERE q.id = ?
            ");

            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error getting question by ID: " . $e->getMessage());
            return false;
        }
    }

    public function addQuestion($questionText, $videoId)
    {
        try {
            $conn = $this->db->getConnection();

            $stmt = $conn->prepare("
                INSERT INTO questions (name, video_id, date_created) 
                VALUES (?, ?, NOW())
            ");

            return $stmt->execute([$questionText, $videoId]);
        } catch (PDOException $e) {
            error_log("Error adding question: " . $e->getMessage());
            return false;
        }
    }

    public function updateQuestion($id, $questionText, $videoId)
    {
        try {
            $conn = $this->db->getConnection();

            $stmt = $conn->prepare("
                UPDATE questions 
                SET name = ?, video_id = ?
                WHERE id = ?
            ");

            return $stmt->execute([$questionText, $videoId, $id]);
        } catch (PDOException $e) {
            error_log("Error updating question: " . $e->getMessage());
            return false;
        }
    }

    public function deleteQuestion($id)
    {
        try {
            $conn = $this->db->getConnection();

            $stmt = $conn->prepare("DELETE FROM questions WHERE id = ?");

            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("Error deleting question: " . $e->getMessage());
            return false;
        }
    }

    public function getAllVideos()
    {
        try {
            $conn = $this->db->getConnection();

            $stmt = $conn->prepare("SELECT id, name FROM video ORDER BY name");
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting videos: " . $e->getMessage());
            return [];
        }
    }
}
