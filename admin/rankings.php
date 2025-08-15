<?php
require_once __DIR__ . "/../middleware/AuthMiddleware.php";
require_once __DIR__ . "/../models/Ranking.php";

// Check if user is admin
$auth = new AuthMiddleware();
$auth->requireRole('admin');

// Initialize models
$rankingModel = new Ranking();

// Get rankings data
$rankings = $rankingModel->getAllPowerRankings();
$rankingStats = $rankingModel->getPowerRankingStats();

// Extract stats
$totalParticipants = $rankingStats['total_participants'] ?? 0;
$totalCorrectAnswers = $rankingStats['total_correct_answers'] ?? 0;
$topScore = $rankingStats['top_score'] ?? 0;
$averageScore = $rankingStats['average_score'] ?? 0;
?>

<!DOCTYPE html>
<html lang="sw">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daraja la Uwezo - Panda Digital</title>
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

        .stats-card.total-participants {
            border-left-color: #000;
        }

        .stats-card.total-answers {
            border-left-color: #28a745;
        }

        .stats-card.top-score {
            border-left-color: #ffc107;
        }

        .stats-card.average-score {
            border-left-color: #17a2b8;
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

        .rankings-table {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .rankings-table .table {
            margin-bottom: 0;
        }

        .rankings-table .table th {
            border-top: none;
            background: #f8f9fa;
            font-weight: 600;
            color: #495057;
            padding: 12px 16px;
        }

        .rankings-table .table td {
            padding: 12px 16px;
            vertical-align: middle;
        }

        .rank-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .rank-badge.gold {
            background: #ffd700;
            color: #000;
        }

        .rank-badge.silver {
            background: #c0c0c0;
            color: #000;
        }

        .rank-badge.bronze {
            background: #cd7f32;
            color: #fff;
        }

        .rank-badge.regular {
            background: #e9ecef;
            color: #495057;
        }

        .action-btn {
            padding: 0.25rem 0.5rem;
            border-radius: 6px;
            text-decoration: none;
            font-size: 0.875rem;
            margin-right: 0.25rem;
        }

        .action-btn.view {
            background: white;
            color: #000;
            border: 1px solid #dee2e6;
        }

        .action-btn.edit {
            background: white;
            color: #000;
            border: 1px solid #dee2e6;
        }

        .action-btn.delete {
            background: white;
            color: #000;
            border: 1px solid #dee2e6;
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

        .crown-icon {
            width: 30px;
            height: 30px;
            margin-right: 10px;
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
                <div class="stats-card total-participants">
                    <h3 class="mb-2"><?= number_format($totalParticipants) ?></h3>
                    <p class="text-muted mb-0">Washiriki Wote</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card total-answers">
                    <h3 class="mb-2"><?= number_format($totalCorrectAnswers) ?></h3>
                    <p class="text-muted mb-0">Majibu Sahihi Yote</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card top-score">
                    <h3 class="mb-2"><?= number_format($topScore) ?></h3>
                    <p class="text-muted mb-0">Alama ya Juu Zaidi</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card average-score">
                    <h3 class="mb-2"><?= number_format($averageScore) ?></h3>
                    <p class="text-muted mb-0">Alama ya Wastani</p>
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
                        <input type="text" class="form-control" id="searchInput" placeholder="Tafuta washiriki...">
                    </div>
                </div>
                <div class="col-md-6 text-end">
                    <div class="export-dropdown d-inline-block">
                        <button class="btn btn-outline-secondary" onclick="toggleExportDropdown()">
                            <i class="fas fa-download me-2"></i>Pakua
                        </button>
                        <div class="export-dropdown-content" id="exportDropdown">
                            <a href="export_rankings.php?format=csv">CSV</a>
                            <a href="export_rankings.php?format=pdf">PDF</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Tabs -->
        <div class="filter-tabs">
            <ul class="nav nav-pills" id="filterTabs">
                <li class="nav-item">
                    <a class="nav-link active" href="#" data-rank="all">
                        Zote (<?= $totalParticipants ?>)
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" data-rank="top3">
                        Top 3
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" data-rank="top10">
                        Top 10
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" data-rank="others">
                        Wengine
                    </a>
                </li>
            </ul>
        </div>

        <!-- Rankings Table -->
        <div class="rankings-table">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Nafasi</th>
                        <th>Jina</th>
                        <th>Alama</th>
                        <th>Vitendo</th>
                    </tr>
                </thead>
                <tbody id="rankingsTableBody">
                    <?php if (empty($rankings)): ?>
                        <tr>
                            <td colspan="4" class="text-center py-4">
                                <i class="fas fa-trophy fa-2x text-muted mb-2"></i>
                                <p class="text-muted mb-0">Hakuna washiriki walio patikana</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($rankings as $index => $item): ?>
                            <?php
                            $rank = $index + 1;
                            $rankClass = '';
                            $crownIcon = '';

                            if ($rank <= 3) {
                                $rankClass = 'gold';
                                $crownIcon = '<img src="../assets/images/gold-crown.png" alt="Gold Crown" class="crown-icon">';
                            } elseif ($rank <= 6) {
                                $rankClass = 'silver';
                                $crownIcon = '<img src="../assets/images/silver-crown.png" alt="Silver Crown" class="crown-icon">';
                            } elseif ($rank <= 9) {
                                $rankClass = 'bronze';
                                $crownIcon = '<img src="../assets/images/bronze-crown.png" alt="Bronze Crown" class="crown-icon">';
                            } else {
                                $rankClass = 'regular';
                            }
                            ?>
                            <tr>
                                <td>
                                    <span class="rank-badge <?= $rankClass ?>">
                                        <?= $crownIcon ?><?= $rank ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars($item['fullname'] ?? 'N/A') ?></td>
                                <td>
                                    <strong><?= number_format($item['total_correct_answers']) ?></strong>
                                    <small class="text-muted d-block">majibu sahihi</small>
                                </td>
                                <td>
                                    <button class="btn btn-sm action-btn view" onclick="viewRanking(<?= $rank ?>)">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm action-btn edit" onclick="editRanking(<?= $rank ?>)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm action-btn delete" onclick="deleteRanking(<?= $rank ?>)">
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

    <!-- Ranking View Modal -->
    <div class="modal fade" id="rankingViewModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Maelezo ya Nafasi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="rankingModalBody">
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
            searchRankings(this.value);
        });

        function searchRankings(query) {
            const rows = document.querySelectorAll('#rankingsTableBody tr');
            query = query.toLowerCase();

            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(query) ? '' : 'none';
            });
        }

        // Filter by rank
        document.querySelectorAll('#filterTabs .nav-link').forEach(tab => {
            tab.addEventListener('click', function(e) {
                e.preventDefault();

                // Remove active class from all tabs
                document.querySelectorAll('#filterTabs .nav-link').forEach(t => t.classList.remove('active'));

                // Add active class to clicked tab
                this.classList.add('active');

                // Filter table rows
                const rank = this.dataset.rank;
                filterByRank(rank);
            });
        });

        function filterByRank(rank) {
            const rows = document.querySelectorAll('#rankingsTableBody tr');

            rows.forEach((row, index) => {
                if (rank === 'all') {
                    row.style.display = '';
                    return;
                }

                const rankNumber = index + 1;

                if (rank === 'top3' && rankNumber <= 3) {
                    row.style.display = '';
                } else if (rank === 'top10' && rankNumber <= 10) {
                    row.style.display = '';
                } else if (rank === 'others' && rankNumber > 10) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        // View ranking details
        function viewRanking(rank) {
            // For now, just show basic info
            document.getElementById('rankingModalBody').innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <h6>Maelezo ya Nafasi</h6>
                        <p><strong>Nafasi:</strong> ${rank}</p>
                        <p><strong>Jina:</strong> ${document.querySelector(`#rankingsTableBody tr:nth-child(${rank}) td:nth-child(2)`).textContent}</p>
                        <p><strong>Alama:</strong> ${document.querySelector(`#rankingsTableBody tr:nth-child(${rank}) td:nth-child(3) strong`).textContent}</p>
                    </div>
                    <div class="col-md-6">
                        <h6>Maelezo ya Ziada</h6>
                        <p><strong>Daraja:</strong> ${rank <= 3 ? 'Dhahabu' : rank <= 6 ? 'Fedha' : rank <= 9 ? 'Shaba' : 'Kawaida'}</p>
                        <p><strong>Hali:</strong> ${rank <= 10 ? 'Top Performer' : 'Regular Participant'}</p>
                    </div>
                </div>
            `;

            const modal = new bootstrap.Modal(document.getElementById('rankingViewModal'));
            modal.show();
        }

        // Edit ranking (placeholder)
        function editRanking(rank) {
            alert('Kazi ya kuhariri nafasi itaongezwa hivi karibuni');
        }

        // Delete ranking (placeholder)
        function deleteRanking(rank) {
            if (confirm('Una uhakika unataka kufuta nafasi hii?')) {
                alert('Kazi ya kufuta nafasi itaongezwa hivi karibuni');
            }
        }
    </script>
</body>

</html>