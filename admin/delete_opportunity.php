<?php
require_once __DIR__ . "/../middleware/AuthMiddleware.php";
require_once __DIR__ . "/../models/Fursa.php";

// Check if user is admin
$auth = new AuthMiddleware();
$auth->requireRole('admin');

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['opportunity_id'])) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Opportunity ID haijatolewa'
    ]);
    exit;
}

$opportunityId = (int)$input['opportunity_id'];

if (!$opportunityId) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Opportunity ID si sahihi'
    ]);
    exit;
}

try {
    // Initialize Fursa model
    $fursaModel = new Fursa();

    // Delete opportunity
    $result = $fursaModel->deleteOpportunity($opportunityId);

    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Fursa imefutwa kikamilifu'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Imefeli kufuta fursa'
        ]);
    }
} catch (Exception $e) {
    error_log("Error deleting opportunity: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Kuna tatizo la kufuta fursa'
    ]);
}
