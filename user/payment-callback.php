<?php
require_once __DIR__ . "/../config/init.php";
require_once __DIR__ . "/../services/PaymentService.php";

// Log the raw input for debugging
$logFile = __DIR__ . '/payment_callback_log.txt';
file_put_contents($logFile, date('Y-m-d H:i:s') . " - Callback received\n", FILE_APPEND);
file_put_contents($logFile, "Raw input: " . file_get_contents('php://input') . "\n", FILE_APPEND);

// Get the raw POST data
$input = file_get_contents('php://input');

// Try to decode the JSON
$data = json_decode($input, true);

// Validate JSON decoding
if (json_last_error() !== JSON_ERROR_NONE) {
    // Try parsing $_POST if JSON decoding fails
    $data = $_POST;
}

// Debug: log the received data
file_put_contents($logFile, "Parsed data: " . print_r($data, true) . "\n", FILE_APPEND);

// Extract data from the payload (adjust these keys based on your payment gateway)
$msisdn = $data['msisdn'] ?? $data['phone'] ?? null;
$amount = $data['amount'] ?? null;
$message = $data['message'] ?? null;
$utilityref = $data['utilityref'] ?? null;
$operator = $data['operator'] ?? null;
$reference = $data['reference'] ?? null;
$transactionstatus = $data['transactionstatus'] ?? $data['status'] ?? null;
$submerchantAcc = $data['submerchantAcc'] ?? null;
$externalreference = $data['externalreference'] ?? null;
$transid = $data['transid'] ?? null;
$mnoreference = $data['mnoreference'] ?? null;

// Log extracted data
file_put_contents($logFile, "Extracted data:\n", FILE_APPEND);
file_put_contents($logFile, "MSISDN: $msisdn\n", FILE_APPEND);
file_put_contents($logFile, "Amount: $amount\n", FILE_APPEND);
file_put_contents($logFile, "Status: $transactionstatus\n", FILE_APPEND);
file_put_contents($logFile, "Reference: $reference\n", FILE_APPEND);

// Validate required fields
if (!$msisdn || !$amount || !$reference || !$transactionstatus) {
    http_response_code(400);
    file_put_contents($logFile, "Missing required fields\n", FILE_APPEND);
    echo json_encode(["status" => "error", "message" => "Missing required fields."]);
    exit;
}

try {
    $paymentService = new PaymentService();

    // Check if this is a successful payment
    $isSuccessful = strtolower($transactionstatus) === 'success' ||
        strtolower($transactionstatus) === 'completed' ||
        $transactionstatus === '1';

    if ($isSuccessful) {
        // Extract course ID and user ID from reference number
        // Reference format: PAY-timestamp-userId-courseId
        $referenceParts = explode('-', $reference);

        if (count($referenceParts) >= 4) {
            $userId = $referenceParts[2];
            $courseId = $referenceParts[3];

            file_put_contents($logFile, "Processing successful payment for User: $userId, Course: $courseId\n", FILE_APPEND);

            // Update transaction status to completed (status = 1)
            $updateResult = $paymentService->updateTransactionByReference($reference, 1);

            if ($updateResult) {
                // Create paid course access
                $accessResult = $paymentService->grantCourseAccess($userId, $courseId);

                if ($accessResult) {
                    file_put_contents($logFile, "User successfully granted paid course access\n", FILE_APPEND);

                    // Log successful transaction
                    $paymentService->logCallback([
                        'reference' => $reference,
                        'msisdn' => $msisdn,
                        'amount' => $amount,
                        'status' => $transactionstatus,
                        'operator' => $operator,
                        'gateway_reference' => $transid,
                        'callback_data' => json_encode($data),
                        'processed' => true
                    ]);

                    http_response_code(200);
                    echo json_encode([
                        "status" => "success",
                        "message" => "Payment processed and user granted course access successfully."
                    ]);
                } else {
                    file_put_contents($logFile, "Failed to grant course access\n", FILE_APPEND);
                    http_response_code(500);
                    echo json_encode([
                        "status" => "error",
                        "message" => "Payment processed but course access failed."
                    ]);
                }
            } else {
                file_put_contents($logFile, "Failed to update transaction status\n", FILE_APPEND);
                http_response_code(500);
                echo json_encode([
                    "status" => "error",
                    "message" => "Failed to update transaction status."
                ]);
            }
        } else {
            file_put_contents($logFile, "Invalid reference number format: $reference\n", FILE_APPEND);
            http_response_code(400);
            echo json_encode([
                "status" => "error",
                "message" => "Invalid reference number format."
            ]);
        }
    } else {
        // Payment failed or pending
        file_put_contents($logFile, "Payment not successful. Status: $transactionstatus\n", FILE_APPEND);

        // Update transaction status to failed (status = 3)
        $paymentService->updateTransactionByReference($reference, 3);

        // Log failed transaction
        $paymentService->logCallback([
            'reference' => $reference,
            'msisdn' => $msisdn,
            'amount' => $amount,
            'status' => $transactionstatus,
            'operator' => $operator,
            'gateway_reference' => $transid,
            'callback_data' => json_encode($data),
            'processed' => false
        ]);

        http_response_code(200);
        echo json_encode([
            "status" => "success",
            "message" => "Payment status updated to failed."
        ]);
    }
} catch (Exception $e) {
    file_put_contents($logFile, "Error processing callback: " . $e->getMessage() . "\n", FILE_APPEND);
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Internal server error: " . $e->getMessage()
    ]);
}

file_put_contents($logFile, "Callback processing completed\n\n", FILE_APPEND);
