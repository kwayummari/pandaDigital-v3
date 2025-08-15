<?php
require_once __DIR__ . "/../config/init.php";
require_once __DIR__ . "/../services/GoogleOAuthService.php";

// Check if we have an authorization code
if (!isset($_GET['code'])) {
    // No code provided, redirect to login with error
    header('Location: ' . app_url('login.php?error=google_auth_failed'));
    exit();
}

$code = $_GET['code'];
$googleOAuth = new GoogleOAuthService();

// Check if Google OAuth is configured
if (!$googleOAuth->isConfigured()) {
    header('Location: ' . app_url('login.php?error=google_not_configured'));
    exit();
}

// Handle the OAuth callback
$result = $googleOAuth->handleCallback($code);

if ($result['valid']) {
    // Success - redirect to appropriate dashboard
    header('Location: ' . app_url($result['redirect_url']));
    exit();
} else {
    // Failed - redirect to login with error
    header('Location: ' . app_url('login.php?error=' . urlencode($result['message'])));
    exit();
}

