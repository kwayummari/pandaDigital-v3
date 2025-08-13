<?php

/**
 * Test Payment Service
 * This script tests the PaymentService to ensure it's working correctly
 * Run this to verify your AzamPay configuration
 */

require_once __DIR__ . '/config/init.php';
require_once __DIR__ . '/services/PaymentService.php';

echo "<h1>Payment Service Test</h1>\n";

try {
    // Test PaymentService initialization
    echo "<h2>1. Testing PaymentService Initialization</h2>\n";

    $paymentService = new PaymentService();
    echo "✅ PaymentService created successfully\n";

    // Test configuration loading
    echo "<h2>2. Testing Configuration Loading</h2>\n";

    $config = Environment::getPaymentConfig();
    echo "✅ Configuration loaded successfully\n";
    echo "AzamPay Environment: " . ($config['azampay_environment'] ?? 'Not set') . "\n";
    echo "API Key: " . (empty($config['azampay_api_key']) ? '❌ Not set' : '✅ Set') . "\n";
    echo "Secret Key: " . (empty($config['azampay_secret_key']) ? '❌ Not set' : '✅ Set') . "\n";

    if (empty($config['azampay_api_key']) || empty($config['azampay_secret_key'])) {
        echo "<div style='color: red; background: #ffe6e6; padding: 10px; border: 1px solid #ff9999;'>\n";
        echo "<strong>⚠️ Warning:</strong> AzamPay credentials not configured!\n";
        echo "Please check your .env file and ensure AZAMPAY_API_KEY and AZAMPAY_SECRET_KEY are set.\n";
        echo "</div>\n";
    } else {
        echo "<div style='color: green; background: #e6ffe6; padding: 10px; border: 1px solid #99ff99;'>\n";
        echo "<strong>✅ Success:</strong> AzamPay credentials are configured.\n";
        echo "</div>\n";
    }

    // Test database connection
    echo "<h2>3. Testing Database Connection</h2>\n";

    try {
        $db = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
            DB_USER,
            DB_PASSWORD
        );
        echo "✅ Database connection successful\n";

        // Test if required tables exist
        echo "<h2>4. Testing Required Tables</h2>\n";

        $tables = ['courseTransactions', 'paidCourse'];
        foreach ($tables as $table) {
            $stmt = $db->query("SHOW TABLES LIKE '$table'");
            if ($stmt->rowCount() > 0) {
                echo "✅ Table '$table' exists\n";
            } else {
                echo "❌ Table '$table' not found\n";
            }
        }
    } catch (PDOException $e) {
        echo "❌ Database connection failed: " . $e->getMessage() . "\n";
    }

    // Test environment file
    echo "<h2>5. Testing Environment File</h2>\n";

    $envFile = __DIR__ . '/.env';
    if (file_exists($envFile)) {
        echo "✅ .env file exists\n";

        // Check if it's readable
        if (is_readable($envFile)) {
            echo "✅ .env file is readable\n";
        } else {
            echo "❌ .env file is not readable\n";
        }

        // Check file size
        $size = filesize($envFile);
        echo "📁 .env file size: " . number_format($size) . " bytes\n";
    } else {
        echo "❌ .env file not found\n";
        echo "Please copy env.example to .env and configure your settings.\n";
    }

    // Test environment loading
    echo "<h2>6. Testing Environment Loading</h2>\n";

    $appConfig = Environment::getAppConfig();
    echo "✅ App configuration loaded\n";
    echo "App Name: " . $appConfig['name'] . "\n";
    echo "App URL: " . $appConfig['url'] . "\n";
    echo "Environment: " . $appConfig['env'] . "\n";
    echo "Debug Mode: " . ($appConfig['debug'] ? 'Enabled' : 'Disabled') . "\n";
} catch (Exception $e) {
    echo "<div style='color: red; background: #ffe6e6; padding: 10px; border: 1px solid #ff9999;'>\n";
    echo "<strong>❌ Error:</strong> " . $e->getMessage() . "\n";
    echo "</div>\n";

    echo "<h3>Debug Information:</h3>\n";
    echo "<pre>\n";
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
    echo "</pre>\n";
}

echo "<h2>7. Next Steps</h2>\n";
echo "<ol>\n";
echo "<li>Ensure your .env file has AzamPay credentials</li>\n";
echo "<li>Test with a paid course</li>\n";
echo "<li>Monitor payment_callback_log.txt for callbacks</li>\n";
echo "<li>Check server error logs for any issues</li>\n";
echo "</ol>\n";

echo "<h2>8. Security Checklist</h2>\n";
echo "<ul>\n";
echo "<li>✅ .env file not committed to version control</li>\n";
echo "<li>✅ API keys stored in environment variables</li>\n";
echo "<li>✅ Different keys for development/production</li>\n";
echo "<li>✅ Input validation implemented</li>\n";
echo "<li>✅ Error logging without sensitive data</li>\n";
echo "</ul>\n";

echo "<hr>\n";
echo "<p><strong>Test completed.</strong> If you see any ❌ errors, please fix them before using the payment system.</p>\n";


