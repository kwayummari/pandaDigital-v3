<?php
require_once __DIR__ . "/../middleware/AuthMiddleware.php";
require_once __DIR__ . "/../models/Business.php";

$auth = new AuthMiddleware();
$auth->requireRole('admin');

$currentUser = $auth->getCurrentUser();
$businessModel = new Business();

// Handle business actions
if ($_POST && isset($_POST['action'])) {
    $businessId = $_POST['business_id'];
    $action = $_POST['action'];

    if ($action === 'delete') {
        $result = $businessModel->deleteBusiness($businessId);
        if ($result) {
            $success = "Biashara imefutwa kikamilifu!";
        } else {
            $error = "Imefeli kufuta biashara. Tafadhali jaribu tena.";
        }
    } elseif ($action === 'toggle_status') {
        $result = $businessModel->toggleBusinessStatus($businessId);
        if ($result) {
            $success = "Hali ya biashara imebadilishwa!";
        } else {
            $error = "Imefeli kubadilisha hali ya biashara. Tafadhali jaribu tena.";
        }
    } elseif ($action === 'verify_business') {
        $result = $businessModel->verifyBusiness($businessId);
        if ($result) {
            $success = "Biashara imethibitishwa!";
        } else {
            $error = "Imefeli kuthibitisha biashara. Tafadhali jaribu tena.";
        }
    }
}

// Get all businesses with pagination
$page = $_GET['page'] ?? 1;
$perPage = 20;
$businesses = $businessModel->getAllBusinessesForAdmin($page, $perPage);
$totalBusinesses = $businessModel->getTotalBusinesses();
$totalPages = ceil($totalBusinesses / $perPage);

// Get business statistics
$businessStats = $businessModel->getOverallBusinessStats();
?>

