<?php
require_once 'config/init.php';
require_once 'models/Course.php';

echo "<h1>Database Debug Test</h1>";

try {
    // Test database connection
    echo "<h2>Testing Database Connection</h2>";
    $db = new Database();
    $conn = $db->getConnection();
    echo "<p style='color: green;'>✅ Database connection successful!</p>";

    // Test course query directly
    echo "<h2>Testing Direct Course Query</h2>";
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM course");
    $stmt->execute();
    $result = $stmt->fetch();
    echo "<p>Total courses in database: <strong>" . $result['total'] . "</strong></p>";

    // Test course query with data
    $stmt = $conn->prepare("SELECT id, name, description, photo, date_created FROM course LIMIT 5");
    $stmt->execute();
    $courses = $stmt->fetchAll();

    echo "<h3>First 5 courses:</h3>";
    echo "<ul>";
    foreach ($courses as $course) {
        echo "<li><strong>ID:</strong> " . $course['id'] . " | <strong>Name:</strong> " . htmlspecialchars($course['name']) . "</li>";
    }
    echo "</ul>";

    // Test Course model
    echo "<h2>Testing Course Model</h2>";
    $courseModel = new Course();
    $featuredCourses = $courseModel->getFeaturedCourses(8);
    echo "<p>Featured courses from model: <strong>" . count($featuredCourses) . "</strong></p>";

    if (!empty($featuredCourses)) {
        echo "<h3>First course from model:</h3>";
        echo "<p><strong>Name:</strong> " . htmlspecialchars($featuredCourses[0]['name']) . "</p>";
        echo "<p><strong>Description:</strong> " . htmlspecialchars(substr($featuredCourses[0]['description'], 0, 100)) . "...</p>";
    }

    // Test course categories
    $categories = $courseModel->getCourseCategories();
    echo "<p>Course categories: <strong>" . count($categories) . "</strong></p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
    echo "<p><strong>File:</strong> " . $e->getFile() . "</p>";
    echo "<p><strong>Line:</strong> " . $e->getLine() . "</p>";
}

// Test environment variables
echo "<h2>Environment Variables</h2>";
echo "<p>DB_HOST: " . (env('DB_HOST') ?: 'Not set') . "</p>";
echo "<p>DB_NAME: " . (env('DB_NAME') ?: 'Not set') . "</p>";
echo "<p>DB_USER: " . (env('DB_USER') ?: 'Not set') . "</p>";
echo "<p>DB_PASSWORD: " . (env('DB_PASSWORD') ? 'Set' : 'Not set') . "</p>";

// Test helper functions
echo "<h2>Helper Functions</h2>";
echo "<p>asset() function: " . (function_exists('asset') ? 'Available' : 'Not available') . "</p>";
echo "<p>app_url() function: " . (function_exists('app_url') ? 'Available' : 'Not available') . "</p>";
echo "<p>env() function: " . (function_exists('env') ? 'Available' : 'Not available') . "</p>";

// Test appConfig
echo "<h2>App Config</h2>";
if (isset($appConfig)) {
    echo "<p>App name: " . htmlspecialchars($appConfig['name']) . "</p>";
    echo "<p>App URL: " . htmlspecialchars($appConfig['url']) . "</p>";
} else {
    echo "<p style='color: red;'>❌ App config not available</p>";
}
