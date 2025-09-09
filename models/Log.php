<?php
require_once __DIR__ . "/../config/database.php";

class Log
{
    private $db;

    public function __construct($pdo = null)
    {
        // The Log model uses Database class internally, so we don't need to pass PDO
        $this->db = Database::getInstance();
    }

    /**
     * Log user activity
     */
    public function logActivity($activityName, $userId = null)
    {
        try {
            $conn = $this->db->getConnection();

            // Check if the enhanced fields exist
            $stmt = $conn->prepare("
                SELECT COUNT(*) as field_count 
                FROM INFORMATION_SCHEMA.COLUMNS 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = 'logs' 
                AND COLUMN_NAME = 'user_id'
            ");
            $stmt->execute();
            $result = $stmt->fetch();
            $hasEnhancedFields = $result['field_count'] > 0;

            if ($hasEnhancedFields) {
                // Use enhanced logging with user_id, ip_address, and user_agent
                $stmt = $conn->prepare("
                    INSERT INTO logs (activity_name, user_id, date_created, ip_address, user_agent)
                    VALUES (?, ?, NOW(), ?, ?)
                ");

                $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
                $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';

                $result = $stmt->execute([
                    $activityName,
                    $userId,
                    $ipAddress,
                    $userAgent
                ]);
            } else {
                // Fallback to basic logging (existing structure)
                $stmt = $conn->prepare("
                    INSERT INTO logs (activity_name, date_created)
                    VALUES (?, NOW())
                ");

                $result = $stmt->execute([
                    $activityName
                ]);
            }

            return $result;
        } catch (PDOException $e) {
            error_log("Error logging activity: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get user activity logs
     */
    public function getUserLogs($userId, $limit = 50)
    {
        try {
            $conn = $this->db->getConnection();

            $stmt = $conn->prepare("
                SELECT activity_name, date_created, ip_address
                FROM logs 
                WHERE user_id = ?
                ORDER BY date_created DESC
                LIMIT ?
            ");

            $stmt->execute([$userId, $limit]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error fetching user logs: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get system activity logs
     */
    public function getSystemLogs($limit = 100)
    {
        try {
            $conn = $this->db->getConnection();

            $stmt = $conn->prepare("
                SELECT l.activity_name, l.date_created, l.ip_address, u.email, u.first_name, u.last_name
                FROM logs l
                LEFT JOIN users u ON l.user_id = u.id
                ORDER BY l.date_created DESC
                LIMIT ?
            ");

            $stmt->execute([$limit]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error fetching system logs: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get recent login attempts
     */
    public function getRecentLoginAttempts($email, $hours = 24)
    {
        try {
            $conn = $this->db->getConnection();

            $stmt = $conn->prepare("
                SELECT activity_name, date_created, ip_address
                FROM logs 
                WHERE activity_name LIKE '%ingia%' 
                AND user_id = (SELECT id FROM users WHERE email = ?)
                AND date_created >= DATE_SUB(NOW(), INTERVAL ? HOUR)
                ORDER BY date_created DESC
            ");

            $stmt->execute([$email, $hours]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error fetching login attempts: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Clean old logs (older than specified days)
     */
    public function cleanOldLogs($days = 90)
    {
        try {
            $conn = $this->db->getConnection();

            $stmt = $conn->prepare("
                DELETE FROM logs 
                WHERE date_created < DATE_SUB(NOW(), INTERVAL ? DAY)
            ");

            $result = $stmt->execute([$days]);
            return $result;
        } catch (PDOException $e) {
            error_log("Error cleaning old logs: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get total count of logs
     */
    public function getTotalCount()
    {
        try {
            $conn = $this->db->getConnection();
            $stmt = $conn->prepare("SELECT COUNT(*) as total FROM logs");
            $stmt->execute();
            $result = $stmt->fetch();
            return $result['total'] ?? 0;
        } catch (PDOException $e) {
            error_log("Error counting logs: " . $e->getMessage());
            return 0;
        }
    }
}
