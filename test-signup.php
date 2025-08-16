<?php
// Simple test script to test signup
echo "Testing signup process...\n";

// Test database connection first
try {
    require_once 'config/init.php';
    $database = new Database();
    $db = $database->getConnection();

    if (!$db) {
        throw new Exception('Database connection failed');
    }

    echo "✅ Database connection successful\n";

    // Test if users table exists
    $stmt = $db->query("SHOW TABLES LIKE 'users'");
    if ($stmt->rowCount() > 0) {
        echo "✅ Users table exists\n";

        // Test table structure
        $stmt = $db->query("DESCRIBE users");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "✅ Users table columns:\n";
        foreach ($columns as $col) {
            echo "   - {$col['Field']} ({$col['Type']})\n";
        }

        // Test insert
        $email = 'test' . time() . '@example.com';
        $password = 'test123';
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        echo "\n🔄 Testing insert with email: $email\n";

        $stmt = $db->prepare("INSERT INTO users (email, pass, first_name, last_name, role, status, profile_photo, bio, expert_authorization) VALUES (?, ?, '', '', 'user', 'active', '', '', 0)");

        $result = $stmt->execute([$email, $hashedPassword]);

        if ($result) {
            echo "✅ Insert successful! User ID: " . $db->lastInsertId() . "\n";

            // Clean up - delete test user
            $stmt = $db->prepare("DELETE FROM users WHERE email = ?");
            $stmt->execute([$email]);
            echo "✅ Test user cleaned up\n";
        } else {
            echo "❌ Insert failed\n";
        }
    } else {
        echo "❌ Users table does not exist\n";
    }
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Error code: " . $e->getCode() . "\n";
}

echo "\nTest completed.\n";
