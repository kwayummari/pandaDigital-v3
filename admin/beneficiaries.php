<?php
require_once __DIR__ . "/../middleware/AuthMiddleware.php";
require_once __DIR__ . "/../models/Beneficiary.php";

// Check if user is admin
$auth = new AuthMiddleware();
$auth->requireRole('admin');

// Initialize models
$beneficiaryModel = new Beneficiary();

// Get beneficiaries data
$beneficiaries = $beneficiaryModel->getAllWanufaikaForAdmin();
$beneficiaryStats = $beneficiaryModel->getWanufaikaStats();

// Extract stats
$totalBeneficiaries = $beneficiaryStats['total'] ?? 0;
$thisMonth = $beneficiaryStats['this_month'] ?? 0;
$lastMonth = $beneficiaryStats['last_month'] ?? 0;
$thisYear = $beneficiaryStats['this_year'] ?? 0;
?>

<!DOCTYPE html>
<html lang="sw">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wanufaika - Panda Digital</title>
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

        .stats-card.total-beneficiaries {
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

        .beneficiaries-table {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .beneficiaries-table .table {
            margin-bottom: 0;
        }

        .beneficiaries-table .table th {
            border-top: none;
            background: #f8f9fa;
            font-weight: 600;
            color: #495057;
            padding: 12px 16px;
        }

        .beneficiaries-table .table td {
            padding: 12px 16px;
            vertical-align: middle;
        }

        .beneficiary-thumbnail {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 8px;
            border: 2px solid #dee2e6;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease;
        }

        .beneficiary-thumbnail:hover {
            transform: scale(1.1);
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
                <div class="stats-card total-beneficiaries">
                    <h3 class="mb-2"><?= number_format($totalBeneficiaries) ?></h3>
                    <p class="text-muted mb-0">Wanufaika Wote</p>
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
                        <input type="text" class="form-control" id="searchInput" placeholder="Tafuta wanufaika...">
                    </div>
                </div>
                <div class="col-md-6 text-end">
                    <a href="add-beneficiary.php" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Ongeza Mwanufaika
                    </a>
                    <div class="export-dropdown d-inline-block ms-2">
                        <button class="btn btn-outline-secondary" onclick="toggleExportDropdown()">
                            <i class="fas fa-download me-2"></i>Pakua
                        </button>
                        <div class="export-dropdown-content" id="exportDropdown">
                            <a href="export_beneficiaries.php?format=csv">CSV</a>
                            <a href="export_beneficiaries.php?format=pdf">PDF</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Tabs -->
        <div class="filter-tabs">
            <ul class="nav nav-pills" id="filterTabs">
                <li class="nav-item">
                    <a class="nav-link active" href="#" data-period="all">
                        Zote (<?= $totalBeneficiaries ?>)
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" data-period="this_month">
                        Mwezi Huu (<?= $thisMonth ?>)
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" data-period="last_month">
                        Mwezi Uliopita (<?= $lastMonth ?>)
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" data-period="this_year">
                        Mwaka Huu (<?= $thisYear ?>)
                    </a>
                </li>
            </ul>
        </div>

        <!-- Beneficiaries Table -->
        <div class="beneficiaries-table">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Picha</th>
                        <th>Jina</th>
                        <th>Kichwa Cha Habari</th>
                        <th>Maelezo</th>
                        <th>Tarehe</th>
                        <th>Vitendo</th>
                    </tr>
                </thead>
                <tbody id="beneficiariesTableBody">
                    <?php if (empty($beneficiaries)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <i class="fas fa-users fa-2x text-muted mb-2"></i>
                                <p class="text-muted mb-0">Hakuna wanufaika walio patikana</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($beneficiaries as $item): ?>
                            <tr>
                                <td><?= htmlspecialchars($item['id']) ?></td>
                                <td>
                                    <?php if (!empty($item['photo'])): ?>
                                        <img src="../uploads/Wanufaika/<?= htmlspecialchars($item['photo']) ?>"
                                            alt="Beneficiary Photo" class="beneficiary-thumbnail">
                                    <?php else: ?>
                                        <div class="beneficiary-thumbnail bg-light d-flex align-items-center justify-content-center">
                                            <i class="fas fa-user text-muted"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($item['name'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($item['title'] ?? 'N/A') ?></td>
                                <td>
                                    <?php
                                    $description = $item['description'] ?? '';
                                    echo strlen($description) > 100 ?
                                        htmlspecialchars(substr($description, 0, 100)) . '...' :
                                        htmlspecialchars($description);
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    $date = $item['date_created'] ?? '';
                                    echo $date ? date('d/m/Y H:i', strtotime($date)) : 'N/A';
                                    ?>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-sm btn-outline-info"
                                            onclick="viewBeneficiary(<?= $item['id'] ?>)">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <a href="edit-beneficiary.php?id=<?= $item['id'] ?>"
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                            onclick="deleteBeneficiary(<?= $item['id'] ?>)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Beneficiary View Modal -->
    <div class="modal fade" id="beneficiaryViewModal" tabindex="-1" aria-labelledby="beneficiaryViewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="beneficiaryViewModalLabel">Maelezo ya Mwanufaika</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="beneficiaryModalBody">
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
            searchBeneficiaries(this.value);
        });

        function searchBeneficiaries(query) {
            const rows = document.querySelectorAll('#beneficiariesTableBody tr');
            query = query.toLowerCase();

            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(query) ? '' : 'none';
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

                // Filter table rows
                const period = this.dataset.period;
                filterByPeriod(period);
            });
        });

        function filterByPeriod(period) {
            const rows = document.querySelectorAll('#beneficiariesTableBody tr');

            if (period === 'all') {
                rows.forEach(row => row.style.display = '');
                return;
            }

            // For now, just show all rows since filtering by date requires more complex logic
            // This can be enhanced later with actual date filtering
            rows.forEach(row => row.style.display = '');
        }

        // View beneficiary details
        function viewBeneficiary(beneficiaryId) {
            // Show loading state
            document.getElementById('beneficiaryModalBody').innerHTML =
                '<div class="text-center"><i class="fas fa-spinner fa-spin fa-2x"></i><p>Inapakia...</p></div>';

            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('beneficiaryViewModal'));
            modal.show();

            // Fetch beneficiary details via AJAX
            fetch(`get_beneficiary_details.php?id=${beneficiaryId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const beneficiary = data.beneficiary;
                        document.getElementById('beneficiaryModalBody').innerHTML = `
                            <div class="row">
                                <div class="col-md-4">
                                    ${beneficiary.photo ? 
                                        `<img src="../uploads/Wanufaika/${beneficiary.photo}" alt="Beneficiary Photo" class="img-fluid rounded">` :
                                        `<div class="bg-light rounded p-4 text-center"><i class="fas fa-user fa-3x text-muted"></i></div>`
                                    }
                                </div>
                                <div class="col-md-8">
                                    <h5>${beneficiary.name}</h5>
                                    <h6 class="text-muted">${beneficiary.title}</h6>
                                    <hr>
                                    <p><strong>Maelezo:</strong></p>
                                    <p>${beneficiary.description}</p>
                                    <hr>
                                    <p><strong>Tarehe ya Uundaji:</strong> ${beneficiary.date_created}</p>
                                </div>
                            </div>
                        `;
                    } else {
                        document.getElementById('beneficiaryModalBody').innerHTML =
                            '<div class="alert alert-danger">Imefeli kupata maelezo ya mwanufaika.</div>';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('beneficiaryModalBody').innerHTML =
                        '<div class="alert alert-danger">Kuna tatizo la mtandao. Jaribu tena.</div>';
                });
        }

        // Delete beneficiary
        function deleteBeneficiary(beneficiaryId) {
            if (confirm('Je, una uhakika unataka kufuta mwanufaika huyu? Kitendo hiki hakiwezi kubatilishwa!')) {
                // Show loading state
                const btn = event.target.closest('button');
                const originalContent = btn.innerHTML;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                btn.disabled = true;

                // Send delete request
                fetch('delete_beneficiary.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `id=${beneficiaryId}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Remove row from table
                            const row = btn.closest('tr');
                            row.remove();

                            // Show success message
                            alert('Mwanufaika amefutwa kikamilifu!');

                            // Refresh page to update stats
                            location.reload();
                        } else {
                            alert('Imefeli kufuta mwanufaika: ' + (data.message || 'Tafadhali jaribu tena.'));
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Kuna tatizo la mtandao. Jaribu tena.');
                    })
                    .finally(() => {
                        // Restore button
                        btn.innerHTML = originalContent;
                        btn.disabled = false;
                    });
            }
        }
    </script>
</body>

</html>