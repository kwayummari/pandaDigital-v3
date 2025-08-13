<?php
require_once __DIR__ . "/../config/database.php";

class ExpertQuestion
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    /**
     * Create a new expert question
     */
    public function createQuestion($data)
    {
        try {
            $conn = $this->db->getConnection();

            $stmt = $conn->prepare("
                INSERT INTO expertqn (user_id, expert_id, qn, phone, status, date_created)
                VALUES (?, ?, ?, ?, '0', NOW())
            ");

            $result = $stmt->execute([
                $data['user_id'],
                $data['expert_id'],
                $data['question'],
                $data['phone']
            ]);

            if ($result) {
                return $conn->lastInsertId();
            }

            return false;
        } catch (PDOException $e) {
            error_log("Error creating expert question: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get all available experts
     */
    public function getAvailableExperts()
    {
        try {
            $conn = $this->db->getConnection();

            // First, let's check if the experts table exists and has data
            $checkStmt = $conn->prepare("SHOW TABLES LIKE 'experts'");
            $checkStmt->execute();
            $tableExists = $checkStmt->fetch();

            if (!$tableExists) {
                error_log("Experts table does not exist");
                return [];
            }

            // Check total count
            $countStmt = $conn->prepare("SELECT COUNT(*) as total FROM experts");
            $countStmt->execute();
            $totalCount = $countStmt->fetch()['total'];
            error_log("Total experts in table: " . $totalCount);

            // Check status values
            $statusStmt = $conn->prepare("SELECT DISTINCT status FROM experts");
            $statusStmt->execute();
            $statuses = $statusStmt->fetchAll(PDO::FETCH_COLUMN);
            error_log("Available statuses: " . implode(', ', $statuses));

            $stmt = $conn->prepare("
                SELECT id, name, bio, photo, phone, email, status
                FROM experts 
                WHERE status IN ('free', 'premium')
                ORDER BY name ASC
            ");

            $stmt->execute();
            $experts = $stmt->fetchAll();
            error_log("Found " . count($experts) . " available experts");

            return $experts;
        } catch (PDOException $e) {
            error_log("Error fetching experts: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get expert by ID
     */
    public function getExpertById($expertId)
    {
        try {
            $conn = $this->db->getConnection();

            $stmt = $conn->prepare("
                SELECT id, name, bio, photo, phone, email
                FROM experts 
                WHERE id = ? AND status = '1'
            ");

            $stmt->execute([$expertId]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error fetching expert: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Ask a new question (simplified version for user interface)
     */
    public function askQuestion($userId, $expertId, $question, $category = '')
    {
        try {
            $conn = $this->db->getConnection();

            $stmt = $conn->prepare("
                INSERT INTO expertqn (user_id, expert_id, qn, phone, status, date_created)
                VALUES (?, ?, ?, ?, '0', NOW())
            ");

            $result = $stmt->execute([
                $userId,
                $expertId,
                $question,
                '' // phone field is required but not used in this context
            ]);

            if ($result) {
                return $conn->lastInsertId();
            }

            return false;
        } catch (PDOException $e) {
            error_log("Error asking question: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Answer an expert question
     */
    public function answerQuestion($questionId, $answer, $expertId)
    {
        try {
            $conn = $this->db->getConnection();

            $stmt = $conn->prepare("
                UPDATE expertqn 
                SET answer = ?, status = '1', date_created = NOW()
                WHERE id = ? AND expert_id = ?
            ");

            return $stmt->execute([$answer, $questionId, $expertId]);
        } catch (PDOException $e) {
            error_log("Error answering question: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get pending questions for an expert
     */
    public function getPendingQuestions($expertId, $limit = 50)
    {
        try {
            $conn = $this->db->getConnection();

            $stmt = $conn->prepare("
                SELECT eq.*, u.first_name, u.last_name, u.email, u.phone as user_phone
                FROM expertqn eq
                JOIN users u ON eq.user_id = u.id
                WHERE eq.expert_id = ? AND eq.status = '0'
                ORDER BY eq.date_created ASC
                LIMIT ?
            ");

            $stmt->execute([$expertId, $limit]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error fetching pending questions: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get answered questions for an expert
     */
    public function getAnsweredQuestions($expertId, $limit = 50)
    {
        try {
            $conn = $this->db->getConnection();

            $stmt = $conn->prepare("
                SELECT eq.*, u.first_name, u.last_name, u.email, u.phone as user_phone
                FROM expertqn eq
                JOIN users u ON eq.user_id = u.id
                WHERE eq.expert_id = ? AND eq.status = '1'
                ORDER BY eq.date_created DESC
                LIMIT ?
            ");

            $stmt->execute([$expertId, $limit]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error fetching answered questions: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get questions asked by a user
     */
    public function getUserQuestions($userId, $limit = 50)
    {
        try {
            $conn = $this->db->getConnection();

            $stmt = $conn->prepare("
                SELECT eq.*, e.name as expert_name
                FROM expertqn eq
                LEFT JOIN experts e ON eq.expert_id = e.id
                WHERE eq.user_id = ?
                ORDER BY eq.date_created DESC
                LIMIT ?
            ");

            $stmt->execute([$userId, $limit]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error fetching user questions: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get question statistics for an expert
     */
    public function getExpertStats($expertId)
    {
        try {
            $conn = $this->db->getConnection();

            $stmt = $conn->prepare("
                SELECT 
                    COUNT(*) as total_questions,
                    SUM(CASE WHEN status = '0' THEN 1 ELSE 0 END) as pending_questions,
                    SUM(CASE WHEN status = '1' THEN 1 ELSE 0 END) as answered_questions
                FROM expertqn 
                WHERE expert_id = ?
            ");

            $stmt->execute([$expertId]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error fetching expert stats: " . $e->getMessage());
            return [
                'total_questions' => 0,
                'pending_questions' => 0,
                'answered_questions' => 0
            ];
        }
    }

    /**
     * Get question by ID
     */
    public function getQuestionById($questionId)
    {
        try {
            $conn = $this->db->getConnection();

            $stmt = $conn->prepare("
                SELECT eq.*, u.first_name, u.last_name, u.email, u.phone as user_phone
                FROM expertqn eq
                JOIN users u ON eq.user_id = u.id
                WHERE eq.id = ?
            ");

            $stmt->execute([$questionId]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error fetching question: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete a question (admin only)
     */
    public function deleteQuestion($questionId)
    {
        try {
            $conn = $this->db->getConnection();
            $stmt = $conn->prepare("DELETE FROM expertqn WHERE id = ?");
            return $stmt->execute([$questionId]);
        } catch (PDOException $e) {
            error_log("Error deleting question: " . $e->getMessage());
            return false;
        }
    }

    public function getTotalQuestions()
    {
        try {
            $conn = $this->db->getConnection();
            $stmt = $conn->prepare("SELECT COUNT(*) as total FROM expertqn");
            $stmt->execute();
            $result = $stmt->fetch();
            return $result['total'] ?? 0;
        } catch (PDOException $e) {
            error_log("Error getting total questions: " . $e->getMessage());
            return 0;
        }
    }
}
