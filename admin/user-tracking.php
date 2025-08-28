<?php
require_once __DIR__ . "/../config/init.php";
require_once __DIR__ . "/../middleware/AuthMiddleware.php";

$auth = new AuthMiddleware();
$auth->requireRole('admin');

$pageTitle = 'User Tracking Analytics - Panda Digital';

// Get tracking statistics
$statsQuery = "
    SELECT 
        COUNT(*) as total_visits,
        COUNT(DISTINCT user_id) as unique_users,
        COUNT(DISTINCT DATE(visit_date)) as total_days
    FROM user_page_tracking
";
$statsStmt = $pdo->prepare($statsQuery);
$statsStmt->execute();
$stats = $statsStmt->fetch();

// Get page popularity
$pageStatsQuery = "
    SELECT 
        page_type,
        COUNT(*) as total_visits,
        COUNT(DISTINCT user_id) as unique_users
    FROM user_page_tracking
    GROUP BY page_type
    ORDER BY total_visits DESC
";
$pageStatsStmt = $pdo->prepare($pageStatsQuery);
$pageStatsStmt->execute();
$pageStats = $pageStatsStmt->fetchAll();

// Get most active users
$activeUsersQuery = "
    SELECT 
        u.first_name,
        u.last_name,
        u.email,
        COUNT(*) as total_visits,
        MAX(t.visit_date) as last_visit
    FROM user_page_tracking t
    JOIN users u ON t.user_id = u.id
    GROUP BY t.user_id
    ORDER BY total_visits DESC
    LIMIT 10
";
$activeUsersStmt = $pdo->prepare($activeUsersQuery);
$activeUsersStmt->execute();
$activeUsers = $activeUsersStmt->fetchAll();

// Get recent activity
$recentActivityQuery = "
    SELECT 
        t.*,
        u.first_name,
        u.last_name
    FROM user_page_tracking t
    JOIN users u ON t.user_id = u.id
    ORDER BY t.visit_date DESC
    LIMIT 20
";
$recentActivityStmt = $pdo->prepare($recentActivityQuery);
$recentActivityStmt->execute();
$recentActivity = $recentActivityStmt->fetchAll();

include '../includes/header.php';
?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <h1 class="h3 mb-4">User Tracking Analytics</h1>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Visits</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= number_format($stats['total_visits']) ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Unique Users</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= number_format($stats['unique_users']) ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Tracking Days</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= number_format($stats['total_days']) ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Avg Visits/User</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= $stats['unique_users'] > 0 ? number_format($stats['total_visits'] / $stats['unique_users'], 1) : 0 ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Page Popularity -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Page Popularity</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Page Type</th>
                                    <th>Total Visits</th>
                                    <th>Unique Users</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pageStats as $page): ?>
                                <tr>
                                    <td><?= ucfirst(str_replace('_', ' ', $page['page_type'])) ?></td>
                                    <td><?= number_format($page['total_visits']) ?></td>
                                    <td><?= number_format($page['unique_users']) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Most Active Users -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Most Active Users</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Total Visits</th>
                                    <th>Last Visit</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($activeUsers as $user): ?>
                                <tr>
                                    <td><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></td>
                                    <td><?= number_format($user['total_visits']) ?></td>
                                    <td><?= date('M j, Y H:i', strtotime($user['last_visit'])) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Activity</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Page Type</th>
                                    <th>Action</th>
                                    <th>Course ID</th>
                                    <th>Date</th>
                                    <th>IP Address</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentActivity as $activity): ?>
                                <tr>
                                    <td><?= htmlspecialchars($activity['first_name'] . ' ' . $activity['last_name']) ?></td>
                                    <td><?= ucfirst(str_replace('_', ' ', $activity['page_type'])) ?></td>
                                    <td><?= ucfirst(str_replace('_', ' ', $activity['action_type'])) ?></td>
                                    <td><?= $activity['course_id'] ?: '-' ?></td>
                                    <td><?= date('M j, Y H:i', strtotime($activity['visit_date'])) ?></td>
                                    <td><?= htmlspecialchars($activity['ip_address']) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
