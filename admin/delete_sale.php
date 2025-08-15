<?php
require_once __DIR__ . "/../middleware/AuthMiddleware.php";
require_once __DIR__ . "/../models/Sales.php";

// Check if user is admin
$auth = new AuthMiddleware();
$auth->requireRole('admin');

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);
$saleId = $input['sale_id'] ?? null;

if (!$saleId) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Sale ID haijatolewa'
    ]);
    exit;
}

try {
    // Initialize Sales model
    $salesModel = new Sales();

    // Delete sale
    $result = $salesModel->deleteSale($saleId);

    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Sale imefutwa kikamilifu'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Imefeli kufuta sale'
        ]);
    }
} catch (Exception $e) {
    error_log("Error deleting sale: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Kuna tatizo la kufuta sale'
    ]);
}
