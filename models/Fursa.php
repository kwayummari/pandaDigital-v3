<?php
require_once __DIR__ . '/../config/database.php';

class Fursa
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function getLatestOpportunities($limit = 6)
    {
        try {
            $conn = $this->db->getConnection();
            $stmt = $conn->prepare("SELECT id, name, description, image, date, month, date_created FROM fursa ORDER BY date_created DESC LIMIT ?");
            $stmt->bindParam(1, $limit, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error fetching opportunities: " . $e->getMessage());
            return [];
        }
    }

    public function getOpportunityById($id)
    {
        try {
            $conn = $this->db->getConnection();
            $stmt = $conn->prepare("SELECT id, name, description, image, date, month, date_created FROM fursa WHERE id = ?");
            $stmt->bindParam(1, $id, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error fetching opportunity: " . $e->getMessage());
            return null;
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
        $imagePath = __DIR__ . '/../uploads/Fursa/' . $imageName;
        if (file_exists($imagePath)) {
            return upload_url('Fursa/' . $imageName);
        }

        // Fallback to a default image
        return asset('images/blog/post-1.jpg');
    }
}
