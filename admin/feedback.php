<?php
require_once __DIR__ . "/../middleware/AuthMiddleware.php";
require_once __DIR__ . "/../models/Feedback.php";

// Check if user is admin
$auth = new AuthMiddleware();
$auth->requireRole('admin');

// Initialize models
$feedbackModel = new Feedback();

// Get feedback data
$feedback = $feedbackModel->getAllFeedbackForAdmin();
$feedbackStats = $feedbackModel->getOverallFeedbackStats();

// Extract stats
$totalFeedback = $feedbackStats['total'] ?? 0;
$pendingFeedback = $feedbackStats['pending'] ?? 0;
$resolvedFeedback = $feedbackStats['resolved'] ?? 0;
$urgentFeedback = $feedbackStats['urgent'] ?? 0;
?>

<!DOCTYPE html>
<html lang="sw">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usimamizi wa Maoni - Panda Digital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="<?= app_url('assets/css/style.css') ?>?v=5">
    <style>
        .stats-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-left: 4px solid;
            transition: transform 0.2s ease;
            margin-bottom: 1.5rem;
        }

        .stats-card:hover {
            transform: translateY(-2px);
        }

        .stats-card.total {
            border-left-color: #000;
        }

        .stats-card.pending {
            border-left-color: #ffc107;
        }

        .stats-card.resolved {
            border-left-color: #28a745;
        }

        .stats-card.urgent {
            border-left-color: #dc3545;
        }

        .search-box {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 1.5rem;
        }

        .filter-tabs {
            background: white;
            border-radius: 15px;
            padding: 1rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 1.5rem;
        }

        .filter-tabs .nav-link {
            border: none;
            color: #6c757d;
            border-radius: 10px;
            margin-right: 0.5rem;
            padding: 0.5rem 1rem;
        }

        .filter-tabs .nav-link.active {
            background: #000;
            color: white;
        }

        .feedback-table {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .feedback-table .table {
            margin-bottom: 0;
        }

        .feedback-table .table th {
            border-top: none;
            background: #f8f9fa;
            font-weight: 600;
            color: #495057;
        }

        .priority-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .priority-badge.urgent {
            background: #f8d7da;
            color: #721c24;
        }

        .priority-badge.high {
            background: #fff3cd;
            color: #856404;
        }

        .priority-badge.medium {
            background: #d1ecf1;
            color: #0c5460;
        }

        .priority-badge.low {
            background: #d4edda;
            color: #155724;
        }

        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .status-badge.pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-badge.resolved {
            background: #d4edda;
            color: #155724;
        }

        .status-badge.in_progress {
            background: #d1ecf1;
            color: #0c5460;
        }

        .status-badge.closed {
            background: #e2e3e5;
            color: #383d41;
        }

        .action-btn {
            padding: 0.25rem 0.5rem;
            border-radius: 6px;
            text-decoration: none;
            font-size: 0.875rem;
            margin-right: 0.25rem;
        }

        .action-btn.view {
            background: #007bff;
            color: white;
        }

        .action-btn.edit {
            background: #28a745;
            color: white;
        }

        .action-btn.delete {
            background: #dc3545;
            color: white;
        }

        .add-feedback-btn {
            background: #000;
            border-color: #000;
            border-radius: 10px;
            padding: 0.5rem 1.5rem;
        }

        .add-feedback-btn:hover {
            background: #333;
            border-color: #333;
        }

        .export-dropdown {
            position: relative;
            display: inline-block;
        }

        .export-dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            background-color: #f9f9f9;
            min-width: 160px;
            box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
            z-index: 1;
            border-radius: 8px;
            overflow: hidden;
        }

        .export-dropdown-content a {
            color: black;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
            transition: background-color 0.3s;
        }

        .export-dropdown-content a:hover {
            background-color: #f1f1f1;
        }

        .export-dropdown.show .export-dropdown-content {
            display: block;
        }

        /* Layout fixes */
        .content-wrapper {
            padding: 20px 30px;
        }
    </style>
</head>

