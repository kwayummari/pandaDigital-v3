<?php

/**
 * Database Connection Monitor
 * This script helps monitor database connection usage and provides diagnostics
 */

require_once __DIR__ . '/../config/init.php';

// Check if user is admin (add your admin check here)
// if (!isAdmin()) {
//     die('Access denied');
// }

$db = Database::getInstance();
$conn = $db->getConnection();

if ($conn === null) {
    die('Database connection failed');
}

echo "<h2>Database Connection Monitor</h2>";

// Check current connection status
try {
    $stmt = $conn->query("SHOW STATUS LIKE 'Connections'");
    $connections = $stmt->fetch();

    $stmt = $conn->query("SHOW STATUS LIKE 'Max_used_connections'");
    $maxUsed = $stmt->fetch();

    $stmt = $conn->query("SHOW VARIABLES LIKE 'max_connections'");
    $maxConnections = $stmt->fetch();

    echo "<h3>Connection Statistics</h3>";
    echo "<p><strong>Total Connections:</strong> " . $connections['Value'] . "</p>";
    echo "<p><strong>Max Used Connections:</strong> " . $maxUsed['Value'] . "</p>";
    echo "<p><strong>Max Allowed Connections:</strong> " . $maxConnections['Value'] . "</p>";

    // Check current processes
    $stmt = $conn->query("SHOW PROCESSLIST");
    $processes = $stmt->fetchAll();

    echo "<h3>Current Processes (" . count($processes) . ")</h3>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>ID</th><th>User</th><th>Host</th><th>DB</th><th>Command</th><th>Time</th><th>State</th><th>Info</th></tr>";

    foreach ($processes as $process) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($process['Id']) . "</td>";
        echo "<td>" . htmlspecialchars($process['User']) . "</td>";
        echo "<td>" . htmlspecialchars($process['Host']) . "</td>";
        echo "<td>" . htmlspecialchars($process['db'] ?? 'NULL') . "</td>";
        echo "<td>" . htmlspecialchars($process['Command']) . "</td>";
        echo "<td>" . htmlspecialchars($process['Time']) . "</td>";
        echo "<td>" . htmlspecialchars($process['State']) . "</td>";
        echo "<td>" . htmlspecialchars(substr($process['Info'] ?? '', 0, 100)) . "</td>";
        echo "</tr>";
    }
    echo "</table>";

    // Check for long-running queries
    $longQueries = array_filter($processes, function ($process) {
        return $process['Time'] > 10 && $process['Command'] !== 'Sleep';
    });

    if (!empty($longQueries)) {
        echo "<h3>⚠️ Long Running Queries (>10 seconds)</h3>";
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ID</th><th>Time</th><th>Query</th></tr>";
        foreach ($longQueries as $query) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($query['Id']) . "</td>";
            echo "<td>" . htmlspecialchars($query['Time']) . "</td>";
            echo "<td>" . htmlspecialchars($query['Info']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
} catch (PDOException $e) {
    echo "<p style='color: red;'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<h3>Recommendations</h3>";
echo "<ul>";
echo "<li>Monitor this page regularly to track connection usage</li>";
echo "<li>If you see many connections, check for connection leaks in your code</li>";
echo "<li>Consider implementing connection pooling if using multiple database connections</li>";
echo "<li>Optimize long-running queries to reduce connection time</li>";
echo "</ul>";

echo "<p><a href='javascript:location.reload()'>Refresh</a> | <a href='../index.php'>Back to Site</a></p>";
