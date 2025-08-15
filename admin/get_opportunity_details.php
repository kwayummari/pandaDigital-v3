<?php
require_once __DIR__ . "/../middleware/AuthMiddleware.php";
require_once __DIR__ . "/../models/Fursa.php";

// Check if user is admin
$auth = new AuthMiddleware();
$auth->requireRole('admin');

// Get opportunity ID from request
$opportunityId = $_GET['id'] ?? null;

if (!$opportunityId) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Opportunity ID haijatolewa'
    ]);
    exit;
}

try {
    // Initialize Fursa model
    $fursaModel = new Fursa();

    // Get opportunity details
    $opportunity = $fursaModel->getOpportunityById($opportunityId);

    // Debug logging
    error_log("Opportunity ID requested: " . $opportunityId);
    error_log("Opportunity data returned: " . json_encode($opportunity));

    if (!$opportunity) {
        echo json_encode([
            'success' => false,
            'message' => 'Opportunity haijapatikana'
        ]);
        exit;
    }

    // Return opportunity data
    echo json_encode([
        'success' => true,
        'opportunity' => $opportunity
    ]);
} catch (Exception $e) {
    error_log("Error getting opportunity details: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Kuna tatizo la kupata maelezo ya opportunity'
    ]);
}
