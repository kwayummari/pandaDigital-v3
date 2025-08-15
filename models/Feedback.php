<?php
require_once __DIR__ . "/../config/database.php";

class Feedback
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function getAllFeedbackForAdmin($page = 1, $perPage = 20)
    {
        try {
            $conn = $this->db->getConnection();
            $offset = ($page - 1) * $perPage;

            $stmt = $conn->prepare("
                SELECT 
                    sf.id, sf.feedback_type, sf.subject, sf.message, sf.priority, sf.status, 
                    sf.date_created, sf.date_updated, sf.attachment_url,
                    u.username as student_name, u.email as student_email,
                    c.name as course_name
                FROM student_feedback sf
                INNER JOIN users u ON sf.student_id = u.id
                LEFT JOIN course c ON sf.course_id = c.id
                ORDER BY 
                    CASE sf.priority 
                        WHEN 'urgent' THEN 1 
                        WHEN 'high' THEN 2 
                        WHEN 'medium' THEN 3 
                        WHEN 'low' THEN 4 
                    END,
                    sf.date_created DESC
                LIMIT ? OFFSET ?
            ");
            $stmt->execute([$perPage, $offset]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting all feedback for admin: " . $e->getMessage());
            return [];
        }
    }

    public function getTotalFeedback()
    {
        try {
            $conn = $this->db->getConnection();
            $stmt = $conn->prepare("SELECT COUNT(*) as total FROM student_feedback");
            $stmt->execute();
            $result = $stmt->fetch();
            return $result['total'] ?? 0;
        } catch (PDOException $e) {
            error_log("Error getting total feedback: " . $e->getMessage());
            return 0;
        }
    }

    public function getOverallFeedbackStats()
    {
        try {
            $conn = $this->db->getConnection();

            // Get total feedback count
            $stmt = $conn->prepare("SELECT COUNT(*) as total FROM student_feedback");
            $stmt->execute();
            $totalResult = $stmt->fetch();

            // Get pending feedback count
            $stmt = $conn->prepare("SELECT COUNT(*) as pending FROM student_feedback WHERE status = 'pending'");
            $stmt->execute();
            $pendingResult = $stmt->fetch();

            // Get resolved feedback count
            $stmt = $conn->prepare("SELECT COUNT(*) as resolved FROM student_feedback WHERE status = 'resolved'");
            $stmt->execute();
            $resolvedResult = $stmt->fetch();

            // Get urgent feedback count
            $stmt = $conn->prepare("SELECT COUNT(*) as urgent FROM student_feedback WHERE priority = 'urgent'");
            $stmt->execute();
            $urgentResult = $stmt->fetch();

            return [
                'total' => $totalResult['total'] ?? 0,
                'pending' => $pendingResult['pending'] ?? 0,
                'resolved' => $resolvedResult['resolved'] ?? 0,
                'urgent' => $urgentResult['urgent'] ?? 0
            ];
        } catch (PDOException $e) {
            error_log("Error getting overall feedback stats: " . $e->getMessage());
            return [
                'total' => 0,
                'pending' => 0,
                'resolved' => 0,
                'urgent' => 0
            ];
        }
    }

    public function deleteFeedback($feedbackId)
    {
        try {
            $conn = $this->db->getConnection();

            // Check if feedback exists
            $stmt = $conn->prepare("SELECT id FROM student_feedback WHERE id = ?");
            $stmt->execute([$feedbackId]);
            if (!$stmt->fetch()) {
                return false;
            }

            // Delete feedback (you might want to soft delete instead)
            $stmt = $conn->prepare("DELETE FROM student_feedback WHERE id = ?");
            return $stmt->execute([$feedbackId]);
        } catch (PDOException $e) {
            error_log("Error deleting feedback: " . $e->getMessage());
            return false;
        }
    }

    public function markFeedbackResolved($feedbackId)
    {
        try {
            $conn = $this->db->getConnection();

            // Check if feedback exists
            $stmt = $conn->prepare("SELECT id FROM student_feedback WHERE id = ?");
            $stmt->execute([$feedbackId]);
            if (!$stmt->fetch()) {
                return false;
            }

            // Mark as resolved
            $stmt = $conn->prepare("UPDATE student_feedback SET status = 'resolved', date_updated = NOW() WHERE id = ?");
            return $stmt->execute([$feedbackId]);
        } catch (PDOException $e) {
            error_log("Error marking feedback resolved: " . $e->getMessage());
            return false;
        }
    }

    public function addAdminResponse($feedbackId, $adminId, $response)
    {
        try {
            $conn = $this->db->getConnection();

            // Check if feedback exists
            $stmt = $conn->prepare("SELECT id FROM student_feedback WHERE id = ?");
            $stmt->execute([$feedbackId]);
            if (!$stmt->fetch()) {
                return false;
            }

            // Add admin response
            $stmt = $conn->prepare("
                INSERT INTO feedback_responses (feedback_id, admin_id, response_text, date_created) 
                VALUES (?, ?, ?, NOW())
            ");

            return $stmt->execute([$feedbackId, $adminId, $response]);
        } catch (PDOException $e) {
            error_log("Error adding admin response: " . $e->getMessage());
            return false;
        }
    }

    public function addFeedback($userId, $feedbackText, $description, $feedbackType, $priority = 'medium')
    {
        try {
            $conn = $this->db->getConnection();

            // Check if user exists
            $stmt = $conn->prepare("SELECT id FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            if (!$stmt->fetch()) {
                return false;
            }

            // Insert new feedback
            $stmt = $conn->prepare("
                INSERT INTO feedback (user_id, feedback_text, description, feedback_type, priority, status, date_created) 
                VALUES (?, ?, ?, ?, ?, 'pending', NOW())
            ");

            return $stmt->execute([$userId, $feedbackText, $description, $feedbackType, $priority]);
        } catch (PDOException $e) {
            error_log("Error adding feedback: " . $e->getMessage());
            return false;
        }
    }

    public function getFeedbackById($feedbackId)
    {
        try {
            $conn = $this->db->getConnection();

            $stmt = $conn->prepare("
                SELECT 
                    sf.id, sf.feedback_type, sf.subject, sf.message, sf.priority, sf.status, 
                    sf.date_created, sf.date_updated, sf.attachment_url,
                    u.username as student_name, u.email as student_email,
                    c.name as course_name
                FROM student_feedback sf
                INNER JOIN users u ON sf.student_id = u.id
                LEFT JOIN course c ON sf.course_id = c.id
                WHERE sf.id = ?
            ");
            $stmt->execute([$feedbackId]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error getting feedback by ID: " . $e->getMessage());
            return false;
        }
    }

    public function getFeedbackResponses($feedbackId)
    {
        try {
            $conn = $this->db->getConnection();

            $stmt = $conn->prepare("
                SELECT 
                    r.id, r.response_text, r.date_created,
                    u.first_name, u.last_name, u.role
                FROM feedback_responses r
                LEFT JOIN users u ON r.admin_id = u.id
                WHERE r.feedback_id = ?
                ORDER BY r.date_created ASC
            ");
            $stmt->execute([$feedbackId]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting feedback responses: " . $e->getMessage());
            return [];
        }
    }

    public function getUserFeedback($userId, $page = 1, $perPage = 10)
    {
        try {
            $conn = $this->db->getConnection();
            $offset = ($page - 1) * $perPage;

            $stmt = $conn->prepare("
                SELECT 
                    f.id, f.feedback_text, f.description, f.feedback_type, f.priority, f.status, f.date_created,
                    COUNT(DISTINCT r.id) as responses
                FROM feedback f
                LEFT JOIN feedback_responses r ON f.id = r.feedback_id
                WHERE f.user_id = ?
                GROUP BY f.id
                ORDER BY f.date_created DESC 
                LIMIT ? OFFSET ?
            ");
            $stmt->execute([$userId, $perPage, $offset]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting user feedback: " . $e->getMessage());
            return [];
        }
    }

    public function getFeedbackByType($type, $page = 1, $perPage = 10)
    {
        try {
            $conn = $this->db->getConnection();
            $offset = ($page - 1) * $perPage;

            $stmt = $conn->prepare("
                SELECT 
                    f.id, f.feedback_text, f.description, f.feedback_type, f.priority, f.status, f.date_created,
                    u.first_name, u.last_name,
                    COUNT(DISTINCT r.id) as responses
                FROM feedback f
                LEFT JOIN users u ON f.user_id = u.id
                LEFT JOIN feedback_responses r ON f.id = r.feedback_id
                WHERE f.feedback_type = ?
                GROUP BY f.id
                ORDER BY f.date_created DESC 
                LIMIT ? OFFSET ?
            ");
            $stmt->execute([$type, $perPage, $offset]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting feedback by type: " . $e->getMessage());
            return [];
        }
    }

    public function getFeedbackByPriority($priority, $page = 1, $perPage = 10)
    {
        try {
            $conn = $this->db->getConnection();
            $offset = ($page - 1) * $perPage;

            $stmt = $conn->prepare("
                SELECT 
                    f.id, f.feedback_text, f.description, f.feedback_type, f.priority, f.status, f.date_created,
                    u.first_name, u.last_name,
                    COUNT(DISTINCT r.id) as responses
                FROM feedback f
                LEFT JOIN users u ON f.user_id = u.id
                LEFT JOIN feedback_responses r ON f.id = r.feedback_id
                WHERE f.priority = ?
                GROUP BY f.id
                ORDER BY f.date_created DESC 
                LIMIT ? OFFSET ?
            ");
            $stmt->execute([$priority, $perPage, $offset]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting feedback by priority: " . $e->getMessage());
            return [];
        }
    }
}
