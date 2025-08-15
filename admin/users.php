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

    <style>
        .table th {
            background-color: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
            font-weight: 600;
            color: #495057;
        }

        .table td {
            vertical-align: middle;
        }

        .badge-role,
        .badge-status {
            font-size: 0.75rem;
            padding: 0.375rem 0.75rem;
            border-radius: 0.375rem;
        }

        .btn-group .btn {
            margin-right: 2px;
        }

        .avatar-sm {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .dropdown-menu {
            min-width: 200px;
        }

        .dropdown-item {
            padding: 0.5rem 1rem;
            transition: all 0.2s ease;
        }

        .dropdown-item:hover {
            background-color: #f8f9fa;
            transform: translateX(5px);
        }

        .dropdown-item i {
            width: 20px;
            text-align: center;
        }

        /* Custom dropdown styling */
        #exportDropdownContainer {
            position: relative;
        }

        #exportDropdownMenu {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            z-index: 1000;
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            margin-top: 0.125rem;
        }

        #exportDropdownMenu .dropdown-item {
            padding: 0.5rem 1rem;
            border-bottom: 1px solid #f8f9fa;
            transition: all 0.2s ease;
        }

        #exportDropdownMenu .dropdown-item:last-child {
            border-bottom: none;
        }

        #exportDropdownMenu .dropdown-item:hover {
            background-color: #f8f9fa;
            transform: translateX(5px);
        }
        
        /* User view modal styling */
        .user-avatar-large {
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        .modal-lg .modal-body {
            padding: 2rem;
        }
        
        .form-label.fw-bold {
            color: #495057;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .modal-body p {
            color: #6c757d;
            font-size: 1rem;
        }
        
        .modal-body .badge {
            font-size: 0.875rem;
            padding: 0.5rem 1rem;
        }
    </style>
</head>

<body>
    <!-- Sidebar and Main Content Layout -->
    <div class="dashboard-container">
        <?php include __DIR__ . '/includes/admin_sidebar.php'; ?>

        <!-- Main Content -->
        <div class="main-content">
            <?php include __DIR__ . '/includes/admin_topnav.php'; ?>

            <div class="content-wrapper">


                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card stats-card">
                            <div class="card-body text-center">
                                <h3 class="mb-1"><?php echo $totalUsers; ?></h3>
                                <p class="mb-0">Jumla ya Watumiaji</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stats-card success">
                            <div class="card-body text-center">
                                <h3 class="mb-1"><?php echo $userStats['user'] ?? 0; ?></h3>
                                <p class="mb-0">Wanafunzi</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stats-card info">
                            <div class="card-body text-center">
                                <h3 class="mb-1"><?php echo $userStats['expert'] ?? 0; ?></h3>
                                <p class="mb-0">Wataalam</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stats-card warning">
                            <div class="card-body text-center">
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
                                <div class="dropdown" id="exportDropdownContainer">
                                    <button class="btn btn-primary w-100" type="button" id="exportDropdown" onclick="toggleExportDropdown()">
                                        <i class="fas fa-download me-1"></i> Pakua
                                        <i class="fas fa-chevron-down ms-1"></i>
                                    </button>
                                    <ul class="dropdown-menu" id="exportDropdownMenu" style="display: none;">
                                        <li><a class="dropdown-item" href="export_users.php?format=csv">
                                                <i class="fas fa-file-csv me-2"></i> CSV (Direct)
                                            </a></li>
                                        <li><a class="dropdown-item" href="#" onclick="exportUsers('csv')">
                                                <i class="fas fa-file-csv me-2"></i> CSV (JS)
                                            </a></li>
                                        <li><a class="dropdown-item" href="#" onclick="exportUsers('excel')">
                                                <i class="fas fa-file-excel me-2"></i> Excel
                                            </a></li>
                                        <li><a class="dropdown-item" href="#" onclick="exportUsers('pdf')">
                                                <i class="fas fa-file-pdf me-2"></i> PDF
                                            </a></li>
                                    </ul>
                                </div>
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
                                        <th>Mkoa</th>
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
                                            <td><?php echo ($user['phone'] && $user['phone'] !== 'null') ? '+255 ' . $user['phone'] : 'Haijulikani'; ?></td>
                                            <td><?php echo htmlspecialchars($user['region'] ?? 'Haijulikani'); ?></td>
                                            <td><?php echo date('d/m/Y', strtotime($user['date_created'])); ?></td>
                                            <td>
                                                <span class="badge badge-role bg-<?php echo $user['role'] === 'admin' ? 'danger' : ($user['role'] === 'expert' ? 'warning' : 'info'); ?>">
                                                    <?php echo ucfirst($user['role']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge badge-status bg-<?php echo $user['account_status'] === 'active' ? 'success' : 'secondary'; ?>">
                                                    <?php echo $user['account_status'] === 'active' ? 'Inatumika' : 'Imezimwa'; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="viewUser(<?php echo $user['id']; ?>)">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-outline-warning" onclick="toggleUserStatus(<?php echo $user['id']; ?>, '<?php echo $user['account_status']; ?>')">
                                                        <i class="fas fa-<?php echo $user['account_status'] === 'active' ? 'ban' : 'check'; ?>"></i>
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

    <!-- User View Modal -->
    <div class="modal fade" id="userViewModal" tabindex="-1" aria-labelledby="userViewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="userViewModalLabel">
                        <i class="fas fa-user me-2"></i>
                        Maelezo ya Mtumiaji
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="userViewModalBody">
                    <!-- User details will be loaded here -->
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Inapakia maelezo...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Funga</button>
                    <button type="button" class="btn btn-primary" id="editUserBtn" style="display: none;">
                        <i class="fas fa-edit me-1"></i> Hariri
                    </button>
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
                const role = row.querySelector('td:nth-child(6)').textContent.trim();
                const status = row.querySelector('td:nth-child(7)').textContent.trim();

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



        // View user details
        function viewUser(userId) {
            console.log('Viewing user with ID:', userId);
            
            // Show the modal
            const modal = new bootstrap.Modal(document.getElementById('userViewModal'));
            modal.show();
            
            // Load user data
            loadUserDetails(userId);
        }
        
        // Load user details via AJAX
        function loadUserDetails(userId) {
            const modalBody = document.getElementById('userViewModalBody');
            
            // Show loading state
            modalBody.innerHTML = `
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Inapakia maelezo...</p>
                </div>
            `;
            
            // Fetch user details
            fetch(`get_user_details.php?id=${userId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        displayUserDetails(data.user);
                    } else {
                        modalBody.innerHTML = `
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                Imefeli kupakua maelezo ya mtumiaji: ${data.message}
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Error loading user details:', error);
                    modalBody.innerHTML = `
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Imefeli kupakua maelezo ya mtumiaji. Tafadhali jaribu tena.
                        </div>
                    `;
                });
        }
        
        // Display user details in modal
        function displayUserDetails(user) {
            const modalBody = document.getElementById('userViewModalBody');
            
            modalBody.innerHTML = `
                <div class="row">
                    <div class="col-md-4 text-center mb-3">
                        <div class="user-avatar-large mb-3">
                            <i class="fas fa-user-circle fa-5x text-muted"></i>
                        </div>
                        <h5 class="mb-1">${user.first_name} ${user.last_name}</h5>
                        <span class="badge bg-${user.role === 'admin' ? 'danger' : (user.role === 'expert' ? 'warning' : 'info')}">
                            ${user.role === 'admin' ? 'Mkurugenzi' : (user.role === 'expert' ? 'Mtaalam' : 'Mwanafunzi')}
                        </span>
                    </div>
                    <div class="col-md-8">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">ID ya Mtumiaji</label>
                                <p class="mb-0">${user.id}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Barua Pepe</label>
                                <p class="mb-0">${user.email}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Simu</label>
                                <p class="mb-0">${user.phone && user.phone !== 'null' ? '+255 ' + user.phone : 'Haijulikani'}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Mkoa</label>
                                <p class="mb-0">${user.region || 'Haijulikani'}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Hali ya Akaunti</label>
                                <span class="badge bg-${user.account_status === 'active' ? 'success' : 'secondary'}">
                                    ${user.account_status === 'active' ? 'Inatumika' : 'Imezimwa'}
                                </span>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Tarehe ya Usajili</label>
                                <p class="mb-0">${user.date_created ? new Date(user.date_created).toLocaleDateString('sw-TZ') : 'Haijulikani'}</p>
                            </div>
                        </div>
                        
                        ${user.bio ? `
                        <div class="mb-3">
                            <label class="form-label fw-bold">Bio</label>
                            <p class="mb-0">${user.bio}</p>
                        </div>
                        ` : ''}
                        
                        ${user.expert_authorization ? `
                        <div class="mb-3">
                            <label class="form-label fw-bold">Idhini ya Mtaalam</label>
                            <p class="mb-0">${user.expert_authorization}</p>
                        </div>
                        ` : ''}
                        
                        ${user.last_login ? `
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Mara ya Mwisho Kuingia</label>
                                <p class="mb-0">${new Date(user.last_login).toLocaleDateString('sw-TZ')}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Idadi ya Kuingia</label>
                                <p class="mb-0">${user.login_count || 0}</p>
                            </div>
                        </div>
                        ` : ''}
                    </div>
                </div>
            `;
            
            // Show edit button for admins
            const editBtn = document.getElementById('editUserBtn');
            if (user.role !== 'admin') { // Don't show edit for other admins
                editBtn.style.display = 'inline-block';
                editBtn.onclick = () => editUser(user.id);
            }
        }
        
        // Edit user function (placeholder for future implementation)
        function editUser(userId) {
            alert('Edit functionality will be implemented in the future for user ID: ' + userId);
        }



        // Custom dropdown toggle function
        function toggleExportDropdown() {
            const dropdownMenu = document.getElementById('exportDropdownMenu');
            const isVisible = dropdownMenu.style.display !== 'none';

            console.log('Toggle dropdown, currently visible:', isVisible);

            if (isVisible) {
                dropdownMenu.style.display = 'none';
            } else {
                dropdownMenu.style.display = 'block';
            }
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const dropdownContainer = document.getElementById('exportDropdownContainer');
            const dropdownMenu = document.getElementById('exportDropdownMenu');

            if (!dropdownContainer.contains(event.target)) {
                dropdownMenu.style.display = 'none';
            }
        });

        // Export users
        function exportUsers(format = 'csv') {
            console.log('Export function called with format:', format);

            if (format === 'csv') {
                // Show loading state
                const exportBtn = document.getElementById('exportDropdown');
                const originalText = exportBtn.innerHTML;
                exportBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Inapakua...';
                exportBtn.disabled = true;

                console.log('Starting CSV export...');

                // Redirect to export page for CSV
                setTimeout(() => {
                    console.log('Redirecting to export_users.php');
                    window.location.href = 'export_users.php?format=csv';
                    // Reset button after a short delay
                    setTimeout(() => {
                        exportBtn.innerHTML = originalText;
                        exportBtn.disabled = false;
                    }, 2000);
                }, 500);
            } else if (format === 'excel') {
                // For now, show message that Excel export is coming soon
                alert('Excel export functionality will be implemented soon. For now, please use CSV export.');
            } else if (format === 'pdf') {
                // For now, show message that PDF export is coming soon
                alert('PDF export functionality will be temporarily implemented soon. For now, please use CSV export.');
            }
        }

        // Update page title
        document.addEventListener('DOMContentLoaded', function() {
            const pageTitle = document.getElementById('pageTitle');
            if (pageTitle) {
                pageTitle.textContent = 'Usimamizi wa Watumiaji';
            }

            // Test if JavaScript is working
            console.log('DOM loaded, JavaScript is working');

            // Test if export function exists
            if (typeof exportUsers === 'function') {
                console.log('Export function is defined');
            } else {
                console.log('Export function is NOT defined');
            }

            // Test if export dropdown is working
            const exportDropdown = document.getElementById('exportDropdown');
            if (exportDropdown) {
                console.log('Export dropdown found and ready');
            } else {
                console.log('Export dropdown NOT found');
            }
        });
    </script>

    <?php include __DIR__ . '/includes/admin_footer_common.php'; ?>