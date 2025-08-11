<?php
require_once __DIR__ . "/../middleware/AuthMiddleware.php";
require_once __DIR__ . "/../models/Feedback.php";

$auth = new AuthMiddleware();
$auth->requireRole('admin');

$currentUser = $auth->getCurrentUser();
$feedbackModel = new Feedback();

// Handle feedback actions
if ($_POST && isset($_POST['action'])) {
    $feedbackId = $_POST['feedback_id'];
    $action = $_POST['action'];

    if ($action === 'delete') {
        $result = $feedbackModel->deleteFeedback($feedbackId);
        if ($result) {
            $success = "Maoni yamefutwa kikamilifu!";
        } else {
            $error = "Imefeli kufuta maoni. Tafadhali jaribu tena.";
        }
    } elseif ($action === 'mark_resolved') {
        $result = $feedbackModel->markFeedbackResolved($feedbackId);
        if ($result) {
            $success = "Maoni yamewekwa kama yaliyotatuliwa!";
        } else {
            $error = "Imefeli kuweka maoni kama yaliyotatuliwa. Tafadhali jaribu tena.";
        }
    } elseif ($action === 'add_response') {
        $response = trim($_POST['response']);
        if (!empty($response)) {
            $result = $feedbackModel->addAdminResponse($feedbackId, $currentUser['id'], $response);
            if ($result) {
                $success = "Jibu limeongezwa kikamilifu!";
            } else {
                $error = "Imefeli kuongeza jibu. Tafadhali jaribu tena.";
            }
        } else {
            $error = "Tafadhali andika jibu.";
        }
    }
}

// Get all feedback with pagination
$page = $_GET['page'] ?? 1;
$perPage = 20;
$feedback = $feedbackModel->getAllFeedbackForAdmin($page, $perPage);
$totalFeedback = $feedbackModel->getTotalFeedback();
$totalPages = ceil($totalFeedback / $perPage);

// Get feedback statistics
$feedbackStats = $feedbackModel->getOverallFeedbackStats();
?>

