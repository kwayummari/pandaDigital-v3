<?php
require_once __DIR__ . "/../config/database.php";

class User
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    /**
     * Create a new user
     */
    public function createUser($data)
    {
        try {
            $conn = $this->db->getConnection();

            // Hash the password
            $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

            // Handle expert authorization and account status
            $expertAuth = isset($data['expert_authorization']) ? $data['expert_authorization'] : null;
            $accountStatus = isset($data['account_status']) ? $data['account_status'] : 'active';

            $stmt = $conn->prepare("
                INSERT INTO users (
                    first_name, last_name, email, phone, pass, role, region, business, 
                    gender, date_of_birth, expert_authorization, account_status, 
                    date_created, updated_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
            ");

            $result = $stmt->execute([
                $data['first_name'],
                $data['last_name'],
                $data['email'],
                $data['phone'],
                $hashedPassword,
                $data['role'] ?? 'user',
                $data['region'] ?? null,
                $data['business'] ?? null,
                $data['gender'] ?? null,
                $data['date_of_birth'] ?? null,
                $expertAuth,
                $accountStatus
            ]);

            if ($result) {
                return $conn->lastInsertId();
            }

            return false;
        } catch (PDOException $e) {
            error_log("Error creating user: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Authenticate user login
     */
    public function authenticateUser($email, $password)
    {
        try {
            $conn = $this->db->getConnection();

            $stmt = $conn->prepare("
                SELECT id, first_name, last_name, email, phone, pass, role, region, business, 
                       profile_photo, expert_authorization, date_created
                FROM users 
                WHERE email = ?
            ");

            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['pass'])) {
                // Remove password from user data before returning
                unset($user['pass']);
                return $user;
            }

            return false;
        } catch (PDOException $e) {
            error_log("Error authenticating user: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get user by ID
     */
    public function getUserById($userId)
    {
        try {
            $conn = $this->db->getConnection();

            $stmt = $conn->prepare("
                SELECT id, first_name, last_name, email, phone, role, region, business, 
                       profile_photo, expert_authorization, date_created
                FROM users 
                WHERE id = ?
            ");

            $stmt->execute([$userId]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error fetching user: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get user by email
     */
    public function getUserByEmail($email)
    {
        try {
            $conn = $this->db->getConnection();

            $stmt = $conn->prepare("
                SELECT id, first_name, last_name, email, phone, role, region, business, 
                       profile_photo, expert_authorization, date_created
                FROM users 
                WHERE email = ?
            ");

            $stmt->execute([$email]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error fetching user by email: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update user profile
     */
    public function updateUser($userId, $data)
    {
        try {
            $conn = $this->db->getConnection();

            $updateFields = [];
            $params = [];

            if (isset($data['first_name'])) {
                $updateFields[] = "first_name = ?";
                $params[] = $data['first_name'];
            }

            if (isset($data['last_name'])) {
                $updateFields[] = "last_name = ?";
                $params[] = $data['last_name'];
            }

            if (isset($data['phone'])) {
                $updateFields[] = "phone = ?";
                $params[] = $data['phone'];
            }

            if (isset($data['region'])) {
                $updateFields[] = "region = ?";
                $params[] = $data['region'];
            }

            if (isset($data['business'])) {
                $updateFields[] = "business = ?";
                $params[] = $data['business'];
            }

            if (isset($data['profile_photo'])) {
                $updateFields[] = "profile_photo = ?";
                $params[] = $data['profile_photo'];
            }

            if (empty($updateFields)) {
                return false;
            }

            $updateFields[] = "updated_at = NOW()";
            $params[] = $userId;

            $sql = "UPDATE users SET " . implode(", ", $updateFields) . " WHERE id = ?";

            $stmt = $conn->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("Error updating user: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Change user password
     */
    public function changePassword($userId, $currentPassword, $newPassword)
    {
        try {
            $conn = $this->db->getConnection();

            // First verify current password
            $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch();

            if (!$user || !password_verify($currentPassword, $user['password'])) {
                return false;
            }

            // Hash new password and update
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("UPDATE users SET password = ?, updated_at = NOW() WHERE id = ?");
            return $stmt->execute([$hashedPassword, $userId]);
        } catch (PDOException $e) {
            error_log("Error changing password: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Check if email already exists
     */
    public function emailExists($email, $excludeUserId = null)
    {
        try {
            $conn = $this->db->getConnection();

            $sql = "SELECT id FROM users WHERE email = ?";
            $params = [$email];

            if ($excludeUserId) {
                $sql .= " AND id != ?";
                $params[] = $excludeUserId;
            }

            $stmt = $conn->prepare($sql);
            $stmt->execute($params);

            return $stmt->fetch() ? true : false;
        } catch (PDOException $e) {
            error_log("Error checking email existence: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Request expert role upgrade
     */
    public function requestExpertRole($userId, $bio = '')
    {
        try {
            $conn = $this->db->getConnection();

            $stmt = $conn->prepare("
                UPDATE users 
                SET role = 'expert', 
                    expert_authorization = '0', 
                    bio = ?, 
                    account_status = 'pending',
                    updated_at = NOW()
                WHERE id = ?
            ");

            return $stmt->execute([$bio, $userId]);
        } catch (PDOException $e) {
            error_log("Error requesting expert role: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Approve expert role
     */
    public function approveExpertRole($userId)
    {
        try {
            $conn = $this->db->getConnection();

            $stmt = $conn->prepare("
                UPDATE users 
                SET expert_authorization = '1', 
                    account_status = 'active',
                    updated_at = NOW()
                WHERE id = ? AND role = 'expert'
            ");

            return $stmt->execute([$userId]);
        } catch (PDOException $e) {
            error_log("Error approving expert role: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Reject expert role
     */
    public function rejectExpertRole($userId)
    {
        try {
            $conn = $this->db->getConnection();

            $stmt = $conn->prepare("
                UPDATE users 
                SET role = 'user', 
                    expert_authorization = NULL, 
                    bio = 'none',
                    account_status = 'active',
                    updated_at = NOW()
                WHERE id = ? AND role = 'expert'
            ");

            return $stmt->execute([$userId]);
        } catch (PDOException $e) {
            error_log("Error rejecting expert role: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get pending expert requests
     */
    public function getPendingExpertRequests($limit = 50)
    {
        try {
            $conn = $this->db->getConnection();

            $stmt = $conn->prepare("
                SELECT id, first_name, last_name, email, phone, region, business, bio, date_created
                FROM users 
                WHERE role = 'expert' 
                AND expert_authorization = '0' 
                AND account_status = 'pending'
                ORDER BY date_created ASC
                LIMIT ?
            ");

            $stmt->execute([$limit]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error fetching pending expert requests: " . $e->getMessage());
            return [];
        }
    }

    public function getTotalUsers()
    {
        try {
            $conn = $this->db->getConnection();
            $stmt = $conn->prepare("SELECT COUNT(*) as total FROM users");
            $stmt->execute();
            $result = $stmt->fetch();
            return $result['total'] ?? 0;
        } catch (PDOException $e) {
            error_log("Error getting total users: " . $e->getMessage());
            return 0;
        }
    }

    public function getUserStatsByRole()
    {
        try {
            $conn = $this->db->getConnection();
            $stmt = $conn->prepare("
                SELECT role, COUNT(*) as count 
                FROM users 
                GROUP BY role
            ");
            $stmt->execute();
            $results = $stmt->fetchAll();

            $stats = [];
            foreach ($results as $result) {
                $stats[$result['role']] = $result['count'];
            }

            return $stats;
        } catch (PDOException $e) {
            error_log("Error getting user stats by role: " . $e->getMessage());
            return [];
        }
    }

    public function getRecentRegistrations($limit = 5)
    {
        try {
            $conn = $this->db->getConnection();
            $stmt = $conn->prepare("
                SELECT id, first_name, last_name, email, role, date_created
                FROM users 
                ORDER BY date_created DESC 
                LIMIT ?
            ");
            $stmt->execute([$limit]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting recent registrations: " . $e->getMessage());
            return [];
        }
    }

    public function getAllUsers($page = 1, $perPage = 20)
    {
        try {
            $conn = $this->db->getConnection();
            $offset = ($page - 1) * $perPage;

            $stmt = $conn->prepare("
                SELECT id, first_name, last_name, email, phone, role, account_status, region, date_created
                FROM users 
                ORDER BY id DESC 
                LIMIT ? OFFSET ?
            ");
            $stmt->execute([$perPage, $offset]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting all users: " . $e->getMessage());
            return [];
        }
    }

    public function getAllUsersForExport()
    {
        try {
            $conn = $this->db->getConnection();

            $stmt = $conn->prepare("
                SELECT id, first_name, last_name, email, phone, role, account_status, region, 
                       date_created, last_login, login_count, bio, expert_authorization, isSeller
                FROM users 
                ORDER BY id DESC
            ");
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting all users for export: " . $e->getMessage());
            return [];
        }
    }

    public function deleteUser($userId)
    {
        try {
            $conn = $this->db->getConnection();

            // Check if user exists and is not the current user
            $stmt = $conn->prepare("SELECT id FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            if (!$stmt->fetch()) {
                return false;
            }

            // Delete user (you might want to soft delete instead)
            $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
            return $stmt->execute([$userId]);
        } catch (PDOException $e) {
            error_log("Error deleting user: " . $e->getMessage());
            return false;
        }
    }

    public function toggleUserStatus($userId)
    {
        try {
            $conn = $this->db->getConnection();

            // Get current status
            $stmt = $conn->prepare("SELECT account_status FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch();

            if (!$user) {
                return false;
            }

            // Toggle status
            $newStatus = $user['account_status'] === 'active' ? 'suspended' : 'active';

            $stmt = $conn->prepare("UPDATE users SET account_status = ? WHERE id = ?");
            return $stmt->execute([$newStatus, $userId]);
        } catch (PDOException $e) {
            error_log("Error toggling user status: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get user profile photo URL
     */
    public function getProfilePhotoUrl($profilePhoto)
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
}
