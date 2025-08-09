<?php
require_once 'config/init.php';
require_once 'models/Fursa.php';

echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Test Fursa - Panda Digital V3</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
</head>
<body>
    <div class='container mt-5'>
        <h1>🐼 Test Fursa Database Connection</h1>";

try {
    // Test database connection
    echo "<div class='alert alert-info'>
        <h5>🔧 Testing Database Connection...</h5>";

    $fursaModel = new Fursa();
    echo "<p>✅ Fursa model initialized successfully</p>";

    // Test getting latest opportunities
    echo "<h5>📊 Testing Latest Opportunities...</h5>";
    $latestOpportunities = $fursaModel->getLatestOpportunities(5);

    if (!empty($latestOpportunities)) {
        echo "<p>✅ Found " . count($latestOpportunities) . " opportunities</p>";
        echo "<div class='table-responsive'>
            <table class='table table-striped'>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Image</th>
                        <th>Date Created</th>
                    </tr>
                </thead>
                <tbody>";

        foreach ($latestOpportunities as $opp) {
            echo "<tr>
                <td>{$opp['id']}</td>
                <td>" . htmlspecialchars($opp['name']) . "</td>
                <td>" . htmlspecialchars(substr($opp['description'], 0, 100)) . "...</td>
                <td>" . htmlspecialchars($opp['image']) . "</td>
                <td>" . htmlspecialchars($opp['date_created']) . "</td>
            </tr>";
        }

        echo "</tbody></table></div>";

        // Test specific ID
        echo "<h5>🔍 Testing Specific ID (24)...</h5>";
        $specificOpportunity = $fursaModel->getOpportunityById(24);

        if ($specificOpportunity) {
            echo "<div class='alert alert-success'>
                <h6>✅ Opportunity ID 24 Found!</h6>
                <p><strong>Name:</strong> " . htmlspecialchars($specificOpportunity['name']) . "</p>
                <p><strong>Description:</strong> " . htmlspecialchars(substr($specificOpportunity['description'], 0, 200)) . "...</p>
                <p><strong>Image:</strong> " . htmlspecialchars($specificOpportunity['image']) . "</p>
                <p><strong>Date Created:</strong> " . htmlspecialchars($specificOpportunity['date_created']) . "</p>
            </div>";
        } else {
            echo "<div class='alert alert-warning'>
                <h6>⚠️ Opportunity ID 24 Not Found</h6>
                <p>This means the opportunity with ID 24 doesn't exist in the database.</p>
            </div>";
        }
    } else {
        echo "<div class='alert alert-warning'>
            <h6>⚠️ No Opportunities Found</h6>
            <p>No opportunities were found in the database. This could mean:</p>
            <ul>
                <li>The database is empty</li>
                <li>The table structure is different</li>
                <li>There's a database connection issue</li>
            </ul>
        </div>";
    }
} catch (Exception $e) {
    echo "<div class='alert alert-danger'>
        <h6>❌ Error Occurred</h6>
        <p><strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>
        <p><strong>File:</strong> " . htmlspecialchars($e->getFile()) . "</p>
        <p><strong>Line:</strong> " . htmlspecialchars($e->getLine()) . "</p>
    </div>";
}

echo "<div class='mt-4'>
    <a href='index.php' class='btn btn-primary'>🏠 Go to Homepage</a>
    <a href='fursa-details.php?id=1' class='btn btn-secondary'>🔗 Test Fursa ID 1</a>
    <a href='setup.php?setup=1' class='btn btn-info'>⚙️ Setup Wizard</a>
</div>
</div>
</body>
</html>";
