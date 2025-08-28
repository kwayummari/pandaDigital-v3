<?php
require_once __DIR__ . "/../config/init.php";
require_once __DIR__ . "/../middleware/AuthMiddleware.php";

$auth = new AuthMiddleware();
$auth->requireRole('admin');

$pageTitle = 'User Tracking Analytics - Panda Digital';

// Get date range filters
$startDate = $_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
$endDate = $_GET['end_date'] ?? date('Y-m-d');
$pageType = $_GET['page_type'] ?? '';
$userId = $_GET['user_id'] ?? '';

// Build WHERE clause for filtering
$whereConditions = [];
$params = [];

if ($startDate && $endDate) {
    $whereConditions[] = "DATE(t.visit_date) BETWEEN ? AND ?";
    $params[] = $startDate;
    $params[] = $endDate;
}

if ($pageType) {
    $whereConditions[] = "t.page_type = ?";
    $params[] = $pageType;
}

if ($userId) {
    $whereConditions[] = "t.user_id = ?";
    $params[] = $userId;
}

$whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';

// Get tracking statistics
$statsQuery = "
    SELECT 
        COUNT(*) as total_visits,
        COUNT(DISTINCT t.user_id) as unique_users,
        COUNT(DISTINCT DATE(t.visit_date)) as total_days,
        AVG(daily_visits.visits_per_day) as avg_visits_per_day
    FROM user_page_tracking t
    LEFT JOIN (
        SELECT DATE(visit_date) as visit_date, COUNT(*) as visits_per_day
        FROM user_page_tracking
        GROUP BY DATE(visit_date)
    ) daily_visits ON DATE(t.visit_date) = daily_visits.visit_date
    $whereClause
";
$statsStmt = $pdo->prepare($statsQuery);
$statsStmt->execute($params);
$stats = $statsStmt->fetch();

// Get page popularity
$pageStatsQuery = "
    SELECT 
        t.page_type,
        COUNT(*) as total_visits,
        COUNT(DISTINCT t.user_id) as unique_users,
        AVG(visits_per_user.visits_per_user) as avg_visits_per_user
    FROM user_page_tracking t
    LEFT JOIN (
        SELECT user_id, page_type, COUNT(*) as visits_per_user
        FROM user_page_tracking
        GROUP BY user_id, page_type
    ) visits_per_user ON t.user_id = visits_per_user.user_id AND t.page_type = visits_per_user.page_type
    $whereClause
    GROUP BY t.page_type
    ORDER BY total_visits DESC
";
$pageStatsStmt = $pdo->prepare($pageStatsQuery);
$pageStatsStmt->execute($params);
$pageStats = $pageStatsStmt->fetchAll();

// Get most active users
$activeUsersQuery = "
    SELECT 
        u.first_name,
        u.last_name,
        u.email,
        u.role,
        COUNT(*) as total_visits,
        COUNT(DISTINCT t.page_type) as pages_visited,
        MAX(t.visit_date) as last_visit,
        MIN(t.visit_date) as first_visit
    FROM user_page_tracking t
    JOIN users u ON t.user_id = u.id
    $whereClause
    GROUP BY t.user_id
    ORDER BY total_visits DESC
    LIMIT 15
";
$activeUsersStmt = $pdo->prepare($activeUsersQuery);
$activeUsersStmt->execute($params);
$activeUsers = $activeUsersStmt->fetchAll();

// Get recent activity
$recentActivityQuery = "
    SELECT 
        t.*,
        u.first_name,
        u.last_name,
        u.email,
        u.role,
        c.name as course_name
    FROM user_page_tracking t
    JOIN users u ON t.user_id = u.id
    LEFT JOIN course c ON t.course_id = c.id
    $whereClause
    ORDER BY t.visit_date DESC
    LIMIT 50
";
$recentActivityStmt = $pdo->prepare($recentActivityQuery);
$recentActivityStmt->execute($params);
$recentActivity = $recentActivityStmt->fetchAll();

// Get daily activity chart data
$dailyActivityQuery = "
    SELECT 
        DATE(t.visit_date) as visit_date,
        COUNT(*) as total_visits,
        COUNT(DISTINCT t.user_id) as unique_users
    FROM user_page_tracking t
    $whereClause
    GROUP BY DATE(t.visit_date)
    ORDER BY visit_date DESC
    LIMIT 30
";
$dailyActivityStmt = $pdo->prepare($dailyActivityQuery);
$dailyActivityStmt->execute($params);
$dailyActivity = $dailyActivityStmt->fetchAll();

// Get all users for filter dropdown
$usersQuery = "SELECT id, first_name, last_name, email FROM users ORDER BY first_name, last_name";
$usersStmt = $pdo->prepare($usersQuery);
$usersStmt->execute();
$allUsers = $usersStmt->fetchAll();

