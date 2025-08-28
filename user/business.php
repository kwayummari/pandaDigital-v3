<?php
require_once __DIR__ . "/../config/init.php";
require_once __DIR__ . "/../middleware/AuthMiddleware.php";
require_once __DIR__ . "/../models/Business.php";
require_once __DIR__ . "/../models/User.php";
require_once __DIR__ . "/../includes/profile-check.php";

$auth = new AuthMiddleware();
$auth->requireRole('user');

$currentUser = $auth->getCurrentUser();

// Check profile completion for business access
$userModel = new User($pdo);
$profileCompletionStatus = getProfileCompletionStatus($userModel, $currentUser['id']);

// If profile is not complete, require completion before accessing business features
if (!$hasMinimumProfile($userModel, $currentUser['id'])) {
    requireProfileCompletion($userModel, $currentUser['id'], 'business');
}
$businessModel = new Business();

// Get user's businesses
$userBusinesses = $businessModel->getBusinessesByUserId($currentUser['id']);

// Get active tab from URL parameter
$activeTab = $_GET['tab'] ?? 'overview';

// Calculate business statistics
$totalBusinesses = count($userBusinesses);
$approvedBusinesses = count(array_filter($userBusinesses, function ($b) {
    return $b['status'] === 'approved';
}));
$pendingBusinesses = count(array_filter($userBusinesses, function ($b) {
    return $b['status'] === 'pending';
}));

// Get selected business for detailed view
$selectedBusinessId = $_GET['business_id'] ?? ($userBusinesses[0]['id'] ?? null);
$selectedBusiness = null;
$businessProducts = [];
$businessSales = [];

if ($selectedBusinessId) {
    $selectedBusiness = array_filter($userBusinesses, function ($b) use ($selectedBusinessId) {
        return $b['id'] == $selectedBusinessId;
    });
    $selectedBusiness = reset($selectedBusiness);

    if ($selectedBusiness) {
        $businessProducts = $businessModel->getProductsByBusinessId($selectedBusinessId);
        $businessSales = $businessModel->getSalesByBusinessId($selectedBusinessId);
    }
}

// Calculate sales statistics
$totalSales = count($businessSales);
$totalRevenue = array_sum(array_column($businessSales, 'amount') ?: [0]);
$completedSales = count(array_filter($businessSales, function ($s) {
    return isset($s['status']) && $s['status'] == '1';
}));
$pendingSales = count(array_filter($businessSales, function ($s) {
    return isset($s['status']) && $s['status'] == '0';
}));
?>

