<?php
require_once __DIR__ . "/../middleware/AuthMiddleware.php";
require_once __DIR__ . "/../models/Sales.php";

// Check if user is admin
$auth = new AuthMiddleware();
$auth->requireRole('admin');

// Get sale ID from request
$saleId = $_GET['id'] ?? null;

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

    // Get sale details
    $sale = $salesModel->getSaleById($saleId);

    // Debug logging
    error_log("Sale ID requested: " . $saleId);
    error_log("Sale data returned: " . json_encode($sale));

    if (!$sale) {
        echo json_encode([
            'success' => false,
            'message' => 'Sale haijapatikana'
        ]);
        exit;
    }

    // Return sale data
    echo json_encode([
        'success' => true,
        'sale' => $sale
    ]);
} catch (Exception $e) {
    error_log("Error getting sale details: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Kuna tatizo la kupata maelezo ya sale'
    ]);
}
