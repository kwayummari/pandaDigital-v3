<?php
// Capture any output that might interfere with JSON
ob_start();

ini_set('display_errors', '0'); // Turn off display errors for production
ini_set('log_errors', '1');
error_reporting(E_ALL);

try {
    session_start();

    // Check if database connection file exists
    if (!file_exists("connect.php")) {
        throw new Exception("Database connection file not found");
    }

    include "connect.php";

    // Check if database connection was successful
    if (!$conn) {
        throw new Exception("Database connection failed");
    }

    // Clear any output buffer before sending JSON
    ob_clean();
    header('Content-Type: application/json');

    $response = array(
        'success' => false,
        'message' => '',
        'data' => null
    );

    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    if (empty($email) || empty($password)) {
        $response['message'] = 'Barua pepe na nenosiri vinahitajika.';
    } else {
        $query = "SELECT * FROM users WHERE email = ?";
        $stmt = mysqli_prepare($conn, $query);

        if (!$stmt) {
            throw new Exception("Database prepare failed: " . mysqli_error($conn));
        }

        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result && mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);
            if (password_verify($password, $user['pass'])) {
                $_SESSION['userId'] = $user['id'];
                $_SESSION['userEmail'] = $user['email'];
                $_SESSION['userPhone'] = $user['phone'];
                $_SESSION['userFullName'] = $user['first_name'] . ' ' . $user['last_name'];
                $_SESSION['isSeller'] = $user['isSeller'];
                $_SESSION['role'] = $user['role'];

                $response['success'] = true;
                $response['message'] = 'Kuingia kumefaulu.';
                $response['data'] = array(
                    'userId' => $user['id'],
                    'email' => $user['email'],
                    'fullName' => $user['first_name'] . ' ' . $user['last_name'],
                    'role' => $user['role']
                );
            } else {
                $response['message'] = 'Barua pepe au nenosiri batili.';
            }
        } else {
            $response['message'] = 'Barua pepe au nenosiri batili.';
        }

        mysqli_stmt_close($stmt);
    }

    mysqli_close($conn);
} catch (Exception $e) {
    // Clear any output buffer
    ob_clean();
    header('Content-Type: application/json');

    $response = array(
        'success' => false,
        'message' => 'Server error occurred: ' . $e->getMessage(),
        'data' => null
    );
}

// End output buffering and send JSON
ob_end_clean();
echo json_encode($response);
