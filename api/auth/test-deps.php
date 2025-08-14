<?php
header('Content-Type: application/json');

$results = [];
$errors = [];

// Test 1: Basic PHP
$results['php'] = 'OK';

// Test 2: Check if files exist
$files = [
    'AuthService' => __DIR__ . '/../../services/AuthService.php',
    'User' => __DIR__ . '/../../models/User.php',
    'Database' => __DIR__ . '/../../config/database.php',
    'Environment' => __DIR__ . '/../../config/Environment.php'
];

foreach ($files as $name => $path) {
    if (file_exists($path)) {
        $results[$name . '_exists'] = 'OK';
    } else {
        $results[$name . '_exists'] = 'FAIL';
        $errors[] = "$name file not found at: $path";
    }
}

// Test 3: Try to load Environment class
try {
    require_once __DIR__ . '/../../config/Environment.php';
    $results['Environment_load'] = 'OK';
} catch (Exception $e) {
    $results['Environment_load'] = 'FAIL';
    $errors[] = 'Environment load error: ' . $e->getMessage();
}

// Test 4: Try to load Database class
try {
    require_once __DIR__ . '/../../config/database.php';
    $results['Database_load'] = 'OK';
} catch (Exception $e) {
    $results['Database_load'] = 'FAIL';
    $errors[] = 'Database load error: ' . $e->getMessage();
}

// Test 5: Try to create Database instance
try {
    $db = new Database();
    $results['Database_instance'] = 'OK';
} catch (Exception $e) {
    $results['Database_instance'] = 'FAIL';
    $errors[] = 'Database instance error: ' . $e->getMessage();
}

// Test 6: Try to get database connection
try {
    if (isset($db)) {
        $conn = $db->getConnection();
        if ($conn) {
            $results['Database_connection'] = 'OK';
        } else {
            $results['Database_connection'] = 'FAIL';
            $errors[] = 'Database connection returned null';
        }
    } else {
        $results['Database_connection'] = 'SKIP';
    }
} catch (Exception $e) {
    $results['Database_connection'] = 'FAIL';
    $errors[] = 'Database connection error: ' . $e->getMessage();
}

// Test 7: Try to load User model
try {
    require_once __DIR__ . '/../../models/User.php';
    $results['User_load'] = 'OK';
} catch (Exception $e) {
    $results['User_load'] = 'FAIL';
    $errors[] = 'User load error: ' . $e->getMessage();
}

// Test 8: Try to create User instance
try {
    if (isset($db)) {
        $user = new User();
        $results['User_instance'] = 'OK';
    } else {
        $results['User_instance'] = 'SKIP';
    }
} catch (Exception $e) {
    $results['User_instance'] = 'FAIL';
    $errors[] = 'User instance error: ' . $e->getMessage();
}

// Test 9: Try to load AuthService
try {
    require_once __DIR__ . '/../../services/AuthService.php';
    $results['AuthService_load'] = 'OK';
} catch (Exception $e) {
    $results['AuthService_load'] = 'FAIL';
    $errors[] = 'AuthService load error: ' . $e->getMessage();
}

// Test 10: Try to create AuthService instance
try {
    if (isset($db)) {
        $auth = new AuthService();
        $results['AuthService_instance'] = 'OK';
    } else {
        $results['AuthService_instance'] = 'SKIP';
    }
} catch (Exception $e) {
    $results['AuthService_instance'] = 'FAIL';
    $errors[] = 'AuthService instance error: ' . $e->getMessage();
}

echo json_encode([
    'success' => empty($errors),
    'results' => $results,
    'errors' => $errors,
    'timestamp' => date('Y-m-d H:i:s')
]);
