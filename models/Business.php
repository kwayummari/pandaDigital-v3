<?php

class Business
{
    private $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    /**
     * Get all approved businesses
     */
    public function getAllApprovedBusinesses($limit = null)
    {
        try {
            $sql = "SELECT b.*, u.name as owner_name, u.phone as owner_phone, u.email as owner_email 
                    FROM business b 
                    LEFT JOIN users u ON b.user_id = u.id 
                    WHERE b.status = 'approved' 
                    ORDER BY b.date_created DESC";

            if ($limit) {
                $sql .= " LIMIT " . (int)$limit;
            }

            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting approved businesses: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get business by ID
     */
    public function getBusinessById($id)
    {
        try {
            $sql = "SELECT b.*, u.name as owner_name, u.phone as owner_phone, u.email as owner_email 
                    FROM business b 
                    LEFT JOIN users u ON b.user_id = u.id 
                    WHERE b.id = :id AND b.status = 'approved'";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error getting business by ID: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get business photos by user ID
     */
    public function getBusinessPhotos($userId)
    {
        try {
            $sql = "SELECT * FROM business_photo WHERE user_id = ? ORDER BY id ASC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting business photos: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get similar businesses by location
     */
    public function getSimilarBusinesses($excludeId, $location, $limit = 4)
    {
        try {
            $sql = "SELECT b.*, u.name as owner_name, u.phone as owner_phone, u.email as owner_email 
                    FROM business b 
                    LEFT JOIN users u ON b.user_id = u.id 
                    WHERE b.status = 'approved' 
                    AND b.id != ? 
                    AND b.location LIKE ? 
                    ORDER BY b.date_created DESC 
                    LIMIT ?";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([$excludeId, '%' . $location . '%', $limit]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting similar businesses: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get businesses by category (location-based)
     */
    public function getBusinessesByLocation($location, $limit = 6)
    {
        try {
            $sql = "SELECT b.*, u.name as owner_name 
                    FROM business b 
                    LEFT JOIN users u ON b.user_id = u.id 
                    WHERE b.status = 'approved' AND b.location LIKE :location 
                    ORDER BY b.date_created DESC 
                    LIMIT :limit";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':location', '%' . $location . '%', PDO::PARAM_STR);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting businesses by location: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Search businesses
     */
    public function searchBusinesses($searchTerm, $limit = 12)
    {
        try {
            $sql = "SELECT b.*, u.name as owner_name 
                    FROM business b 
                    LEFT JOIN users u ON b.user_id = u.id 
                    WHERE b.status = 'approved' 
                    AND (b.name LIKE :search OR b.maelezo LIKE :search OR b.location LIKE :search)
                    ORDER BY b.date_created DESC 
                    LIMIT :limit";

            $stmt = $this->db->prepare($sql);
            $searchPattern = '%' . $searchTerm . '%';
            $stmt->bindParam(':search', $searchPattern, PDO::PARAM_STR);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error searching businesses: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get business statistics
     */
    public function getBusinessStats()
    {
        try {
            $sql = "SELECT 
                        COUNT(*) as total_businesses,
                        COUNT(CASE WHEN status = 'approved' THEN 1 END) as approved_businesses,
                        COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending_businesses
                    FROM business";

            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error getting business stats: " . $e->getMessage());
            return [
                'total_businesses' => 0,
                'approved_businesses' => 0,
                'pending_businesses' => 0
            ];
        }
    }

    /**
     * Format date
     */
    public function formatDate($date)
    {
        if (empty($date)) {
            return 'Hivi karibuni';
        }

        $timestamp = strtotime($date);
        if ($timestamp === false) {
            return 'Hivi karibuni';
        }

        $now = time();
        $diff = $now - $timestamp;

        if ($diff < 60) {
            return 'Hivi karibuni';
        } elseif ($diff < 3600) {
            $minutes = floor($diff / 60);
            return "Mda wa dakika $minutes";
        } elseif ($diff < 86400) {
            $hours = floor($diff / 3600);
            return "Mda wa saa $hours";
        } elseif ($diff < 2592000) {
            $days = floor($diff / 86400);
            return "Mda wa siku $days";
        } else {
            return date('d/m/Y', $timestamp);
        }
    }

    /**
     * Truncate text
     */
    public function truncateText($text, $length = 100)
    {
        if (strlen($text) <= $length) {
            return $text;
        }

        return substr($text, 0, $length) . '...';
    }

    /**
     * Get image URL
     */
    public function getImageUrl($photoPath)
    {
        if (empty($photoPath)) {
            return asset('images/business/default-business.jpg');
        }

        // Check if it's already a full URL
        if (filter_var($photoPath, FILTER_VALIDATE_URL)) {
            return $photoPath;
        }

        // Return relative path
        return 'uploads/Business/' . $photoPath;
    }
}
