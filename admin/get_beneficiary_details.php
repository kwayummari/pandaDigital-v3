<?php
require_once __DIR__ . "/../middleware/AuthMiddleware.php";
require_once __DIR__ . "/../models/Beneficiary.php";

// Check if user is admin
$auth = new AuthMiddleware();
$auth->requireRole('admin');

// Set JSON header
header('Content-Type: application/json');

// Check if ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'ID ya mwanufaika haijatolewa']);
    exit;
}

$beneficiaryId = (int)$_GET['id'];

try {
    // Initialize model
    $beneficiaryModel = new Beneficiary();

    // Get beneficiary details
    $beneficiary = $beneficiaryModel->getWanufaikaById($beneficiaryId);

    if ($beneficiary) {
        // Format date
        $beneficiary['date_created'] = date('d/m/Y H:i', strtotime($beneficiary['date_created']));

        echo json_encode([
            'success' => true,
            'beneficiary' => $beneficiary
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Mwanufaika hajapatikana'
        ]);
    }
} catch (Exception $e) {
    error_log("Error getting beneficiary details: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Imefeli kupata maelezo ya mwanufaika'
    ]);
}
