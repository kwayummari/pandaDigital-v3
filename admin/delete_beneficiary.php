<?php
require_once __DIR__ . "/../middleware/AuthMiddleware.php";
require_once __DIR__ . "/../models/Beneficiary.php";

// Check if user is admin
$auth = new AuthMiddleware();
$auth->requireRole('admin');

// Set JSON header
header('Content-Type: application/json');

// Check if ID is provided
if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
    echo json_encode(['success' => false, 'message' => 'ID ya mwanufaika haijatolewa']);
    exit;
}

$beneficiaryId = (int)$_POST['id'];

try {
    // Initialize model
    $beneficiaryModel = new Beneficiary();

    // Get beneficiary details first to get photo filename
    $beneficiary = $beneficiaryModel->getWanufaikaById($beneficiaryId);

    if (!$beneficiary) {
        echo json_encode(['success' => false, 'message' => 'Mwanufaika hajapatikana']);
        exit;
    }

    // Delete from database
    $result = $beneficiaryModel->deleteWanufaika($beneficiaryId);

    if ($result) {
        // Delete photo file if it exists
        if (!empty($beneficiary['photo'])) {
            $photoPath = '../uploads/Wanufaika/' . $beneficiary['photo'];
            if (file_exists($photoPath)) {
                unlink($photoPath);
            }
        }

        echo json_encode(['success' => true, 'message' => 'Mwanufaika amefutwa kikamilifu']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Imefeli kufuta mwanufaika']);
    }
} catch (Exception $e) {
    error_log("Error deleting beneficiary: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Imefeli kufuta mwanufaika'
    ]);
}
