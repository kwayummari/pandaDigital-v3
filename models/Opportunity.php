<?php
require_once __DIR__ . "/../config/database.php";

class Opportunity
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function getAllOpportunitiesForAdmin($page = 1, $perPage = 20)
    {
        try {
            $conn = $this->db->getConnection();
            $offset = ($page - 1) * $perPage;

            $stmt = $conn->prepare("
                SELECT 
                    o.id, o.title, o.description, o.requirements, o.budget, o.deadline, 
                    o.category, o.location, o.contact_info, o.status, o.date_created,
                    COUNT(DISTINCT a.id) as applications_count
                FROM opportunities o
                LEFT JOIN opportunity_applications a ON o.id = a.opportunity_id
                GROUP BY o.id
                ORDER BY o.date_created DESC 
                LIMIT ? OFFSET ?
            ");
            $stmt->execute([$perPage, $offset]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting all opportunities for admin: " . $e->getMessage());
            return [];
        }
    }

    public function getTotalOpportunities()
    {
        try {
            $conn = $this->db->getConnection();
            $stmt = $conn->prepare("SELECT COUNT(*) as total FROM opportunities");
            $stmt->execute();
            $result = $stmt->fetch();
            return $result['total'] ?? 0;
        } catch (PDOException $e) {
            error_log("Error getting total opportunities: " . $e->getMessage());
            return 0;
        }
    }

    public function getOverallOpportunityStats()
    {
        try {
            $conn = $this->db->getConnection();

            // Get active opportunities count
            $stmt = $conn->prepare("SELECT COUNT(*) as active FROM opportunities WHERE status = 'active'");
            $stmt->execute();
            $activeResult = $stmt->fetch();

            // Get pending opportunities count
            $stmt = $conn->prepare("SELECT COUNT(*) as pending FROM opportunities WHERE status = 'pending'");
            $stmt->execute();
            $pendingResult = $stmt->fetch();

            // Get total applications
            $stmt = $conn->prepare("SELECT COUNT(*) as total_applications FROM opportunity_applications");
            $stmt->execute();
            $applicationsResult = $stmt->fetch();

            return [
                'active' => $activeResult['active'] ?? 0,
                'pending' => $pendingResult['pending'] ?? 0,
                'total_applications' => $applicationsResult['total_applications'] ?? 0
            ];
        } catch (PDOException $e) {
            error_log("Error getting overall opportunity stats: " . $e->getMessage());
            return [
                'active' => 0,
                'pending' => 0,
                'total_applications' => 0
            ];
        }
    }

    public function addOpportunity($title, $description, $requirements, $budget, $deadline, $category, $location, $contactInfo)
    {
        try {
            $conn = $this->db->getConnection();

            $stmt = $conn->prepare("
                INSERT INTO opportunities (title, description, requirements, budget, deadline, category, location, contact_info, status, date_created) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())
            ");

            return $stmt->execute([$title, $description, $requirements, $budget, $deadline, $category, $location, $contactInfo]);
        } catch (PDOException $e) {
            error_log("Error adding opportunity: " . $e->getMessage());
            return false;
        }
    }

    public function updateOpportunity($opportunityId, $title, $description, $requirements, $budget, $deadline, $category, $location, $contactInfo)
    {
        try {
            $conn = $this->db->getConnection();

            // Check if opportunity exists
            $stmt = $conn->prepare("SELECT id FROM opportunities WHERE id = ?");
            $stmt->execute([$opportunityId]);
            if (!$stmt->fetch()) {
                return false;
            }

            $stmt = $conn->prepare("
                UPDATE opportunities 
                SET title = ?, description = ?, requirements = ?, budget = ?, deadline = ?, 
                    category = ?, location = ?, contact_info = ?, date_updated = NOW()
                WHERE id = ?
            ");

            return $stmt->execute([$title, $description, $requirements, $budget, $deadline, $category, $location, $contactInfo, $opportunityId]);
        } catch (PDOException $e) {
            error_log("Error updating opportunity: " . $e->getMessage());
            return false;
        }
    }

    public function deleteOpportunity($opportunityId)
    {
        try {
            $conn = $this->db->getConnection();

            // Check if opportunity exists
            $stmt = $conn->prepare("SELECT id FROM opportunities WHERE id = ?");
            $stmt->execute([$opportunityId]);
            if (!$stmt->fetch()) {
                return false;
            }

            // Delete opportunity (you might want to soft delete instead)
            $stmt = $conn->prepare("DELETE FROM opportunities WHERE id = ?");
            return $stmt->execute([$opportunityId]);
        } catch (PDOException $e) {
            error_log("Error deleting opportunity: " . $e->getMessage());
            return false;
        }
    }

    public function toggleOpportunityStatus($opportunityId)
    {
        try {
            $conn = $this->db->getConnection();

            // Check if opportunity exists
            $stmt = $conn->prepare("SELECT id, status FROM opportunities WHERE id = ?");
            $stmt->execute([$opportunityId]);
            $opportunity = $stmt->fetch();
            if (!$opportunity) {
                return false;
            }

            // Toggle status
            $newStatus = ($opportunity['status'] == 'active') ? 'pending' : 'active';
            $stmt = $conn->prepare("UPDATE opportunities SET status = ?, date_updated = NOW() WHERE id = ?");
            return $stmt->execute([$newStatus, $opportunityId]);
        } catch (PDOException $e) {
            error_log("Error toggling opportunity status: " . $e->getMessage());
            return false;
        }
    }

    public function getOpportunityById($opportunityId)
    {
        try {
            $conn = $this->db->getConnection();

            $stmt = $conn->prepare("
                SELECT 
                    o.*, COUNT(DISTINCT a.id) as applications_count
                FROM opportunities o
                LEFT JOIN opportunity_applications a ON o.id = a.opportunity_id
                WHERE o.id = ?
                GROUP BY o.id
            ");
            $stmt->execute([$opportunityId]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error getting opportunity by ID: " . $e->getMessage());
            return false;
        }
    }

    public function getOpportunitiesByCategory($category, $page = 1, $perPage = 10)
    {
        try {
            $conn = $this->db->getConnection();
            $offset = ($page - 1) * $perPage;

            $stmt = $conn->prepare("
                SELECT 
                    o.*, COUNT(DISTINCT a.id) as applications_count
                FROM opportunities o
                LEFT JOIN opportunity_applications a ON o.id = a.opportunity_id
                WHERE o.category = ? AND o.status = 'active'
                GROUP BY o.id
                ORDER BY o.date_created DESC 
                LIMIT ? OFFSET ?
            ");
            $stmt->execute([$category, $perPage, $offset]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting opportunities by category: " . $e->getMessage());
            return [];
        }
    }

    public function getActiveOpportunities($page = 1, $perPage = 10)
    {
        try {
            $conn = $this->db->getConnection();
            $offset = ($page - 1) * $perPage;

            $stmt = $conn->prepare("
                SELECT 
                    o.*, COUNT(DISTINCT a.id) as applications_count
                FROM opportunities o
                LEFT JOIN opportunity_applications a ON o.id = a.opportunity_id
                WHERE o.status = 'active'
                GROUP BY o.id
                ORDER BY o.date_created DESC 
                LIMIT ? OFFSET ?
            ");
            $stmt->execute([$perPage, $offset]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting active opportunities: " . $e->getMessage());
            return [];
        }
    }

    public function searchOpportunities($searchTerm, $category = null, $page = 1, $perPage = 10)
    {
        try {
            $conn = $this->db->getConnection();
            $offset = ($page - 1) * $perPage;

            $sql = "
                SELECT 
                    o.*, COUNT(DISTINCT a.id) as applications_count
                FROM opportunities o
                LEFT JOIN opportunity_applications a ON o.id = a.opportunity_id
                WHERE o.status = 'active' AND (o.title LIKE ? OR o.description LIKE ?)
            ";
            $params = ["%$searchTerm%", "%$searchTerm%"];

            if ($category) {
                $sql .= " AND o.category = ?";
                $params[] = $category;
            }

            $sql .= " GROUP BY o.id ORDER BY o.date_created DESC LIMIT ? OFFSET ?";
            $params[] = $perPage;
            $params[] = $offset;

            $stmt = $conn->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error searching opportunities: " . $e->getMessage());
            return [];
        }
    }

    public function getOpportunityApplications($opportunityId)
    {
        try {
            $conn = $this->db->getConnection();

            $stmt = $conn->prepare("
                SELECT 
                    a.id, a.proposal, a.budget_proposal, a.timeline, a.status, a.date_created,
                    u.first_name, u.last_name, u.email, u.phone
                FROM opportunity_applications a
                LEFT JOIN users u ON a.user_id = u.id
                WHERE a.opportunity_id = ?
                ORDER BY a.date_created DESC
            ");
            $stmt->execute([$opportunityId]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting opportunity applications: " . $e->getMessage());
            return [];
        }
    }

    public function addApplication($opportunityId, $userId, $proposal, $budgetProposal, $timeline)
    {
        try {
            $conn = $this->db->getConnection();

            // Check if opportunity exists and is active
            $stmt = $conn->prepare("SELECT id FROM opportunities WHERE id = ? AND status = 'active'");
            $stmt->execute([$opportunityId]);
            if (!$stmt->fetch()) {
                return false;
            }

            // Check if user already applied
            $stmt = $conn->prepare("SELECT id FROM opportunity_applications WHERE opportunity_id = ? AND user_id = ?");
            $stmt->execute([$opportunityId, $userId]);
            if ($stmt->fetch()) {
                return false;
            }

            // Add application
            $stmt = $conn->prepare("
                INSERT INTO opportunity_applications (opportunity_id, user_id, proposal, budget_proposal, timeline, status, date_created) 
                VALUES (?, ?, ?, ?, ?, 'pending', NOW())
            ");

            return $stmt->execute([$opportunityId, $userId, $proposal, $budgetProposal, $timeline]);
        } catch (PDOException $e) {
            error_log("Error adding opportunity application: " . $e->getMessage());
            return false;
        }
    }

    public function updateApplicationStatus($applicationId, $status)
    {
        try {
            $conn = $this->db->getConnection();

            $stmt = $conn->prepare("UPDATE opportunity_applications SET status = ?, date_updated = NOW() WHERE id = ?");
            return $stmt->execute([$status, $applicationId]);
        } catch (PDOException $e) {
            error_log("Error updating application status: " . $e->getMessage());
            return false;
        }
    }
}
