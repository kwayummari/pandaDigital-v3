<?php
require_once __DIR__ . "/../middleware/AuthMiddleware.php";
require_once __DIR__ . "/../models/Business.php";

// Check if user is admin
$auth = new AuthMiddleware();
$auth->requireRole('admin');

// Set content type to JSON
header('Content-Type: application/json');

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed'
    ]);
    exit;
}

// Get business ID from POST data
$businessId = $_POST['id'] ?? null;

if (!$businessId || !is_numeric($businessId)) {
    echo json_encode([
        'success' => false,
        'message' => 'ID ya biashara si sahihi'
    ]);
    exit;
}

try {
    // Initialize model
    $businessModel = new Business();

    // Get business details first to check if it exists
    $business = $businessModel->getBusinessByIdOld($businessId);

    if (!$business) {
        echo json_encode([
            'success' => false,
            'message' => 'Biashara haijulikani'
        ]);
        exit;
    }

    // Delete the business
    if ($businessModel->deleteBusinessOld($businessId)) {
        echo json_encode([
            'success' => true,
            'message' => 'Biashara imefutwa kikamilifu!'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Imefeli kufuta biashara. Tafadhali jaribu tena.'
        ]);
    }
} catch (Exception $e) {
    error_log("Error deleting business: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Kuna tatizo la mtandao. Jaribu tena.'
    ]);
}
