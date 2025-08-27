<?php
require_once __DIR__ . "/../config/database.php";

/**
 * User Model - Handles user profile validation and completion requirements
 */
class User
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
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
}