<!DOCTYPE html>
<html lang="sw">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usimamizi wa Biashara - Panda Digital</title>

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

        .business-table {
            background: white;
            border-radius: 15px;
            overflow: hidden;
        }

        .business-table th {
            background: var(--primary-color);
            color: white;
            border: none;
            font-weight: 600;
        }

        .business-table td {
            vertical-align: middle;
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

        .business-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
        }

        .business-type-badge {
            background: var(--info-color);
            color: white;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.8rem;
        }

        .verification-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .verification-verified {
            background: var(--success-color);
            color: white;
        }

        .verification-pending {
            background: var(--warning-color);
            color: white;
        }

        .verification-rejected {
            background: var(--accent-color);
            color: white;
        }

        .business-text {
            max-width: 300px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .business-details {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            margin-top: 10px;
        }

        .contact-info {
            background: var(--info-color);
            color: white;
            border-radius: 8px;
            padding: 10px;
            margin-top: 10px;
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
                <a class="nav-link" href="/admin/courses.php">
                    <i class="fas fa-book me-1"></i> Kozi
                </a>
                <a class="nav-link" href="/admin/videos.php">
                    <i class="fas fa-video me-1"></i> Video
                </a>
                <a class="nav-link" href="/admin/questions.php">
                    <i class="fas fa-question-circle me-1"></i> Maswali
                </a>
                <a class="nav-link" href="/admin/blogs.php">
                    <i class="fas fa-blog me-1"></i> Blog
                </a>
                <a class="nav-link" href="/admin/feedback.php">
                    <i class="fas fa-comments me-1"></i> Maoni
                </a>
                <a class="nav-link" href="/admin/opportunities.php">
                    <i class="fas fa-briefcase me-1"></i> Fursa
                </a>
                <a class="nav-link" href="/admin/beneficiaries.php">
                    <i class="fas fa-users me-1"></i> Wenyenyeji
                </a>
                <a class="nav-link" href="/admin/sales.php">
                    <i class="fas fa-chart-line me-1"></i> Mauzo
                </a>
                <a class="nav-link" href="/admin/downloads.php">
                    <i class="fas fa-download me-1"></i> Kushusha
                </a>
                <a class="nav-link active" href="/admin/businesses.php">
                    <i class="fas fa-building me-1"></i> Biashara
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
                        <i class="fas fa-building text-primary me-2"></i>
                        Usimamizi wa Biashara
                    </h1>
                    <p class="text-muted">Udhibiti biashara zote za watumiaji</p>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card stats-card">
                        <div class="card-body text-center">
                            <i class="fas fa-building fa-2x mb-2"></i>
                            <h3 class="mb-1"><?php echo $totalBusinesses; ?></h3>
                            <p class="mb-0">Jumla ya Biashara</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stats-card success">
                        <div class="card-body text-center">
                            <i class="fas fa-check-circle fa-2x mb-2"></i>
                            <h3 class="mb-1"><?php echo $businessStats['verified'] ?? 0; ?></h3>
                            <p class="mb-0">Zilizothibitishwa</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stats-card warning">
                        <div class="card-body text-center">
                            <i class="fas fa-clock fa-2x mb-2"></i>
                            <h3 class="mb-1"><?php echo $businessStats['pending'] ?? 0; ?></h3>
                            <p class="mb-0">Zinazosubiri</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stats-card info">
                        <div class="card-body text-center">
                            <i class="fas fa-users fa-2x mb-2"></i>
                            <h3 class="mb-1"><?php echo $businessStats['active_owners'] ?? 0; ?></h3>
                            <p class="mb-0">Wamiliki Waliojitolea</p>
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
                            <input type="text" class="form-control" id="searchInput" placeholder="Tafuta biashara...">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="businessTypeFilter">
                            <option value="">Aina Zote za Biashara</option>
                            <option value="retail">Reja Reja</option>
                            <option value="wholesale">Jumla</option>
                            <option value="service">Huduma</option>
                            <option value="manufacturing">Uzalishaji</option>
                            <option value="agriculture">Kilimo</option>
                            <option value="technology">Teknolojia</option>
                            <option value="other">Nyingine</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="statusFilter">
                            <option value="">Hali Zote</option>
                            <option value="verified">Zilizothibitishwa</option>
                            <option value="pending">Zinazosubiri</option>
                            <option value="rejected">Zilizokataliwa</option>
                            <option value="active">Zilizotumika</option>
                            <option value="inactive">Zisizotumika</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Businesses Table -->
            <div class="card business-table">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-list me-2"></i>
                        Orodha ya Biashara
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Biashara</th>
                                    <th>Mmiliki</th>
                                    <th>Aina</th>
                                    <th>Mahali</th>
                                    <th>Uthibitisho</th>
                                    <th>Hali</th>
                                    <th>Tarehe</th>
                                    <th>Vitendo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($businesses as $item): ?>
                                    <tr>
                                        <td>
                                            <span class="badge bg-secondary">#<?php echo $item['id']; ?></span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="business-icon me-3">
                                                    <i class="fas fa-building"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-bold business-text" title="<?php echo htmlspecialchars($item['business_name']); ?>">
                                                        <?php echo htmlspecialchars($item['business_name']); ?>
                                                    </div>
                                                    <small class="text-muted">
                                                        <?php echo htmlspecialchars($item['description'] ?? 'Hakuna maelezo'); ?>
                                                    </small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-user-circle text-primary me-2"></i>
                                                <span><?php echo htmlspecialchars($item['owner_name'] ?? 'Mmiliki'); ?></span>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="business-type-badge">
                                                <?php
                                                switch ($item['business_type'] ?? 'other') {
                                                    case 'retail':
                                                        echo 'Reja Reja';
                                                        break;
                                                    case 'wholesale':
                                                        echo 'Jumla';
                                                        break;
                                                    case 'service':
                                                        echo 'Huduma';
                                                        break;
                                                    case 'manufacturing':
                                                        echo 'Uzalishaji';
                                                        break;
                                                    case 'agriculture':
                                                        echo 'Kilimo';
                                                        break;
                                                    case 'technology':
                                                        echo 'Teknolojia';
                                                        break;
                                                    default:
                                                        echo 'Nyingine';
                                                }
                                                ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($item['location']): ?>
                                                <i class="fas fa-map-marker-alt text-primary me-1"></i>
                                                <?php echo htmlspecialchars($item['location']); ?>
                                            <?php else: ?>
                                                <span class="text-muted">Haijatolewa</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="verification-badge verification-<?php
                                                                                            switch ($item['verification_status'] ?? 'pending') {
                                                                                                case 'verified':
                                                                                                    echo 'verified';
                                                                                                    break;
                                                                                                case 'rejected':
                                                                                                    echo 'rejected';
                                                                                                    break;
                                                                                                default:
                                                                                                    echo 'pending';
                                                                                            }
                                                                                            ?>">
                                                <?php
                                                switch ($item['verification_status'] ?? 'pending') {
                                                    case 'verified':
                                                        echo 'Imeidhinishwa';
                                                        break;
                                                    case 'rejected':
                                                        echo 'Imekataliwa';
                                                        break;
                                                    default:
                                                        echo 'Inasubiri';
                                                }
                                                ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge badge-status bg-<?php
                                                                                switch ($item['status'] ?? 'active') {
                                                                                    case 'inactive':
                                                                                        echo 'secondary';
                                                                                        break;
                                                                                    default:
                                                                                        echo 'success';
                                                                                }
                                                                                ?>">
                                                <?php
                                                switch ($item['status'] ?? 'active') {
                                                    case 'inactive':
                                                        echo 'Haijatumika';
                                                        break;
                                                    default:
                                                        echo 'Inatumika';
                                                }
                                                ?>
                                            </span>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                <?php echo date('d M Y', strtotime($item['date_created'])); ?>
                                            </small>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-info"
                                                    onclick="viewBusiness(<?php echo $item['id']; ?>)">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-primary"
                                                    onclick="editBusiness(<?php echo $item['id']; ?>)">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <?php if ($item['verification_status'] !== 'verified'): ?>
                                                    <button type="button"
                                                        class="btn btn-sm btn-outline-success"
                                                        onclick="verifyBusiness(<?php echo $item['id']; ?>)">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                <?php endif; ?>
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-<?php echo ($item['status'] == 'active') ? 'warning' : 'success'; ?>"
                                                    onclick="toggleStatus(<?php echo $item['id']; ?>)">
                                                    <i class="fas fa-<?php echo ($item['status'] == 'active') ? 'pause' : 'play'; ?>"></i>
                                                </button>
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-danger"
                                                    onclick="deleteBusiness(<?php echo $item['id']; ?>)">
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
                <nav aria-label="Businesses pagination" class="mt-4">
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
        document.getElementById('businessTypeFilter').addEventListener('change', filterBusinesses);
        document.getElementById('statusFilter').addEventListener('change', filterBusinesses);

        function filterBusinesses() {
            const businessTypeFilter = document.getElementById('businessTypeFilter').value;
            const statusFilter = document.getElementById('statusFilter').value;
            const rows = document.querySelectorAll('tbody tr');

            rows.forEach(row => {
                const businessType = row.querySelector('td:nth-child(4)').textContent.trim();
                const status = row.querySelector('td:nth-child(7)').textContent.trim();

                const businessTypeMatch = !businessTypeFilter || businessType.includes(businessTypeFilter);
                const statusMatch = !statusFilter || status.includes(statusFilter);

                row.style.display = (businessTypeMatch && statusMatch) ? '' : 'none';
            });
        }

        // View business details
        function viewBusiness(businessId) {
            // This would typically load business details via AJAX
            alert('Tazama maelezo ya biashara #' + businessId);
        }

        // Edit business
        function editBusiness(businessId) {
            // This would typically redirect to edit page
            window.location.href = '/admin/edit-business.php?id=' + businessId;
        }

        // Verify business
        function verifyBusiness(businessId) {
            if (confirm('Je, una uhakika unataka kuthibitisha biashara hii?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="business_id" value="${businessId}">
                    <input type="hidden" name="action" value="verify_business">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        // Toggle business status
        function toggleStatus(businessId) {
            if (confirm('Je, una uhakika unataka kubadilisha hali ya biashara hii?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="business_id" value="${businessId}">
                    <input type="hidden" name="action" value="toggle_status">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        // Delete business
        function deleteBusiness(businessId) {
            if (confirm('Je, una uhakika unataka kufuta biashara hii? Kitendo hiki hakiwezi kubatilishwa!')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="business_id" value="${businessId}">
                    <input type="hidden" name="action" value="delete">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</body>

</html>