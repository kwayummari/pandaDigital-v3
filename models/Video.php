<?php
require_once __DIR__ . "/../config/database.php";

class Video
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getAllVideosForAdmin()
    {
        try {
            $conn = $this->db->getConnection();

            $stmt = $conn->prepare("
                SELECT 
                    v.id, v.name, v.description, v.course_id,
                    c.name as course_name
                FROM video v
                LEFT JOIN course c ON v.course_id = c.id
                ORDER BY v.id DESC
            ");

            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting videos for admin: " . $e->getMessage());
            return [];
        }
    }

    public function getOverallVideoStats()
    {
        try {
            $conn = $this->db->getConnection();

            // Get total videos
            $stmt = $conn->prepare("SELECT COUNT(*) as total_videos FROM video");
            $stmt->execute();
            $totalVideos = $stmt->fetch()['total_videos'];

            // Get total courses
            $stmt = $conn->prepare("SELECT COUNT(*) as total_courses FROM course");
            $stmt->execute();
            $totalCourses = $stmt->fetch()['total_courses'];

            // Get videos this month
            $stmt = $conn->prepare("
                SELECT COUNT(*) as this_month 
                FROM video 
                WHERE MONTH(date_created) = MONTH(CURRENT_DATE()) 
                AND YEAR(date_created) = YEAR(CURRENT_DATE())
            ");
            $stmt->execute();
            $thisMonth = $stmt->fetch()['this_month'];

            // Get videos last month
            $stmt = $conn->prepare("
                SELECT COUNT(*) as last_month 
                FROM video 
                WHERE MONTH(date_created) = MONTH(DATE_SUB(CURRENT_DATE(), INTERVAL 1 MONTH)) 
                AND YEAR(date_created) = YEAR(DATE_SUB(CURRENT_DATE(), INTERVAL 1 MONTH))
            ");
            $stmt->execute();
            $lastMonth = $stmt->fetch()['last_month'];

            return [
                'total_videos' => $totalVideos,
                'total_courses' => $totalCourses,
                'this_month' => $thisMonth,
                'last_month' => $lastMonth
            ];
        } catch (PDOException $e) {
            error_log("Error getting video stats: " . $e->getMessage());
            return [
                'total_videos' => 0,
                'total_courses' => 0,
                'this_month' => 0,
                'last_month' => 0
            ];
        }
    }

    public function getVideoById($id)
    {
        try {
            $conn = $this->db->getConnection();

            $stmt = $conn->prepare("
                SELECT 
                    v.id, v.name, v.description, v.course_id,
                    c.name as course_name
                FROM video v
                LEFT JOIN course c ON v.course_id = c.id
                WHERE v.id = ?
            ");

            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error getting video by ID: " . $e->getMessage());
            return false;
        }
    }

    public function addVideo($name, $description, $courseId)
    {
        try {
            $conn = $this->db->getConnection();

            $stmt = $conn->prepare("
                INSERT INTO video (name, description, course_id, date_created) 
                VALUES (?, ?, ?, NOW())
            ");

            return $stmt->execute([$name, $description, $courseId]);
        } catch (PDOException $e) {
            error_log("Error adding video: " . $e->getMessage());
            return false;
        }
    }

    public function updateVideo($id, $name, $description, $courseId)
    {
        try {
            $conn = $this->db->getConnection();

            $stmt = $conn->prepare("
                UPDATE video 
                SET name = ?, description = ?, course_id = ?
                WHERE id = ?
            ");

            return $stmt->execute([$name, $description, $courseId, $id]);
        } catch (PDOException $e) {
            error_log("Error updating video: " . $e->getMessage());
            return false;
        }
    }

    public function deleteVideo($id)
    {
        try {
            $conn = $this->db->getConnection();

            $stmt = $conn->prepare("DELETE FROM video WHERE id = ?");

            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("Error deleting video: " . $e->getMessage());
            return false;
        }
    }

    public function getAllCourses()
    {
        try {
            $conn = $this->db->getConnection();

            $stmt = $conn->prepare("SELECT id, name FROM course ORDER BY name");
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting courses: " . $e->getMessage());
            return [];
        }
    }
}
