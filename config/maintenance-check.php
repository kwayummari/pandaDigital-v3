<?php

/**
 * Maintenance Check System
 * Include this file at the top of your pages to check for maintenance mode
 */

// Maintenance control file
$maintenanceFile = __DIR__ . '/../.maintenance';

// Check if maintenance mode is active
if (file_exists($maintenanceFile)) {
    $maintenanceData = json_decode(file_get_contents($maintenanceFile), true);

    // Allow access for specific IPs (admin, developers, etc.)
    $allowedIPs = ['127.0.0.1', '::1', 'localhost'];
    $clientIP = $_SERVER['REMOTE_ADDR'] ?? '';

    // Allow access if user has admin key
    $adminKey = 'panda_maintenance_2024';
    $hasAdminKey = isset($_GET['admin_key']) && $_GET['admin_key'] === $adminKey;

    // If not allowed IP and no admin key, redirect to maintenance page
    if (!in_array($clientIP, $allowedIPs) && !$hasAdminKey) {
        // Set maintenance data for the maintenance page
        $GLOBALS['maintenanceData'] = $maintenanceData;

        // Include maintenance page and exit
        include __DIR__ . '/../maintenance.php';
        exit;
    }
}

// If we reach here, maintenance mode is not active or user has access
// Continue with normal page execution
