<?php
require_once __DIR__ . "/../middleware/AuthMiddleware.php";
require_once __DIR__ . "/../models/Sales.php";

// Check if user is admin
$auth = new AuthMiddleware();
$auth->requireRole('admin');

// Initialize models
$salesModel = new Sales();

// Get sales data
$sales = $salesModel->getAllSalesForAdmin();
$salesStats = $salesModel->getOverallSalesStats();

// Extract stats
$totalSales = $salesStats['total_sales'] ?? 0;
$totalRevenue = $salesStats['total_revenue'] ?? 0;
$companyProfit = $salesStats['company_profit'] ?? 0;
$totalSellers = $salesStats['total_sellers'] ?? 0;
?>

<!DOCTYPE html>
<html lang="sw">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usimamizi wa Mauzo - Panda Digital</title>
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

        .stats-card.total-sales {
            border-left-color: #000;
        }

        .stats-card.total-revenue {
            border-left-color: #28a745;
        }

        .stats-card.company-profit {
            border-left-color: #ffc107;
        }

        .stats-card.total-sellers {
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

        .sales-table {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .sales-table .table {
            margin-bottom: 0;
        }

        .sales-table .table th {
            border-top: none;
            background: #f8f9fa;
            font-weight: 600;
            color: #495057;
            padding: 12px 16px;
        }

        .sales-table .table td {
            padding: 12px 16px;
            vertical-align: middle;
        }

        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .status-badge.success {
            background: #d4edda;
            color: #155724;
        }

        .status-badge.failed {
            background: #f8d7da;
            color: #721c24;
        }

        .status-badge.pending {
            background: #fff3cd;
            color: #856404;
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

        .add-sale-btn {
            background: #000;
            border-color: #000;
            border-radius: 10px;
            padding: 0.5rem 1.5rem;
        }

        .add-sale-btn:hover {
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
                <div class="stats-card total-sales">
                    <h3 class="mb-2"><?= number_format($totalSales) ?></h3>
                    <p class="text-muted mb-0">Mauzo Yote</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card total-revenue">
                    <h3 class="mb-2">TSh <?= number_format($totalRevenue, 2) ?></h3>
                    <p class="text-muted mb-0">Mapato Yote</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card company-profit">
                    <h3 class="mb-2">TSh <?= number_format($companyProfit, 2) ?></h3>
                    <p class="text-muted mb-0">Faida ya Kampuni (6%)</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card total-sellers">
                    <h3 class="mb-2"><?= number_format($totalSellers) ?></h3>
                    <p class="text-muted mb-0">Wauzaji Wote</p>
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
                        <input type="text" class="form-control" id="searchInput" placeholder="Tafuta mauzo...">
                    </div>
                </div>
                <div class="col-md-6 text-end">
                    <a href="add-sale.php" class="btn btn-primary add-sale-btn">
                        <i class="fas fa-plus me-2"></i>Ongeza Muuzo
                    </a>
                    <div class="export-dropdown d-inline-block ms-2">
                        <button class="btn btn-outline-secondary" onclick="toggleExportDropdown()">
                            <i class="fas fa-download me-2"></i>Pakua
                        </button>
                        <div class="export-dropdown-content" id="exportDropdown">
                            <a href="export_sales.php?format=csv">CSV</a>
                            <a href="export_sales.php?format=pdf">PDF</a>
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
                        Zote (<?= $totalSales ?>)
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" data-status="Success">
                        Yaliyofanikiwa
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" data-status="Failed">
                        Yaliyoshindwa
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" data-status="Pending">
                        Yanayosubiri
                    </a>
                </li>
            </ul>
        </div>

        <!-- Sales Table -->
        <div class="sales-table">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Namba ya Rejeleo</th>
                        <th>Bidhaa</th>
                        <th>Muuzaji</th>
                        <th>Kiasi</th>
                        <th>Idadi</th>
                        <th>Faida ya Kampuni</th>
                        <th>Tarehe</th>
                        <th>Hali</th>
                        <th>Vitendo</th>
                    </tr>
                </thead>
                <tbody id="salesTableBody">
                    <?php if (empty($sales)): ?>
                        <tr>
                            <td colspan="10" class="text-center py-4">
                                <i class="fas fa-shopping-cart fa-2x text-muted mb-2"></i>
                                <p class="text-muted mb-0">Hakuna mauzo yaliyopatikana</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($sales as $item): ?>
                            <tr>
                                <td><?= htmlspecialchars($item['sale_id']) ?></td>
                                <td><?= htmlspecialchars($item['reference_no']) ?></td>
                                <td><?= htmlspecialchars($item['product_name'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars(($item['first_name'] ?? '') . ' ' . ($item['last_name'] ?? '')) ?></td>
                                <td>TSh <?= number_format($item['amount'], 2) ?></td>
                                <td><?= htmlspecialchars($item['quantity']) ?></td>
                                <td>TSh <?= number_format($item['amount'] * 0.06, 2) ?></td>
                                <td>
                                    <?php
                                    $date = $item['sale_date'] ?? '';
                                    echo $date ? date('d/m/Y H:i', strtotime($date)) : 'N/A';
                                    ?>
                                </td>
                                <td>
                                    <?php if (isset($item['transaction_status'])): ?>
                                        <span class="status-badge <?= strtolower($item['transaction_status']) ?>">
                                            <?= htmlspecialchars($item['transaction_status']) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted">N/A</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <button class="btn btn-sm action-btn view" onclick="viewSale(<?= $item['sale_id'] ?>)">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <a href="edit-sale.php?id=<?= $item['sale_id'] ?>" class="btn btn-sm action-btn edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button class="btn btn-sm action-btn delete" onclick="deleteSale(<?= $item['sale_id'] ?>)">
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

    <!-- Sale View Modal -->
    <div class="modal fade" id="saleViewModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Maelezo ya Muuzo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="saleModalBody">
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
            searchSales(this.value);
        });

        function searchSales(query) {
            const rows = document.querySelectorAll('#salesTableBody tr');
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
            const rows = document.querySelectorAll('#salesTableBody tr');

            rows.forEach(row => {
                if (status === 'all') {
                    row.style.display = '';
                    return;
                }

                const statusCell = row.querySelector('.status-badge');

                if (statusCell && statusCell.textContent.trim() === status) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        // View sale details
        function viewSale(saleId) {
            fetch(`get_sale_details.php?id=${saleId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const sale = data.sale;
                        document.getElementById('saleModalBody').innerHTML = `
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Maelezo ya Msingi</h6>
                                    <p><strong>ID:</strong> ${sale.sale_id || 'N/A'}</p>
                                    <p><strong>Namba ya Rejeleo:</strong> ${sale.reference_no || 'N/A'}</p>
                                    <p><strong>Bidhaa:</strong> ${sale.product_name || 'N/A'}</p>
                                    <p><strong>Kiasi:</strong> TSh ${sale.amount ? parseFloat(sale.amount).toLocaleString('en-US', {minimumFractionDigits: 2}) : 'N/A'}</p>
                                </div>
                                <div class="col-md-6">
                                    <h6>Maelezo ya Muuzaji</h6>
                                    <p><strong>Jina:</strong> ${(sale.first_name || '') + ' ' + (sale.last_name || '')}</p>
                                    <p><strong>Hali:</strong> ${sale.transaction_status || 'N/A'}</p>
                                    <p><strong>Tarehe:</strong> ${sale.sale_date ? new Date(sale.sale_date).toLocaleDateString('sw-TZ') : 'N/A'}</p>
                                    <p><strong>Faida ya Kampuni:</strong> TSh ${sale.amount ? (parseFloat(sale.amount) * 0.06).toLocaleString('en-US', {minimumFractionDigits: 2}) : 'N/A'}</p>
                                </div>
                            </div>
                        `;

                        const modal = new bootstrap.Modal(document.getElementById('saleViewModal'));
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

        // Delete sale
        function deleteSale(saleId) {
            if (confirm('Una uhakika unataka kufuta muuzo huu?')) {
                fetch('delete_sale.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            sale_id: saleId
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert(data.message || 'Imefeli kufuta muuzo');
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