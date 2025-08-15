<?php
include 'connection/index.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Get the raw POST data
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Log incoming data
error_log("Received data: " . print_r($data, true));

// Validate input
if (!$data || !isset($data['amount']) || !isset($data['bankName']) || !isset($data['accountNumber']) || !isset($data['accountName']) || !isset($data['sellerId'])) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error', 
        'message' => 'Invalid input',
        'data' => $data
    ]);
    exit;
}

try {
    // Prepare the withdrawal data
    $amount = floatval($data['amount']);
    $bankName = mysqli_real_escape_string($connect, $data['bankName']);
    $accountNumber = mysqli_real_escape_string($connect, $data['accountNumber']);
    $accountName = mysqli_real_escape_string($connect, $data['accountName']);
    $sellerId = intval($data['sellerId']);

    // Validate minimum withdrawal amount
    if ($amount < 50000) {
        throw new Exception('Minimum withdrawal amount is TSh. 50,000');
    }

    // Check available amount
    $query = "SELECT 
        (SUM(sales.amount) - (SUM(sales.amount) * 0.03) - COALESCE(
            (SELECT SUM(amount) FROM withdrawals WHERE sellerId = ?), 
            0
        )) as available_amount
    FROM sales
    JOIN products ON sales.productId = products.id
    JOIN transactions ON sales.reference_no = transactions.reference
    WHERE sales.status = 1 
        AND products.sellerId = ? 
        AND transactions.message = 'Success'";

    $stmt = $connect->prepare($query);
    if (!$stmt) {
        throw new Exception("Query preparation failed: " . $connect->error);
    }

    $stmt->bind_param("ii", $sellerId, $sellerId);
    if (!$stmt->execute()) {
        throw new Exception("Query execution failed: " . $stmt->error);
    }

    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $available_amount = $row['available_amount'];

    if ($amount > $available_amount) {
        throw new Exception('Insufficient funds');
    }
    $stmt->close();

    // Insert withdrawal request
    $sql = "INSERT INTO withdrawals (amount, bank_name, account_number, account_name, status, created_at, sellerId) 
            VALUES (?, ?, ?, ?, 0, NOW(), ?)";

    $stmt = $connect->prepare($sql);
    if (!$stmt) {
        throw new Exception("Insert preparation failed: " . $connect->error);
    }

    $stmt->bind_param("dsssi", $amount, $bankName, $accountNumber, $accountName, $sellerId);
    
    if (!$stmt->execute()) {
        throw new Exception("Insert execution failed: " . $stmt->error);
    }

    echo json_encode([
        'status' => 'success', 
        'message' => 'Withdrawal request submitted successfully'
    ]);

    $stmt->close();

} catch (Exception $e) {
    error_log("Withdrawal error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}

$connect->close();
?>