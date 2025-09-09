<?php
require_once __DIR__ . '/../config/database.php';

class Soko
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Get all approved businesses with their photos
     */
    public function getAllBusinesses()
    {
        try {
            $conn = $this->db->getConnection();

            $query = "SELECT b.*, bp.photo 
                      FROM business b 
                      LEFT JOIN business_photo bp ON b.user_id = bp.user_id 
                      WHERE b.status = 'approved' 
                      GROUP BY b.id 
                      ORDER BY b.date_created DESC";

            $stmt = $conn->prepare($query);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching businesses: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get business by ID with photo
     */
    public function getBusinessById($id)
    {
        try {
            $conn = $this->db->getConnection();

            $query = "SELECT b.*, bp.photo 
                      FROM business b 
                      LEFT JOIN business_photo bp ON b.user_id = bp.user_id 
                      WHERE b.id = :id AND b.status = 'approved'
                      GROUP BY b.id";

            $stmt = $conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching business by ID: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get latest businesses (for featured section)
     */
    public function getLatestBusinesses($limit = 6, $excludeId = null)
    {
        try {
            $conn = $this->db->getConnection();

            $query = "SELECT b.*, bp.photo 
                      FROM business b 
                      LEFT JOIN business_photo bp ON b.user_id = bp.user_id 
                      WHERE b.status = 'approved'";

            if ($excludeId) {
                $query .= " AND b.id != :exclude_id";
            }

            $query .= " GROUP BY b.id ORDER BY b.date_created DESC LIMIT :limit";

            $stmt = $conn->prepare($query);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);

            if ($excludeId) {
                $stmt->bindParam(':exclude_id', $excludeId, PDO::PARAM_INT);
            }

            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching latest businesses: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get business count by status
     */
    public function getBusinessCount($status = 'approved')
    {
        try {
            $conn = $this->db->getConnection();

            $query = "SELECT COUNT(*) as count FROM business WHERE status = :status";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':status', $status, PDO::PARAM_STR);
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'] ?? 0;
        } catch (PDOException $e) {
            error_log("Error fetching business count: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get image URL for business
     */
    public function getImageUrl($image)
    {
        if (empty($image)) {
            return asset('images/business/default-business.jpg');
        }
        return upload_url('Business/' . $image);
    }

    /**
     * Format date for display
     */
    public function formatDate($date)
    {
        if (empty($date)) {
            return 'Tarehe haijulikani';
        }

        $timestamp = strtotime($date);
        if ($timestamp === false) {
            return 'Tarehe haijulikani';
        }

        return date('d/m/Y', $timestamp);
    }

    /**
     * Truncate text for display
     */
    public function truncateText($text, $length = 150)
    {
        if (empty($text)) {
            return '';
        }

        if (strlen($text) <= $length) {
            return $text;
        }

        return substr($text, 0, $length) . ' [...]';
    }
}
