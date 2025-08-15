<?php
session_start();
include "../admin/includes/db/connect.php";

header('Content-Type: application/json');

$response = array(
    'success' => false,
    'message' => '',
    'data' => null
);

// Get data from GET parameters
$first_name = trim($_GET['first_name'] ?? '');
$last_name = trim($_GET['last_name'] ?? '');
$email = trim($_GET['email'] ?? '');
$phone = substr(trim($_GET['phone'] ?? ''), 1);
$region = trim($_GET['region'] ?? '');
$business = trim($_GET['business'] ?? '');
$gender = $_GET['gender'] ?? '';
$date_of_birth = $_GET['date_of_birth'] ?? '';
$isSeller = trim($_GET['isSeller'] ?? '');
$password = password_hash($_GET['password'] ?? '', PASSWORD_DEFAULT);

// Validate required fields
if (empty($first_name) || empty($last_name) || empty($email) || empty($phone)) {
    $response['message'] = 'All fields are required.';
    echo json_encode($response);
    exit;
}

// Check email existence
$email_check_query = "SELECT id FROM users WHERE email = ?";
$stmt = mysqli_prepare($conn, $email_check_query);
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
$email_check_result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($email_check_result) > 0) {
    $response['message'] = 'Email already exists.';
    echo json_encode($response);
    exit;
}

// Check phone existence
$phone_check_query = "SELECT id FROM users WHERE phone = ?";
$stmt = mysqli_prepare($conn, $phone_check_query);
mysqli_stmt_bind_param($stmt, "s", $phone);
mysqli_stmt_execute($stmt);
$phone_check_result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($phone_check_result) > 0) {
    $response['message'] = 'Phone number already exists.';
    echo json_encode($response);
    exit;
}

// Insert user
$query = "INSERT INTO users (first_name, last_name, email, phone, region, business, gender, date_of_birth, pass, isSeller) 
          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "ssssssssss", 
    $first_name, $last_name, $email, $phone, $region, $business, $gender, $date_of_birth, $password, $isSeller
);

if (mysqli_stmt_execute($stmt)) {
    $user_id = mysqli_insert_id($conn);
    
    $_SESSION['userId'] = $user_id;
    $_SESSION['userEmail'] = $email;
    $_SESSION['userPhone'] = $phone;
    $_SESSION['userFullName'] = "$first_name $last_name";
    $_SESSION['isSeller'] = $isSeller;
    
    $response['success'] = true;
    $response['message'] = 'Usajili umefaulu. Tafadhali onyesha upya ukurasa na uendelee.';
    $response['data'] = array(
        'userId' => $user_id,
        'email' => $email,
        'fullName' => "$first_name $last_name"
    );
} else {
    $response['message'] = 'Usajili umeshindwa.';
}

mysqli_close($conn);
echo json_encode($response);
?>