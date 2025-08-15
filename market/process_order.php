<?php
session_start();
include "connect.php";

// Load configuration
$config = require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $buyersId = $_SESSION['userId'];
        $productId = $_POST['product_id'];
        $quantity = (int)$_POST['quantity'];
        $price = (float)preg_replace('/[^0-9.]/', '', $_POST['price']);
        $total_amount = $quantity * $price;
        $phone = $_POST['phone'];
        $reference_no = '-';
        $mobile_type = $_POST['mobile_type'];

        // Validate inputs
        if (!$buyersId || !$productId || !$quantity || !$total_amount || !$phone || !$mobile_type) {
            throw new Exception("All fields are required");
        }

        // Database transaction
        $conn->begin_transaction();

        // Insert sale record
        $query = "INSERT INTO sales (buyersId, productId, amount, phone, reference_no, mobile_type, quantity) 
                  VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);

        if (!$stmt) {
            throw new Exception("Error preparing statement: " . $conn->error);
        }

        $stmt->bind_param("iisssss", $buyersId, $productId, $total_amount, $phone, $reference_no, $mobile_type, $quantity);
        
        if (!$stmt->execute()) {
            throw new Exception("Error inserting sales record: " . $stmt->error);
        }

        $lastInsertedId = $conn->insert_id;
        $stmt->close();

        // Token handling
        $clientId = $config['AZAMPAY_CLIENT_ID'];
        $storedToken = $_SESSION['token'] ?? null;
        $storedExpiry = $_SESSION['token_expiry'] ?? null;

        $currentTime = time();
        if (!$storedToken || !$storedExpiry || $currentTime >= strtotime($storedExpiry)) {
            $token = generateNewToken($config);
            $_SESSION['token'] = $token;
            $_SESSION['token_expiry'] = date('Y-m-d\TH:i:s\Z', strtotime('+1 hour'));
        } else {
            $token = $storedToken;
        }

        // AzamPay API request
        $response = processPayment([
            'accountNumber' => $phone,
            'amount' => $total_amount,
            'currency' => 'TZS',
            'externalId' => '123',
            'provider' => $mobile_type,
            'additionalProperties' => null
        ], $token, $clientId);

        $responseData = json_decode($response, true);

        if (!isset($responseData['transactionId'])) {
            throw new Exception("Error: Transaction ID not found in the response.");
        }

        // Update reference number
        $updateQuery = "UPDATE sales SET reference_no = ? WHERE id = ?";
        $updateStmt = $conn->prepare($updateQuery);
        
        if (!$updateStmt) {
            throw new Exception("Error preparing update statement: " . $conn->error);
        }

        $updateStmt->bind_param("si", $responseData['transactionId'], $lastInsertedId);
        
        if (!$updateStmt->execute()) {
            throw new Exception("Error updating transaction ID: " . $updateStmt->error);
        }

        $updateStmt->close();
        $conn->commit();

        $message = "You will receive a confirmation USSD on your phone number ($phone). Enter your password and confirm payment.";
        echo "<script>alert('$message');</script>";

    } catch (Exception $e) {
        $conn->rollback();
        echo "Error: " . $e->getMessage();
        error_log("Payment Error: " . $e->getMessage());
    }

    mysqli_close($conn);
} else {
    echo "Invalid request method.";
}

function generateNewToken($config) {
    $authUrl = 'https://authenticator.azampay.co.tz/AppRegistration/GenerateToken';
    
    $authData = [
        'clientId' => $config['AZAMPAY_CLIENT_ID'],
        'clientSecret' => $config['AZAMPAY_CLIENT_SECRET'],
        'appName' => 'Panda Innovation'
    ];

    $authOptions = [
        'http' => [
            'method' => 'POST',
            'header' => 'Content-Type: application/json',
            'content' => json_encode($authData),
            'ignore_errors' => true
        ]
    ];

    $authContext = stream_context_create($authOptions);
    $authResponse = file_get_contents($authUrl, false, $authContext);

    if ($authResponse === false) {
        throw new Exception('Error fetching token');
    }

    $authResponseData = json_decode($authResponse, true);
    if (!isset($authResponseData['data']['accessToken'])) {
        throw new Exception('Error: Invalid token response');
    }

    return $authResponseData['data']['accessToken'];
}

function processPayment($paymentData, $token, $clientId) {
    $checkoutUrl = "https://checkout.azampay.co.tz/azampay/mno/checkout";
    
    $headers = [
        "Content-Type: application/json",
        "Authorization: Bearer " . $token,
        "X-API-Key: $clientId"
    ];

    $options = [
        'http' => [
            'method' => 'POST',
            'header' => implode("\r\n", $headers),
            'content' => json_encode($paymentData),
            'ignore_errors' => true
        ]
    ];

    $context = stream_context_create($options);
    $response = file_get_contents($checkoutUrl, false, $context);

    if ($response === false) {
        throw new Exception("Unable to process payment request");
    }

    return $response;
}