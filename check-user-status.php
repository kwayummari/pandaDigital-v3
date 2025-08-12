<?php
require_once 'config/init.php';
require_once 'models/User.php';

// Test user email
$testEmail = 'kwayu2004@gmail.com';

try {
    $db = new Database();
    $conn = $db->getConnection();

    echo "<h2>User Status Check for: $testEmail</h2>";

    // Check if user exists without account_status filter
    $stmt = $conn->prepare("SELECT id, first_name, last_name, email, role, account_status, pass FROM users WHERE email = ?");
    $stmt->execute([$testEmail]);
    $user = $stmt->fetch();

    if ($user) {
        echo "<h3>User Found:</h3>";
        echo "<ul>";
        echo "<li><strong>ID:</strong> " . $user['id'] . "</li>";
        echo "<li><strong>Name:</strong> " . htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) . "</li>";
        echo "<li><strong>Email:</strong> " . htmlspecialchars($user['email']) . "</li>";
        echo "<li><strong>Role:</strong> " . htmlspecialchars($user['role']) . "</li>";
        echo "<li><strong>Account Status:</strong> " . htmlspecialchars($user['account_status']) . "</li>";
        echo "<li><strong>Password Hash:</strong> " . substr($user['pass'], 0, 20) . "...</li>";
        echo "</ul>";

        // Test password verification
        $testPassword = 'Gudboy24@';
        if (password_verify($testPassword, $user['pass'])) {
            echo "<p style='color: green;'><strong>✓ Password verification successful!</strong></p>";
        } else {
            echo "<p style='color: red;'><strong>✗ Password verification failed!</strong></p>";
        }

        // Check if user would pass the new system's authentication
        if ($user['account_status'] === 'active') {
            echo "<p style='color: green;'><strong>✓ User would pass new system authentication</strong></p>";
        } else {
            echo "<p style='color: red;'><strong>✗ User would FAIL new system authentication due to account_status: " . htmlspecialchars($user['account_status']) . "</strong></p>";
        }
    } else {
        echo "<p style='color: red;'><strong>User not found!</strong></p>";
    }

    // Check all users with their account statuses
    echo "<h3>All Users Account Statuses:</h3>";
    $stmt = $conn->prepare("SELECT id, first_name, last_name, email, role, account_status FROM users ORDER BY id");
    $stmt->execute();
    $users = $stmt->fetchAll();

    if ($users) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Account Status</th></tr>";
        foreach ($users as $u) {
            $statusColor = $u['account_status'] === 'active' ? 'green' : 'red';
            echo "<tr>";
            echo "<td>" . $u['id'] . "</td>";
            echo "<td>" . htmlspecialchars($u['first_name'] . ' ' . $u['last_name']) . "</td>";
            echo "<td>" . htmlspecialchars($u['email']) . "</td>";
            echo "<td>" . htmlspecialchars($u['role']) . "</td>";
            echo "<td style='color: $statusColor;'>" . htmlspecialchars($u['account_status']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No users found in database.</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>

<style>
    body {
        font-family: Arial, sans-serif;
        margin: 20px;
    }

    table {
        margin-top: 20px;
    }

    th,
    td {
        padding: 8px;
        text-align: left;
    }

    th {
        background-color: #f2f2f2;
    }
</style>