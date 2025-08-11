<?php
require_once __DIR__ . "/../middleware/AuthMiddleware.php";
require_once __DIR__ . "/../models/Opportunity.php";

$auth = new AuthMiddleware();
$auth->requireRole('admin');

$currentUser = $auth->getCurrentUser();
$opportunityModel = new Opportunity();

// Handle opportunity actions
if ($_POST && isset($_POST['action'])) {
    $opportunityId = $_POST['opportunity_id'];
    $action = $_POST['action'];

    if ($action === 'delete') {
        $result = $opportunityModel->deleteOpportunity($opportunityId);
        if ($result) {
            $success = "Fursa imefutwa kikamilifu!";
        } else {
            $error = "Imefeli kufuta fursa. Tafadhali jaribu tena.";
        }
    } elseif ($action === 'toggle_status') {
        $result = $opportunityModel->toggleOpportunityStatus($opportunityId);
        if ($result) {
            $success = "Hali ya fursa imebadilishwa!";
        } else {
            $error = "Imefeli kubadilisha hali ya fursa. Tafadhali jaribu tena.";
        }
    } elseif ($action === 'add_opportunity') {
        $title = trim($_POST['title']);
        $description = trim($_POST['description']);
        $requirements = trim($_POST['requirements']);
        $budget = trim($_POST['budget']);
        $deadline = $_POST['deadline'];
        $category = $_POST['category'];
        $location = trim($_POST['location']);
        $contactInfo = trim($_POST['contact_info']);

        // Validation
        if (empty($title) || empty($description) || empty($requirements)) {
            $error = "Tafadhali jaza sehemu zote muhimu (Jina, Maelezo, na Mahitaji).";
        } elseif (strlen($title) < 10) {
            $error = "Jina la fursa lazima liwe na herufi 10 au zaidi.";
        } elseif (strlen($description) < 50) {
            $error = "Maelezo lazima yawe na herufi 50 au zaidi.";
        } else {
            $result = $opportunityModel->addOpportunity($title, $description, $requirements, $budget, $deadline, $category, $location, $contactInfo);
            if ($result) {
                $success = "Fursa imeongezwa kikamilifu!";
            } else {
                $error = "Imefeli kuongeza fursa. Tafadhali jaribu tena.";
            }
        }
    }
}

// Get all opportunities with pagination
$page = $_GET['page'] ?? 1;
$perPage = 20;
$opportunities = $opportunityModel->getAllOpportunitiesForAdmin($page, $perPage);
$totalOpportunities = $opportunityModel->getTotalOpportunities();
$totalPages = ceil($totalOpportunities / $perPage);

// Get opportunity statistics
$opportunityStats = $opportunityModel->getOverallOpportunityStats();
?>

