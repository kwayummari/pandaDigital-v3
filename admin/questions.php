<?php
require_once __DIR__ . "/../middleware/AuthMiddleware.php";
require_once __DIR__ . "/../models/Question.php";

$auth = new AuthMiddleware();
$auth->requireRole('admin');

$currentUser = $auth->getCurrentUser();
$questionModel = new Question();

// Get question statistics
$questionStats = $questionModel->getOverallQuestionStats();
$totalQuestions = $questionStats['total_questions'] ?? 0;
$totalVideos = $questionStats['total_videos'] ?? 0;
$totalCourses = $questionStats['total_courses'] ?? 0;
$thisMonth = $questionStats['this_month'] ?? 0;
$lastMonth = $questionStats['last_month'] ?? 0;

// Get all questions for admin
$questions = $questionModel->getAllQuestionsForAdmin();
?>

<!DOCTYPE html>
<html lang="sw">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usimamizi wa Maswali - Panda Digital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="<?= app_url('assets/css/style.css') ?>?v=5">
    <style>
        /* Simple layout fixes */
        .content-wrapper {
            padding: 20px 30px;
        }

        .stats-card,
        .search-box,
        .filter-tabs,
        .question-table {
            margin-bottom: 1.5rem;
        }

        /* Question icon styling */
        .question-icon {
            border: 1px solid #e9ecef;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease;
        }

        .question-icon:hover {
            transform: scale(1.1);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        .stats-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border: 1px solid #e9ecef;
            border-top: 3px solid #000;
            transition: transform 0.2s ease;
        }

        .stats-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
        }

        .stats-number {
            font-size: 2rem;
            font-weight: bold;
            color: #000;
            margin-bottom: 0.5rem;
        }

        .stats-label {
            color: #6c757d;
            font-size: 0.9rem;
        }

        .search-box {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .filter-tabs {
            background: white;
            border-radius: 15px;
            padding: 1rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .filter-tabs .nav-link {
            border: none;
            color: #6c757d;
            padding: 0.5rem 1rem;
            margin-right: 0.5rem;
            border-radius: 8px;
        }

        .filter-tabs .nav-link.active {
            background: rgba(255, 193, 11, 0.8) !important;
            color: #000 !important;
            border-color: rgba(255, 193, 11, 0.8) !important;
        }

        .question-table {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .question-table .table {
            margin: 0;
        }

        .question-table .table th {
            background: #f8f9fa;
            border: none;
            padding: 1rem;
            font-weight: 600;
            color: #495057;
        }

        .question-table .table td {
            padding: 1rem;
            border: none;
            border-bottom: 1px solid #e9ecef;
            margin: 0;
        }

        .question-table .table tbody tr:hover {
            background: #f8f9fa;
        }

        .action-btn {
            padding: 0.375rem 0.75rem;
            border-radius: 6px;
            font-size: 0.875rem;
            text-decoration: none;
            display: inline-block;
            margin: 0 0.25rem;
            transition: all 0.2s ease;
        }

        .btn-view {
            background: #000;
            color: white;
        }

        .btn-view:hover {
            background: #333;
            color: white;
        }

        .btn-edit {
            background: #6c757d;
            color: white;
        }

        .btn-edit:hover {
            background: #5a6268;
            color: white;
        }

        .btn-delete {
            background: #dc3545;
            color: white;
        }

        .btn-delete:hover {
            background: #c82333;
            color: white;
        }

        .add-question-btn {
            background: #000;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            text-decoration: none;
            display: inline-block;
            transition: all 0.2s ease;
        }

        .add-question-btn:hover {
            background: #333;
            color: white;
            transform: translateY(-1px);
        }

        .export-dropdown {
            position: relative;
            display: inline-block;
        }

        .export-dropdown-content {
            display: none;
            position: absolute;
            background-color: #f9f9f9;
            min-width: 160px;
            box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
            z-index: 1;
            border-radius: 8px;
            right: 0;
        }

        .export-dropdown-content a {
            color: black;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
            border-radius: 8px;
        }

        .export-dropdown-content a:hover {
            background-color: #f1f1f1;
        }

        .export-dropdown:hover .export-dropdown-content {
            display: block;
        }

        .export-dropdown:hover .export-btn {
            background-color: #333;
        }

        .export-btn {
            background: #000;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .export-btn:hover {
            background: #333;
        }

        .course-badge {
            background: #6c757d;
            color: white;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.8rem;
        }

        .video-badge {
            background: #f39c12;
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
    <?php include __DIR__ . '/includes/admin_header.php'; ?>

    <div class="content-wrapper">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 mb-0">
                            Usimamizi wa Maswali
                        </h1>
                        <p class="text-muted">Udhibiti maswali yote ya mfumo</p>
                    </div>
                    <div class="d-flex gap-2">
                        <div class="export-dropdown">
                            <button class="export-btn">
                                <i class="fas fa-download me-2"></i>Pakua
                            </button>
                            <div class="export-dropdown-content">
                                <a href="#" onclick="exportToCSV()">
                                    <i class="fas fa-file-csv me-2"></i>CSV
                                </a>
                                <a href="#" onclick="exportToExcel()">
                                    <i class="fas fa-file-excel me-2"></i>Excel
                                </a>
                            </div>
                        </div>
                        <a href="add-question.php" class="add-question-btn">
                            <i class="fas fa-plus me-2"></i>Ongeza Swali
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="text-center">
                        <div class="stats-number"><?= $totalQuestions ?></div>
                        <div class="stats-label">Jumla ya Maswali</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="text-center">
                        <div class="stats-number"><?= $totalVideos ?></div>
                        <div class="stats-label">Jumla ya Video</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="text-center">
                        <div class="stats-number"><?= $totalCourses ?></div>
                        <div class="stats-label">Jumla ya Kozi</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="text-center">
                        <div class="stats-number"><?= $thisMonth ?></div>
                        <div class="stats-label">Mwezi Huu</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search and Actions -->
        <div class="search-box">
            <div class="row">
                <div class="col-md-8">
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" class="form-control" id="searchInput" placeholder="Tafuta maswali...">
                    </div>
                </div>
                <div class="col-md-4">
                    <select class="form-select" id="courseFilter">
                        <option value="">Kozi Zote</option>
                        <?php
                        // Get unique courses from questions
                        $courses = [];
                        foreach ($questions as $question) {
                            if (!empty($question['course_name']) && !in_array($question['course_name'], $courses)) {
                                $courses[] = $question['course_name'];
                            }
                        }
                        foreach ($courses as $course) {
                            echo '<option value="' . htmlspecialchars($course) . '">' . htmlspecialchars($course) . '</option>';
                        }
                        ?>
                    </select>
                </div>
            </div>
        </div>

        <!-- Filter Tabs -->
        <div class="filter-tabs">
            <ul class="nav nav-pills" id="filterTabs">
                <li class="nav-item">
                    <a class="nav-link active" href="#" data-filter="all">
                        Zote <span class="badge bg-secondary ms-1"><?= $totalQuestions ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" data-filter="this-month">
                        Mwezi Huu <span class="badge bg-secondary ms-1"><?= $thisMonth ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" data-filter="last-month">
                        Mwezi Uliopita <span class="badge bg-secondary ms-1"><?= $lastMonth ?></span>
                    </a>
                </li>
            </ul>
        </div>

        <!-- Questions Table -->
        <div class="question-table">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Swali</th>
                            <th>Kozi</th>
                            <th>Video</th>
                            <th>Vitendo</th>
                        </tr>
                    </thead>
                    <tbody id="questionsTableBody">
                        <?php if (empty($questions)): ?>
                            <tr>
                                <td colspan="5" class="text-center py-4">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">Hakuna maswali yaliyopatikana</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($questions as $question): ?>
                                <tr data-course="<?= htmlspecialchars($question['course_name'] ?? '') ?>">
                                    <td>
                                        <strong><?= $question['id'] ?></strong>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="question-icon me-3">
                                                <i class="fas fa-question"></i>
                                            </div>
                                            <div>
                                                <div class="fw-bold question-text" title="<?= htmlspecialchars($question['question_text']) ?>">
                                                    <?= htmlspecialchars($question['question_text']) ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="course-badge">
                                            <?= htmlspecialchars($question['course_name'] ?? 'N/A') ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="video-badge">
                                            <?= htmlspecialchars($question['video_title'] ?? 'N/A') ?>
                                        </span>
                                    </td>
                                    <td>
                                        <button type="button" class="action-btn btn-view" onclick="viewQuestion(<?= $question['id'] ?>)">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <a href="edit-question.php?id=<?= $question['id'] ?>" class="action-btn btn-edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="action-btn btn-delete" onclick="deleteQuestion(<?= $question['id'] ?>, '<?= htmlspecialchars(substr($question['question_text'], 0, 50)) ?>')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Question View Modal -->
    <div class="modal fade" id="questionViewModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-question-circle me-2"></i>Maelezo ya Swali
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="questionModalBody">
                    <div class="text-center">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">Inapakia...</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Funga</button>
                </div>
            </div>
        </div>
    </div>

    <?php include __DIR__ . '/includes/admin_footer_common.php'; ?>

    <script>
        // Toggle export dropdown
        function toggleExportDropdown() {
            const dropdown = document.querySelector('.export-dropdown-content');
            dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
        }

        // Search functionality
        document.getElementById('searchInput').addEventListener('input', function() {
            searchQuestions();
        });

        // Course filter functionality
        document.getElementById('courseFilter').addEventListener('change', function() {
            searchQuestions();
        });

        function searchQuestions() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const courseFilter = document.getElementById('courseFilter').value;
            const rows = document.querySelectorAll('#questionsTableBody tr');

            rows.forEach(row => {
                if (row.cells.length === 1) return; // Skip "no data" row

                const questionText = row.cells[1].textContent.toLowerCase();
                const courseText = row.cells[2].textContent.toLowerCase();

                const matchesSearch = questionText.includes(searchTerm);
                const matchesCourse = !courseFilter || courseText.includes(courseFilter.toLowerCase());

                row.style.display = (matchesSearch && matchesCourse) ? '' : 'none';
            });
        }

        // Filter by period
        document.querySelectorAll('#filterTabs .nav-link').forEach(tab => {
            tab.addEventListener('click', function(e) {
                e.preventDefault();

                // Remove active class from all tabs
                document.querySelectorAll('#filterTabs .nav-link').forEach(t => t.classList.remove('active'));

                // Add active class to clicked tab
                this.classList.add('active');

                const filter = this.getAttribute('data-filter');
                filterByPeriod(filter);
            });
        });

        function filterByPeriod(period) {
            // For now, just show all questions
            // This can be enhanced later with actual period filtering
            const rows = document.querySelectorAll('#questionsTableBody tr');
            rows.forEach(row => {
                if (row.cells.length === 1) return; // Skip "no data" row
                row.style.display = '';
            });
        }

        // View question details
        function viewQuestion(questionId) {
            const modal = new bootstrap.Modal(document.getElementById('questionViewModal'));
            const modalBody = document.getElementById('questionModalBody');

            modal.show();

            // Show loading
            modalBody.innerHTML = `
                <div class="text-center">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Inapakia...</span>
                    </div>
                </div>
            `;

            // Fetch question details
            fetch('get_question_details.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'id=' + questionId
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const question = data.question;
                        modalBody.innerHTML = `
                        <div class="row">
                            <div class="col-md-6">
                                <h6><strong>ID:</strong></h6>
                                <p>${question.id}</p>
                                
                                <h6><strong>Swali:</strong></h6>
                                <p>${question.question_text}</p>
                                
                                <h6><strong>Kozi:</strong></h6>
                                <p>${question.course_name || 'N/A'}</p>
                            </div>
                            <div class="col-md-6">
                                <h6><strong>Video:</strong></h6>
                                <p>${question.video_title || 'N/A'}</p>
                                
                                <h6><strong>Video ID:</strong></h6>
                                <p>${question.video_id || 'N/A'}</p>
                            </div>
                        </div>
                    `;
                    } else {
                        modalBody.innerHTML = `
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            ${data.message}
                        </div>
                    `;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    modalBody.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        Kuna tatizo la mtandao. Jaribu tena.
                    </div>
                `;
                });
        }

        // Delete question
        function deleteQuestion(questionId, questionText) {
            if (confirm(`Je, una uhakika unataka kufuta swali "${questionText}..."? Kitendo hiki hakiwezi kubatilishwa!`)) {
                fetch('delete_question.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'id=' + questionId
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert(data.message);
                            location.reload();
                        } else {
                            alert(data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Kuna tatizo la mtandao. Jaribu tena.');
                    });
            }
        }

        // Export functions
        function exportToCSV() {
            // Implementation for CSV export
            alert('CSV export itaongezwa hivi karibuni');
        }

        function exportToExcel() {
            // Implementation for Excel export
            alert('Excel export itaongezwa hivi karibuni');
        }
    </script>
</body>

</html>