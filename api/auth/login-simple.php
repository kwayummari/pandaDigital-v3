<?php
header('Content-Type: application/json');

// Simple test response
echo json_encode([
    'success' => true,
    'message' => 'Simple login endpoint working',
    'timestamp' => date('Y-m-d H:i:s')
]);


