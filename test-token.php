<?php
require_once __DIR__ . '/config/init.php';
require_once __DIR__ . '/services/PaymentService.php';

try {
    echo "Testing AzamPay Token Generation...\n";

    $paymentService = new PaymentService();
    echo "✅ PaymentService created successfully\n";

    // Test token generation
    $reflection = new ReflectionClass($paymentService);
    $method = $reflection->getMethod('getAuthToken');
    $method->setAccessible(true);

    echo "🔄 Generating token...\n";
    $token = $method->invoke($paymentService);

    echo "✅ Token generated successfully: " . substr($token, 0, 20) . "...\n";
    echo "Token length: " . strlen($token) . " characters\n";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