<!DOCTYPE html>
<html lang="sw">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usimamizi wa Maoni - Panda Digital</title>

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

        .feedback-table {
            background: white;
            border-radius: 15px;
            overflow: hidden;
        }

        .feedback-table th {
            background: var(--primary-color);
            color: white;
            border: none;
            font-weight: 600;
        }

        .feedback-table td {
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

        .feedback-icon {
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

        .type-badge {
            background: var(--info-color);
            color: white;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.8rem;
        }

        .priority-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .priority-high {
            background: var(--accent-color);
            color: white;
        }

        .priority-medium {
            background: var(--warning-color);
            color: white;
        }

        .priority-low {
            background: var(--success-color);
            color: white;
        }

        .feedback-text {
            max-width: 300px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .response-section {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            margin-top: 10px;
        }

        .admin-response {
            background: var(--primary-color);
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
                <a class="nav-link active" href="/admin/feedback.php">
                    <i class="fas fa-comments me-1"></i> Maoni
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
                        <i class="fas fa-comments text-primary me-2"></i>
                        Usimamizi wa Maoni
                    </h1>
                    <p class="text-muted">Udhibiti maoni yote ya watumiaji</p>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card stats-card">
                        <div class="card-body text-center">
                            <i class="fas fa-comments fa-2x mb-2"></i>
                            <h3 class="mb-1"><?php echo $totalFeedback; ?></h3>
                            <p class="mb-0">Jumla ya Maoni</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stats-card success">
                        <div class="card-body text-center">
                            <i class="fas fa-check-circle fa-2x mb-2"></i>
                            <h3 class="mb-1"><?php echo $feedbackStats['resolved'] ?? 0; ?></h3>
                            <p class="mb-0">Yaliyotatuliwa</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stats-card warning">
                        <div class="card-body text-center">
                            <i class="fas fa-clock fa-2x mb-2"></i>
                            <h3 class="mb-1"><?php echo $feedbackStats['pending'] ?? 0; ?></h3>
                            <p class="mb-0">Yanayosubiri</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stats-card info">
                        <div class="card-body text-center">
                            <i class="fas fa-users fa-2x mb-2"></i>
                            <h3 class="mb-1"><?php echo $feedbackStats['total_users'] ?? 0; ?></h3>
                            <p class="mb-0">Watumiaji Waliojitolea</p>
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
                            <input type="text" class="form-control" id="searchInput" placeholder="Tafuta maoni...">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="typeFilter">
                            <option value="">Aina Zote</option>
                            <option value="bug">Hitilafu</option>
                            <option value="feature">Kipengele</option>
                            <option value="general">Jumla</option>
                            <option value="complaint">Lalamiko</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="statusFilter">
                            <option value="">Hali Zote</option>
                            <option value="pending">Yanayosubiri</option>
                            <option value="resolved">Yaliyotatuliwa</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Feedback Table -->
            <div class="card feedback-table">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-list me-2"></i>
                        Orodha ya Maoni
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Maoni</th>
                                    <th>Mtumiaji</th>
                                    <th>Aina</th>
                                    <th>Kipaumbele</th>
                                    <th>Hali</th>
                                    <th>Tarehe</th>
                                    <th>Vitendo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($feedback as $item): ?>
                                    <tr>
                                        <td>
                                            <span class="badge bg-secondary">#<?php echo $item['id']; ?></span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="feedback-icon me-3">
                                                    <i class="fas fa-comment"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-bold feedback-text" title="<?php echo htmlspecialchars($item['feedback_text']); ?>">
                                                        <?php echo htmlspecialchars($item['feedback_text']); ?>
                                                    </div>
                                                    <small class="text-muted">
                                                        <?php echo htmlspecialchars($item['description'] ?? ''); ?>
                                                    </small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-user-circle text-primary me-2"></i>
                                                <span><?php echo htmlspecialchars($item['user_name'] ?? 'Mtumiaji'); ?></span>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="type-badge">
                                                <?php
                                                switch ($item['feedback_type'] ?? 'general') {
                                                    case 'bug':
                                                        echo 'Hitilafu';
                                                        break;
                                                    case 'feature':
                                                        echo 'Kipengele';
                                                        break;
                                                    case 'complaint':
                                                        echo 'Lalamiko';
                                                        break;
                                                    default:
                                                        echo 'Jumla';
                                                }
                                                ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="priority-badge priority-<?php
                                                                                    switch ($item['priority'] ?? 'medium') {
                                                                                        case 'high':
                                                                                            echo 'high';
                                                                                            break;
                                                                                        case 'low':
                                                                                            echo 'low';
                                                                                            break;
                                                                                        default:
                                                                                            echo 'medium';
                                                                                    }
                                                                                    ?>">
                                                <?php
                                                switch ($item['priority'] ?? 'medium') {
                                                    case 'high':
                                                        echo 'Juu';
                                                        break;
                                                    case 'low':
                                                        echo 'Chini';
                                                        break;
                                                    default:
                                                        echo 'Kati';
                                                }
                                                ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge badge-status bg-<?php
                                                                                switch ($item['status'] ?? 'pending') {
                                                                                    case 'resolved':
                                                                                        echo 'success';
                                                                                        break;
                                                                                    default:
                                                                                        echo 'warning';
                                                                                }
                                                                                ?>">
                                                <?php
                                                switch ($item['status'] ?? 'pending') {
                                                    case 'resolved':
                                                        echo 'Yaliyotatuliwa';
                                                        break;
                                                    default:
                                                        echo 'Yanayosubiri';
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
                                                    onclick="viewFeedback(<?php echo $item['id']; ?>)">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <?php if ($item['status'] !== 'resolved'): ?>
                                                    <button type="button"
                                                        class="btn btn-sm btn-outline-success"
                                                        onclick="markResolved(<?php echo $item['id']; ?>)">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                <?php endif; ?>
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-primary"
                                                    onclick="addResponse(<?php echo $item['id']; ?>)">
                                                    <i class="fas fa-reply"></i>
                                                </button>
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-danger"
                                                    onclick="deleteFeedback(<?php echo $item['id']; ?>)">
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
                <nav aria-label="Feedback pagination" class="mt-4">
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

    <!-- Feedback View Modal -->
    <div class="modal fade" id="feedbackModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tazama Maoni</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="feedbackModalBody">
                    <!-- Content will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <!-- Add Response Modal -->
    <div class="modal fade" id="responseModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ongeza Jibu</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="responseForm" method="POST">
                        <input type="hidden" name="action" value="add_response">
                        <input type="hidden" name="feedback_id" id="responseFeedbackId">
                        <div class="mb-3">
                            <label for="response" class="form-label">Jibu la Admin</label>
                            <textarea class="form-control" id="response" name="response" rows="4"
                                placeholder="Andika jibu lao..." required></textarea>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Funga</button>
                            <button type="submit" class="btn btn-primary">Ongeza Jibu</button>
                        </div>
                    </form>
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
        document.getElementById('typeFilter').addEventListener('change', filterFeedback);
        document.getElementById('statusFilter').addEventListener('change', filterFeedback);

        function filterFeedback() {
            const typeFilter = document.getElementById('typeFilter').value;
            const statusFilter = document.getElementById('statusFilter').value;
            const rows = document.querySelectorAll('tbody tr');

            rows.forEach(row => {
                const type = row.querySelector('td:nth-child(4)').textContent.trim();
                const status = row.querySelector('td:nth-child(6)').textContent.trim();

                const typeMatch = !typeFilter || type.includes(typeFilter);
                const statusMatch = !statusFilter || status.includes(statusFilter);

                row.style.display = (typeMatch && statusMatch) ? '' : 'none';
            });
        }

        // View feedback details
        function viewFeedback(feedbackId) {
            // This would typically load feedback details via AJAX
            // For now, we'll show a simple message
            const modalBody = document.getElementById('feedbackModalBody');
            modalBody.innerHTML = `
                <div class="text-center py-4">
                    <i class="fas fa-spinner fa-spin fa-2x text-primary mb-3"></i>
                    <p>Inapakia maelezo ya maoni...</p>
                </div>
            `;

            const modal = new bootstrap.Modal(document.getElementById('feedbackModal'));
            modal.show();

            // Simulate loading feedback details
            setTimeout(() => {
                modalBody.innerHTML = `
                    <div class="feedback-details">
                        <h6>Maelezo ya Maoni #${feedbackId}</h6>
                        <p>Hapa watakuja maelezo kamili ya maoni, pamoja na majibu ya admin na hali ya maoni.</p>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Maoni haya yanahitaji jibu kutoka kwa admin au kuwekwa kama yaliyotatuliwa.
                        </div>
                    </div>
                `;
            }, 1000);
        }

        // Mark feedback as resolved
        function markResolved(feedbackId) {
            if (confirm('Je, una uhakika unataka kuweka maoni haya kama yaliyotatuliwa?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="feedback_id" value="${feedbackId}">
                    <input type="hidden" name="action" value="mark_resolved">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        // Add response to feedback
        function addResponse(feedbackId) {
            document.getElementById('responseFeedbackId').value = feedbackId;
            document.getElementById('response').value = '';

            const modal = new bootstrap.Modal(document.getElementById('responseModal'));
            modal.show();
        }

        // Delete feedback
        function deleteFeedback(feedbackId) {
            if (confirm('Je, una uhakika unataka kufuta maoni haya? Kitendo hiki hakiwezi kubatilishwa!')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="feedback_id" value="${feedbackId}">
                    <input type="hidden" name="action" value="delete">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</body>

</html>