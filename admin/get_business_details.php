<?php
require_once __DIR__ . "/../middleware/AuthMiddleware.php";
require_once __DIR__ . "/../models/Business.php";

// Check if user is admin
$auth = new AuthMiddleware();
$auth->requireRole('admin');

// Set JSON header
header('Content-Type: application/json');

// Check if ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'ID ya biashara haijatolewa']);
    exit;
}

$businessId = (int)$_GET['id'];

try {
    // Initialize model
    $businessModel = new Business();

    // Get business details
    $business = $businessModel->getBusinessByIdOld($businessId);

    if ($business) {
        // Format date
        $business['date_created'] = date('d/m/Y H:i', strtotime($business['date_created']));

        // Format owner name
        $ownerName = '';
        if (!empty($business['first_name']) && !empty($business['last_name'])) {
            $ownerName = $business['first_name'] . ' ' . $business['last_name'];
        } elseif (!empty($business['username'])) {
            $ownerName = $business['username'];
        } else {
            $ownerName = 'N/A';
        }
        $business['owner_name'] = $ownerName;

        echo json_encode([
            'success' => true,
            'business' => $business
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Biashara haijapatikana'
        ]);
    }
} catch (Exception $e) {
    error_log("Error getting business details: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Imefeli kupata maelezo ya biashara'
    ]);
}
