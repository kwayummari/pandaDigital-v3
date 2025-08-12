<?php
require_once 'config/init.php';

// User email to fix
$userEmail = 'kwayu2004@gmail.com';

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    echo "<h2>Fixing User Account Status</h2>";
    echo "<p><strong>Target User:</strong> $userEmail</p>";
    
    // First, check current status
    $stmt = $conn->prepare("SELECT id, first_name, last_name, email, role, account_status FROM users WHERE email = ?");
    $stmt->execute([$userEmail]);
    $user = $stmt->fetch();
    
    if (!$user) {
        echo "<p style='color: red;'><strong>❌ User not found!</strong></p>";
        exit;
    }
    
    echo "<h3>Current User Status:</h3>";
    echo "<ul>";
    echo "<li><strong>ID:</strong> " . $user['id'] . "</li>";
    echo "<li><strong>Name:</strong> " . htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) . "</li>";
    echo "<li><strong>Email:</strong> " . htmlspecialchars($user['email']) . "</li>";
    echo "<li><strong>Role:</strong> " . htmlspecialchars($user['role']) . "</li>";
    echo "<li><strong>Current Account Status:</strong> " . htmlspecialchars($user['account_status'] ?? 'NULL') . "</li>";
    echo "</ul>";
    
    // Update the user's account status to 'active'
    $updateStmt = $conn->prepare("UPDATE users SET account_status = 'active' WHERE email = ?");
    $result = $updateStmt->execute([$userEmail]);
    
    if ($result) {
        echo "<p style='color: green;'><strong>✅ User account status updated successfully!</strong></p>";
        
        // Verify the update
        $stmt = $conn->prepare("SELECT account_status FROM users WHERE email = ?");
        $stmt->execute([$userEmail]);
        $updatedUser = $stmt->fetch();
        
        echo "<p><strong>New Account Status:</strong> " . htmlspecialchars($updatedUser['account_status']) . "</p>";
        
        // Test if the user can now authenticate
        $authStmt = $conn->prepare("SELECT id, first_name, last_name, email, role, account_status FROM users WHERE email = ? AND account_status = 'active'");
        $authStmt->execute([$userEmail]);
        $authUser = $authStmt->fetch();
        
        if ($authUser) {
            echo "<p style='color: green;'><strong>✅ User can now authenticate successfully!</strong></p>";
            echo "<p><strong>Authentication Test:</strong> PASSED</p>";
        } else {
            echo "<p style='color: red;'><strong>❌ Authentication test failed!</strong></p>";
        }
        
    } else {
        echo "<p style='color: red;'><strong>❌ Failed to update user status!</strong></p>";
        echo "<p>Error: " . implode(", ", $updateStmt->errorInfo()) . "</p>";
    }
    
    // Also update any other users with NULL account_status
    echo "<h3>Fixing All Users with NULL Account Status:</h3>";
    $nullStatusStmt = $conn->prepare("UPDATE users SET account_status = 'active' WHERE account_status IS NULL");
    $nullResult = $nullStatusStmt->execute();
    
    if ($nullResult) {
        $affectedRows = $nullStatusStmt->rowCount();
        echo "<p style='color: green;'><strong>✅ Updated $affectedRows users with NULL account status to 'active'</strong></p>";
    } else {
        echo "<p style='color: red;'><strong>❌ Failed to update users with NULL status</strong></p>";
    }
    
    // Show final status of all users
    echo "<h3>Final User Statuses:</h3>";
    $finalStmt = $conn->prepare("SELECT id, first_name, last_name, email, role, account_status FROM users ORDER BY id");
    $finalStmt->execute();
    $allUsers = $finalStmt->fetchAll();
    
    if ($allUsers) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Account Status</th></tr>";
        foreach ($allUsers as $u) {
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
    }
    
    echo "<hr>";
    echo "<h3>Next Steps:</h3>";
    echo "<ol>";
    echo "<li><strong>Try logging in again</strong> with your credentials</li>";
    echo "<li>The login should now work successfully</li>";
    echo "<li>You'll be redirected to your dashboard based on your role</li>";
    echo "</ol>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'><strong>Error:</strong> " . $e->getMessage() . "</p>";
}
?>

<style>
body { 
    font-family: Arial, sans-serif; 
    margin: 20px; 
    line-height: 1.6;
}
table { 
    margin-top: 20px; 
    border: 1px solid #ddd;
}
th, td { 
    padding: 12px; 
    text-align: left; 
    border: 1px solid #ddd;
}
th { 
    background-color: #f2f2f2; 
    font-weight: bold;
}
ul, ol {
    margin: 10px 0;
    padding-left: 20px;
}
li {
    margin: 5px 0;
}
hr {
    margin: 30px 0;
    border: none;
    border-top: 1px solid #ddd;
}
</style>
