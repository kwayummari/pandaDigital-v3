<?php
require_once __DIR__ . "/../config/database.php";

class Expert
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function getAllExperts()
    {
        try {
            $conn = $this->db->getConnection();
            $stmt = $conn->prepare("
                SELECT u.id, u.first_name, u.last_name, u.profile_photo, u.business, u.region, u.email, u.phone,
                       COALESCE(e.status, 'free') as status
                FROM users u 
                LEFT JOIN experts e ON u.email COLLATE utf8mb4_general_ci = e.email COLLATE utf8mb4_general_ci
                WHERE u.role = 'expert' 
                ORDER BY u.first_name, u.last_name
            ");
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error fetching experts: " . $e->getMessage());
            return [];
        }
    }

    public function getExpertImageUrl($profilePhoto)
    {
        if (empty($profilePhoto) || $profilePhoto === "Profile-01.jpg") {
            return asset("images/logo/logo.png");
        }
        $imagePath = __DIR__ . "/../uploads/ProfilePhotos/" . $profilePhoto;
        if (file_exists($imagePath)) {
            return upload_url("ProfilePhotos/" . $profilePhoto);
        }
        return asset("images/logo/logo.png");
    }

    public function getExpertStatus($expertId)
    {
        try {
            $conn = $this->db->getConnection();
            $stmt = $conn->prepare("
                SELECT COALESCE(e.status, 'free') as status
                FROM users u 
                LEFT JOIN experts e ON u.email COLLATE utf8mb4_general_ci = e.email COLLATE utf8mb4_general_ci
                WHERE u.id = ? AND u.role = 'expert'
            ");
            $stmt->execute([$expertId]);
            $result = $stmt->fetch();
            return $result ? $result['status'] : 'free';
        } catch (PDOException $e) {
            error_log("Error fetching expert status: " . $e->getMessage());
            return 'free';
        }
    }

    public function getExpertStats()
    {
        try {
            $conn = $this->db->getConnection();

            // Get total experts count
            $stmt = $conn->prepare("SELECT COUNT(*) as total FROM users WHERE role = 'expert'");
            $stmt->execute();
            $totalExperts = $stmt->fetch()['total'];

            // Get experts by region
            $stmt = $conn->prepare("SELECT region, COUNT(*) as count FROM users WHERE role = 'expert' AND region IS NOT NULL GROUP BY region ORDER BY count DESC LIMIT 5");
            $stmt->execute();
            $expertsByRegion = $stmt->fetchAll();

            // Get experts by business type
            $stmt = $conn->prepare("SELECT business, COUNT(*) as count FROM users WHERE role = 'expert' AND business IS NOT NULL GROUP BY business ORDER BY count DESC LIMIT 5");
            $stmt->execute();
            $expertsByBusiness = $stmt->fetchAll();

            return [
                'total' => $totalExperts,
                'byRegion' => $expertsByRegion,
                'byBusiness' => $expertsByBusiness
            ];
        } catch (PDOException $e) {
            error_log("Error fetching expert stats: " . $e->getMessage());
            return [
                'total' => 0,
                'byRegion' => [],
                'byBusiness' => []
            ];
        }
    }

    public function searchExperts($searchQuery)
    {
        try {
            $conn = $this->db->getConnection();
            $searchTerm = '%' . $searchQuery . '%';

            $stmt = $conn->prepare("
                SELECT u.id, u.first_name, u.last_name, u.profile_photo, u.business, u.region, u.email, u.phone,
                       COALESCE(e.status, 'free') as status
                FROM users u 
                LEFT JOIN experts e ON u.email COLLATE utf8mb4_general_ci = e.email COLLATE utf8mb4_general_ci
                WHERE u.role = 'expert' 
                AND (
                    u.first_name LIKE ? OR 
                    u.business LIKE ? OR 
                    u.region LIKE ?
                )
                ORDER BY u.first_name, u.last_name
            ");

            $stmt->execute([$searchTerm, $searchTerm, $searchTerm]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error searching experts: " . $e->getMessage());
            return [];
        }
    }

    public function getExpertById($expertId)
    {
        try {
            $conn = $this->db->getConnection();
            $stmt = $conn->prepare("
                SELECT u.id, u.first_name, u.last_name, u.profile_photo, u.business, u.region, u.email, u.phone,
                       COALESCE(e.status, 'free') as status
                FROM users u 
                LEFT JOIN experts e ON u.email COLLATE utf8mb4_general_ci = e.email COLLATE utf8mb4_general_ci
                WHERE u.id = ? AND u.role = 'expert'
            ");
            $stmt->execute([$expertId]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error fetching expert by ID: " . $e->getMessage());
            return null;
        }
    }

    public function getOtherExperts($excludeId, $limit = 8)
    {
        try {
            $conn = $this->db->getConnection();
            $stmt = $conn->prepare("
                SELECT u.id, u.first_name, u.last_name, u.profile_photo, u.business, u.region, u.email, u.phone,
                       COALESCE(e.status, 'free') as status
                FROM users u 
                LEFT JOIN experts e ON u.email COLLATE utf8mb4_general_ci = e.email COLLATE utf8mb4_general_ci
                WHERE u.role = 'expert' AND u.id != ?
                ORDER BY RAND()
                LIMIT ?
            ");
            $stmt->execute([$excludeId, $limit]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error fetching other experts: " . $e->getMessage());
            return [];
        }
    }
}
