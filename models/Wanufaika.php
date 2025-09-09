<?php
require_once __DIR__ . '/../config/database.php';

class Wanufaika
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getLatestWanufaika($limit = 6, $excludeId = null)
    {
        try {
            $conn = $this->db->getConnection();

            if ($excludeId) {
                $stmt = $conn->prepare("SELECT id, name, title, description, photo, date_created FROM wanufaika WHERE id != ? ORDER BY date_created DESC LIMIT ?");
                $stmt->bindParam(1, $excludeId, PDO::PARAM_INT);
                $stmt->bindParam(2, $limit, PDO::PARAM_INT);
            } else {
                $stmt = $conn->prepare("SELECT id, name, title, description, photo, date_created FROM wanufaika ORDER BY date_created DESC LIMIT ?");
                $stmt->bindParam(1, $limit, PDO::PARAM_INT);
            }

            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error fetching wanufaika: " . $e->getMessage());
            return [];
        }
    }

    public function getWanufaikaById($id)
    {
        try {
            $conn = $this->db->getConnection();
            $stmt = $conn->prepare("SELECT id, name, title, description, photo, date_created FROM wanufaika WHERE id = ?");
            $stmt->bindParam(1, $id, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error fetching wanufaika: " . $e->getMessage());
            return null;
        }
    }

    public function getAllWanufaika($page = 1, $perPage = 12)
    {
        try {
            $conn = $this->db->getConnection();
            $offset = ($page - 1) * $perPage;

            $stmt = $conn->prepare("SELECT id, name, title, description, photo, date_created FROM wanufaika ORDER BY date_created DESC LIMIT ? OFFSET ?");
            $stmt->bindParam(1, $perPage, PDO::PARAM_INT);
            $stmt->bindParam(2, $offset, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error fetching all wanufaika: " . $e->getMessage());
            return [];
        }
    }

    public function getTotalCount()
    {
        try {
            $conn = $this->db->getConnection();
            $stmt = $conn->prepare("SELECT COUNT(*) as total FROM wanufaika");
            $stmt->execute();
            $result = $stmt->fetch();
            return $result['total'] ?? 0;
        } catch (PDOException $e) {
            error_log("Error counting wanufaika: " . $e->getMessage());
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
        error_log("Debug: getImageUrl called with: " . $imageName);

        // Check if it's a full URL or just filename
        if (filter_var($imageName, FILTER_VALIDATE_URL)) {
            error_log("Debug: Image is a full URL, returning as is");
            return $imageName;
        }

        // Check if image exists in uploads directory
        $imagePath = __DIR__ . '/../uploads/Wanufaika/' . $imageName;
        error_log("Debug: Checking image path: " . $imagePath);
        error_log("Debug: File exists: " . (file_exists($imagePath) ? 'YES' : 'NO'));

        if (file_exists($imagePath)) {
            $uploadUrl = upload_url('Wanufaika/' . $imageName);
            error_log("Debug: File exists, returning upload URL: " . $uploadUrl);
            return $uploadUrl;
        }

        // Fallback to a default image
        error_log("Debug: File not found, using fallback image");
        return upload_url('Wanufaika/1.jpeg');
    }
}
