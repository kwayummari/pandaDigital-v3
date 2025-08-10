<?php
require_once __DIR__ . '/../config/database.php';

class Blog
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function getLatestBlogPosts($limit = 8, $excludeId = null)
    {
        try {
            $conn = $this->db->getConnection();

            if ($excludeId) {
                $stmt = $conn->prepare("SELECT id, name, maelezo, photo, date_created FROM blog WHERE id != ? ORDER BY date_created DESC LIMIT ?");
                $stmt->bindParam(1, $excludeId, PDO::PARAM_INT);
                $stmt->bindParam(2, $limit, PDO::PARAM_INT);
            } else {
                $stmt = $conn->prepare("SELECT id, name, maelezo, photo, date_created FROM blog ORDER BY date_created DESC LIMIT ?");
                $stmt->bindParam(1, $limit, PDO::PARAM_INT);
            }

            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error fetching blog posts: " . $e->getMessage());
            return [];
        }
    }

    public function getBlogPostById($id)
    {
        try {
            $conn = $this->db->getConnection();
            $stmt = $conn->prepare("SELECT id, name, maelezo, photo, date_created FROM blog WHERE id = ?");
            $stmt->bindParam(1, $id, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error fetching blog post: " . $e->getMessage());
            return null;
        }
    }

    public function getAllBlogPosts($page = 1, $perPage = 12)
    {
        try {
            $conn = $this->db->getConnection();
            $offset = ($page - 1) * $perPage;

            $stmt = $conn->prepare("SELECT id, name, maelezo, photo, date_created FROM blog ORDER BY date_created DESC LIMIT ? OFFSET ?");
            $stmt->bindParam(1, $perPage, PDO::PARAM_INT);
            $stmt->bindParam(2, $offset, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error fetching all blog posts: " . $e->getMessage());
            return [];
        }
    }

    public function getTotalCount()
    {
        try {
            $conn = $this->db->getConnection();
            $stmt = $conn->prepare("SELECT COUNT(*) as total FROM blog");
            $stmt->execute();
            $result = $stmt->fetch();
            return $result['total'] ?? 0;
        } catch (PDOException $e) {
            error_log("Error counting blog posts: " . $e->getMessage());
            return 0;
        }
    }

    public function formatDate($dateString)
    {
        $date = new DateTime($dateString);
        return $date->format('d M Y');
    }

    public function truncateText($text, $length = 120)
    {
        $text = strip_tags($text);
        if (strlen($text) <= $length) {
            return $text;
        }
        return substr($text, 0, $length) . '...';
    }

    public function getImageUrl($imageName)
    {
        // Check if it's a full URL or just filename
        if (filter_var($imageName, FILTER_VALIDATE_URL)) {
            return $imageName;
        }

        // Check if image exists in uploads directory
        $imagePath = __DIR__ . '/../uploads/Blog/' . $imageName;
        if (file_exists($imagePath)) {
            return upload_url('Blog/' . $imageName);
        }

        // Fallback to a default image
        return upload_url('Blog/TDA4.jpg');
    }
}
