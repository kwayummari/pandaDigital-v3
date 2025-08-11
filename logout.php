<?php
require_once __DIR__ . "/services/AuthService.php";

$authService = new AuthService();

// Logout user
$result = $authService->logoutUser();

// Redirect to home page with success message
session_start();
$_SESSION['logout_message'] = $result['message'];

header('Location: /');
exit();

