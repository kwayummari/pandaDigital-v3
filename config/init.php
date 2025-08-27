<?php

/**
 * Panda Digital V3 - Application Initialization
 * This file should be included at the beginning of all pages
 */

// Load environment configuration
require_once __DIR__ . '/Environment.php';
Environment::load();

// Set timezone
date_default_timezone_set(Environment::get('APP_TIMEZONE', 'Africa/Dar_es_Salaam'));

// Set error reporting based on environment
if (Environment::isDevelopment()) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Start session with secure settings
if (session_status() === PHP_SESSION_NONE) {
    $sessionConfig = [
        'cookie_httponly' => Environment::get('SESSION_HTTP_ONLY', 'true') === 'true',
        'cookie_secure' => Environment::get('SESSION_SECURE', 'false') === 'true',
        'cookie_samesite' => Environment::get('SESSION_SAME_SITE', 'lax'),
        'cookie_path' => '/',
        'use_strict_mode' => true,
        'use_cookies' => true,
        'use_only_cookies' => true
    ];

    session_start($sessionConfig);
}

// Set security headers
if (!headers_sent()) {
    // Prevent XSS attacks
    header('X-XSS-Protection: 1; mode=block');

    // Prevent MIME type sniffing
    header('X-Content-Type-Options: nosniff');

    // Prevent clickjacking
    header('X-Frame-Options: SAMEORIGIN');

    // Referrer policy
    header('Referrer-Policy: strict-origin-when-cross-origin');

    // Content Security Policy (basic)
    if (Environment::isProduction()) {
        header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://unpkg.com; style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com; img-src 'self' data: https:; connect-src 'self';");
    }
}

// Load database configuration
require_once __DIR__ . '/database.php';

// Initialize database connection and make it globally available
global $pdo;
try {
    $database = new Database();
    $pdo = $database->getConnection();
} catch (Exception $e) {
    error_log('Failed to initialize database connection: ' . $e->getMessage());
    $pdo = null;
}

// Helper functions
if (!function_exists('env')) {
    /**
     * Get environment variable
     */
    function env($key, $default = null)
    {
        return Environment::get($key, $default);
    }
}

if (!function_exists('app_url')) {
    /**
     * Get application URL
     */
    function app_url($path = '')
    {
        $baseUrl = Environment::get('APP_URL', 'http://localhost/pandadigitalV3');
        return rtrim($baseUrl, '/') . '/' . ltrim($path, '/');
    }
}

if (!function_exists('asset')) {
    /**
     * Get asset URL
     */
    function asset($path)
    {
        return app_url('assets/' . ltrim($path, '/'));
    }
}

if (!function_exists('upload_url')) {
    /**
     * Get upload URL
     */
    function upload_url($path)
    {
        return app_url('uploads/' . ltrim($path, '/'));
    }
}

if (!function_exists('csrf_token')) {
    /**
     * Generate CSRF token
     */
    function csrf_token()
    {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
}

if (!function_exists('verify_csrf_token')) {
    /**
     * Verify CSRF token
     */
    function verify_csrf_token($token)
    {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
}

if (!function_exists('old')) {
    /**
     * Get old input value
     */
    function old($key, $default = '')
    {
        return $_SESSION['old_input'][$key] ?? $default;
    }
}

if (!function_exists('flash')) {
    /**
     * Set flash message
     */
    function flash($key, $message)
    {
        $_SESSION['flash'][$key] = $message;
    }
}

if (!function_exists('get_flash')) {
    /**
     * Get flash message
     */
    function get_flash($key)
    {
        $message = $_SESSION['flash'][$key] ?? null;
        unset($_SESSION['flash'][$key]);
        return $message;
    }
}

if (!function_exists('has_flash')) {
    /**
     * Check if flash message exists
     */
    function has_flash($key)
    {
        return isset($_SESSION['flash'][$key]);
    }
}

if (!function_exists('redirect')) {
    /**
     * Redirect to URL
     */
    function redirect($url)
    {
        header('Location: ' . $url);
        exit;
    }
}

if (!function_exists('back')) {
    /**
     * Redirect back
     */
    function back()
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? app_url();
        redirect($referer);
    }
}

// Load application configuration
$appConfig = Environment::getAppConfig();
$contactConfig = Environment::getContactConfig();
$socialConfig = Environment::getSocialConfig();

// Make configuration available globally
$GLOBALS['app_config'] = $appConfig;
$GLOBALS['contact_config'] = $contactConfig;
$GLOBALS['social_config'] = $socialConfig;
