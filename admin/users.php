<?php
require_once __DIR__ . "/../middleware/AuthMiddleware.php";
require_once __DIR__ . "/../models/User.php";

$auth = new AuthMiddleware();
$auth->requireRole('admin');

$currentUser = $auth->getCurrentUser();
$userModel = new User();

// Handle user actions
if ($_POST && isset($_POST['action'])) {
    $userId = $_POST['user_id'];
    $action = $_POST['action'];

    if ($action === 'delete') {
        $result = $userModel->deleteUser($userId);
        if ($result) {
            $success = "Mtumiaji amefutwa kikamilifu!";
        } else {
            $error = "Imefeli kufuta mtumiaji. Tafadhali jaribu tena.";
        }
    } elseif ($action === 'toggle_status') {
        $result = $userModel->toggleUserStatus($userId);
        if ($result) {
            $success = "Hali ya mtumiaji imebadilishwa!";
        } else {
            $error = "Imefeli kubadilisha hali ya mtumiaji. Tafadhali jaribu tena.";
        }
    }
}

// Get all users with pagination
$page = $_GET['page'] ?? 1;
$perPage = 20;
$users = $userModel->getAllUsers($page, $perPage);
$totalUsers = $userModel->getTotalUsers();
$totalPages = ceil($totalUsers / $perPage);

// Get user statistics
$userStats = $userModel->getUserStatsByRole();
?>

<!DOCTYPE html>
<html lang="sw">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usimamizi wa Watumiaji - Panda Digital</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= app_url('assets/css/style.css') ?>?v=5">
</head>