<!DOCTYPE html>
<html lang="sw">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Biashara - Panda Digital</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= app_url('assets/css/style.css') ?>?v=7">
    <style>
        /* Additional styles for business page */
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease;
        }

        .card:hover {
            transform: translateY(-2px);
        }

        .stats-card {
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .stats-card .card-body {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            text-align: center;
        }

        .business-card {
            border: 1px solid var(--border-color);
            border-radius: 15px;
            transition: all 0.3s ease;
            background: white;
            margin-bottom: 20px;
        }

        .business-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }

        .business-header {
            background: #f8f9fa;
            border-bottom: 1px solid var(--border-color);
            border-radius: 14px 14px 0 0;
            padding: 20px;
        }

        .business-body {
            padding: 20px;
        }

        .nav-tabs {
            border-bottom: 2px solid var(--border-color);
        }

        .nav-tabs .nav-link {
            border: none;
            color: var(--gray-color);
            font-weight: 500;
            padding: 12px 20px;
            border-radius: 0;
        }

        .nav-tabs .nav-link.active {
            color: var(--primary-color);
            background: none;
            border-bottom: 3px solid var(--primary-color);
        }

        .nav-tabs .nav-link:hover {
            border-color: transparent;
            color: var(--primary-color);
        }

        .product-item {
            border: 1px solid var(--border-color);
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            transition: all 0.3s ease;
        }

        .product-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .product-photo {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
        }

        .sale-item {
            border-left: 4px solid var(--primary-color);
            padding: 15px;
            margin-bottom: 15px;
            background: #f8f9fa;
            border-radius: 0 8px 8px 0;
        }

        .sale-item.completed {
            border-left-color: var(--success-color);
        }

        .sale-item.pending {
            border-left-color: var(--warning-color);
        }

        .status-badge {
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .status-approved {
            background: var(--success-color);
            color: white;
        }

        .status-pending {
            background: var(--warning-color);
            color: white;
        }

        .status-completed {
            background: var(--success-color);
            color: white;
        }

        .status-pending-sale {
            background: var(--warning-color);
            color: white;
        }

        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
        }

        .empty-state h5 {
            margin-top: 1rem;
            font-weight: 600;
            color: var(--gray-color);
        }

        .empty-state p {
            color: var(--gray-color);
            margin-bottom: 1.5rem;
        }

        .business-selector {
            background: #f8f9fa;
            border: 1px solid var(--border-color);
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <!-- Sidebar and Main Content Layout -->
    <div class="dashboard-container">
        <?php include __DIR__ . '/../includes/user_sidebar.php'; ?>

        <!-- Main Content -->
        <div class="main-content">
            <?php
            $page_title = 'Biashara';
            include __DIR__ . '/../includes/user_top_nav.php';
            ?>

            <div class="content-wrapper">
                <!-- Header -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h1 class="h3 mb-0">
                            Biashara
                        </h1>
                        <p class="text-muted">Dhibiti na uone maendeleo ya biashara zako</p>
                    </div>
                </div>

                <?php if (empty($userBusinesses)): ?>
                    <!-- No Businesses State -->
                    <div class="empty-state">
                        <h5>Huna biashara zilizosajiliwa bado</h5>
                        <p>Jisajili biashara yako ili uone maendeleo na bidhaa zako</p>
                        <a href="<?= app_url('user/register-business.php') ?>" class="btn btn-primary">
                            Sajili Biashara
                        </a>
                    </div>
                <?php else: ?>
                    <!-- Business Selector -->
                    <?php if (count($userBusinesses) > 1): ?>
                        <div class="business-selector">
                            <label for="businessSelect" class="form-label fw-bold mb-2">Chagua Biashara:</label>
                            <select class="form-select" id="businessSelect" onchange="changeBusiness(this.value)">
                                <?php foreach ($userBusinesses as $business): ?>
                                    <option value="<?php echo $business['id']; ?>"
                                        <?php echo $business['id'] == $selectedBusinessId ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($business['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php endif; ?>

                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card stats-card">
                                <div class="card-body">
                                    <h3 class="mb-1"><?php echo $totalBusinesses; ?></h3>
                                    <p class="mb-0">Jumla ya Biashara</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card stats-card">
                                <div class="card-body">
                                    <h3 class="mb-1"><?php echo $approvedBusinesses; ?></h3>
                                    <p class="mb-0">Zilizoidhinishwa</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card stats-card">
                                <div class="card-body">
                                    <h3 class="mb-1"><?php echo count($businessProducts); ?></h3>
                                    <p class="mb-0">Bidhaa</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card stats-card">
                                <div class="card-body">
                                    <h3 class="mb-1"><?php echo $totalSales; ?></h3>
                                    <p class="mb-0">Mauzo</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php if ($selectedBusiness): ?>
                        <!-- Business Details -->
                        <div class="business-card">
                            <div class="business-header">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <h4 class="mb-1"><?php echo htmlspecialchars($selectedBusiness['name']); ?></h4>
                                        <p class="mb-0 text-muted">
                                            <span class="status-badge status-<?php echo $selectedBusiness['status']; ?>">
                                                <?php echo $selectedBusiness['status'] === 'approved' ? 'Imekamilika' : 'Inasubiri'; ?>
                                            </span>
                                            • <?php echo htmlspecialchars($selectedBusiness['location']); ?>
                                        </p>
                                    </div>
                                    <div class="col-md-4 text-end">
                                        <small class="text-muted">
                                            Imeanzishwa: <?php echo date('d/m/Y', strtotime($selectedBusiness['date_created'])); ?>
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <div class="business-body">
                                <p class="mb-3"><?php echo htmlspecialchars($selectedBusiness['maelezo']); ?></p>

                                <!-- Navigation Tabs -->
                                <ul class="nav nav-tabs" id="businessTabs" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link <?php echo $activeTab === 'overview' ? 'active' : ''; ?>"
                                            id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview"
                                            type="button" role="tab">
                                            Muhtasari
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link <?php echo $activeTab === 'products' ? 'active' : ''; ?>"
                                            id="products-tab" data-bs-toggle="tab" data-bs-target="#products"
                                            type="button" role="tab">
                                            Bidhaa
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link <?php echo $activeTab === 'sales' ? 'active' : ''; ?>"
                                            id="sales-tab" data-bs-toggle="tab" data-bs-target="#sales"
                                            type="button" role="tab">
                                            Mauzo
                                        </button>
                                    </li>
                                </ul>

                                <!-- Tab Content -->
                                <div class="tab-content mt-4" id="businessTabContent">
                                    <!-- Overview Tab -->
                                    <div class="tab-pane fade <?php echo $activeTab === 'overview' ? 'show active' : ''; ?>"
                                        id="overview" role="tabpanel">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <h6>Taarifa za Biashara</h6>
                                                <div class="mb-3">
                                                    <strong>Jina:</strong> <?php echo htmlspecialchars($selectedBusiness['name']); ?>
                                                </div>
                                                <div class="mb-3">
                                                    <strong>Mahali:</strong> <?php echo htmlspecialchars($selectedBusiness['location']); ?>
                                                </div>
                                                <div class="mb-3">
                                                    <strong>Hali:</strong>
                                                    <span class="status-badge status-<?php echo $selectedBusiness['status']; ?>">
                                                        <?php echo $selectedBusiness['status'] === 'approved' ? 'Imekamilika' : 'Inasubiri'; ?>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <h6>Takwimu</h6>
                                                <div class="mb-3">
                                                    <strong>Bidhaa:</strong> <?php echo count($businessProducts); ?>
                                                </div>
                                                <div class="mb-3">
                                                    <strong>Mauzo:</strong> <?php echo $totalSales; ?>
                                                </div>
                                                <div class="mb-3">
                                                    <strong>Mapato:</strong> TSh <?php echo number_format($totalRevenue); ?>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Business Actions -->
                                        <div class="mt-4 pt-3 border-top">
                                            <div class="d-flex gap-3">
                                                <a href="<?= app_url('user/edit-business.php?business_id=' . $selectedBusinessId) ?>" class="btn btn-outline-primary">
                                                    <i class="fas fa-edit me-2"></i>Hariri Biashara
                                                </a>
                                                <a href="<?= app_url('user/add-product.php?business_id=' . $selectedBusinessId) ?>" class="btn btn-primary">
                                                    <i class="fas fa-plus me-2"></i>Ongeza Bidhaa
                                                </a>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Products Tab -->
                                    <div class="tab-pane fade <?php echo $activeTab === 'products' ? 'show active' : ''; ?>"
                                        id="products" role="tabpanel">
                                        <?php if (empty($businessProducts)): ?>
                                            <div class="empty-state">
                                                <h5>Hakuna bidhaa bado</h5>
                                                <p>Ongeza bidhaa kwenye biashara yako</p>
                                                <a href="<?= app_url('user/add-product.php?business_id=' . $selectedBusinessId) ?>" class="btn btn-primary">
                                                    Ongeza Bidhaa
                                                </a>
                                            </div>
                                        <?php else: ?>
                                            <div class="row">
                                                <?php foreach ($businessProducts as $product): ?>
                                                    <div class="col-md-6 col-lg-4 mb-3">
                                                        <div class="product-item">
                                                            <div class="d-flex align-items-start">
                                                                <?php if (!empty($product['image'])): ?>
                                                                    <img src="<?= app_url('uploads/' . $product['image']) ?>"
                                                                        alt="<?php echo htmlspecialchars($product['name'] ?? ''); ?>"
                                                                        class="product-photo me-3">
                                                                <?php endif; ?>
                                                                <div class="flex-grow-1">
                                                                    <div class="d-flex align-items-center gap-2 mb-1">
                                                                        <h6 class="mb-0"><?php echo htmlspecialchars($product['name'] ?? ''); ?></h6>
                                                                        <div class="d-flex gap-1">
                                                                            <span class="badge bg-<?php echo ($product['status'] ?? '1') == '1' ? 'success' : 'secondary'; ?> fs-6">
                                                                                <?php echo ($product['status'] ?? '1') == '1' ? 'Iko Soko' : 'Haiko Soko'; ?>
                                                                            </span>
                                                                            <?php if (($product['isOffered'] ?? '0') == '1' && !empty($product['offer'])): ?>
                                                                                <span class="badge bg-danger fs-6">
                                                                                    -<?php echo $product['offer']; ?>%
                                                                                </span>
                                                                            <?php endif; ?>
                                                                        </div>
                                                                    </div>
                                                                    <p class="mb-2 text-muted small">
                                                                        <?php echo htmlspecialchars(substr($product['description'] ?? '', 0, 100)) . '...'; ?>
                                                                    </p>
                                                                    <div class="d-flex justify-content-between align-items-center">
                                                                        <div class="d-flex flex-column">
                                                                            <?php if (($product['isOffered'] ?? '0') == '1' && !empty($product['offer'])): ?>
                                                                                <span class="text-decoration-line-through text-muted small">
                                                                                    TSh <?php echo number_format($product['amount'] ?? 0); ?>
                                                                                </span>
                                                                                <span class="fw-bold text-danger">
                                                                                    TSh <?php echo number_format(($product['amount'] ?? 0) - (($product['amount'] ?? 0) * ($product['offer'] ?? 0) / 100)) ?>
                                                                                </span>
                                                                            <?php else: ?>
                                                                                <span class="fw-bold text-primary">
                                                                                    TSh <?php echo number_format($product['amount'] ?? 0); ?>
                                                                                </span>
                                                                            <?php endif; ?>
                                                                        </div>
                                                                        <div class="d-flex align-items-center gap-2">
                                                                            <small class="text-muted">
                                                                                <?php echo $product['category_name'] ?? 'Hakuna Kategoria'; ?>
                                                                            </small>
                                                                            <div class="d-flex gap-1">
                                                                                <a href="<?= app_url('user/edit-product.php?business_id=' . $selectedBusinessId . '&product_id=' . $product['id']) ?>"
                                                                                    class="btn btn-sm btn-outline-primary">
                                                                                    Hariri
                                                                                </a>
                                                                                <button type="button"
                                                                                    class="btn btn-sm btn-<?php echo ($product['status'] ?? '1') == '1' ? 'outline-warning' : 'outline-success'; ?>"
                                                                                    onclick="toggleProductStatus(<?php echo $product['id']; ?>, <?php echo $selectedBusinessId; ?>, <?php echo ($product['status'] ?? '1') == '1' ? 0 : 1; ?>)">
                                                                                    <?php echo ($product['status'] ?? '1') == '1' ? 'Funga' : 'Fungua'; ?>
                                                                                </button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Sales Tab -->
                                    <div class="tab-pane fade <?php echo $activeTab === 'sales' ? 'show active' : ''; ?>"
                                        id="sales" role="tabpanel">
                                        <?php if (empty($businessSales)): ?>
                                            <div class="empty-state">
                                                <h5>Hakuna mauzo bado</h5>
                                                <p>Mauzo yako yataonekana hapa</p>
                                            </div>
                                        <?php else: ?>
                                            <div class="row mb-3">
                                                <div class="col-md-4">
                                                    <div class="card">
                                                        <div class="card-body text-center">
                                                            <h5 class="mb-1"><?php echo $totalSales; ?></h5>
                                                            <p class="mb-0">Jumla ya Mauzo</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="card">
                                                        <div class="card-body text-center">
                                                            <h5 class="mb-1">TSh <?php echo number_format($totalRevenue); ?></h5>
                                                            <p class="mb-0">Jumla ya Mapato</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="card">
                                                        <div class="card-body text-center">
                                                            <h5 class="mb-1"><?php echo $completedSales; ?></h5>
                                                            <p class="mb-0">Yaliyokamilika</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="sales-list">
                                                <?php foreach ($businessSales as $sale): ?>
                                                    <div class="sale-item <?php echo $sale['status'] == '1' ? 'completed' : 'pending'; ?>">
                                                        <div class="d-flex justify-content-between align-items-start">
                                                            <div class="flex-grow-1">
                                                                <h6 class="mb-1"><?php echo htmlspecialchars($sale['product_name']); ?></h6>
                                                                <div class="d-flex align-items-center">
                                                                    <span class="me-3">
                                                                        <strong>Idadi:</strong> <?php echo $sale['quantity'] ?? 0; ?>
                                                                    </span>
                                                                    <span class="me-3">
                                                                        <strong>Bei:</strong> TSh <?php echo number_format($sale['amount'] ?? 0); ?>
                                                                    </span>
                                                                    <span class="status-badge status-<?php echo ($sale['status'] ?? '0') == '1' ? 'completed' : 'pending-sale'; ?>">
                                                                        <?php echo ($sale['status'] ?? '0') == '1' ? 'Imekamilika' : 'Inasubiri'; ?>
                                                                    </span>
                                                                </div>
                                                                <small class="text-muted">
                                                                    Tarehe: <?php echo date('d/m/Y H:i', strtotime($sale['date'] ?? 'now')); ?>
                                                                </small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>

    <script>
        function changeBusiness(businessId) {
            const currentUrl = new URL(window.location);
            currentUrl.searchParams.set('business_id', businessId);
            currentUrl.searchParams.set('tab', 'overview');
            window.location.href = currentUrl.toString();
        }

        // Toggle product status
        function toggleProductStatus(productId, businessId, newStatus) {
            const statusText = newStatus == 1 ? 'Iko Soko' : 'Haiko Soko';
            const buttonText = newStatus == 1 ? 'Funga' : 'Fungua';

            if (confirm(`Una uhakika unataka kubadilisha hali ya bidhaa kuwa "${statusText}"?`)) {
                fetch('<?= app_url("user/toggle-product-status.php") ?>', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `product_id=${productId}&business_id=${businessId}&status=${newStatus}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Reload the page to show updated status
                            window.location.reload();
                        } else {
                            alert('Kuna tatizo la kiufundi. Jaribu tena.');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Kuna tatizo la kiufundi. Jaribu tena.');
                    });
            }
        }

        // Set active tab based on URL parameter
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const tab = urlParams.get('tab');
            if (tab) {
                const tabElement = document.querySelector(`#${tab}-tab`);
                if (tabElement) {
                    tabElement.click();
                }
            }
        });
    </script>

    <!-- Profile Completion Modal -->
    <?php include __DIR__ . '/../includes/profile-completion-modal.php'; ?>

</body>

</html>