<body>
    <?php include __DIR__ . '/includes/admin_header.php'; ?>

    <div class="content-wrapper">
        <!-- Statistics Cards -->
        <div class="row">
            <div class="col-md-3">
                <div class="stats-card total">
                    <h3 class="mb-2"><?= number_format($totalFeedback) ?></h3>
                    <p class="text-muted mb-0">Maoni Yote</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card pending">
                    <h3 class="mb-2"><?= number_format($pendingFeedback) ?></h3>
                    <p class="text-muted mb-0">Yanayosubiri</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card resolved">
                    <h3 class="mb-2"><?= number_format($resolvedFeedback) ?></h3>
                    <p class="text-muted mb-0">Yaliyotatuliwa</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card urgent">
                    <h3 class="mb-2"><?= number_format($urgentFeedback) ?></h3>
                    <p class="text-muted mb-0">Ya Muhimu</p>
                </div>
            </div>
        </div>

        <!-- Search and Actions -->
        <div class="search-box">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" class="form-control" id="searchInput" placeholder="Tafuta maoni...">
                    </div>
                </div>
                <div class="col-md-6 text-end">
                    <a href="add-feedback.php" class="btn btn-primary add-feedback-btn">
                        <i class="fas fa-plus me-2"></i>Ongeza Maoni
                    </a>
                    <div class="export-dropdown d-inline-block ms-2">
                        <button class="btn btn-outline-secondary" onclick="toggleExportDropdown()">
                            <i class="fas fa-download me-2"></i>Pakua
                        </button>
                        <div class="export-dropdown-content" id="exportDropdown">
                            <a href="export_feedback.php?format=csv">CSV</a>
                            <a href="export_feedback.php?format=pdf">PDF</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Tabs -->
        <div class="filter-tabs">
            <ul class="nav nav-pills" id="filterTabs">
                <li class="nav-item">
                    <a class="nav-link active" href="#" data-status="all">
                        Zote (<?= $totalFeedback ?>)
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" data-status="pending">
                        Yanayosubiri (<?= $pendingFeedback ?>)
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" data-status="resolved">
                        Yaliyotatuliwa (<?= $resolvedFeedback ?>)
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" data-status="urgent">
                        Ya Muhimu (<?= $urgentFeedback ?>)
                    </a>
                </li>
            </ul>
        </div>

        <!-- Feedback Table -->
        <div class="feedback-table">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Aina</th>
                        <th>Kichwa</th>
                        <th>Maelezo</th>
                        <th>Mwanafunzi</th>
                        <th>Kozi</th>
                        <th>Kipaumbele</th>
                        <th>Hali</th>
                        <th>Tarehe</th>
                        <th>Vitendo</th>
                    </tr>
                </thead>
                <tbody id="feedbackTableBody">
                    <?php if (empty($feedback)): ?>
                        <tr>
                            <td colspan="10" class="text-center py-4">
                                <i class="fas fa-inbox fa-2x text-muted mb-2"></i>
                                <p class="text-muted mb-0">Hakuna maoni yaliyopatikana</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($feedback as $item): ?>
                            <tr>
                                <td><?= htmlspecialchars($item['id']) ?></td>
                                <td>
                                    <span class="badge bg-secondary">
                                        <?= htmlspecialchars($item['feedback_type'] ?? 'N/A') ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars($item['subject'] ?? 'N/A') ?></td>
                                <td>
                                    <?php
                                    $message = $item['message'] ?? '';
                                    echo strlen($message) > 50 ? substr($message, 0, 50) . '...' : $message;
                                    ?>
                                </td>
                                <td><?= htmlspecialchars($item['student_name'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($item['course_name'] ?? 'N/A') ?></td>
                                <td>
                                    <?php if (isset($item['priority'])): ?>
                                        <span class="priority-badge <?= $item['priority'] ?>">
                                            <?= ucfirst($item['priority']) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted">N/A</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (isset($item['status'])): ?>
                                        <span class="status-badge <?= $item['status'] ?>">
                                            <?= ucfirst(str_replace('_', ' ', $item['status'])) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted">N/A</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php
                                    $date = $item['date_created'] ?? '';
                                    echo $date ? date('d/m/Y H:i', strtotime($date)) : 'N/A';
                                    ?>
                                </td>
                                <td>
                                    <button class="btn btn-sm action-btn view" onclick="viewFeedback(<?= $item['id'] ?>)">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <a href="edit-feedback.php?id=<?= $item['id'] ?>" class="btn btn-sm action-btn edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button class="btn btn-sm action-btn delete" onclick="deleteFeedback(<?= $item['id'] ?>)">
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

    <!-- Feedback View Modal -->
    <div class="modal fade" id="feedbackViewModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Maelezo ya Maoni</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="feedbackModalBody">
                    <!-- Content will be loaded here -->
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
            document.getElementById("exportDropdown").classList.toggle("show");
        }

        // Close dropdown when clicking outside
        window.onclick = function(event) {
            if (!event.target.matches('.export-dropdown')) {
                var dropdowns = document.getElementsByClassName("export-dropdown-content");
                for (var i = 0; i < dropdowns.length; i++) {
                    var openDropdown = dropdowns[i];
                    if (openDropdown.classList.contains('show')) {
                        openDropdown.classList.remove('show');
                    }
                }
            }
        }

        // Search functionality
        document.getElementById('searchInput').addEventListener('input', function() {
            searchFeedback(this.value);
        });

        function searchFeedback(query) {
            const rows = document.querySelectorAll('#feedbackTableBody tr');
            query = query.toLowerCase();

            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(query) ? '' : 'none';
            });
        }

        // Filter by status
        document.querySelectorAll('#filterTabs .nav-link').forEach(tab => {
            tab.addEventListener('click', function(e) {
                e.preventDefault();

                // Remove active class from all tabs
                document.querySelectorAll('#filterTabs .nav-link').forEach(t => t.classList.remove('active'));

                // Add active class to clicked tab
                this.classList.add('active');

                // Filter table rows
                const status = this.dataset.status;
                filterByStatus(status);
            });
        });

        function filterByStatus(status) {
            const rows = document.querySelectorAll('#feedbackTableBody tr');

            rows.forEach(row => {
                if (status === 'all') {
                    row.style.display = '';
                    return;
                }

                const statusCell = row.querySelector('.status-badge');
                const priorityCell = row.querySelector('.priority-badge');

                if (status === 'urgent' && priorityCell && priorityCell.classList.contains('urgent')) {
                    row.style.display = '';
                } else if (status !== 'urgent' && statusCell && statusCell.classList.contains(status)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        // View feedback details
        function viewFeedback(feedbackId) {
            fetch(`get_feedback_details.php?id=${feedbackId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const feedback = data.feedback;
                        document.getElementById('feedbackModalBody').innerHTML = `
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Maelezo ya Msingi</h6>
                                    <p><strong>Aina:</strong> ${feedback.feedback_type || 'N/A'}</p>
                                    <p><strong>Kichwa:</strong> ${feedback.subject || 'N/A'}</p>
                                    <p><strong>Kipaumbele:</strong> ${feedback.priority || 'N/A'}</p>
                                    <p><strong>Hali:</strong> ${feedback.status || 'N/A'}</p>
                                </div>
                                <div class="col-md-6">
                                    <h6>Maelezo ya Mwanafunzi</h6>
                                    <p><strong>Jina:</strong> ${feedback.student_name || 'N/A'}</p>
                                    <p><strong>Barua pepe:</strong> ${feedback.student_email || 'N/A'}</p>
                                    <p><strong>Kozi:</strong> ${feedback.course_name || 'N/A'}</p>
                                    <p><strong>Tarehe:</strong> ${feedback.date_created ? new Date(feedback.date_created).toLocaleDateString('sw-TZ') : 'N/A'}</p>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-12">
                                    <h6>Maelezo ya Maoni</h6>
                                    <p>${feedback.message || 'N/A'}</p>
                                </div>
                            </div>
                        `;

                        const modal = new bootstrap.Modal(document.getElementById('feedbackViewModal'));
                        modal.show();
                    } else {
                        alert('Kuna tatizo la mtandao. Jaribu tena.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Kuna tatizo la mtandao. Jaribu tena.');
                });
        }

        // Delete feedback
        function deleteFeedback(feedbackId) {
            if (confirm('Una uhakika unataka kufuta maoni haya?')) {
                fetch('delete_feedback.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            feedback_id: feedbackId
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert(data.message || 'Imefeli kufuta maoni');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Kuna tatizo la mtandao. Jaribu tena.');
                    });
            }
        }
    </script>
</body>

</html>