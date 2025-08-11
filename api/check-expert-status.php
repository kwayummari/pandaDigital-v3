<?php
header('Content-Type: application/json');
require_once __DIR__ . "/../services/AuthService.php";

$authService = new AuthService();

// Check if user is logged in
if (!$authService->isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$currentUser = $authService->getCurrentUser();

// Check if user is an expert
if ($currentUser['role'] !== 'expert') {
    http_response_code(403);
    echo json_encode(['error' => 'Forbidden']);
    exit();
}

// Check if expert is authorized
$isAuthorized = isset($currentUser['expert_authorization']) && $currentUser['expert_authorization'] == '1';

echo json_encode([
    'authorized' => $isAuthorized,
    'status' => $currentUser['account_status'] ?? 'unknown',
    'expert_authorization' => $currentUser['expert_authorization'] ?? '0'
]);
