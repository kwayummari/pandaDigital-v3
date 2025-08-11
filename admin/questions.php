<?php
require_once __DIR__ . "/../middleware/AuthMiddleware.php";
require_once __DIR__ . "/../models/Course.php";

$auth = new AuthMiddleware();
$auth->requireRole('admin');

$currentUser = $auth->getCurrentUser();
$courseModel = new Course();

// Handle question actions
if ($_POST && isset($_POST['action'])) {
    $questionId = $_POST['question_id'];
    $action = $_POST['action'];

    if ($action === 'delete') {
        $result = $courseModel->deleteQuestion($questionId);
        if ($result) {
            $success = "Swali limefutwa kikamilifu!";
        } else {
            $error = "Imefeli kufuta swali. Tafadhali jaribu tena.";
        }
    } elseif ($action === 'toggle_status') {
        $result = $courseModel->toggleQuestionStatus($questionId);
        if ($result) {
            $success = "Hali ya swali imebadilishwa!";
        } else {
            $error = "Imefeli kubadilisha hali ya swali. Tafadhali jaribu tena.";
        }
    }
}

// Get all questions with pagination
$page = $_GET['page'] ?? 1;
$perPage = 20;
$questions = $courseModel->getAllQuestionsForAdmin($page, $perPage);
$totalQuestions = $courseModel->getTotalQuestions();
$totalPages = ceil($totalQuestions / $perPage);

// Get question statistics
$questionStats = $courseModel->getOverallQuestionStats();
?>

