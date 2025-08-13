<?php
// Simple test to check if API endpoint is accessible
echo "Testing API endpoint accessibility...\n";

// Test if the login API file exists
$loginApiPath = __DIR__ . '/api/auth/login.php';
if (file_exists($loginApiPath)) {
    echo "✓ Login API file exists at: $loginApiPath\n";
} else {
    echo "✗ Login API file NOT found at: $loginApiPath\n";
}

// Test if the AuthService exists
$authServicePath = __DIR__ . '/services/AuthService.php';
if (file_exists($authServicePath)) {
    echo "✓ AuthService file exists at: $authServicePath\n";
} else {
    echo "✗ AuthService file NOT found at: $authServicePath\n";
}

// Test if the User model exists
$userModelPath = __DIR__ . '/models/User.php';
if (file_exists($userModelPath)) {
    echo "✓ User model file exists at: $userModelPath\n";
} else {
    echo "✗ User model file NOT found at: $userModelPath\n";
}

// Test if the Log model exists
$logModelPath = __DIR__ . '/models/Log.php';
if (file_exists($logModelPath)) {
    echo "✓ Log model file exists at: $logModelPath\n";
} else {
    echo "✗ Log model file NOT found at: $logModelPath\n";
}

// Test database connection
try {
    require_once __DIR__ . '/config/init.php';
    echo "✓ Config/init.php loaded successfully\n";

    // Try to get database connection
    $db = new Database();
    $conn = $db->getConnection();
    echo "✓ Database connection successful\n";
} catch (Exception $e) {
    echo "✗ Database connection failed: " . $e->getMessage() . "\n";
}

// Test current URL structure
echo "\nCurrent URL structure:\n";
echo "REQUEST_URI: " . ($_SERVER['REQUEST_URI'] ?? 'NOT SET') . "\n";
echo "SCRIPT_NAME: " . ($_SERVER['SCRIPT_NAME'] ?? 'NOT SET') . "\n";
echo "PHP_SELF: " . ($_SERVER['PHP_SELF'] ?? 'NOT SET') . "\n";

// Test app_url function
if (function_exists('app_url')) {
    echo "✓ app_url function exists\n";
    echo "app_url('api'): " . app_url('api') . "\n";
    echo "app_url(''): " . app_url('') . "\n";
} else {
    echo "✗ app_url function NOT found\n";
}

echo "\nTest completed!\n";
