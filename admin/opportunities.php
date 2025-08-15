<?php
require_once __DIR__ . "/../middleware/AuthMiddleware.php";
require_once __DIR__ . "/../models/Fursa.php";

// Check if user is admin
$auth = new AuthMiddleware();
$auth->requireRole('admin');

// Initialize models
$fursaModel = new Fursa();

// Get opportunities data
$opportunities = $fursaModel->getAllOpportunitiesForAdmin();
$opportunityStats = $fursaModel->getOverallOpportunityStats();

// Extract stats
$totalOpportunities = $opportunityStats['total'] ?? 0;
$thisMonth = $opportunityStats['this_month'] ?? 0;
$lastMonth = $opportunityStats['last_month'] ?? 0;
$thisYear = $opportunityStats['this_year'] ?? 0;
?>

<!DOCTYPE html>
<html lang="sw">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usimamizi wa Fursa - Panda Digital</title>
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

        .stats-card.this-month {
            border-left-color: #28a745;
        }

        .stats-card.last-month {
            border-left-color: #ffc107;
        }

        .stats-card.this-year {
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

        .opportunity-table {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .opportunity-table .table {
            margin-bottom: 0;
        }

        .opportunity-table .table th {
            border-top: none;
            background: #f8f9fa;
            font-weight: 600;
            color: #495057;
        }

        .opportunity-thumbnail {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 8px;
            border: 2px solid #e9ecef;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease;
        }

        .opportunity-thumbnail:hover {
            transform: scale(1.1);
        }

        .action-btn {
            padding: 0.25rem 0.5rem;
            border-radius: 6px;
            text-decoration: none;
            font-size: 0.875rem;
            margin-right: 0.25rem;
        }

        .action-btn.view {
            background: #6c757d;
            color: white;
        }

        .action-btn.edit {
            background: #6c757d;
            color: white;
        }

        .action-btn.delete {
            background: #6c757d;
            color: white;
        }

        .add-opportunity-btn {
            background: #000;
            border-color: #000;
            border-radius: 10px;
            padding: 0.5rem 1.5rem;
        }

        .add-opportunity-btn:hover {
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
                    <h3 class="mb-2"><?= number_format($totalOpportunities) ?></h3>
                    <p class="text-muted mb-0">Fursa Zote</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card this-month">
                    <h3 class="mb-2"><?= number_format($thisMonth) ?></h3>
                    <p class="text-muted mb-0">Mwezi Huu</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card last-month">
                    <h3 class="mb-2"><?= number_format($lastMonth) ?></h3>
                    <p class="text-muted mb-0">Mwezi Uliopita</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card this-year">
                    <h3 class="mb-2"><?= number_format($thisYear) ?></h3>
                    <p class="text-muted mb-0">Mwaka Huu</p>
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
                        <input type="text" class="form-control" id="searchInput" placeholder="Tafuta fursa...">
                    </div>
                </div>
                <div class="col-md-6 text-end">
                    <a href="add-opportunity.php" class="btn btn-primary add-opportunity-btn">
                        <i class="fas fa-plus me-2"></i>Ongeza Fursa
                    </a>
                    <div class="export-dropdown d-inline-block ms-2">
                        <button class="btn btn-outline-secondary" onclick="toggleExportDropdown()">
                            <i class="fas fa-download me-2"></i>Pakua
                        </button>
                        <div class="export-dropdown-content" id="exportDropdown">
                            <a href="export_opportunities.php?format=csv">CSV</a>
                            <a href="export_opportunities.php?format=pdf">PDF</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Tabs -->
        <div class="filter-tabs">
            <ul class="nav nav-pills" id="filterTabs">
                <li class="nav-item">
                    <a class="nav-link active" href="#" data-filter="all">
                        Zote (<?= $totalOpportunities ?>)
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" data-filter="this-month">
                        Mwezi Huu (<?= $thisMonth ?>)
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" data-filter="last-month">
                        Mwezi Uliopita (<?= $lastMonth ?>)
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" data-filter="this-year">
                        Mwaka Huu (<?= $thisYear ?>)
                    </a>
                </li>
            </ul>
        </div>

        <!-- Opportunities Table -->
        <div class="opportunity-table">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Picha</th>
                        <th>Kichwa Cha Habari</th>
                        <th>Maelezo</th>
                        <th>Mwezi</th>
                        <th>Tarehe</th>
                        <th>Vitendo</th>
                    </tr>
                </thead>
                <tbody id="opportunityTableBody">
                    <?php if (empty($opportunities)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <i class="fas fa-lightbulb fa-2x text-muted mb-2"></i>
                                <p class="text-muted mb-0">Hakuna fursa zilizopatikana</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($opportunities as $item): ?>
                            <tr>
                                <td><?= htmlspecialchars($item['id']) ?></td>
                                <td>
                                    <?php if (!empty($item['image'])): ?>
                                        <img src="../uploads/Fursa/<?= htmlspecialchars($item['image']) ?>"
                                            alt="Opportunity Image"
                                            class="opportunity-thumbnail">
                                    <?php else: ?>
                                        <span class="text-muted">Hakuna picha</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($item['name'] ?? 'N/A') ?></td>
                                <td>
                                    <?php
                                    $description = $item['description'] ?? '';
                                    echo strlen($description) > 50 ? substr($description, 0, 50) . '...' : $description;
                                    ?>
                                </td>
                                <td>
                                    <span class="badge bg-info">
                                        <?= htmlspecialchars($item['month'] ?? 'N/A') ?>
                                    </span>
                                </td>
                                <td>
                                    <?php
                                    $date = $item['date_created'] ?? '';
                                    echo $date ? date('d/m/Y H:i', strtotime($date)) : 'N/A';
                                    ?>
                                </td>
                                <td>
                                    <button class="btn btn-sm action-btn view" onclick="viewOpportunity(<?= $item['id'] ?>)">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <a href="edit-opportunity.php?id=<?= $item['id'] ?>" class="btn btn-sm action-btn edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button class="btn btn-sm action-btn delete" onclick="deleteOpportunity(<?= $item['id'] ?>)">
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

    <!-- Opportunity View Modal -->
    <div class="modal fade" id="opportunityViewModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Maelezo ya Fursa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="opportunityModalBody">
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
            searchOpportunities(this.value);
        });

        function searchOpportunities(query) {
            const rows = document.querySelectorAll('#opportunityTableBody tr');
            query = query.toLowerCase();

            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(query) ? '' : 'none';
            });
        }

        // Filter by month/period
        document.querySelectorAll('#filterTabs .nav-link').forEach(tab => {
            tab.addEventListener('click', function(e) {
                e.preventDefault();

                // Remove active class from all tabs
                document.querySelectorAll('#filterTabs .nav-link').forEach(t => t.classList.remove('active'));

                // Add active class to clicked tab
                this.classList.add('active');

                // Filter table rows
                const filter = this.dataset.filter;
                filterByPeriod(filter);
            });
        });

        function filterByPeriod(filter) {
            const rows = document.querySelectorAll('#opportunityTableBody tr');

            rows.forEach(row => {
                if (filter === 'all') {
                    row.style.display = '';
                    return;
                }

                const monthCell = row.querySelector('td:nth-child(5) .badge');
                const dateCell = row.querySelector('td:nth-child(6)');

                if (monthCell && dateCell) {
                    const month = monthCell.textContent.trim();
                    const date = dateCell.textContent.trim();

                    let shouldShow = false;

                    switch (filter) {
                        case 'this-month':
                            shouldShow = month === new Date().toLocaleDateString('en-US', {
                                month: 'long'
                            });
                            break;
                        case 'last-month':
                            const lastMonth = new Date();
                            lastMonth.setMonth(lastMonth.getMonth() - 1);
                            shouldShow = month === lastMonth.toLocaleDateString('en-US', {
                                month: 'long'
                            });
                            break;
                        case 'this-year':
                            const currentYear = new Date().getFullYear().toString();
                            shouldShow = date.includes(currentYear);
                            break;
                    }

                    row.style.display = shouldShow ? '' : 'none';
                }
            });
        }

        // View opportunity details
        function viewOpportunity(opportunityId) {
            fetch(`get_opportunity_details.php?id=${opportunityId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const opportunity = data.opportunity;
                        document.getElementById('opportunityModalBody').innerHTML = `
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Maelezo ya Msingi</h6>
                                    <p><strong>Kichwa:</strong> ${opportunity.name || 'N/A'}</p>
                                    <p><strong>Mwezi:</strong> ${opportunity.month || 'N/A'}</p>
                                    <p><strong>Tarehe:</strong> ${opportunity.date_created ? new Date(opportunity.date_created).toLocaleDateString('sw-TZ') : 'N/A'}</p>
                                </div>
                                <div class="col-md-6">
                                    <h6>Picha</h6>
                                    ${opportunity.image ? `<img src="../uploads/Fursa/${opportunity.image}" alt="Opportunity Image" class="img-fluid rounded" style="max-width: 200px;">` : '<p class="text-muted">Hakuna picha</p>'}
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-12">
                                    <h6>Maelezo ya Fursa</h6>
                                    <p>${opportunity.description || 'N/A'}</p>
                                </div>
                            </div>
                        `;

                        const modal = new bootstrap.Modal(document.getElementById('opportunityViewModal'));
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

        // Delete opportunity
        function deleteOpportunity(opportunityId) {
            if (confirm('Una uhakika unataka kufuta fursa hii?')) {
                fetch('delete_opportunity.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            opportunity_id: opportunityId
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert(data.message || 'Imefeli kufuta fursa');
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