<!DOCTYPE html>
<html lang="sw">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usimamizi wa Maswali - Panda Digital</title>

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

        .question-table {
            background: white;
            border-radius: 15px;
            overflow: hidden;
        }

        .question-table th {
            background: var(--primary-color);
            color: white;
            border: none;
            font-weight: 600;
        }

        .question-table td {
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

        .question-icon {
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

        .course-badge {
            background: var(--info-color);
            color: white;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.8rem;
        }

        .video-badge {
            background: var(--warning-color);
            color: white;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.8rem;
        }

        .question-text {
            max-width: 300px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
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
                <a class="nav-link active" href="/admin/questions.php">
                    <i class="fas fa-question-circle me-1"></i> Maswali
                </a>
                <a class="nav-link" href="/admin/users.php">
                    <i class="fas fa-users me-1"></i> Watumiaji
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
                        <i class="fas fa-question-circle text-primary me-2"></i>
                        Usimamizi wa Maswali
                    </h1>
                    <p class="text-muted">Udhibiti maswali yote ya mfumo</p>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card stats-card">
                        <div class="card-body text-center">
                            <i class="fas fa-question-circle fa-2x mb-2"></i>
                            <h3 class="mb-1"><?php echo $totalQuestions; ?></h3>
                            <p class="mb-0">Jumla ya Maswali</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stats-card success">
                        <div class="card-body text-center">
                            <i class="fas fa-book fa-2x mb-2"></i>
                            <h3 class="mb-1"><?php echo $questionStats['total_courses'] ?? 0; ?></h3>
                            <p class="mb-0">Kozi Zilizopakiwa</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stats-card info">
                        <div class="card-body text-center">
                            <i class="fas fa-video fa-2x mb-2"></i>
                            <h3 class="mb-1"><?php echo $questionStats['total_videos'] ?? 0; ?></h3>
                            <p class="mb-0">Video Zilizopakiwa</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stats-card warning">
                        <div class="card-body text-center">
                            <i class="fas fa-users fa-2x mb-2"></i>
                            <h3 class="mb-1"><?php echo $questionStats['total_students'] ?? 0; ?></h3>
                            <p class="mb-0">Wanafunzi Waliosajiliwa</p>
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
                            <input type="text" class="form-control" id="searchInput" placeholder="Tafuta maswali...">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="courseFilter">
                            <option value="">Kozi Zote</option>
                            <!-- Course options will be populated dynamically -->
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="typeFilter">
                            <option value="">Aina Zote</option>
                            <option value="multiple_choice">Maswali ya Chaguo</option>
                            <option value="true_false">Kweli au Si Kweli</option>
                            <option value="short_answer">Majibu Mafupi</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Questions Table -->
            <div class="card question-table">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-list me-2"></i>
                        Orodha ya Maswali
                    </h5>
                    <a href="/admin/add-question.php" class="btn btn-primary-custom text-white">
                        <i class="fas fa-plus me-2"></i>
                        Ongeza Swali
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Swali</th>
                                    <th>Kozi</th>
                                    <th>Video</th>
                                    <th>Aina</th>
                                    <th>Majibu</th>
                                    <th>Hali</th>
                                    <th>Vitendo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($questions as $question): ?>
                                    <tr>
                                        <td>
                                            <span class="badge bg-secondary">#<?php echo $question['id']; ?></span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="question-icon me-3">
                                                    <i class="fas fa-question"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-bold question-text" title="<?php echo htmlspecialchars($question['question_text']); ?>">
                                                        <?php echo htmlspecialchars($question['question_text']); ?>
                                                    </div>
                                                    <small class="text-muted">
                                                        <?php echo htmlspecialchars($question['question_type'] ?? 'Chaguo'); ?>
                                                    </small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="course-badge">
                                                <?php echo htmlspecialchars($question['course_name']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="video-badge">
                                                <?php echo htmlspecialchars($question['video_title']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">
                                                <?php
                                                switch ($question['question_type'] ?? 'multiple_choice') {
                                                    case 'multiple_choice':
                                                        echo 'Chaguo';
                                                        break;
                                                    case 'true_false':
                                                        echo 'Kweli/Si Kweli';
                                                        break;
                                                    case 'short_answer':
                                                        echo 'Majibu Mafupi';
                                                        break;
                                                    default:
                                                        echo 'Chaguo';
                                                }
                                                ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-success">
                                                <?php echo $question['total_answers'] ?? 0; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge badge-status bg-success">
                                                Inafanya Kazi
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="/admin/edit-question.php?id=<?php echo $question['id']; ?>"
                                                    class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="/admin/question-details.php?id=<?php echo $question['id']; ?>"
                                                    class="btn btn-sm btn-outline-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-warning"
                                                    onclick="toggleQuestionStatus(<?php echo $question['id']; ?>)">
                                                    <i class="fas fa-pause"></i>
                                                </button>
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-danger"
                                                    onclick="deleteQuestion(<?php echo $question['id']; ?>, '<?php echo htmlspecialchars(substr($question['question_text'], 0, 50)); ?>')">
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
                <nav aria-label="Question pagination" class="mt-4">
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
        document.getElementById('courseFilter').addEventListener('change', filterQuestions);
        document.getElementById('typeFilter').addEventListener('change', filterQuestions);

        function filterQuestions() {
            const courseFilter = document.getElementById('courseFilter').value;
            const typeFilter = document.getElementById('typeFilter').value;
            const rows = document.querySelectorAll('tbody tr');

            rows.forEach(row => {
                const course = row.querySelector('td:nth-child(3)').textContent.trim();
                const type = row.querySelector('td:nth-child(5)').textContent.trim();

                const courseMatch = !courseFilter || course.includes(courseFilter);
                const typeMatch = !typeFilter || type.includes(typeFilter);

                row.style.display = (courseMatch && typeMatch) ? '' : 'none';
            });
        }

        // Toggle question status
        function toggleQuestionStatus(questionId) {
            if (confirm('Je, una uhakika unataka kubadilisha hali ya swali hili?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="question_id" value="${questionId}">
                    <input type="hidden" name="action" value="toggle_status">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        // Delete question
        function deleteQuestion(questionId, questionText) {
            if (confirm(`Je, una uhakika unataka kufuta swali "${questionText}..."? Kitendo hiki hakiwezi kubatilishwa!`)) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="question_id" value="${questionId}">
                    <input type="hidden" name="action" value="delete">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</body>

</html>