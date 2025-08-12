<?php
require_once __DIR__ . "/config/init.php";
require_once __DIR__ . "/services/AuthService.php";

$authService = new AuthService();

// Logout user
$result = $authService->logoutUser();

// Redirect to home page with success message
$_SESSION['logout_message'] = $result['message'];

header('Location: ' . app_url(''));
exit();