// Get page types for filter dropdown
$pageTypesQuery = "SELECT DISTINCT page_type FROM user_page_tracking ORDER BY page_type";
$pageTypesStmt = $pdo->prepare($pageTypesQuery);
$pageTypesStmt->execute();
$pageTypes = $pageTypesStmt->fetchAll();

include 'includes/admin_header.php';
?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">User Tracking Analytics</h1>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-primary" onclick="exportData()">
                        <i class="fas fa-download me-2"></i>Export Data
                    </button>
                    <button class="btn btn-outline-secondary" onclick="refreshData()">
                        <i class="fas fa-sync-alt me-2"></i>Refresh
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Filters</h5>
        </div>
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label for="start_date" class="form-label">Start Date</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" value="<?= htmlspecialchars($startDate) ?>">
                </div>
                <div class="col-md-3">
                    <label for="end_date" class="form-label">End Date</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" value="<?= htmlspecialchars($endDate) ?>">
                </div>
                <div class="col-md-2">
                    <label for="page_type" class="form-label">Page Type</label>
                    <select class="form-select" id="page_type" name="page_type">
                        <option value="">All Pages</option>
                        <?php foreach ($pageTypes as $type): ?>
                            <option value="<?= htmlspecialchars($type['page_type']) ?>" <?= $pageType === $type['page_type'] ? 'selected' : '' ?>>
                                <?= ucfirst(str_replace('_', ' ', $type['page_type'])) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="user_id" class="form-label">User</label>
                    <select class="form-select" id="user_id" name="user_id">
                        <option value="">All Users</option>
                        <?php foreach ($allUsers as $user): ?>
                            <option value="<?= $user['id'] ?>" <?= $userId == $user['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-2"></i>Apply Filters
                    </button>
                </div>
            </form>
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
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= number_format($stats['total_visits'] ?? 0) ?></div>
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
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= number_format($stats['unique_users'] ?? 0) ?></div>
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
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= number_format($stats['total_days'] ?? 0) ?></div>
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
                                Avg Visits/Day</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= number_format($stats['avg_visits_per_day'] ?? 0, 1) ?>
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

    <!-- Charts Row -->
    <div class="row mb-4">
        <!-- Daily Activity Chart -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Daily Activity (Last 30 Days)</h6>
                </div>
                <div class="card-body">
                    <canvas id="dailyActivityChart" height="100"></canvas>
                </div>
            </div>
        </div>

        <!-- Page Type Distribution -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Page Type Distribution</h6>
                </div>
                <div class="card-body">
                    <canvas id="pageTypeChart" height="100"></canvas>
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
                                    <th>Avg Visits/User</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pageStats as $page): ?>
                                    <tr>
                                        <td><?= ucfirst(str_replace('_', ' ', $page['page_type'])) ?></td>
                                        <td><?= number_format($page['total_visits']) ?></td>
                                        <td><?= number_format($page['unique_users']) ?></td>
                                        <td><?= number_format($page['avg_visits_per_user'] ?? 0, 1) ?></td>
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
                                    <th>Role</th>
                                    <th>Total Visits</th>
                                    <th>Pages Visited</th>
                                    <th>Last Visit</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($activeUsers as $user): ?>
                                    <tr>
                                        <td>
                                            <div>
                                                <strong><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></strong>
                                                <br><small class="text-muted"><?= htmlspecialchars($user['email']) ?></small>
                                            </div>
                                        </td>
                                        <td><span class="badge bg-<?= $user['role'] === 'admin' ? 'danger' : ($user['role'] === 'expert' ? 'warning' : 'primary') ?>"><?= ucfirst($user['role']) ?></span></td>
                                        <td><?= number_format($user['total_visits']) ?></td>
                                        <td><?= number_format($user['pages_visited']) ?></td>
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
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Activity</h6>
                    <small class="text-muted">Showing last 50 activities</small>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Page Type</th>
                                    <th>Action</th>
                                    <th>Course Name</th>
                                    <th>Date</th>
                                    <th>IP Address</th>
                                    <th>Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentActivity as $activity): ?>
                                    <tr>
                                        <td>
                                            <div>
                                                <strong><?= htmlspecialchars($activity['first_name'] . ' ' . $activity['last_name']) ?></strong>
                                                <br><small class="text-muted"><?= htmlspecialchars($activity['email']) ?></small>
                                                <br><span class="badge bg-<?= $activity['role'] === 'admin' ? 'danger' : ($activity['role'] === 'expert' ? 'warning' : 'primary') ?>"><?= ucfirst($activity['role']) ?></span>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-info"><?= ucfirst(str_replace('_', ' ', $activity['page_type'])) ?></span>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?= $activity['action_type'] === 'download_click' ? 'success' : 'secondary' ?>">
                                                <?= ucfirst(str_replace('_', ' ', $activity['action_type'])) ?>
                                            </span>
                                        </td>
                                        <td><?= $activity['course_name'] ? htmlspecialchars($activity['course_name']) : '-' ?></td>
                                        <td><?= date('M j, Y H:i', strtotime($activity['visit_date'])) ?></td>
                                        <td><code><?= htmlspecialchars($activity['ip_address']) ?></code></td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-info" onclick="showActivityDetails(<?= htmlspecialchars(json_encode($activity)) ?>)">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </td>
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

