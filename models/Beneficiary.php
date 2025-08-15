<?php
require_once __DIR__ . "/../config/database.php";

class Beneficiary
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function getAllBeneficiariesForAdmin($page = 1, $perPage = 20)
    {
        try {
            $conn = $this->db->getConnection();
            $offset = ($page - 1) * $perPage;

            $stmt = $conn->prepare("
                SELECT 
                    b.id, b.first_name, b.last_name, b.email, b.phone, b.location, 
                    b.benefit_type, b.description, b.amount, b.status, b.date_created
                FROM beneficiaries b
                ORDER BY b.date_created DESC 
                LIMIT ? OFFSET ?
            ");
            $stmt->execute([$perPage, $offset]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting all beneficiaries for admin: " . $e->getMessage());
            return [];
        }
    }

    public function getTotalBeneficiaries()
    {
        try {
            $conn = $this->db->getConnection();
            $stmt = $conn->prepare("SELECT COUNT(*) as total FROM beneficiaries");
            $stmt->execute();
            $result = $stmt->fetch();
            return $result['total'] ?? 0;
        } catch (PDOException $e) {
            error_log("Error getting total beneficiaries: " . $e->getMessage());
            return 0;
        }
    }

    public function getOverallBeneficiaryStats()
    {
        try {
            $conn = $this->db->getConnection();

            // Get active beneficiaries count
            $stmt = $conn->prepare("SELECT COUNT(*) as active FROM beneficiaries WHERE status = 'active'");
            $stmt->execute();
            $activeResult = $stmt->fetch();

            // Get pending beneficiaries count
            $stmt = $conn->prepare("SELECT COUNT(*) as pending FROM beneficiaries WHERE status = 'pending'");
            $stmt->execute();
            $pendingResult = $stmt->fetch();

            // Get total amount distributed
            $stmt = $conn->prepare("SELECT SUM(amount) as total_amount FROM beneficiaries WHERE amount IS NOT NULL AND status = 'active'");
            $stmt->execute();
            $amountResult = $stmt->fetch();

            return [
                'active' => $activeResult['active'] ?? 0,
                'pending' => $pendingResult['pending'] ?? 0,
                'total_amount' => $amountResult['total_amount'] ?? 0
            ];
        } catch (PDOException $e) {
            error_log("Error getting overall beneficiary stats: " . $e->getMessage());
            return [
                'active' => 0,
                'pending' => 0,
                'total_amount' => 0
            ];
        }
    }

    public function getAllWanufaikaForAdmin()
    {
        try {
            $conn = $this->db->getConnection();

            $sql = "
                SELECT 
                    id, name, title, description, photo, date_created
                FROM wanufaika 
                ORDER BY id DESC
            ";

            $stmt = $conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting all wanufaika for admin: " . $e->getMessage());
            return [];
        }
    }

    public function getWanufaikaStats()
    {
        try {
            $conn = $this->db->getConnection();

            // Get total wanufaika count
            $stmt = $conn->prepare("SELECT COUNT(*) as total FROM wanufaika");
            $stmt->execute();
            $totalResult = $stmt->fetch();

            // Get this month's count
            $stmt = $conn->prepare("SELECT COUNT(*) as this_month FROM wanufaika WHERE MONTH(date_created) = MONTH(NOW()) AND YEAR(date_created) = YEAR(NOW())");
            $stmt->execute();
            $thisMonthResult = $stmt->fetch();

            // Get last month's count
            $stmt = $conn->prepare("SELECT COUNT(*) as last_month FROM wanufaika WHERE MONTH(date_created) = MONTH(DATE_SUB(NOW(), INTERVAL 1 MONTH)) AND YEAR(date_created) = YEAR(DATE_SUB(NOW(), INTERVAL 1 MONTH))");
            $stmt->execute();
            $lastMonthResult = $stmt->fetch();

            // Get this year's count
            $stmt = $conn->prepare("SELECT COUNT(*) as this_year FROM wanufaika WHERE YEAR(date_created) = YEAR(NOW())");
            $stmt->execute();
            $thisYearResult = $stmt->fetch();

            return [
                'total' => $totalResult['total'] ?? 0,
                'this_month' => $thisMonthResult['this_month'] ?? 0,
                'last_month' => $lastMonthResult['last_month'] ?? 0,
                'this_year' => $thisYearResult['this_year'] ?? 0
            ];
        } catch (PDOException $e) {
            error_log("Error getting wanufaika stats: " . $e->getMessage());
            return [
                'total' => 0,
                'this_month' => 0,
                'last_month' => 0,
                'this_year' => 0
            ];
        }
    }

    public function addWanufaika($name, $title, $description, $photo = '')
    {
        try {
            $conn = $this->db->getConnection();

            $stmt = $conn->prepare("
                INSERT INTO wanufaika (name, title, description, photo, date_created) 
                VALUES (?, ?, ?, ?, NOW())
            ");

            return $stmt->execute([$name, $title, $description, $photo]);
        } catch (PDOException $e) {
            error_log("Error adding wanufaika: " . $e->getMessage());
            return false;
        }
    }

    public function getWanufaikaById($id)
    {
        try {
            $conn = $this->db->getConnection();

            $stmt = $conn->prepare("
                SELECT id, name, title, description, photo, date_created
                FROM wanufaika 
                WHERE id = ?
            ");

            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error getting wanufaika by ID: " . $e->getMessage());
            return false;
        }
    }

    public function deleteWanufaika($id)
    {
        try {
            $conn = $this->db->getConnection();

            $stmt = $conn->prepare("DELETE FROM wanufaika WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("Error deleting wanufaika: " . $e->getMessage());
            return false;
        }
    }

    public function addBeneficiary($firstName, $lastName, $email, $phone, $location, $benefitType, $description, $amount)
    {
        try {
            $conn = $this->db->getConnection();

            // Check if email already exists
            $stmt = $conn->prepare("SELECT id FROM beneficiaries WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                return false; // Email already exists
            }

            $stmt = $conn->prepare("
                INSERT INTO beneficiaries (first_name, last_name, email, phone, location, benefit_type, description, amount, status, date_created) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())
            ");

            return $stmt->execute([$firstName, $lastName, $email, $phone, $location, $benefitType, $description, $amount]);
        } catch (PDOException $e) {
            error_log("Error adding beneficiary: " . $e->getMessage());
            return false;
        }
    }

    public function updateBeneficiary($beneficiaryId, $firstName, $lastName, $email, $phone, $location, $benefitType, $description, $amount)
    {
        try {
            $conn = $this->db->getConnection();

            // Check if beneficiary exists
            $stmt = $conn->prepare("SELECT id FROM beneficiaries WHERE id = ?");
            $stmt->execute([$beneficiaryId]);
            if (!$stmt->fetch()) {
                return false;
            }

            // Check if email already exists for another beneficiary
            $stmt = $conn->prepare("SELECT id FROM beneficiaries WHERE email = ? AND id != ?");
            $stmt->execute([$email, $beneficiaryId]);
            if ($stmt->fetch()) {
                return false; // Email already exists for another beneficiary
            }

            $stmt = $conn->prepare("
                UPDATE beneficiaries 
                SET first_name = ?, last_name = ?, email = ?, phone = ?, location = ?, 
                    benefit_type = ?, description = ?, amount = ?, date_updated = NOW()
                WHERE id = ?
            ");

            return $stmt->execute([$firstName, $lastName, $email, $phone, $location, $benefitType, $description, $amount, $beneficiaryId]);
        } catch (PDOException $e) {
            error_log("Error updating beneficiary: " . $e->getMessage());
            return false;
        }
    }

    public function deleteBeneficiary($beneficiaryId)
    {
        try {
            $conn = $this->db->getConnection();

            // Check if beneficiary exists
            $stmt = $conn->prepare("SELECT id FROM beneficiaries WHERE id = ?");
            $stmt->execute([$beneficiaryId]);
            if (!$stmt->fetch()) {
                return false;
            }

            // Delete beneficiary (you might want to soft delete instead)
            $stmt = $conn->prepare("DELETE FROM beneficiaries WHERE id = ?");
            return $stmt->execute([$beneficiaryId]);
        } catch (PDOException $e) {
            error_log("Error deleting beneficiary: " . $e->getMessage());
            return false;
        }
    }

    public function toggleBeneficiaryStatus($beneficiaryId)
    {
        try {
            $conn = $this->db->getConnection();

            // Check if beneficiary exists
            $stmt = $conn->prepare("SELECT id, status FROM beneficiaries WHERE id = ?");
            $stmt->execute([$beneficiaryId]);
            $beneficiary = $stmt->fetch();
            if (!$beneficiary) {
                return false;
            }

            // Toggle status
            $newStatus = ($beneficiary['status'] == 'active') ? 'pending' : 'active';
            $stmt = $conn->prepare("UPDATE beneficiaries SET status = ?, date_updated = NOW() WHERE id = ?");
            return $stmt->execute([$newStatus, $beneficiaryId]);
        } catch (PDOException $e) {
            error_log("Error toggling beneficiary status: " . $e->getMessage());
            return false;
        }
    }

    public function getBeneficiaryById($beneficiaryId)
    {
        try {
            $conn = $this->db->getConnection();

            $stmt = $conn->prepare("SELECT * FROM beneficiaries WHERE id = ?");
            $stmt->execute([$beneficiaryId]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error getting beneficiary by ID: " . $e->getMessage());
            return false;
        }
    }

    public function getBeneficiariesByType($benefitType, $page = 1, $perPage = 10)
    {
        try {
            $conn = $this->db->getConnection();
            $offset = ($page - 1) * $perPage;

            $stmt = $conn->prepare("
                SELECT * FROM beneficiaries 
                WHERE benefit_type = ? 
                ORDER BY date_created DESC 
                LIMIT ? OFFSET ?
            ");
            $stmt->execute([$benefitType, $perPage, $offset]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting beneficiaries by type: " . $e->getMessage());
            return [];
        }
    }

    public function getBeneficiariesByStatus($status, $page = 1, $perPage = 10)
    {
        try {
            $conn = $this->db->getConnection();
            $offset = ($page - 1) * $perPage;

            $stmt = $conn->prepare("
                SELECT * FROM beneficiaries 
                WHERE status = ? 
                ORDER BY date_created DESC 
                LIMIT ? OFFSET ?
            ");
            $stmt->execute([$status, $perPage, $offset]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting beneficiaries by status: " . $e->getMessage());
            return [];
        }
    }

    public function searchBeneficiaries($searchTerm, $benefitType = null, $status = null, $page = 1, $perPage = 10)
    {
        try {
            $conn = $this->db->getConnection();
            $offset = ($page - 1) * $perPage;

            $sql = "
                SELECT * FROM beneficiaries 
                WHERE (first_name LIKE ? OR last_name LIKE ? OR email LIKE ? OR location LIKE ?)
            ";
            $params = ["%$searchTerm%", "%$searchTerm%", "%$searchTerm%", "%$searchTerm%"];

            if ($benefitType) {
                $sql .= " AND benefit_type = ?";
                $params[] = $benefitType;
            }

            if ($status) {
                $sql .= " AND status = ?";
                $params[] = $status;
            }

            $sql .= " ORDER BY date_created DESC LIMIT ? OFFSET ?";
            $params[] = $perPage;
            $params[] = $offset;

            $stmt = $conn->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error searching beneficiaries: " . $e->getMessage());
            return [];
        }
    }

    public function getBeneficiariesByLocation($location, $page = 1, $perPage = 10)
    {
        try {
            $conn = $this->db->getConnection();
            $offset = ($page - 1) * $perPage;

            $stmt = $conn->prepare("
                SELECT * FROM beneficiaries 
                WHERE location LIKE ? 
                ORDER BY date_created DESC 
                LIMIT ? OFFSET ?
            ");
            $stmt->execute(["%$location%", $perPage, $offset]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting beneficiaries by location: " . $e->getMessage());
            return [];
        }
    }

    public function getBeneficiaryProgress($beneficiaryId)
    {
        try {
            $conn = $this->db->getConnection();

            // Get beneficiary details
            $stmt = $conn->prepare("SELECT * FROM beneficiaries WHERE id = ?");
            $stmt->execute([$beneficiaryId]);
            $beneficiary = $stmt->fetch();

            if (!$beneficiary) {
                return false;
            }

            // Get progress tracking data if available
            $stmt = $conn->prepare("
                SELECT * FROM beneficiary_progress 
                WHERE beneficiary_id = ? 
                ORDER BY date_created DESC
            ");
            $stmt->execute([$beneficiaryId]);
            $progress = $stmt->fetchAll();

            return [
                'beneficiary' => $beneficiary,
                'progress' => $progress
            ];
        } catch (PDOException $e) {
            error_log("Error getting beneficiary progress: " . $e->getMessage());
            return false;
        }
    }

    public function addProgressUpdate($beneficiaryId, $update, $notes = null)
    {
        try {
            $conn = $this->db->getConnection();

            // Check if beneficiary exists
            $stmt = $conn->prepare("SELECT id FROM beneficiaries WHERE id = ?");
            $stmt->execute([$beneficiaryId]);
            if (!$stmt->fetch()) {
                return false;
            }

            $stmt = $conn->prepare("
                INSERT INTO beneficiary_progress (beneficiary_id, update_text, notes, date_created) 
                VALUES (?, ?, ?, NOW())
            ");

            return $stmt->execute([$beneficiaryId, $update, $notes]);
        } catch (PDOException $e) {
            error_log("Error adding progress update: " . $e->getMessage());
            return false;
        }
    }

    public function getBeneficiaryReport($startDate = null, $endDate = null)
    {
        try {
            $conn = $this->db->getConnection();

            $sql = "
                SELECT 
                    benefit_type,
                    status,
                    COUNT(*) as count,
                    SUM(amount) as total_amount
                FROM beneficiaries
            ";
            $params = [];

            if ($startDate && $endDate) {
                $sql .= " WHERE date_created BETWEEN ? AND ?";
                $params = [$startDate, $endDate];
            }

            $sql .= " GROUP BY benefit_type, status ORDER BY benefit_type, status";

            $stmt = $conn->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting beneficiary report: " . $e->getMessage());
            return [];
        }
    }
}
