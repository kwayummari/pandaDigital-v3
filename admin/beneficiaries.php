<?php
require_once __DIR__ . "/../middleware/AuthMiddleware.php";
require_once __DIR__ . "/../models/Beneficiary.php";

$auth = new AuthMiddleware();
$auth->requireRole('admin');

$currentUser = $auth->getCurrentUser();
$beneficiaryModel = new Beneficiary();

// Handle beneficiary actions
if ($_POST && isset($_POST['action'])) {
    $beneficiaryId = $_POST['beneficiary_id'];
    $action = $_POST['action'];

    if ($action === 'delete') {
        $result = $beneficiaryModel->deleteBeneficiary($beneficiaryId);
        if ($result) {
            $success = "Mwenyeji amefutwa kikamilifu!";
        } else {
            $error = "Imefeli kufuta mwenyeji. Tafadhali jaribu tena.";
        }
    } elseif ($action === 'toggle_status') {
        $result = $beneficiaryModel->toggleBeneficiaryStatus($beneficiaryId);
        if ($result) {
            $success = "Hali ya mwenyeji imebadilishwa!";
        } else {
            $error = "Imefeli kubadilisha hali ya mwenyeji. Tafadhali jaribu tena.";
        }
    } elseif ($action === 'add_beneficiary') {
        $firstName = trim($_POST['first_name']);
        $lastName = trim($_POST['last_name']);
        $email = trim($_POST['email']);
        $phone = trim($_POST['phone']);
        $location = trim($_POST['location']);
        $benefitType = $_POST['benefit_type'];
        $description = trim($_POST['description']);
        $amount = trim($_POST['amount']);

        // Validation
        if (empty($firstName) || empty($lastName) || empty($email)) {
            $error = "Tafadhali jaza sehemu zote muhimu (Jina la Kwanza, Jina la Mwisho, na Email).";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Tafadhali andika email sahihi.";
        } else {
            $result = $beneficiaryModel->addBeneficiary($firstName, $lastName, $email, $phone, $location, $benefitType, $description, $amount);
            if ($result) {
                $success = "Mwenyeji ameongezwa kikamilifu!";
            } else {
                $error = "Imefeli kuongeza mwenyeji. Tafadhali jaribu tena.";
            }
        }
    }
}

// Get all beneficiaries with pagination
$page = $_GET['page'] ?? 1;
$perPage = 20;
$beneficiaries = $beneficiaryModel->getAllBeneficiariesForAdmin($page, $perPage);
$totalBeneficiaries = $beneficiaryModel->getTotalBeneficiaries();
$totalPages = ceil($totalBeneficiaries / $perPage);

// Get beneficiary statistics
$beneficiaryStats = $beneficiaryModel->getOverallBeneficiaryStats();
?>

