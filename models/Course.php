<?php
require_once __DIR__ . '/../config/database.php';

class Course
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function getAllCourses($limit = null)
    {
        try {
            $conn = $this->db->getConnection();

            $sql = "SELECT c.*, cc.name as category_name, cc.icon as category_icon, cc.color as category_color 
                    FROM course c 
                    LEFT JOIN course_categories cc ON c.category_id = cc.id 
                    WHERE c.id IS NOT NULL 
                    ORDER BY c.date_created DESC";

            if ($limit) {
                $sql .= " LIMIT ?";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(1, $limit, PDO::PARAM_INT);
            } else {
                $stmt = $conn->prepare($sql);
            }

            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error fetching courses: " . $e->getMessage());
            return [];
        }
    }

    public function getFeaturedCourses($limit = 8)
    {
        try {
            $conn = $this->db->getConnection();

            $sql = "SELECT c.*, cc.name as category_name, cc.icon as category_icon, cc.color as category_color 
                    FROM course c 
                    LEFT JOIN course_categories cc ON c.category_id = cc.id 
                    WHERE c.is_featured = 1 OR c.total_enrollments > 0
                    ORDER BY c.total_enrollments DESC, c.date_created DESC 
                    LIMIT ?";

            $stmt = $conn->prepare($sql);
            $stmt->bindParam(1, $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error fetching featured courses: " . $e->getMessage());
            return [];
        }
    }

    public function getCoursesByCategory($categoryId, $limit = null)
    {
        try {
            $conn = $this->db->getConnection();

            $sql = "SELECT c.*, cc.name as category_name, cc.icon as category_icon, cc.color as category_color 
                    FROM course c 
                    LEFT JOIN course_categories cc ON c.category_id = cc.id 
                    WHERE c.category_id = ? 
                    ORDER BY c.date_created DESC";

            if ($limit) {
                $sql .= " LIMIT ?";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(1, $categoryId, PDO::PARAM_INT);
                $stmt->bindParam(2, $limit, PDO::PARAM_INT);
            } else {
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(1, $categoryId, PDO::PARAM_INT);
            }

            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error fetching courses by category: " . $e->getMessage());
            return [];
        }
    }

    public function getCourseCategories()
    {
        try {
            $conn = $this->db->getConnection();

            $sql = "SELECT * FROM course_categories WHERE is_active = 1 ORDER BY sort_order ASC, name ASC";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error fetching course categories: " . $e->getMessage());
            return [];
        }
    }

    public function getCourseById($id)
    {
        try {
            $conn = $this->db->getConnection();

            $sql = "SELECT c.*, cc.name as category_name, cc.icon as category_icon, cc.color as category_color 
                    FROM course c 
                    LEFT JOIN course_categories cc ON c.category_id = cc.id 
                    WHERE c.id = ?";

            $stmt = $conn->prepare($sql);
            $stmt->bindParam(1, $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error fetching course by ID: " . $e->getMessage());
            return null;
        }
    }

    public function getImageUrl($photo)
    {
        if (empty($photo)) {
            return asset('images/courses/default-course.jpg');
        }

        // Check if it's a full URL
        if (filter_var($photo, FILTER_VALIDATE_URL)) {
            return $photo;
        }

        // Check if it's a relative path
        if (strpos($photo, '/') === 0) {
            return $photo;
        }

        // Return the asset path
        return asset('uploads/IntroPhoto/' . $photo);
    }

    public function formatDate($date)
    {
        if (empty($date)) {
            return 'Tarehe haijulikani';
        }

        try {
            $timestamp = strtotime($date);
            if ($timestamp === false) {
                return 'Tarehe haijulikani';
            }

            return date('d M Y', $timestamp);
        } catch (Exception $e) {
            return 'Tarehe haijulikani';
        }
    }

    public function truncateText($text, $length = 150)
    {
        if (empty($text)) {
            return 'Maelezo ya kozi hayajapatikana.';
        }

        if (strlen($text) <= $length) {
            return $text;
        }

        return substr($text, 0, $length) . ' [...]';
    }

    public function getDifficultyLevel($level)
    {
        $levels = [
            'beginner' => 'Mwanzo',
            'intermediate' => 'Kati',
            'advanced' => 'Juu'
        ];

        return $levels[$level] ?? 'Mwanzo';
    }

    public function formatDuration($duration)
    {
        if (empty($duration)) {
            return 'Muda haujulikani';
        }

        return $duration;
    }

    public function formatPrice($price)
    {
        if (empty($price) || $price == 0) {
            return 'Bure';
        }

        return 'TSh ' . number_format($price);
    }
}
