<?php
require_once __DIR__ . '/../config/database.php';

class Fursa
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getLatestOpportunities($limit = 6, $excludeId = null)
    {
        try {
            $conn = $this->db->getConnection();

            if ($excludeId) {
                $stmt = $conn->prepare("SELECT id, name, description, image, date, month, date_created FROM fursa WHERE id != ? ORDER BY date_created DESC LIMIT ?");
                $stmt->bindParam(1, $excludeId, PDO::PARAM_INT);
                $stmt->bindParam(2, $limit, PDO::PARAM_INT);
            } else {
                $stmt = $conn->prepare("SELECT id, name, description, image, date, month, date_created FROM fursa ORDER BY date_created DESC LIMIT ?");
                $stmt->bindParam(1, $limit, PDO::PARAM_INT);
            }

            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error fetching opportunities: " . $e->getMessage());
            return [];
        }
    }

    public function getAllOpportunitiesForAdmin()
    {
        try {
            $conn = $this->db->getConnection();
            $stmt = $conn->prepare("SELECT id, name, description, image, date, month, date_created FROM fursa ORDER BY id DESC");
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting all opportunities for admin: " . $e->getMessage());
            return [];
        }
    }

    public function getOpportunityById($id)
    {
        try {
            $conn = $this->db->getConnection();
            $stmt = $conn->prepare("SELECT id, name, description, image, date_created FROM fursa WHERE id = ?");
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

    /**
     * Get total count of fursa
     */
    public function getTotalFursa()
    {
        try {
            $conn = $this->db->getConnection();
            $stmt = $conn->prepare("SELECT COUNT(*) as total FROM fursa");
            $stmt->execute();
            $result = $stmt->fetch();
            return $result['total'] ?? 0;
        } catch (PDOException $e) {
            error_log("Error getting total fursa: " . $e->getMessage());
            return 0;
        }
    }

    public function getOverallOpportunityStats()
    {
        try {
            $conn = $this->db->getConnection();

            // Get total opportunities count
            $stmt = $conn->prepare("SELECT COUNT(*) as total FROM fursa");
            $stmt->execute();
            $totalResult = $stmt->fetch();

            // Get opportunities by month (this month)
            $currentMonth = date('F');
            $stmt = $conn->prepare("SELECT COUNT(*) as this_month FROM fursa WHERE month = ?");
            $stmt->execute([$currentMonth]);
            $thisMonthResult = $stmt->fetch();

            // Get opportunities by month (last month)
            $lastMonth = date('F', strtotime('-1 month'));
            $stmt = $conn->prepare("SELECT COUNT(*) as last_month FROM fursa WHERE month = ?");
            $stmt->execute([$lastMonth]);
            $lastMonthResult = $stmt->fetch();

            // Get opportunities by month (this year)
            $currentYear = date('Y');
            $stmt = $conn->prepare("SELECT COUNT(*) as this_year FROM fursa WHERE YEAR(date_created) = ?");
            $stmt->execute([$currentYear]);
            $thisYearResult = $stmt->fetch();

            return [
                'total' => $totalResult['total'] ?? 0,
                'this_month' => $thisMonthResult['this_month'] ?? 0,
                'last_month' => $lastMonthResult['last_month'] ?? 0,
                'this_year' => $thisYearResult['this_year'] ?? 0
            ];
        } catch (PDOException $e) {
            error_log("Error getting overall opportunity stats: " . $e->getMessage());
            return [
                'total' => 0,
                'this_month' => 0,
                'last_month' => 0,
                'this_year' => 0
            ];
        }
    }

    public function updateOpportunity($opportunityId, $opportunityData)
    {
        try {
            $conn = $this->db->getConnection();

            // Check if opportunity exists
            $stmt = $conn->prepare("SELECT id FROM fursa WHERE id = ?");
            $stmt->execute([$opportunityId]);
            if (!$stmt->fetch()) {
                return false;
            }

            // Update opportunity with simple fields matching the old system
            $stmt = $conn->prepare("
                UPDATE fursa 
                SET name = ?, description = ?, image = ?
                WHERE id = ?
            ");

            return $stmt->execute([
                $opportunityData['name'],
                $opportunityData['description'],
                $opportunityData['image'],
                $opportunityId
            ]);
        } catch (PDOException $e) {
            error_log("Error updating opportunity: " . $e->getMessage());
            return false;
        }
    }

    public function addOpportunity($opportunityData)
    {
        try {
            $conn = $this->db->getConnection();

            // Insert opportunity with fields matching the old system
            $stmt = $conn->prepare("
                INSERT INTO fursa (name, description, image, date, month, date_created) 
                VALUES (?, ?, ?, ?, ?, ?)
            ");

            return $stmt->execute([
                $opportunityData['name'],
                $opportunityData['description'],
                $opportunityData['image'],
                $opportunityData['date'],
                $opportunityData['month'],
                $opportunityData['date_created']
            ]);
        } catch (PDOException $e) {
            error_log("Error adding opportunity: " . $e->getMessage());
            return false;
        }
    }

    public function deleteOpportunity($opportunityId)
    {
        try {
            $conn = $this->db->getConnection();

            // Check if opportunity exists
            $stmt = $conn->prepare("SELECT id FROM fursa WHERE id = ?");
            $stmt->execute([$opportunityId]);
            if (!$stmt->fetch()) {
                return false;
            }

            // Delete opportunity
            $stmt = $conn->prepare("DELETE FROM fursa WHERE id = ?");
            return $stmt->execute([$opportunityId]);
        } catch (PDOException $e) {
            error_log("Error deleting opportunity: " . $e->getMessage());
            return false;
        }
    }
}