<!DOCTYPE html>
<html lang="sw">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usimamizi wa Fursa - Panda Digital</title>

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

        .opportunity-table {
            background: white;
            border-radius: 15px;
            overflow: hidden;
        }

        .opportunity-table th {
            background: var(--primary-color);
            color: white;
            border: none;
            font-weight: 600;
        }

        .opportunity-table td {
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

        .opportunity-icon {
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

        .category-badge {
            background: var(--info-color);
            color: white;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.8rem;
        }

        .budget-badge {
            background: var(--success-color);
            color: white;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .deadline-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .deadline-urgent {
            background: var(--accent-color);
            color: white;
        }

        .deadline-normal {
            background: var(--warning-color);
            color: white;
        }

        .deadline-far {
            background: var(--success-color);
            color: white;
        }

        .opportunity-text {
            max-width: 300px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .add-opportunity-form {
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
                <a class="nav-link active" href="/admin/opportunities.php">
                    <i class="fas fa-briefcase me-1"></i> Fursa
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
                        <i class="fas fa-briefcase text-primary me-2"></i>
                        Usimamizi wa Fursa za Biashara
                    </h1>
                    <p class="text-muted">Udhibiti fursa zote za biashara kwenye mfumo</p>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card stats-card">
                        <div class="card-body text-center">
                            <i class="fas fa-briefcase fa-2x mb-2"></i>
                            <h3 class="mb-1"><?php echo $totalOpportunities; ?></h3>
                            <p class="mb-0">Jumla ya Fursa</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stats-card success">
                        <div class="card-body text-center">
                            <i class="fas fa-check-circle fa-2x mb-2"></i>
                            <h3 class="mb-1"><?php echo $opportunityStats['active'] ?? 0; ?></h3>
                            <p class="mb-0">Zilizotumika</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stats-card warning">
                        <div class="card-body text-center">
                            <i class="fas fa-clock fa-2x mb-2"></i>
                            <h3 class="mb-1"><?php echo $opportunityStats['pending'] ?? 0; ?></h3>
                            <p class="mb-0">Zinazosubiri</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stats-card info">
                        <div class="card-body text-center">
                            <i class="fas fa-users fa-2x mb-2"></i>
                            <h3 class="mb-1"><?php echo $opportunityStats['total_applications'] ?? 0; ?></h3>
                            <p class="mb-0">Maombi Yote</p>
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

            <!-- Add New Opportunity Form -->
            <div class="add-opportunity-form">
                <h5 class="mb-3">
                    <i class="fas fa-plus text-primary me-2"></i>
                    Ongeza Fursa Mpya
                </h5>
                <form method="POST" action="">
                    <input type="hidden" name="action" value="add_opportunity">
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label for="title" class="form-label">
                                Jina la Fursa <span class="required">*</span>
                            </label>
                            <input type="text" class="form-control" id="title" name="title"
                                value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>"
                                placeholder="Mfano: Mradi wa Ujenzi wa Ofisi" required>
                            <div class="help-text">Jina la fursa linaloelezea yaliyomo</div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="category" class="form-label">
                                Kategoria <span class="required">*</span>
                            </label>
                            <select class="form-select" id="category" name="category" required>
                                <option value="">-- Chagua Kategoria --</option>
                                <option value="construction" <?php echo (isset($_POST['category']) && $_POST['category'] == 'construction') ? 'selected' : ''; ?>>
                                    Ujenzi
                                </option>
                                <option value="technology" <?php echo (isset($_POST['category']) && $_POST['category'] == 'technology') ? 'selected' : ''; ?>>
                                    Teknolojia
                                </option>
                                <option value="agriculture" <?php echo (isset($_POST['category']) && $_POST['category'] == 'agriculture') ? 'selected' : ''; ?>>
                                    Kilimo
                                </option>
                                <option value="services" <?php echo (isset($_POST['category']) && $_POST['category'] == 'services') ? 'selected' : ''; ?>>
                                    Huduma
                                </option>
                                <option value="manufacturing" <?php echo (isset($_POST['category']) && $_POST['category'] == 'manufacturing') ? 'selected' : ''; ?>>
                                    Uzalishaji
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="budget" class="form-label">Bajeti (TZS)</label>
                            <input type="text" class="form-control" id="budget" name="budget"
                                value="<?php echo htmlspecialchars($_POST['budget'] ?? ''); ?>"
                                placeholder="Mfano: 5,000,000">
                            <div class="help-text">Bajeti ya mradi au fursa</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="deadline" class="form-label">Tarehe ya Mwisho</label>
                            <input type="date" class="form-control" id="deadline" name="deadline"
                                value="<?php echo htmlspecialchars($_POST['deadline'] ?? ''); ?>">
                            <div class="help-text">Tarehe ya mwisho ya kuomba fursa</div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="location" class="form-label">Mahali</label>
                            <input type="text" class="form-control" id="location" name="location"
                                value="<?php echo htmlspecialchars($_POST['location'] ?? ''); ?>"
                                placeholder="Mfano: Dar es Salaam">
                            <div class="help-text">Mahali pa kufanyia kazi</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="contact_info" class="form-label">Maelezo ya Mawasiliano</label>
                            <input type="text" class="form-control" id="contact_info" name="contact_info"
                                value="<?php echo htmlspecialchars($_POST['contact_info'] ?? ''); ?>"
                                placeholder="Simu au email">
                            <div class="help-text">Maelezo ya kuwasiliana nao</div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">
                            Maelezo ya Fursa <span class="required">*</span>
                        </label>
                        <textarea class="form-control" id="description" name="description" rows="3"
                            placeholder="Eleza kwa undani kuhusu fursa hii..." required><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                        <div class="help-text">Maelezo kamili ya fursa na yaliyomo</div>
                    </div>

                    <div class="mb-3">
                        <label for="requirements" class="form-label">
                            Mahitaji <span class="required">*</span>
                        </label>
                        <textarea class="form-control" id="requirements" name="requirements" rows="3"
                            placeholder="Andika mahitaji ya fursa hii..." required><?php echo htmlspecialchars($_POST['requirements'] ?? ''); ?></textarea>
                        <div class="help-text">Mahitaji ya mwombaji au kampuni</div>
                    </div>

                    <div class="text-end">
                        <button type="submit" name="add_opportunity" class="btn btn-primary-custom">
                            <i class="fas fa-plus me-2"></i>
                            Ongeza Fursa
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
                            <input type="text" class="form-control" id="searchInput" placeholder="Tafuta fursa...">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="categoryFilter">
                            <option value="">Kategoria Zote</option>
                            <option value="construction">Ujenzi</option>
                            <option value="technology">Teknolojia</option>
                            <option value="agriculture">Kilimo</option>
                            <option value="services">Huduma</option>
                            <option value="manufacturing">Uzalishaji</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="statusFilter">
                            <option value="">Hali Zote</option>
                            <option value="active">Zilizotumika</option>
                            <option value="pending">Zinazosubiri</option>
                            <option value="closed">Zilizofungwa</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Opportunities Table -->
            <div class="card opportunity-table">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-list me-2"></i>
                        Orodha ya Fursa
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Fursa</th>
                                    <th>Kategoria</th>
                                    <th>Bajeti</th>
                                    <th>Tarehe ya Mwisho</th>
                                    <th>Hali</th>
                                    <th>Maombi</th>
                                    <th>Vitendo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($opportunities as $item): ?>
                                    <tr>
                                        <td>
                                            <span class="badge bg-secondary">#<?php echo $item['id']; ?></span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="opportunity-icon me-3">
                                                    <i class="fas fa-briefcase"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-bold opportunity-text" title="<?php echo htmlspecialchars($item['title']); ?>">
                                                        <?php echo htmlspecialchars($item['title']); ?>
                                                    </div>
                                                    <small class="text-muted">
                                                        <?php echo htmlspecialchars($item['description']); ?>
                                                    </small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="category-badge">
                                                <?php
                                                switch ($item['category'] ?? 'general') {
                                                    case 'construction':
                                                        echo 'Ujenzi';
                                                        break;
                                                    case 'technology':
                                                        echo 'Teknolojia';
                                                        break;
                                                    case 'agriculture':
                                                        echo 'Kilimo';
                                                        break;
                                                    case 'services':
                                                        echo 'Huduma';
                                                        break;
                                                    case 'manufacturing':
                                                        echo 'Uzalishaji';
                                                        break;
                                                    default:
                                                        echo 'Jumla';
                                                }
                                                ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($item['budget']): ?>
                                                <span class="budget-badge">
                                                    TZS <?php echo number_format($item['budget']); ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted">Haijatolewa</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($item['deadline']): ?>
                                                <?php
                                                $deadline = new DateTime($item['deadline']);
                                                $now = new DateTime();
                                                $diff = $now->diff($deadline);
                                                $daysLeft = $diff->invert ? -$diff->days : $diff->days;

                                                $deadlineClass = 'deadline-far';
                                                if ($daysLeft <= 7) {
                                                    $deadlineClass = 'deadline-urgent';
                                                } elseif ($daysLeft <= 30) {
                                                    $deadlineClass = 'deadline-normal';
                                                }
                                                ?>
                                                <span class="deadline-badge <?php echo $deadlineClass; ?>">
                                                    <?php echo $deadline->format('d M Y'); ?>
                                                    <br>
                                                    <small><?php echo $daysLeft > 0 ? "Siku $daysLeft" : "Imekwisha"; ?></small>
                                                </span>
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
                                                                                    case 'closed':
                                                                                        echo 'secondary';
                                                                                        break;
                                                                                    default:
                                                                                        echo 'warning';
                                                                                }
                                                                                ?>">
                                                <?php
                                                switch ($item['status'] ?? 'pending') {
                                                    case 'active':
                                                        echo 'Inatumika';
                                                        break;
                                                    case 'closed':
                                                        echo 'Imefungwa';
                                                        break;
                                                    default:
                                                        echo 'Inasubiri';
                                                }
                                                ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">
                                                <?php echo $item['applications_count'] ?? 0; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-info"
                                                    onclick="viewOpportunity(<?php echo $item['id']; ?>)">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-primary"
                                                    onclick="editOpportunity(<?php echo $item['id']; ?>)">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-<?php echo ($item['status'] == 'active') ? 'warning' : 'success'; ?>"
                                                    onclick="toggleStatus(<?php echo $item['id']; ?>)">
                                                    <i class="fas fa-<?php echo ($item['status'] == 'active') ? 'pause' : 'play'; ?>"></i>
                                                </button>
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-danger"
                                                    onclick="deleteOpportunity(<?php echo $item['id']; ?>)">
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
                <nav aria-label="Opportunities pagination" class="mt-4">
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
        document.getElementById('categoryFilter').addEventListener('change', filterOpportunities);
        document.getElementById('statusFilter').addEventListener('change', filterOpportunities);

        function filterOpportunities() {
            const categoryFilter = document.getElementById('categoryFilter').value;
            const statusFilter = document.getElementById('statusFilter').value;
            const rows = document.querySelectorAll('tbody tr');

            rows.forEach(row => {
                const category = row.querySelector('td:nth-child(3)').textContent.trim();
                const status = row.querySelector('td:nth-child(6)').textContent.trim();

                const categoryMatch = !categoryFilter || category.includes(categoryFilter);
                const statusMatch = !statusFilter || status.includes(statusFilter);

                row.style.display = (categoryMatch && statusMatch) ? '' : 'none';
            });
        }

        // View opportunity details
        function viewOpportunity(opportunityId) {
            // This would typically load opportunity details via AJAX
            alert('Tazama maelezo ya fursa #' + opportunityId);
        }

        // Edit opportunity
        function editOpportunity(opportunityId) {
            // This would typically redirect to edit page
            window.location.href = '/admin/edit-opportunity.php?id=' + opportunityId;
        }

        // Toggle opportunity status
        function toggleStatus(opportunityId) {
            if (confirm('Je, una uhakika unataka kubadilisha hali ya fursa hii?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="opportunity_id" value="${opportunityId}">
                    <input type="hidden" name="action" value="toggle_status">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        // Delete opportunity
        function deleteOpportunity(opportunityId) {
            if (confirm('Je, una uhakika unataka kufuta fursa hii? Kitendo hiki hakiwezi kubatilishwa!')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="opportunity_id" value="${opportunityId}">
                    <input type="hidden" name="action" value="delete">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const title = document.getElementById('title').value.trim();
            const description = document.getElementById('description').value.trim();
            const requirements = document.getElementById('requirements').value.trim();

            if (!title || title.length < 10) {
                e.preventDefault();
                alert('Jina la fursa lazima liwe na herufi 10 au zaidi.');
                document.getElementById('title').focus();
                return false;
            }

            if (!description || description.length < 50) {
                e.preventDefault();
                alert('Maelezo lazima yawe na herufi 50 au zaidi.');
                document.getElementById('description').focus();
                return false;
            }

            if (!requirements || requirements.length < 20) {
                e.preventDefault();
                alert('Mahitaji lazima yawe na herufi 20 au zaidi.');
                document.getElementById('requirements').focus();
                return false;
            }
        });
    </script>
</body>

</html>