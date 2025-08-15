<?php
require_once __DIR__ . '/config/init.php';
require_once __DIR__ . '/services/PaymentService.php';

try {
    echo "Testing AzamPay Payment Service...\n";

    $paymentService = new PaymentService();
    echo "✅ PaymentService created successfully\n";

    // Test payment processing with sample data
    echo "🔄 Testing payment processing...\n";

    $testPaymentData = [
        'accountNumber' => '0712345678',
        'amount' => 1000,
        'currency' => 'TZS',
        'externalId' => 'TEST-' . time(),
        'provider' => 'mpesa',
        'additionalProperties' => null
    ];

    $result = $paymentService->processPayment($testPaymentData);

    if ($result['success']) {
        echo "✅ Payment processed successfully!\n";
        echo "Transaction ID: " . $result['transactionId'] . "\n";
        echo "Message: " . $result['message'] . "\n";
    } else {
        echo "❌ Payment failed: " . $result['message'] . "\n";
    }
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