<body>
    <!-- Sidebar and Main Content Layout -->
    <div class="dashboard-container">
        <?php include __DIR__ . '/includes/admin_sidebar.php'; ?>

        <!-- Main Content -->
        <div class="main-content">
            <?php include __DIR__ . '/includes/admin_topnav.php'; ?>

            <div class="content-wrapper">
                <!-- Page Header -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h1 class="h3 mb-0">
                            <i class="fas fa-users text-primary me-2"></i>
                            Usimamizi wa Watumiaji
                        </h1>
                        <p class="text-muted">Udhibiti watumiaji wote wa mfumo</p>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card stats-card">
                            <div class="card-body text-center">
                                <i class="fas fa-users fa-2x mb-2"></i>
                                <h3 class="mb-1"><?php echo $totalUsers; ?></h3>
                                <p class="mb-0">Jumla ya Watumiaji</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stats-card success">
                            <div class="card-body text-center">
                                <i class="fas fa-user-graduate fa-2x mb-2"></i>
                                <h3 class="mb-1"><?php echo $userStats['user'] ?? 0; ?></h3>
                                <p class="mb-0">Wanafunzi</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stats-card info">
                            <div class="card-body text-center">
                                <i class="fas fa-user-tie fa-2x mb-2"></i>
                                <h3 class="mb-1"><?php echo $userStats['expert'] ?? 0; ?></h3>
                                <p class="mb-0">Wataalam</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stats-card warning">
                            <div class="card-body text-center">
                                <i class="fas fa-user-shield fa-2x mb-2"></i>
                                <h3 class="mb-1"><?php echo $userStats['admin'] ?? 0; ?></h3>
                                <p class="mb-0">Wakurugenzi</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Search and Filter Box -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-search"></i>
                                    </span>
                                    <input type="text" class="form-control" id="searchInput" placeholder="Tafuta watumiaji...">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <select class="form-select" id="roleFilter">
                                    <option value="">Jumla ya Majukumu</option>
                                    <option value="user">Mwanafunzi</option>
                                    <option value="expert">Mtaalam</option>
                                    <option value="admin">Mkurugenzi</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select class="form-select" id="statusFilter">
                                    <option value="">Jumla ya Hali</option>
                                    <option value="active">Inatumika</option>
                                    <option value="suspended">Imezimwa</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button class="btn btn-primary w-100" onclick="exportUsers()">
                                    <i class="fas fa-download me-1"></i> Pakua
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Alerts -->
                <?php if (isset($success)): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        <?php echo $success; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <?php echo $error; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Users Table -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-list me-2"></i>
                            Orodha ya Watumiaji
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Jina</th>
                                        <th>Barua Pepe</th>
                                        <th>Simu</th>
                                        <th>Tarehe ya Usajili</th>
                                        <th>Jukumu</th>
                                        <th>Hali</th>
                                        <th>Vitendo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($users as $user): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm me-3">
                                                        <i class="fas fa-user-circle fa-2x text-muted"></i>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></h6>
                                                        <small class="text-muted">ID: <?php echo $user['id']; ?></small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                                            <td><?php echo htmlspecialchars($user['phone'] ?? 'Haijulikani'); ?></td>
                                            <td><?php echo date('d/m/Y', strtotime($user['created_at'])); ?></td>
                                            <td>
                                                <span class="badge badge-role bg-<?php echo $user['role'] === 'admin' ? 'danger' : ($user['role'] === 'expert' ? 'warning' : 'info'); ?>">
                                                    <?php echo ucfirst($user['role']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge badge-status bg-<?php echo $user['status'] === 'active' ? 'success' : 'secondary'; ?>">
                                                    <?php echo $user['status'] === 'active' ? 'Inatumika' : 'Imezimwa'; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="viewUser(<?php echo $user['id']; ?>)">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-outline-warning" onclick="toggleUserStatus(<?php echo $user['id']; ?>, '<?php echo $user['status']; ?>')">
                                                        <i class="fas fa-<?php echo $user['status'] === 'active' ? 'ban' : 'check'; ?>"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteUser(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <?php if ($totalPages > 1): ?>
                            <nav aria-label="User pagination" class="mt-4">
                                <ul class="pagination justify-content-center">
                                    <?php if ($page > 1): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?page=<?php echo $page - 1; ?>">
                                                <i class="fas fa-chevron-left"></i>
                                            </a>
                                        </li>
                                    <?php endif; ?>

                                    <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                                        <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                            <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                        </li>
                                    <?php endfor; ?>

                                    <?php if ($page < $totalPages): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?page=<?php echo $page + 1; ?>">
                                                <i class="fas fa-chevron-right"></i>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </nav>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Search functionality
        document.getElementById('searchInput').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('tbody tr');

            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });

        // Filter functionality
        document.getElementById('roleFilter').addEventListener('change', filterUsers);
        document.getElementById('statusFilter').addEventListener('change', filterUsers);

        function filterUsers() {
            const roleFilter = document.getElementById('roleFilter').value;
            const statusFilter = document.getElementById('statusFilter').value;
            const rows = document.querySelectorAll('tbody tr');

            rows.forEach(row => {
                const role = row.querySelector('td:nth-child(5)').textContent.trim();
                const status = row.querySelector('td:nth-child(6)').textContent.trim();

                const roleMatch = !roleFilter || role.includes(roleFilter);
                const statusMatch = !statusFilter || status.includes(statusFilter);

                row.style.display = (roleMatch && statusMatch) ? '' : 'none';
            });
        }

        // Toggle user status
        function toggleUserStatus(userId, currentStatus) {
            const newStatus = currentStatus === 'active' ? 'suspended' : 'active';
            const action = newStatus === 'active' ? 'kuwezesha' : 'kufunga';

            if (confirm(`Je, una uhakika unataka ${action} mtumiaji huyu?`)) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="user_id" value="${userId}">
                    <input type="hidden" name="action" value="toggle_status">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        // Delete user
        function deleteUser(userId, userName) {
            if (confirm(`Je, una uhakika unataka kufuta mtumiaji "${userName}"? Kitendo hiki hakiwezi kubatilishwa!`)) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="user_id" value="${userId}">
                    <input type="hidden" name="action" value="delete">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        // View user details
        function viewUser(userId) {
            // Implement user view functionality
            alert('View user functionality will be implemented here');
        }

        // Export users
        function exportUsers() {
            // Implement export functionality
            alert('Export functionality will be implemented here');
        }

        // Update page title
        document.addEventListener('DOMContentLoaded', function() {
            const pageTitle = document.getElementById('pageTitle');
            if (pageTitle) {
                pageTitle.textContent = 'Usimamizi wa Watumiaji';
            }
        });
    </script>

    <?php include __DIR__ . '/includes/admin_footer_common.php'; ?>