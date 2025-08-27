<?php

/**
 * Profile Update Handler
 * Processes profile completion form submissions
 */

require_once __DIR__ . '/config/init.php';
require_once __DIR__ . '/models/User.php';

// Set JSON response header
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['userId'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Unafahamika. Tafadhali ingia tena.'
    ]);
    exit;
}

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Njia si sahihi.'
    ]);
    exit;
}

try {
    // Initialize User model
    $userModel = new User($pdo);

    // Get user ID from session
    $userId = $_SESSION['userId'];

    // Validate user exists
    $user = $userModel->getUserById($userId);
    if (!$user) {
        echo json_encode([
            'success' => false,
            'message' => 'Mtumiaji hajulikani.'
        ]);
        exit;
    }

    // Prepare data for update
    $updateData = [];

    // Required fields
    $requiredFields = ['first_name', 'last_name', 'phone', 'region'];
    foreach ($requiredFields as $field) {
        if (isset($_POST[$field]) && !empty(trim($_POST[$field]))) {
            $updateData[$field] = trim($_POST[$field]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Sehemu ya ' . $userModel->getFieldLabels()[$field] . ' inahitajika.'
            ]);
            exit;
        }
    }

    // Optional fields
    $optionalFields = ['business', 'gender', 'date_of_birth', 'bio'];
    foreach ($optionalFields as $field) {
        if (isset($_POST[$field]) && !empty(trim($_POST[$field]))) {
            $updateData[$field] = trim($_POST[$field]);
        }
    }

    // Validate phone number format (Tanzania)
    if (isset($updateData['phone'])) {
        $phone = $updateData['phone'];
        // Remove any non-digit characters
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Check if it's a valid Tanzania phone number
        if (strlen($phone) < 9 || strlen($phone) > 12) {
            echo json_encode([
                'success' => false,
                'message' => 'Nambari ya simu si sahihi. Tafadhali jaza nambari sahihi ya Tanzania.'
            ]);
            exit;
        }

        // Format phone number
        if (strlen($phone) === 9) {
            $phone = '255' . $phone;
        } elseif (strlen($phone) === 10 && substr($phone, 0, 1) === '0') {
            $phone = '255' . substr($phone, 1);
        }

        $updateData['phone'] = $phone;
    }

    // Validate date of birth if provided
    if (isset($updateData['date_of_birth'])) {
        $dob = $updateData['date_of_birth'];
        $dobDate = DateTime::createFromFormat('Y-m-d', $dob);

        if (!$dobDate || $dobDate->format('Y-m-d') !== $dob) {
            echo json_encode([
                'success' => false,
                'message' => 'Tarehe ya kuzaliwa si sahihi.'
            ]);
            exit;
        }

        // Check if user is at least 13 years old
        $today = new DateTime();
        $age = $today->diff($dobDate)->y;
        if ($age < 13) {
            echo json_encode([
                'success' => false,
                'message' => 'Umri wako haufikii miaka 13. Huna ruhusa kutumia jukwaa hili.'
            ]);
            exit;
        }
    }

    // Update user profile
    $updateResult = $userModel->updateProfile($userId, $updateData);

    if ($updateResult) {
        // Update session variables
        $_SESSION['userFirstName'] = $updateData['first_name'] ?? $_SESSION['userFirstName'] ?? '';
        $_SESSION['userLastName'] = $updateData['last_name'] ?? $_SESSION['userLastName'] ?? '';
        $_SESSION['userPhone'] = $updateData['phone'] ?? $_SESSION['userPhone'] ?? '';
        $_SESSION['userRegion'] = $updateData['region'] ?? $_SESSION['userRegion'] ?? '';
        $_SESSION['userBusiness'] = $updateData['business'] ?? $_SESSION['userBusiness'] ?? '';
        $_SESSION['userGender'] = $updateData['gender'] ?? $_SESSION['userGender'] ?? '';
        $_SESSION['userDateOfBirth'] = $updateData['date_of_birth'] ?? $_SESSION['userDateOfBirth'] ?? '';
        $_SESSION['userBio'] = $updateData['bio'] ?? $_SESSION['userBio'] ?? '';

        // Log the profile update
        $logMessage = "User ID $userId updated their profile";
        error_log($logMessage);

        echo json_encode([
            'success' => true,
            'message' => 'Wasifu wako umekamilishwa kwa mafanikio!',
            'data' => [
                'user_id' => $userId,
                'updated_fields' => array_keys($updateData)
            ]
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Kuna tatizo katika kuhifadhi wasifu. Tafadhali jaribu tena.'
        ]);
    }
} catch (Exception $e) {
    error_log('Profile update error: ' . $e->getMessage());

    echo json_encode([
        'success' => false,
        'message' => 'Kuna tatizo la kiufundi. Tafadhali jaribu tena baada ya muda.'
    ]);
}
