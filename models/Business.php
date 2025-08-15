<?php
require_once __DIR__ . "/../config/database.php";

class Business
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function getAllBusinessesForAdmin($page = 1, $perPage = 20)
    {
        try {
            $conn = $this->db->getConnection();
            $offset = ($page - 1) * $perPage;

            $stmt = $conn->prepare("
                SELECT 
                    b.id, b.business_name, b.description, b.business_type, b.location, 
                    b.phone, b.email, b.website, b.verification_status, b.status, b.date_created,
                    u.first_name, u.last_name, u.email as owner_email
                FROM businesses b
                LEFT JOIN users u ON b.owner_id = u.id
                ORDER BY b.date_created DESC 
                LIMIT ? OFFSET ?
            ");
            $stmt->execute([$perPage, $offset]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting all businesses for admin: " . $e->getMessage());
            return [];
        }
    }

    public function getTotalBusinesses()
    {
        try {
            $conn = $this->db->getConnection();
            $stmt = $conn->prepare("SELECT COUNT(*) as total FROM business");
            $stmt->execute();
            $result = $stmt->fetch();
            return $result['total'] ?? 0;
        } catch (PDOException $e) {
            error_log("Error getting total businesses: " . $e->getMessage());
            return 0;
        }
    }

    public function getOverallBusinessStats()
    {
        try {
            $conn = $this->db->getConnection();

            // Get verified businesses count
            $stmt = $conn->prepare("SELECT COUNT(*) as verified FROM businesses WHERE verification_status = 'verified'");
            $stmt->execute();
            $verifiedResult = $stmt->fetch();

            // Get pending businesses count
            $stmt = $conn->prepare("SELECT COUNT(*) as pending FROM businesses WHERE verification_status = 'pending'");
            $stmt->execute();
            $pendingResult = $stmt->fetch();

            // Get active business owners count
            $stmt = $conn->prepare("SELECT COUNT(DISTINCT owner_id) as active_owners FROM businesses WHERE status = 'active'");
            $stmt->execute();
            $ownersResult = $stmt->fetch();

            return [
                'verified' => $verifiedResult['verified'] ?? 0,
                'pending' => $pendingResult['pending'] ?? 0,
                'active_owners' => $ownersResult['active_owners'] ?? 0
            ];
        } catch (PDOException $e) {
            error_log("Error getting overall business stats: " . $e->getMessage());
            return [
                'verified' => 0,
                'pending' => 0,
                'active_owners' => 0
            ];
        }
    }

    public function getAllBusinessesForAdminOld()
    {
        try {
            $conn = $this->db->getConnection();

            $sql = "
                SELECT 
                    b.id, b.name, b.location, b.maelezo, b.date_created,
                    u.first_name, u.last_name, u.username
                FROM business b
                LEFT JOIN users u ON b.user_id = u.id
                ORDER BY b.id DESC
            ";

            $stmt = $conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting all businesses for admin (old system): " . $e->getMessage());
            return [];
        }
    }

    public function getBusinessStatsOld()
    {
        try {
            $conn = $this->db->getConnection();

            // Get total businesses count
            $stmt = $conn->prepare("SELECT COUNT(*) as total FROM business");
            $stmt->execute();
            $totalResult = $stmt->fetch();

            // Get this month's count
            $stmt = $conn->prepare("SELECT COUNT(*) as this_month FROM business WHERE MONTH(date_created) = MONTH(NOW()) AND YEAR(date_created) = YEAR(NOW())");
            $stmt->execute();
            $thisMonthResult = $stmt->fetch();

            // Get last month's count
            $stmt = $conn->prepare("SELECT COUNT(*) as last_month FROM business WHERE MONTH(date_created) = MONTH(DATE_SUB(NOW(), INTERVAL 1 MONTH)) AND YEAR(date_created) = YEAR(DATE_SUB(NOW(), INTERVAL 1 MONTH))");
            $stmt->execute();
            $lastMonthResult = $stmt->fetch();

            // Get this year's count
            $stmt = $conn->prepare("SELECT COUNT(*) as this_year FROM business WHERE YEAR(date_created) = YEAR(NOW())");
            $stmt->execute();
            $thisYearResult = $stmt->fetch();

            return [
                'total' => $totalResult['total'] ?? 0,
                'this_month' => $thisMonthResult['this_month'] ?? 0,
                'last_month' => $lastMonthResult['last_month'] ?? 0,
                'this_year' => $thisYearResult['this_year'] ?? 0
            ];
        } catch (PDOException $e) {
            error_log("Error getting business stats (old system): " . $e->getMessage());
            return [
                'total' => 0,
                'this_month' => 0,
                'last_month' => 0,
                'this_year' => 0
            ];
        }
    }

    public function addBusiness($ownerId, $businessName, $description, $businessType, $location, $phone, $email, $website = null)
    {
        try {
            $conn = $this->db->getConnection();

            // Check if user exists
            $stmt = $conn->prepare("SELECT id FROM users WHERE id = ?");
            $stmt->execute([$ownerId]);
            if (!$stmt->fetch()) {
                return false;
            }

            // Check if user already has a business
            $stmt = $conn->prepare("SELECT id FROM businesses WHERE owner_id = ?");
            $stmt->execute([$ownerId]);
            if ($stmt->fetch()) {
                return false; // User already has a business
            }

            $stmt = $conn->prepare("
                INSERT INTO businesses (owner_id, business_name, description, business_type, location, phone, email, website, verification_status, status, date_created) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending', 'active', NOW())
            ");

            return $stmt->execute([$ownerId, $businessName, $description, $businessType, $location, $phone, $email, $website]);
        } catch (PDOException $e) {
            error_log("Error adding business: " . $e->getMessage());
            return false;
        }
    }

    public function updateBusiness($businessId, $businessName, $description, $businessType, $location, $phone, $email, $website)
    {
        try {
            $conn = $this->db->getConnection();

            // Check if business exists
            $stmt = $conn->prepare("SELECT id FROM businesses WHERE id = ?");
            $stmt->execute([$businessId]);
            if (!$stmt->fetch()) {
                return false;
            }

            $stmt = $conn->prepare("
                UPDATE businesses 
                SET business_name = ?, description = ?, business_type = ?, location = ?, 
                    phone = ?, email = ?, website = ?, date_updated = NOW()
                WHERE id = ?
            ");

            return $stmt->execute([$businessName, $description, $businessType, $location, $phone, $email, $website, $businessId]);
        } catch (PDOException $e) {
            error_log("Error updating business: " . $e->getMessage());
            return false;
        }
    }

    public function deleteBusiness($businessId)
    {
        try {
            $conn = $this->db->getConnection();

            // Check if business exists
            $stmt = $conn->prepare("SELECT id FROM businesses WHERE id = ?");
            $stmt->execute([$businessId]);
            if (!$stmt->fetch()) {
                return false;
            }

            // Delete business (you might want to soft delete instead)
            $stmt = $conn->prepare("DELETE FROM businesses WHERE id = ?");
            return $stmt->execute([$businessId]);
        } catch (PDOException $e) {
            error_log("Error deleting business: " . $e->getMessage());
            return false;
        }
    }

    public function toggleBusinessStatus($businessId)
    {
        try {
            $conn = $this->db->getConnection();

            // Check if business exists
            $stmt = $conn->prepare("SELECT id, status FROM businesses WHERE id = ?");
            $stmt->execute([$businessId]);
            $business = $stmt->fetch();
            if (!$business) {
                return false;
            }

            // Toggle status
            $newStatus = ($business['status'] == 'active') ? 'inactive' : 'active';
            $stmt = $conn->prepare("UPDATE businesses SET status = ?, date_updated = NOW() WHERE id = ?");
            return $stmt->execute([$newStatus, $businessId]);
        } catch (PDOException $e) {
            error_log("Error toggling business status: " . $e->getMessage());
            return false;
        }
    }

    public function verifyBusiness($businessId)
    {
        try {
            $conn = $this->db->getConnection();

            // Check if business exists
            $stmt = $conn->prepare("SELECT id FROM businesses WHERE id = ?");
            $stmt->execute([$businessId]);
            if (!$stmt->fetch()) {
                return false;
            }

            // Mark as verified
            $stmt = $conn->prepare("UPDATE businesses SET verification_status = 'verified', date_updated = NOW() WHERE id = ?");
            return $stmt->execute([$businessId]);
        } catch (PDOException $e) {
            error_log("Error verifying business: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get businesses by user ID (for old system compatibility)
     */
    public function getBusinessesByUserId($userId)
    {
        try {
            $conn = $this->db->getConnection();

            $stmt = $conn->prepare("
                SELECT * FROM business 
                WHERE user_id = ? 
                ORDER BY date_created DESC
            ");

            $stmt->execute([$userId]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting businesses by user ID: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get business details with photos
     */
    public function getBusinessWithPhotos($businessId)
    {
        try {
            $conn = $this->db->getConnection();

            $stmt = $conn->prepare("
                SELECT b.*, bp.photo_path
                FROM business b
                LEFT JOIN business_photo bp ON b.id = bp.business_id
                WHERE b.id = ?
            ");

            $stmt->execute([$businessId]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting business with photos: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get products by business ID
     */
    public function getProductsByBusinessId($businessId)
    {
        try {
            $conn = $this->db->getConnection();

            $stmt = $conn->prepare("
                SELECT p.*, c.name as category_name
                FROM products p
                LEFT JOIN categories c ON p.categoryId = c.id
                WHERE p.sellerId = ?
                ORDER BY p.date DESC
            ");

            $stmt->execute([$businessId]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting products by business ID: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get sales by business ID
     */
    public function getSalesByBusinessId($businessId)
    {
        try {
            $conn = $this->db->getConnection();

            $stmt = $conn->prepare("
                SELECT s.*, p.name as product_name, p.image as product_photo
                FROM sales s
                LEFT JOIN products p ON s.productId = p.id
                WHERE p.sellerId = ?
                ORDER BY s.date DESC
            ");

            $stmt->execute([$businessId]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting sales by business ID: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Register a new business using the old system table structure
     */
    public function registerBusinessOldSystem($userId, $businessName, $description, $location)
    {
        try {
            $conn = $this->db->getConnection();

            $stmt = $conn->prepare("
                INSERT INTO business (name, maelezo, location, status, user_id, date_created) 
                VALUES (?, ?, ?, 'pending', ?, NOW())
            ");

            return $stmt->execute([$businessName, $description, $location, $userId]);
        } catch (PDOException $e) {
            error_log("Error registering business in old system: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update an existing business using the old system table structure
     */
    public function updateBusinessOldSystem($businessId, $businessName, $description, $location, $phone = null, $email = null, $website = null)
    {
        try {
            $conn = $this->db->getConnection();

            $stmt = $conn->prepare("
                UPDATE business 
                SET name = ?, maelezo = ?, location = ?, phone = ?, email = ?, website = ?
                WHERE id = ?
            ");

            return $stmt->execute([$businessName, $description, $location, $phone, $email, $website, $businessId]);
        } catch (PDOException $e) {
            error_log("Error updating business in old system: " . $e->getMessage());
            return false;
        }
    }



    /**
     * Add a product to a business
     */
    public function addProduct($businessId, $productName, $description, $price, $categoryId = null, $imagePath = null, $isOffered = '0', $offer = '')
    {
        try {
            $conn = $this->db->getConnection();

            $stmt = $conn->prepare("
                INSERT INTO products (name, description, amount, image, categoryId, sellerId, status, isOffered, offer, date) 
                VALUES (?, ?, ?, ?, ?, ?, '1', ?, ?, NOW())
            ");

            return $stmt->execute([$productName, $description, $price, $imagePath, $categoryId, $businessId, $isOffered, $offer]);
        } catch (PDOException $e) {
            error_log("Error adding product: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get a single product by ID
     */
    public function getProductById($productId)
    {
        try {
            $conn = $this->db->getConnection();
            $stmt = $conn->prepare("
                SELECT p.*, b.name as business_name
                FROM products p
                LEFT JOIN business b ON p.sellerId = b.id
                WHERE p.id = ?
            ");
            $stmt->execute([$productId]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error getting product by ID: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Update an existing product
     */
    public function updateProduct($productId, $productName, $description, $price, $categoryId = null, $imagePath = null, $status = '1', $isOffered = '0', $offer = '')
    {
        try {
            $conn = $this->db->getConnection();

            $stmt = $conn->prepare("
                UPDATE products 
                SET name = ?, description = ?, amount = ?, image = ?, categoryId = ?, status = ?, isOffered = ?, offer = ?
                WHERE id = ?
            ");

            return $stmt->execute([$productName, $description, $price, $imagePath, $categoryId, $status, $isOffered, $offer, $productId]);
        } catch (PDOException $e) {
            error_log("Error updating product: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Toggle product status (available/not available)
     */
    public function toggleProductStatus($productId, $newStatus)
    {
        try {
            $conn = $this->db->getConnection();

            $stmt = $conn->prepare("
                UPDATE products 
                SET status = ?
                WHERE id = ?
            ");

            return $stmt->execute([$newStatus, $productId]);
        } catch (PDOException $e) {
            error_log("Error toggling product status: " . $e->getMessage());
            return false;
        }
    }

    public function rejectBusiness($businessId, $reason = null)
    {
        try {
            $conn = $this->db->getConnection();

            // Check if business exists
            $stmt = $conn->prepare("SELECT id FROM businesses WHERE id = ?");
            $stmt->execute([$businessId]);
            if (!$stmt->fetch()) {
                return false;
            }

            // Mark as rejected
            $stmt = $conn->prepare("UPDATE businesses SET verification_status = 'rejected', rejection_reason = ?, date_updated = NOW() WHERE id = ?");
            return $stmt->execute([$reason, $businessId]);
        } catch (PDOException $e) {
            error_log("Error rejecting business: " . $e->getMessage());
            return false;
        }
    }

    public function getBusinessById($businessId)
    {
        try {
            $conn = $this->db->getConnection();

            $stmt = $conn->prepare("
                SELECT 
                    b.*, u.first_name, u.last_name, u.email as owner_email
                FROM businesses b
                LEFT JOIN users u ON b.owner_id = u.id
                WHERE b.id = ?
            ");
            $stmt->execute([$businessId]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error getting business by ID: " . $e->getMessage());
            return false;
        }
    }

    public function getBusinessByIdOld($businessId)
    {
        try {
            $conn = $this->db->getConnection();

            $stmt = $conn->prepare("
                SELECT 
                    b.id, b.name, b.location, b.maelezo, b.date_created,
                    u.first_name, u.last_name, u.username
                FROM business b
                LEFT JOIN users u ON b.user_id = u.id
                WHERE b.id = ?
            ");

            $stmt->execute([$businessId]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error getting business by ID (old system): " . $e->getMessage());
            return false;
        }
    }

    public function updateBusinessOld($id, $name, $location, $maelezo)
    {
        try {
            $conn = $this->db->getConnection();

            $stmt = $conn->prepare("
                UPDATE business 
                SET name = ?, location = ?, maelezo = ?
                WHERE id = ?
            ");

            return $stmt->execute([$name, $location, $maelezo, $id]);
        } catch (PDOException $e) {
            error_log("Error updating business (old system): " . $e->getMessage());
            return false;
        }
    }

    public function deleteBusinessOld($id)
    {
        try {
            $conn = $this->db->getConnection();

            $stmt = $conn->prepare("DELETE FROM business WHERE id = ?");

            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("Error deleting business (old system): " . $e->getMessage());
            return false;
        }
    }

    public function getBusinessByOwnerId($ownerId)
    {
        try {
            $conn = $this->db->getConnection();

            $stmt = $conn->prepare("
                SELECT 
                    b.*, u.first_name, u.last_name, u.email as owner_email
                FROM businesses b
                LEFT JOIN users u ON b.owner_id = u.id
                WHERE b.owner_id = ?
            ");
            $stmt->execute([$ownerId]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error getting business by owner ID: " . $e->getMessage());
            return false;
        }
    }

    public function getBusinessesByType($businessType, $page = 1, $perPage = 10)
    {
        try {
            $conn = $this->db->getConnection();
            $offset = ($page - 1) * $perPage;

            $stmt = $conn->prepare("
                SELECT 
                    b.*, u.first_name, u.last_name, u.email as owner_email
                FROM businesses b
                LEFT JOIN users u ON b.owner_id = u.id
                WHERE b.business_type = ? AND b.status = 'active'
                ORDER BY b.date_created DESC 
                LIMIT ? OFFSET ?
            ");
            $stmt->execute([$businessType, $perPage, $offset]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting businesses by type: " . $e->getMessage());
            return [];
        }
    }

    public function getBusinessesByStatus($status, $page = 1, $perPage = 10)
    {
        try {
            $conn = $this->db->getConnection();
            $offset = ($page - 1) * $perPage;

            $stmt = $conn->prepare("
                SELECT 
                    b.*, u.first_name, u.last_name, u.email as owner_email
                FROM businesses b
                LEFT JOIN users u ON b.owner_id = u.id
                WHERE b.status = ?
                ORDER BY b.date_created DESC 
                LIMIT ? OFFSET ?
            ");
            $stmt->execute([$status, $perPage, $offset]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting businesses by status: " . $e->getMessage());
            return [];
        }
    }

    public function getBusinessesByVerificationStatus($verificationStatus, $page = 1, $perPage = 10)
    {
        try {
            $conn = $this->db->getConnection();
            $offset = ($page - 1) * $perPage;

            $stmt = $conn->prepare("
                SELECT 
                    b.*, u.first_name, u.last_name, u.email as owner_email
                FROM businesses b
                LEFT JOIN users u ON b.owner_id = u.id
                WHERE b.verification_status = ?
                ORDER BY b.date_created DESC 
                LIMIT ? OFFSET ?
            ");
            $stmt->execute([$verificationStatus, $perPage, $offset]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting businesses by verification status: " . $e->getMessage());
            return [];
        }
    }

    public function searchBusinesses($searchTerm, $businessType = null, $verificationStatus = null, $page = 1, $perPage = 10)
    {
        try {
            $conn = $this->db->getConnection();
            $offset = ($page - 1) * $perPage;

            $sql = "
                SELECT 
                    b.*, u.first_name, u.last_name, u.email as owner_email
                FROM businesses b
                LEFT JOIN users u ON b.owner_id = u.id
                WHERE (b.business_name LIKE ? OR b.description LIKE ? OR b.location LIKE ? OR u.first_name LIKE ? OR u.last_name LIKE ?)
            ";
            $params = ["%$searchTerm%", "%$searchTerm%", "%$searchTerm%", "%$searchTerm%", "%$searchTerm%"];

            if ($businessType) {
                $sql .= " AND b.business_type = ?";
                $params[] = $businessType;
            }

            if ($verificationStatus) {
                $sql .= " AND b.verification_status = ?";
                $params[] = $verificationStatus;
            }

            $sql .= " ORDER BY b.date_created DESC LIMIT ? OFFSET ?";
            $params[] = $perPage;
            $params[] = $offset;

            $stmt = $conn->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error searching businesses: " . $e->getMessage());
            return [];
        }
    }

    public function getBusinessesByLocation($location, $page = 1, $perPage = 10)
    {
        try {
            $conn = $this->db->getConnection();
            $offset = ($page - 1) * $perPage;

            $stmt = $conn->prepare("
                SELECT 
                    b.*, u.first_name, u.last_name, u.email as owner_email
                FROM businesses b
                LEFT JOIN users u ON b.owner_id = u.id
                WHERE b.location LIKE ? AND b.status = 'active'
                ORDER BY b.date_created DESC 
                LIMIT ? OFFSET ?
            ");
            $stmt->execute(["%$location%", $perPage, $offset]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting businesses by location: " . $e->getMessage());
            return [];
        }
    }

    public function getBusinessReport($startDate = null, $endDate = null)
    {
        try {
            $conn = $this->db->getConnection();

            $sql = "
                SELECT 
                    business_type,
                    verification_status,
                    status,
                    COUNT(*) as count
                FROM businesses
            ";
            $params = [];

            if ($startDate && $endDate) {
                $sql .= " WHERE date_created BETWEEN ? AND ?";
                $params = [$startDate, $endDate];
            }

            $sql .= " GROUP BY business_type, verification_status, status ORDER BY business_type, verification_status";

            $stmt = $conn->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting business report: " . $e->getMessage());
            return [];
        }
    }

    public function getBusinessTypeStats()
    {
        try {
            $conn = $this->db->getConnection();

            $stmt = $conn->prepare("
                SELECT 
                    business_type,
                    COUNT(*) as count
                FROM businesses 
                WHERE status = 'active'
                GROUP BY business_type 
                ORDER BY count DESC
            ");
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting business type stats: " . $e->getMessage());
            return [];
        }
    }
}