<!-- Activity Details Modal -->
<div class="modal fade" id="activityDetailsModal" tabindex="-1" aria-labelledby="activityDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="activityDetailsModalLabel">Activity Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="activityDetailsContent">
                <!-- Content will be populated by JavaScript -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Daily Activity Chart
    const dailyActivityCtx = document.getElementById('dailyActivityChart').getContext('2d');
    new Chart(dailyActivityCtx, {
        type: 'line',
        data: {
            labels: <?= json_encode(array_reverse(array_column($dailyActivity, 'visit_date'))) ?>,
            datasets: [{
                label: 'Total Visits',
                data: <?= json_encode(array_reverse(array_column($dailyActivity, 'total_visits'))) ?>,
                borderColor: '#4e73df',
                backgroundColor: 'rgba(78, 115, 223, 0.1)',
                tension: 0.4,
                fill: true
            }, {
                label: 'Unique Users',
                data: <?= json_encode(array_reverse(array_column($dailyActivity, 'unique_users'))) ?>,
                borderColor: '#1cc88a',
                backgroundColor: 'rgba(28, 200, 138, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0,0,0,0.1)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });

    // Page Type Distribution Chart
    const pageTypeCtx = document.getElementById('pageTypeChart').getContext('2d');
    new Chart(pageTypeCtx, {
        type: 'doughnut',
        data: {
            labels: <?= json_encode(array_map(function ($page) {
                        return ucfirst(str_replace('_', ' ', $page['page_type']));
                    }, $pageStats)) ?>,
            datasets: [{
                data: <?= json_encode(array_column($pageStats, 'total_visits')) ?>,
                backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Show activity details
    function showActivityDetails(activity) {
        const modal = new bootstrap.Modal(document.getElementById('activityDetailsModal'));
        const content = document.getElementById('activityDetailsContent');

        content.innerHTML = `
            <div class="row">
                <div class="col-md-6">
                    <h6>User Information</h6>
                    <p><strong>Name:</strong> ${activity.first_name} ${activity.last_name}</p>
                    <p><strong>Email:</strong> ${activity.email}</p>
                    <p><strong>Role:</strong> ${activity.role}</p>
                </div>
                <div class="col-md-6">
                    <h6>Activity Information</h6>
                    <p><strong>Page Type:</strong> ${activity.page_type.replace(/_/g, ' ')}</p>
                    <p><strong>Action:</strong> ${activity.action_type.replace(/_/g, ' ')}</p>
                    <p><strong>Date:</strong> ${new Date(activity.visit_date).toLocaleString()}</p>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-6">
                    <h6>Technical Details</h6>
                    <p><strong>IP Address:</strong> ${activity.ip_address}</p>
                    <p><strong>Session ID:</strong> ${activity.session_id || 'N/A'}</p>
                    <p><strong>Course Name:</strong> ${activity.course_name || 'N/A'}</p>
                </div>
                <div class="col-md-6">
                    <h6>Page Information</h6>
                    <p><strong>URL:</strong> ${activity.page_url}</p>
                    <p><strong>User Agent:</strong> <small>${activity.user_agent || 'N/A'}</small></p>
                </div>
            </div>
        `;

        modal.show();
    }

    // Export data function
    function exportData() {
        const currentUrl = new URL(window.location);
        const exportUrl = new URL('export-tracking.php', window.location.origin + window.location.pathname.replace('user-tracking.php', ''));

        // Add current filters to export URL
        exportUrl.searchParams.set('start_date', document.getElementById('start_date').value);
        exportUrl.searchParams.set('end_date', document.getElementById('end_date').value);
        exportUrl.searchParams.set('page_type', document.getElementById('page_type').value);
        exportUrl.searchParams.set('user_id', document.getElementById('user_id').value);

        window.location.href = exportUrl.toString();
    }

    // Refresh data function
    function refreshData() {
        window.location.reload();
    }
</script>

<?php include 'includes/admin_footer.php'; ?>