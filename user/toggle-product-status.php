<?php
require_once __DIR__ . "/../config/init.php";
require_once __DIR__ . "/../middleware/AuthMiddleware.php";
require_once __DIR__ . "/../models/Business.php";

// Ensure user is logged in
$auth = new AuthMiddleware();
$auth->requireRole('user');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$currentUser = $auth->getCurrentUser();
$productId = $_POST['product_id'] ?? null;
$businessId = $_POST['business_id'] ?? null;
$newStatus = $_POST['status'] ?? null;

// Validate input
if (!$productId || !$businessId || !in_array($newStatus, ['0', '1'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid input parameters']);
    exit;
}

// Initialize business model
$businessModel = new Business();

// Validate business ownership
$userBusinesses = $businessModel->getBusinessesByUserId($currentUser['id']);
$businessExists = false;

foreach ($userBusinesses as $business) {
    if ($business['id'] == $businessId) {
        $businessExists = true;
        break;
    }
}

if (!$businessExists) {
    echo json_encode(['success' => false, 'message' => 'Business not found or access denied']);
    exit;
}

// Get product details to verify ownership
$product = $businessModel->getProductById($productId);

if (!$product || $product['sellerId'] != $businessId) {
    echo json_encode(['success' => false, 'message' => 'Product not found or access denied']);
    exit;
}

// Toggle the product status
if ($businessModel->toggleProductStatus($productId, $newStatus)) {
    $statusText = $newStatus == '1' ? 'Iko Soko' : 'Haiko Soko';
    echo json_encode([
        'success' => true, 
        'message' => "Hali ya bidhaa imebadilishwa kuwa: $statusText",
        'new_status' => $newStatus
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Kuna tatizo la kiufundi. Jaribu tena.']);
}
?>
