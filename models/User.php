<?php
require_once __DIR__ . "/../config/database.php";

/**
 * User Model - Handles user profile validation and completion requirements
 */
class User
{
    private $pdo;

    public function __construct($pdo = null)
    {
        if ($pdo === null) {
            // Try to get PDO from global scope or create a new connection
            global $pdo;
            if (isset($pdo)) {
                $this->pdo = $pdo;
            } else {
                // Try to include database configuration
                if (file_exists(__DIR__ . '/../config/database.php')) {
                    require_once __DIR__ . '/../config/database.php';
                    $database = Database::getInstance();
                    $this->pdo = $database->getConnection();
                } else {
                    throw new Exception('PDO connection not available and cannot be created');
                }
            }
        } else {
            $this->pdo = $pdo;
        }
    }

    /**
     * Get user by ID
     */
    public function getUserById($userId)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Authenticate user with email and password
     */
    public function authenticateUser($email, $password)
    {
        try {
            // Use the same query as the old working system - no status field check
            $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Use 'pass' field like the old system, not 'password'
            if ($user && password_verify($password, $user['pass'])) {
                return $user;
            }

            return false;
        } catch (Exception $e) {
            error_log('Authentication error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get user by email
     */
    public function getUserByEmail($email)
    {
        try {
            // Use the same query as the old working system - no status field check
            $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log('Get user by email error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get user profile completion status
     */
    public function getProfileCompletionStatus($userId)
    {
        $user = $this->getUserById($userId);
        if (!$user) {
            return false;
        }

        $requiredFields = [
            'first_name' => $user['first_name'],
            'last_name' => $user['last_name'],
            'phone' => $user['phone'],
            'region' => $user['region'],
            'business' => $user['business'],
            'gender' => $user['gender'],
            'date_of_birth' => $user['date_of_birth']
        ];

        $completedFields = 0;
        $totalFields = count($requiredFields);

        foreach ($requiredFields as $field => $value) {
            if (!empty($value) && $value !== 'null' && $value !== null) {
                $completedFields++;
            }
        }

        return [
            'completed' => $completedFields,
            'total' => $totalFields,
            'percentage' => round(($completedFields / $totalFields) * 100, 1),
            'missing_fields' => array_keys(array_filter($requiredFields, function ($value) {
                return empty($value) || $value === 'null' || $value === null;
            }))
        ];
    }

    /**
     * Check if user can perform specific actions
     */
    public function canPerformAction($userId, $action)
    {
        $user = $this->getUserById($userId);
        if (!$user) {
            return false;
        }

        $actionRequirements = [
            'download_certificate' => ['first_name', 'last_name', 'phone', 'region'],
            'contact_expert' => ['first_name', 'last_name', 'phone', 'region'],
            'sell_product' => ['first_name', 'last_name', 'phone', 'region', 'business'],
            'buy_product' => ['first_name', 'last_name', 'phone', 'region'],
            'study_course' => ['first_name', 'last_name', 'phone']
        ];

        if (!isset($actionRequirements[$action])) {
            return true; // No specific requirements
        }

        $requiredFields = $actionRequirements[$action];

        foreach ($requiredFields as $field) {
            if (empty($user[$field]) || $user[$field] === 'null' || $user[$field] === null) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get missing fields for specific action
     */
    public function getMissingFieldsForAction($userId, $action)
    {
        $user = $this->getUserById($userId);
        if (!$user) {
            return [];
        }

        $actionRequirements = [
            'download_certificate' => ['first_name', 'last_name', 'phone', 'region'],
            'contact_expert' => ['first_name', 'last_name', 'phone', 'region'],
            'sell_product' => ['first_name', 'last_name', 'phone', 'region', 'business'],
            'buy_product' => ['first_name', 'last_name', 'phone', 'region'],
            'study_course' => ['first_name', 'last_name', 'phone']
        ];

        if (!isset($actionRequirements[$action])) {
            return [];
        }

        $requiredFields = $actionRequirements[$action];
        $missingFields = [];

        foreach ($requiredFields as $field) {
            if (empty($user[$field]) || $user[$field] === 'null' || $user[$field] === null) {
                $missingFields[] = $field;
            }
        }

        return $missingFields;
    }

    /**
     * Update user profile
     */
    public function updateProfile($userId, $data)
    {
        $allowedFields = [
            'first_name',
            'last_name',
            'phone',
            'region',
            'business',
            'gender',
            'date_of_birth',
            'bio'
        ];

        $updateFields = [];
        $values = [];

        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $updateFields[] = "$field = ?";
                $values[] = $data[$field];
            }
        }

        if (empty($updateFields)) {
            return false;
        }

        $values[] = $userId;
        $sql = "UPDATE users SET " . implode(', ', $updateFields) . " WHERE id = ?";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($values);
    }

    /**
     * Get field labels in Swahili
     */
    public function getFieldLabels()
    {
        return [
            'first_name' => 'Jina la Kwanza',
            'last_name' => 'Jina la Mwisho',
            'phone' => 'Nambari ya Simu',
            'region' => 'Mkoa',
            'business' => 'Biashara',
            'gender' => 'Jinsia',
            'date_of_birth' => 'Tarehe ya Kuzaliwa',
            'bio' => 'Maelezo Binafsi'
        ];
    }

    /**
     * Get regions in Tanzania
     */
    public function getRegions()
    {
        return [
            'Arusha',
            'Dar es Salaam',
            'Dodoma',
            'Geita',
            'Iringa',
            'Kagera',
            'Katavi',
            'Kigoma',
            'Kilimanjaro',
            'Lindi',
            'Manyara',
            'Mara',
            'Mbeya',
            'Morogoro',
            'Mtwara',
            'Mwanza',
            'Njombe',
            'Pemba North',
            'Pemba South',
            'Pwani',
            'Rukwa',
            'Ruvuma',
            'Shinyanga',
            'Simiyu',
            'Singida',
            'Songwe',
            'Tabora',
            'Tanga',
            'Unguja North',
            'Unguja South',
            'Zanzibar Central',
            'Zanzibar North',
            'Zanzibar South',
            'Zanzibar West'
        ];
    }

    /**
     * Get gender options
     */
    public function getGenderOptions()
    {
        return [
            'male' => 'Mwanaume',
            'female' => 'Mwanamke',
            'other' => 'Nyingine'
        ];
    }

    /**
     * Create new user
     */
    public function createUser($userData)
    {
        try {
            $requiredFields = ['email', 'password', 'first_name', 'last_name'];
            foreach ($requiredFields as $field) {
                if (empty($userData[$field])) {
                    throw new Exception("Field $field is required");
                }
            }

            // Hash password
            $userData['password'] = password_hash($userData['password'], PASSWORD_DEFAULT);

            // Set default values
            $userData['status'] = $userData['status'] ?? '1';
            $userData['role'] = $userData['role'] ?? 'user';
            $userData['created_at'] = date('Y-m-d H:i:s');

            $fields = implode(', ', array_keys($userData));
            $placeholders = ':' . implode(', :', array_keys($userData));

            $sql = "INSERT INTO users ($fields) VALUES ($placeholders)";
            $stmt = $this->pdo->prepare($sql);

            if ($stmt->execute($userData)) {
                return $this->pdo->lastInsertId();
            }

            return false;
        } catch (Exception $e) {
            error_log('Create user error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Update user password
     */
    public function updatePassword($userId, $newPassword)
    {
        try {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt = $this->pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            return $stmt->execute([$hashedPassword, $userId]);
        } catch (Exception $e) {
            error_log('Update password error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Check if email exists
     */
    public function emailExists($email, $excludeUserId = null)
    {
        try {
            $sql = "SELECT id FROM users WHERE email = ?";
            $params = [$email];

            if ($excludeUserId) {
                $sql .= " AND id != ?";
                $params[] = $excludeUserId;
            }

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetch() !== false;
        } catch (Exception $e) {
            error_log('Email exists check error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get total number of users
     */
    public function getTotalUsers()
    {
        try {
            $stmt = $this->pdo->prepare("SELECT COUNT(*) as total FROM users");
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'] ?? 0;
        } catch (Exception $e) {
            error_log('Get total users error: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get all users with pagination
     */
    public function getAllUsers($page = 1, $perPage = 20)
    {
        try {
            $offset = ($page - 1) * $perPage;

            $stmt = $this->pdo->prepare("
                SELECT 
                    id, 
                    first_name, 
                    last_name, 
                    email, 
                    phone, 
                    role, 
                    status, 
                    date_created,
                    last_login
                FROM users 
                ORDER BY date_created DESC 
                LIMIT ? OFFSET ?
            ");

            $stmt->execute([$perPage, $offset]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log('Get all users error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get user statistics by role
     */
    public function getUserStatsByRole()
    {
        try {
            $stmt = $this->pdo->prepare("SELECT role, COUNT(*) as count FROM users GROUP BY role");
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $stats = [];
            foreach ($results as $row) {
                $stats[$row['role']] = $row['count'];
            }

            // Ensure all roles are present
            $stats['admin'] = $stats['admin'] ?? 0;
            $stats['user'] = $stats['user'] ?? 0;
            $stats['expert'] = $stats['expert'] ?? 0;

            return $stats;
        } catch (Exception $e) {
            error_log('Get user stats by role error: ' . $e->getMessage());
            return ['admin' => 0, 'user' => 0, 'expert' => 0];
        }
    }

    /**
     * Get total number of instructors
     */
    public function getTotalInstructors()
    {
        try {
            $stmt = $this->pdo->prepare("SELECT COUNT(*) as total FROM users WHERE role = 'expert' OR role = 'instructor'");
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'] ?? 0;
        } catch (Exception $e) {
            error_log('Get total instructors error: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get users by role
     */
    public function getUsersByRole($role, $limit = null, $offset = 0)
    {
        try {
            $sql = "SELECT * FROM users WHERE role = ? ORDER BY id DESC";
            if ($limit) {
                $sql .= " LIMIT ? OFFSET ?";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([$role, $limit, $offset]);
            } else {
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([$role]);
            }

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log('Get users by role error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get recent users
     */
    public function getRecentUsers($limit = 10)
    {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM users ORDER BY id DESC LIMIT ?");
            $stmt->execute([$limit]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log('Get recent users error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Search users
     */
    public function searchUsers($searchTerm, $limit = 20)
    {
        try {
            $searchTerm = '%' . $searchTerm . '%';
            $stmt = $this->pdo->prepare("
                SELECT * FROM users 
                WHERE first_name LIKE ? OR last_name LIKE ? OR email LIKE ? OR phone LIKE ?
                ORDER BY id DESC LIMIT ?
            ");
            $stmt->execute([$searchTerm, $searchTerm, $searchTerm, $searchTerm, $limit]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log('Search users error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Update user role
     */
    public function updateUserRole($userId, $newRole)
    {
        try {
            $stmt = $this->pdo->prepare("UPDATE users SET role = ? WHERE id = ?");
            return $stmt->execute([$newRole, $userId]);
        } catch (Exception $e) {
            error_log('Update user role error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete user
     */
    public function deleteUser($userId)
    {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = ?");
            return $stmt->execute([$userId]);
        } catch (Exception $e) {
            error_log('Delete user error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Toggle user status (active/inactive)
     */
    public function toggleUserStatus($userId)
    {
        try {
            // Get current user status
            $stmt = $this->pdo->prepare("SELECT status FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch();

            if (!$user) {
                return false;
            }

            // Toggle status
            $newStatus = ($user['status'] === 'active') ? 'inactive' : 'active';

            // Update status
            $stmt = $this->pdo->prepare("UPDATE users SET status = ?, updated_at = NOW() WHERE id = ?");
            $result = $stmt->execute([$newStatus, $userId]);

            return $result;
        } catch (PDOException $e) {
            error_log("Error toggling user status: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Request expert role for user
     */
    public function requestExpertRole($userId, $bio)
    {
        try {
            // First check if user exists
            $user = $this->getUserById($userId);
            if (!$user) {
                return false;
            }

            // Check if expert request already exists
            $stmt = $this->pdo->prepare("SELECT id FROM expert_requests WHERE user_id = ? AND status IN ('pending', 'approved')");
            $stmt->execute([$userId]);
            if ($stmt->fetch()) {
                return false; // Request already exists
            }

            // Create expert request
            $stmt = $this->pdo->prepare("INSERT INTO expert_requests (user_id, bio, status, created_at) VALUES (?, ?, 'pending', ?)");
            $result = $stmt->execute([$userId, $bio, date('Y-m-d H:i:s')]);

            if ($result) {
                // Update user role to pending_expert
                $stmt = $this->pdo->prepare("UPDATE users SET role = 'pending_expert' WHERE id = ?");
                $stmt->execute([$userId]);
                return true;
            }

            return false;
        } catch (Exception $e) {
            error_log('Request expert role error: ' . $e->getMessage());
            return false;
        }
    }
}
