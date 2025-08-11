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
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #662e91;
            --secondary-color: #FFC10B;
            --accent-color: #e74c3c;
            --success-color: #27ae60;
            --warning-color: #f39c12;
            --info-color: #3498db;
        }

        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .navbar {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .main-content {
            padding: 20px;
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .stats-card {
            background: linear-gradient(135deg, var(--accent-color), #c0392b);
            color: white;
        }

        .stats-card.success {
            background: linear-gradient(135deg, var(--success-color), #229954);
        }

        .stats-card.info {
            background: linear-gradient(135deg, var(--info-color), #2980b9);
        }

        .stats-card.warning {
            background: linear-gradient(135deg, var(--warning-color), #d68910);
        }

        .btn-primary-custom {
            background: var(--primary-color);
            border: none;
            border-radius: 25px;
            padding: 10px 20px;
            font-weight: 600;
        }

        .btn-primary-custom:hover {
            background: var(--secondary-color);
            transform: translateY(-1px);
        }

        .user-table {
            background: white;
            border-radius: 15px;
            overflow: hidden;
        }

        .user-table th {
            background: var(--primary-color);
            color: white;
            border: none;
            font-weight: 600;
        }

        .user-table td {
            vertical-align: middle;
        }

        .badge-role {
            font-size: 0.8rem;
            padding: 6px 12px;
            border-radius: 15px;
        }

        .badge-status {
            font-size: 0.8rem;
            padding: 6px 12px;
            border-radius: 15px;
        }

        .search-box {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .alert {
            border-radius: 10px;
            border: none;
        }

        .pagination .page-link {
            border-radius: 10px;
            margin: 0 2px;
            border: none;
            color: var(--primary-color);
        }

        .pagination .page-item.active .page-link {
            background: var(--primary-color);
            border-color: var(--primary-color);
        }
    </style>
</head>

<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="/admin/dashboard.php">
                <i class="fas fa-shield-alt me-2"></i>
                Panda Digital - Admin
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="/admin/dashboard.php">
                    <i class="fas fa-tachometer-alt me-1"></i> Dashboard
                </a>
                <a class="nav-link active" href="/admin/users.php">
                    <i class="fas fa-users me-1"></i> Watumiaji
                </a>
                <a class="nav-link" href="/admin/expert-requests.php">
                    <i class="fas fa-user-graduate me-1"></i> Maombi ya Mitaalam
                </a>
                <a class="nav-link" href="/admin/courses.php">
                    <i class="fas fa-book me-1"></i> Kozi
                </a>
                <a class="nav-link" href="/logout.php">
                    <i class="fas fa-sign-out-alt me-1"></i> Toka
                </a>
            </div>
        </div>
    </nav>

    <div class="main-content">
        <div class="container">
            <!-- Header -->
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
                            <i class="fas fa-chalkboard-teacher fa-2x mb-2"></i>
                            <h3 class="mb-1"><?php echo $userStats['expert'] ?? 0; ?></h3>
                            <p class="mb-0">Mitaalam</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stats-card warning">
                        <div class="card-body text-center">
                            <i class="fas fa-shield-alt fa-2x mb-2"></i>
                            <h3 class="mb-1"><?php echo $userStats['admin'] ?? 0; ?></h3>
                            <p class="mb-0">Wasimamizi</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Alerts -->
            <?php if (isset($success)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    <?php echo htmlspecialchars($success); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <?php echo htmlspecialchars($error); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Search and Filters -->
            <div class="search-box">
                <div class="row">
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" class="form-control" id="searchInput" placeholder="Tafuta watumiaji...">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="roleFilter">
                            <option value="">Majukumu Yote</option>
                            <option value="user">Wanafunzi</option>
                            <option value="expert">Mitaalam</option>
                            <option value="admin">Wasimamizi</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="statusFilter">
                            <option value="">Hali Zote</option>
                            <option value="active">Wanafanya Kazi</option>
                            <option value="pending">Wanasubiri</option>
                            <option value="suspended">Wamefungwa</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Users Table -->
            <div class="card user-table">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-list me-2"></i>
                        Orodha ya Watumiaji
                    </h5>
                    <a href="/admin/add-user.php" class="btn btn-primary-custom text-white">
                        <i class="fas fa-plus me-2"></i>
                        Ongeza Mtumiaji
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Jina</th>
                                    <th>Barua Pepe</th>
                                    <th>Simu</th>
                                    <th>Jukumu</th>
                                    <th>Hali</th>
                                    <th>Tarehe ya Usajili</th>
                                    <th>Vitendo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td>
                                            <span class="badge bg-secondary">#<?php echo $user['id']; ?></span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="me-3">
                                                    <i class="fas fa-user-circle text-primary fa-lg"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-bold">
                                                        <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>
                                                    </div>
                                                    <small class="text-muted">
                                                        <?php echo htmlspecialchars($user['region'] ?? 'N/A'); ?>
                                                    </small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <a href="mailto:<?php echo htmlspecialchars($user['email']); ?>"
                                                class="text-decoration-none">
                                                <?php echo htmlspecialchars($user['email']); ?>
                                            </a>
                                        </td>
                                        <td>
                                            <?php if ($user['phone'] && $user['phone'] !== 'null'): ?>
                                                <a href="tel:<?php echo htmlspecialchars($user['phone']); ?>"
                                                    class="text-decoration-none">
                                                    <?php echo htmlspecialchars($user['phone']); ?>
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted">N/A</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge badge-role bg-<?php
                                                                                switch ($user['role']) {
                                                                                    case 'admin':
                                                                                        echo 'danger';
                                                                                        break;
                                                                                    case 'expert':
                                                                                        echo 'warning';
                                                                                        break;
                                                                                    default:
                                                                                        echo 'success';
                                                                                }
                                                                                ?>">
                                                <?php
                                                switch ($user['role']) {
                                                    case 'admin':
                                                        echo 'Msimamizi';
                                                        break;
                                                    case 'expert':
                                                        echo 'Mtaalam';
                                                        break;
                                                    default:
                                                        echo 'Mwanafunzi';
                                                }
                                                ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge badge-status bg-<?php
                                                                                switch ($user['account_status']) {
                                                                                    case 'active':
                                                                                        echo 'success';
                                                                                        break;
                                                                                    case 'pending':
                                                                                        echo 'warning';
                                                                                        break;
                                                                                    case 'suspended':
                                                                                        echo 'danger';
                                                                                        break;
                                                                                    default:
                                                                                        echo 'secondary';
                                                                                }
                                                                                ?>">
                                                <?php
                                                switch ($user['account_status']) {
                                                    case 'active':
                                                        echo 'Wanafanya Kazi';
                                                        break;
                                                    case 'pending':
                                                        echo 'Wanasubiri';
                                                        break;
                                                    case 'suspended':
                                                        echo 'Wamefungwa';
                                                        break;
                                                    default:
                                                        echo 'Haijulikani';
                                                }
                                                ?>
                                            </span>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                <?php echo date('d M Y', strtotime($user['date_created'])); ?>
                                            </small>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="/admin/edit-user.php?id=<?php echo $user['id']; ?>"
                                                    class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-<?php echo $user['account_status'] === 'active' ? 'warning' : 'success'; ?>"
                                                    onclick="toggleUserStatus(<?php echo $user['id']; ?>, '<?php echo $user['account_status']; ?>')">
                                                    <i class="fas fa-<?php echo $user['account_status'] === 'active' ? 'pause' : 'play'; ?>"></i>
                                                </button>
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-danger"
                                                    onclick="deleteUser(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
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
    </script>
</body>

</html>