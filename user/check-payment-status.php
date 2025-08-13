<?php
require_once __DIR__ . "/../middleware/AuthMiddleware.php";
require_once __DIR__ . "/../services/PaymentService.php";
require_once __DIR__ . "/../config/init.php";

// Set content type to JSON
header('Content-Type: application/json');

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

// Get JSON input
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid JSON data']);
    exit();
}

// Validate required fields
$requiredFields = ['courseId', 'userId'];
foreach ($requiredFields as $field) {
    if (!isset($data[$field]) || empty($data[$field])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => "Field '$field' is required"]);
        exit();
    }
}

try {
    $auth = new AuthMiddleware();
    $auth->requireRole('user');

    $currentUser = $auth->getCurrentUser();

    // Verify user ID matches current user
    if ($currentUser['id'] != $data['userId']) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
        exit();
    }

    $paymentService = new PaymentService();

    // Check if user has paid course access
    $hasPaidAccess = $paymentService->hasPaidAccess($data['userId'], $data['courseId']);

    if ($hasPaidAccess) {
        echo json_encode([
            'success' => true,
            'paid' => true,
            'message' => 'Payment completed and user has course access'
        ]);
    } else {
        // Check if there's a pending transaction
        $pendingTransaction = $paymentService->getPendingTransaction($data['userId'], $data['courseId']);

        if ($pendingTransaction) {
            echo json_encode([
                'success' => true,
                'paid' => false,
                'message' => 'Payment still processing',
                'transactionStatus' => $pendingTransaction['status']
            ]);
        } else {
            echo json_encode([
                'success' => true,
                'paid' => false,
                'message' => 'No payment transaction found'
            ]);
        }
    }
} catch (Exception $e) {
    error_log("Payment status check error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Internal server error']);
}
