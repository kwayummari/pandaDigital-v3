<?php
require_once __DIR__ . '/../config/database.php';

class Blog
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function getLatestPosts($limit = 6)
    {
        try {
            $conn = $this->db->getConnection();
            $stmt = $conn->prepare("SELECT id, name, maelezo, photo, date_created FROM blog ORDER BY date_created DESC LIMIT ?");
            $stmt->bindParam(1, $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error fetching blog posts: " . $e->getMessage());
            return [];
        }
    }

    public function getPostById($id)
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

    public function formatDate($dateString)
    {
        $date = new DateTime($dateString);
        return $date->format('d M Y');
    }

    public function truncateText($text, $length = 150)
    {
        $text = strip_tags($text);
        if (strlen($text) <= $length) {
            return $text;
        }
        return substr($text, 0, $length) . '...';
    }
} 