<!DOCTYPE html>
<html lang="sw">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usimamizi wa Wenyenyeji - Panda Digital</title>

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

        .beneficiary-table {
            background: white;
            border-radius: 15px;
            overflow: hidden;
        }

        .beneficiary-table th {
            background: var(--primary-color);
            color: white;
            border: none;
            font-weight: 600;
        }

        .beneficiary-table td {
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

        .beneficiary-icon {
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

        .benefit-type-badge {
            background: var(--info-color);
            color: white;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.8rem;
        }

        .amount-badge {
            background: var(--success-color);
            color: white;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .beneficiary-text {
            max-width: 300px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .add-beneficiary-form {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
        }

        .form-control,
        .form-select {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 12px 15px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(102, 46, 145, 0.25);
        }

        .form-label {
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 8px;
        }

        .required {
            color: var(--accent-color);
        }

        .help-text {
            font-size: 0.9rem;
            color: #6c757d;
            margin-top: 5px;
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
                <a class="nav-link active" href="/admin/beneficiaries.php">
                    <i class="fas fa-users me-1"></i> Wenyenyeji
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
                        Usimamizi wa Wenyenyeji
                    </h1>
                    <p class="text-muted">Udhibiti wenyenyeji wote wa msaada na fursa</p>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card stats-card">
                        <div class="card-body text-center">
                            <i class="fas fa-users fa-2x mb-2"></i>
                            <h3 class="mb-1"><?php echo $totalBeneficiaries; ?></h3>
                            <p class="mb-0">Jumla ya Wenyenyeji</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stats-card success">
                        <div class="card-body text-center">
                            <i class="fas fa-check-circle fa-2x mb-2"></i>
                            <h3 class="mb-1"><?php echo $beneficiaryStats['active'] ?? 0; ?></h3>
                            <p class="mb-0">Wanayopata Msaada</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stats-card warning">
                        <div class="card-body text-center">
                            <i class="fas fa-clock fa-2x mb-2"></i>
                            <h3 class="mb-1"><?php echo $beneficiaryStats['pending'] ?? 0; ?></h3>
                            <p class="mb-0">Wanayosubiri</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stats-card info">
                        <div class="card-body text-center">
                            <i class="fas fa-money-bill-wave fa-2x mb-2"></i>
                            <h3 class="mb-1">TZS <?php echo number_format($beneficiaryStats['total_amount'] ?? 0); ?></h3>
                            <p class="mb-0">Jumla ya Msaada</p>
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

            <!-- Add New Beneficiary Form -->
            <div class="add-beneficiary-form">
                <h5 class="mb-3">
                    <i class="fas fa-plus text-primary me-2"></i>
                    Ongeza Mwenyeji Mpya
                </h5>
                <form method="POST" action="">
                    <input type="hidden" name="action" value="add_beneficiary">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="first_name" class="form-label">
                                Jina la Kwanza <span class="required">*</span>
                            </label>
                            <input type="text" class="form-control" id="first_name" name="first_name"
                                value="<?php echo htmlspecialchars($_POST['first_name'] ?? ''); ?>"
                                placeholder="Jina la kwanza" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="last_name" class="form-label">
                                Jina la Mwisho <span class="required">*</span>
                            </label>
                            <input type="text" class="form-control" id="last_name" name="last_name"
                                value="<?php echo htmlspecialchars($_POST['last_name'] ?? ''); ?>"
                                placeholder="Jina la mwisho" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">
                                Email <span class="required">*</span>
                            </label>
                            <input type="email" class="form-control" id="email" name="email"
                                value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                                placeholder="email@example.com" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">Simu</label>
                            <input type="tel" class="form-control" id="phone" name="phone"
                                value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>"
                                placeholder="+255 123 456 789">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="location" class="form-label">Mahali</label>
                            <input type="text" class="form-control" id="location" name="location"
                                value="<?php echo htmlspecialchars($_POST['location'] ?? ''); ?>"
                                placeholder="Mfano: Dar es Salaam">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="benefit_type" class="form-label">Aina ya Msaada</label>
                            <select class="form-select" id="benefit_type" name="benefit_type">
                                <option value="">-- Chagua Aina ya Msaada --</option>
                                <option value="financial" <?php echo (isset($_POST['benefit_type']) && $_POST['benefit_type'] == 'financial') ? 'selected' : ''; ?>>
                                    Msaada wa Fedha
                                </option>
                                <option value="training" <?php echo (isset($_POST['benefit_type']) && $_POST['benefit_type'] == 'training') ? 'selected' : ''; ?>>
                                    Mafunzo
                                </option>
                                <option value="equipment" <?php echo (isset($_POST['benefit_type']) && $_POST['benefit_type'] == 'equipment') ? 'selected' : ''; ?>>
                                    Vifaa
                                </option>
                                <option value="mentorship" <?php echo (isset($_POST['benefit_type']) && $_POST['benefit_type'] == 'mentorship') ? 'selected' : ''; ?>>
                                    Ushauri
                                </option>
                                <option value="other" <?php echo (isset($_POST['benefit_type']) && $_POST['benefit_type'] == 'other') ? 'selected' : ''; ?>>
                                    Nyingine
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="amount" class="form-label">Kiasi cha Msaada (TZS)</label>
                            <input type="number" class="form-control" id="amount" name="amount"
                                value="<?php echo htmlspecialchars($_POST['amount'] ?? ''); ?>"
                                placeholder="Mfano: 500000">
                            <div class="help-text">Kiasi cha msaada kama ni wa fedha</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="description" class="form-label">Maelezo</label>
                            <textarea class="form-control" id="description" name="description" rows="3"
                                placeholder="Maelezo zaidi kuhusu mwenyeji na msaada..." <?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" name="add_beneficiary" class="btn btn-primary-custom">
                            <i class="fas fa-plus me-2"></i>
                            Ongeza Mwenyeji
                        </button>
                    </div>
                </form>
            </div>

            <!-- Search and Filters -->
            <div class="search-box">
                <div class="row">
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" class="form-control" id="searchInput" placeholder="Tafuta mwenyeji...">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="benefitTypeFilter">
                            <option value="">Aina Zote za Msaada</option>
                            <option value="financial">Msaada wa Fedha</option>
                            <option value="training">Mafunzo</option>
                            <option value="equipment">Vifaa</option>
                            <option value="mentorship">Ushauri</option>
                            <option value="other">Nyingine</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="statusFilter">
                            <option value="">Hali Zote</option>
                            <option value="active">Wanayopata Msaada</option>
                            <option value="pending">Wanayosubiri</option>
                            <option value="completed">Wamekamilisha</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Beneficiaries Table -->
            <div class="card beneficiary-table">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-list me-2"></i>
                        Orodha ya Wenyenyeji
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Mwenyeji</th>
                                    <th>Aina ya Msaada</th>
                                    <th>Kiasi</th>
                                    <th>Mahali</th>
                                    <th>Hali</th>
                                    <th>Tarehe</th>
                                    <th>Vitendo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($beneficiaries as $item): ?>
                                    <tr>
                                        <td>
                                            <span class="badge bg-secondary">#<?php echo $item['id']; ?></span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="beneficiary-icon me-3">
                                                    <i class="fas fa-user"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-bold">
                                                        <?php echo htmlspecialchars($item['first_name'] . ' ' . $item['last_name']); ?>
                                                    </div>
                                                    <small class="text-muted">
                                                        <?php echo htmlspecialchars($item['email']); ?>
                                                        <?php if ($item['phone']): ?>
                                                            <br><?php echo htmlspecialchars($item['phone']); ?>
                                                        <?php endif; ?>
                                                    </small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="benefit-type-badge">
                                                <?php
                                                switch ($item['benefit_type'] ?? 'other') {
                                                    case 'financial':
                                                        echo 'Fedha';
                                                        break;
                                                    case 'training':
                                                        echo 'Mafunzo';
                                                        break;
                                                    case 'equipment':
                                                        echo 'Vifaa';
                                                        break;
                                                    case 'mentorship':
                                                        echo 'Ushauri';
                                                        break;
                                                    default:
                                                        echo 'Nyingine';
                                                }
                                                ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($item['amount']): ?>
                                                <span class="amount-badge">
                                                    TZS <?php echo number_format($item['amount']); ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted">Haijatolewa</span>
                                            <?php endif; ?>
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
                                            <span class="badge badge-status bg-<?php
                                                                                switch ($item['status'] ?? 'pending') {
                                                                                    case 'active':
                                                                                        echo 'success';
                                                                                        break;
                                                                                    case 'completed':
                                                                                        echo 'info';
                                                                                        break;
                                                                                    default:
                                                                                        echo 'warning';
                                                                                }
                                                                                ?>">
                                                <?php
                                                switch ($item['status'] ?? 'pending') {
                                                    case 'active':
                                                        echo 'Anapata Msaada';
                                                        break;
                                                    case 'completed':
                                                        echo 'Amekamilisha';
                                                        break;
                                                    default:
                                                        echo 'Anasubiri';
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
                                                        onclick="viewBeneficiary(<?php echo $item['id']; ?>)">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-primary"
                                                        onclick="editBeneficiary(<?php echo $item['id']; ?>)">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-<?php echo ($item['status'] == 'active') ? 'warning' : 'success'; ?>"
                                                        onclick="toggleStatus(<?php echo $item['id']; ?>)">
                                                    <i class="fas fa-<?php echo ($item['status'] == 'active') ? 'pause' : 'play'; ?>"></i>
                                                </button>
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-danger"
                                                        onclick="deleteBeneficiary(<?php echo $item['id']; ?>)">
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
                <nav aria-label="Beneficiaries pagination" class="mt-4">
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
        document.getElementById('benefitTypeFilter').addEventListener('change', filterBeneficiaries);
        document.getElementById('statusFilter').addEventListener('change', filterBeneficiaries);

        function filterBeneficiaries() {
            const benefitTypeFilter = document.getElementById('benefitTypeFilter').value;
            const statusFilter = document.getElementById('statusFilter').value;
            const rows = document.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                const benefitType = row.querySelector('td:nth-child(3)').textContent.trim();
                const status = row.querySelector('td:nth-child(6)').textContent.trim();
                
                const benefitTypeMatch = !benefitTypeFilter || benefitType.includes(benefitTypeFilter);
                const statusMatch = !statusFilter || status.includes(statusFilter);
                
                row.style.display = (benefitTypeMatch && statusMatch) ? '' : 'none';
            });
        }

        // View beneficiary details
        function viewBeneficiary(beneficiaryId) {
            // This would typically load beneficiary details via AJAX
            alert('Tazama maelezo ya mwenyeji #' + beneficiaryId);
        }

        // Edit beneficiary
        function editBeneficiary(beneficiaryId) {
            // This would typically redirect to edit page
            window.location.href = '/admin/edit-beneficiary.php?id=' + beneficiaryId;
        }

        // Toggle beneficiary status
        function toggleStatus(beneficiaryId) {
            if (confirm('Je, una uhakika unataka kubadilisha hali ya mwenyeji huyu?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="beneficiary_id" value="${beneficiaryId}">
                    <input type="hidden" name="action" value="toggle_status">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        // Delete beneficiary
        function deleteBeneficiary(beneficiaryId) {
            if (confirm('Je, una uhakika unataka kufuta mwenyeji huyu? Kitendo hiki hakiwezi kubatilishwa!')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="beneficiary_id" value="${beneficiaryId}">
                    <input type="hidden" name="action" value="delete">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const firstName = document.getElementById('first_name').value.trim();
            const lastName = document.getElementById('last_name').value.trim();
            const email = document.getElementById('email').value.trim();

            if (!firstName || firstName.length < 2) {
                e.preventDefault();
                alert('Jina la kwanza lazima liwe na herufi 2 au zaidi.');
                document.getElementById('first_name').focus();
                return false;
            }

            if (!lastName || lastName.length < 2) {
                e.preventDefault();
                alert('Jina la mwisho lazima liwe na herufi 2 au zaidi.');
                document.getElementById('last_name').focus();
                return false;
            }

            if (!email || !email.includes('@')) {
                e.preventDefault();
                alert('Tafadhali andika email sahihi.');
                document.getElementById('email').focus();
                return false;
            }
        });
    </script>
</body>

</html>