<?php

/**
 * Quick Maintenance Mode Enabler
 * Simple script to quickly enable/disable maintenance mode
 */

// Security check - allow from localhost, server IP, or with admin key
$allowedIPs = ['127.0.0.1', '::1', 'localhost'];
$clientIP = $_SERVER['REMOTE_ADDR'] ?? '';

// Get server IP to allow access from the same server
$serverIP = $_SERVER['SERVER_ADDR'] ?? '';
if ($serverIP) {
    $allowedIPs[] = $serverIP;
}

// Allow access if it's from the same server or has admin key
$adminKey = 'panda_maintenance_2024';
$isFromServer = in_array($clientIP, $allowedIPs);
$hasAdminKey = isset($_GET['admin_key']) && $_GET['admin_key'] === $adminKey;

if (!$isFromServer && !$hasAdminKey) {
    die('Access denied. This script only works from the server or with admin key.<br><br>Use: <a href="?admin_key=' . $adminKey . '">' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . '?admin_key=' . $adminKey . '</a>');
}

$maintenanceFile = __DIR__ . '/.maintenance';

// Handle GET parameters for quick activation/deactivation
$action = $_GET['action'] ?? '';

if ($action === 'on') {
    // Activate maintenance mode
    file_put_contents($maintenanceFile, json_encode([
        'active' => true,
        'activated_at' => date('Y-m-d H:i:s'),
        'activated_by' => $clientIP,
        'message' => 'Website under maintenance'
    ]));
    echo "✅ Maintenance mode ACTIVATED!\n";
    echo "Visit: " . (isset($_SERVER['HTTPS']) ? 'https' : 'http') . "://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/maintenance.php\n";
} elseif ($action === 'off') {
    // Deactivate maintenance mode
    if (file_exists($maintenanceFile)) {
        unlink($maintenanceFile);
    }
    echo "✅ Maintenance mode DEACTIVATED!\n";
    echo "Website is now LIVE!\n";
} elseif ($action === 'status') {
    // Check status
    if (file_exists($maintenanceFile)) {
        $data = json_decode(file_get_contents($maintenanceFile), true);
        echo "🔴 MAINTENANCE MODE IS ACTIVE\n";
        echo "Activated at: " . $data['activated_at'] . "\n";
        echo "Activated by: " . $data['activated_by'] . "\n";
    } else {
        echo "🟢 WEBSITE IS LIVE\n";
    }
} else {
    // Show usage instructions
    echo "Panda Digital Maintenance Control\n";
    echo "===============================\n\n";
    echo "Usage:\n";
    echo "  ?action=on    - Activate maintenance mode\n";
    echo "  ?action=off   - Deactivate maintenance mode\n";
    echo "  ?action=status - Check current status\n\n";
    echo "Examples:\n";
    echo "  " . (isset($_SERVER['HTTPS']) ? 'https' : 'http') . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . "?action=on\n";
    echo "  " . (isset($_SERVER['HTTPS']) ? 'https' : 'http') . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . "?action=off\n";
    echo "  " . (isset($_SERVER['HTTPS']) ? 'https' : 'http') . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . "?action=status\n\n";

    // Show current status
    if (file_exists($maintenanceFile)) {
        $data = json_decode(file_get_contents($maintenanceFile), true);
        echo "Current Status: 🔴 MAINTENANCE MODE IS ACTIVE\n";
        echo "Activated at: " . $data['activated_at'] . "\n";
    } else {
        echo "Current Status: 🟢 WEBSITE IS LIVE\n";
    }
}
