<?php
require_once '../../config/init.php';
require_once '../../config/database.php';

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in and is a seller
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in'] || !$_SESSION['is_seller']) {
    header('Location: ../../auth/login.php');
    exit;
}

// Initialize database
$database = new Database();
$conn = $database->getConnection();

$userId = $_SESSION['user_id'];
$pageTitle = "Dhibiti Bidhaa - Panda Market";

include '../../includes/header.php';

// Get seller's products
$productsQuery = "SELECT p.*, c.name as category_name 
                  FROM products p 
                  LEFT JOIN categories c ON p.categoryId = c.id 
                  WHERE p.sellerId = ? 
                  ORDER BY p.dateCreated DESC";
$productsStmt = $conn->prepare($productsQuery);
$productsStmt->execute([$userId]);
$products = $productsStmt->fetchAll();

// Get categories for product creation
$categoriesQuery = "SELECT id, name FROM categories WHERE status = '1' ORDER BY name";
$categoriesStmt = $conn->prepare($categoriesQuery);
$categoriesStmt->execute();
$categories = $categoriesStmt->fetchAll();
?>

<!-- Page Header -->
<section class="page-header" style="background-image: url('<?= asset('images/banner/market-banner.jpg') ?>'); background-size: cover; background-position: center; background-repeat: no-repeat; position: relative;">
    <div class="overlay" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(0, 0, 0, 0.6);"></div>
    <div class="container" style="position: relative; z-index: 2;">
        <div class="row align-items-center">
            <div class="col-lg-12" data-aos="fade-right">
                <h1 class="page-title" style="color: #fff;">Dhibiti Bidhaa</h1>
                <p class="page-subtitle">Ongeza, hariri, na dhibiti bidhaa zako</p>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= app_url() ?>">Nyumbani</a></li>
                        <li class="breadcrumb-item"><a href="<?= app_url('panda-market.php') ?>">Panda Market</a></li>
                        <li class="breadcrumb-item active">Dhibiti Bidhaa</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</section>

<!-- Product Management Content -->
<section class="product-management-content py-5">
    <div class="container">
        <!-- Action Buttons -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <h3>Bidhaa Zangu</h3>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">
                        <i class="fas fa-plus me-2"></i>Ongeza Bidhaa Mpya
                    </button>
                </div>
            </div>
        </div>

        <!-- Products Grid -->
        <div class="row">
            <?php if (empty($products)): ?>
                <div class="col-12">
                    <div class="card text-center py-5">
                        <i class="fas fa-box fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Huna bidhaa bado</h5>
                        <p class="text-muted">Anza kuuza bidhaa zako kwenye Panda Market</p>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">
                            <i class="fas fa-plus me-2"></i>Ongeza Bidhaa ya Kwanza
                        </button>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($products as $product): ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100 product-card">
                            <div class="product-image-container">
                                <img src="../../assets/images/<?= htmlspecialchars($product['image']) ?>"
                                    alt="<?= htmlspecialchars($product['name']) ?>"
                                    class="card-img-top" style="height: 200px; object-fit: cover;">

                                <!-- Product Status Badge -->
                                <div class="product-status-badge">
                                    <?php if ($product['status'] == '1'): ?>
                                        <span class="badge bg-success">Inapatikana</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Haipatikani</span>
                                    <?php endif; ?>
                                </div>

                                <!-- Offer Badge -->
                                <?php if ($product['isOffered'] == 1): ?>
                                    <div class="offer-badge">
                                        <span class="badge bg-danger">-<?= $product['offer'] ?>%</span>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="card-body d-flex flex-column">
                                <h6 class="card-title"><?= htmlspecialchars($product['name']) ?></h6>
                                <p class="text-muted small mb-2"><?= htmlspecialchars($product['category_name']) ?></p>

                                <div class="price-info mb-3">
                                    <?php if ($product['isOffered'] == 1): ?>
                                        <?php
                                        $originalPrice = $product['amount'];
                                        $discount = $product['offer'];
                                        $discountedPrice = $originalPrice - ($originalPrice * ($discount / 100));
                                        ?>
                                        <span class="text-decoration-line-through text-muted">Tsh.<?= number_format($originalPrice, 0) ?></span>
                                        <span class="text-primary fw-bold">Tsh.<?= number_format($discountedPrice, 0) ?></span>
                                    <?php else: ?>
                                        <span class="text-primary fw-bold">Tsh.<?= number_format($product['amount'], 0) ?></span>
                                    <?php endif; ?>
                                </div>

                                <div class="product-stats mb-3">
                                    <div class="row text-center">
                                        <div class="col-4">
                                            <small class="text-muted">Stock</small>
                                            <div class="fw-bold"><?= $product['stock'] ?? 'N/A' ?></div>
                                        </div>
                                        <div class="col-4">
                                            <small class="text-muted">Views</small>
                                            <div class="fw-bold"><?= $product['views'] ?? '0' ?></div>
                                        </div>
                                        <div class="col-4">
                                            <small class="text-muted">Sales</small>
                                            <div class="fw-bold"><?= $product['sales'] ?? '0' ?></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-auto">
                                    <div class="btn-group w-100" role="group">
                                        <button class="btn btn-outline-primary btn-sm" onclick="editProduct(<?= $product['id'] ?>)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-outline-info btn-sm" onclick="viewProduct(<?= $product['id'] ?>)">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-outline-warning btn-sm" onclick="toggleProductStatus(<?= $product['id'] ?>, '<?= $product['status'] ?>')">
                                            <i class="fas fa-toggle-<?= $product['status'] == '1' ? 'on' : 'off' ?>"></i>
                                        </button>
                                        <button class="btn btn-outline-danger btn-sm" onclick="deleteProduct(<?= $product['id'] ?>)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Add Product Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addProductModalLabel">
                    <i class="fas fa-plus me-2"></i>Ongeza Bidhaa Mpya
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addProductForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="product_name" class="form-label">Jina la Bidhaa *</label>
                            <input type="text" class="form-control" id="product_name" name="product_name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="product_category" class="form-label">Kategoria *</label>
                            <select class="form-select" id="product_category" name="product_category" required>
                                <option value="">Chagua kategoria</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= $category['id'] ?>"><?= htmlspecialchars($category['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="product_price" class="form-label">Bei (TSh) *</label>
                            <input type="number" class="form-control" id="product_price" name="product_price" min="0" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="product_stock" class="form-label">Stock *</label>
                            <input type="number" class="form-control" id="product_stock" name="product_stock" min="0" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="product_image" class="form-label">Picha ya Bidhaa *</label>
                            <input type="file" class="form-control" id="product_image" name="product_image" accept="image/*" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="product_status" class="form-label">Status</label>
                            <select class="form-select" id="product_status" name="product_status">
                                <option value="1">Inapatikana</option>
                                <option value="0">Haipatikani</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="product_description" class="form-label">Maelezo ya Bidhaa *</label>
                        <textarea class="form-control" id="product_description" name="product_description" rows="4" required></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="product_offered" name="product_offered">
                                <label class="form-check-label" for="product_offered">
                                    Bidhaa ina punguzo
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="product_discount" class="form-label">Punguzo (%)</label>
                            <input type="number" class="form-control" id="product_discount" name="product_discount" min="0" max="100" disabled>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Funga</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Hifadhi Bidhaa
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>

<style>
    .product-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }

    .product-image-container {
        position: relative;
    }

    .product-status-badge {
        position: absolute;
        top: 10px;
        left: 10px;
    }

    .offer-badge {
        position: absolute;
        top: 10px;
        right: 10px;
    }

    .breadcrumb-item a {
        color: #fff;
        text-decoration: none;
    }

    .breadcrumb-item.active {
        color: #ffc107;
    }

    .card {
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .btn-group .btn {
        border-radius: 0;
    }

    .btn-group .btn:first-child {
        border-top-left-radius: 0.375rem;
        border-bottom-left-radius: 0.375rem;
    }

    .btn-group .btn:last-child {
        border-top-right-radius: 0.375rem;
        border-bottom-right-radius: 0.375rem;
    }
</style>

<script>
    // Toggle product discount field
    document.getElementById('product_offered').addEventListener('change', function() {
        const discountField = document.getElementById('product_discount');
        discountField.disabled = !this.checked;
        if (!this.checked) {
            discountField.value = '';
        }
    });

    // Add product form submission
    document.getElementById('addProductForm').addEventListener('submit', function(e) {
        e.preventDefault();

        // Get form data
        const formData = new FormData(this);

        // Show loading state
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Inahifadhi...';
        submitBtn.disabled = true;

        // Submit form data
        fetch('add_product.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Bidhaa imeongezwa kwa mafanikio!');
                    location.reload();
                } else {
                    alert('Kosa: ' + data.message);
                }
            })
            .catch(error => {
                alert('Kosa la mfumo. Tafadhali jaribu tena.');
                console.error('Error:', error);
            })
            .finally(() => {
                // Reset button state
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
    });

    // Product management functions
    function editProduct(productId) {
        // Implement edit product functionality
        alert('Edit product ' + productId + ' - To be implemented');
    }

    function viewProduct(productId) {
        // Open product in new tab
        window.open('../single-product.php?id=' + productId, '_blank');
    }

    function toggleProductStatus(productId, currentStatus) {
        const newStatus = currentStatus == '1' ? '0' : '1';
        const statusText = newStatus == '1' ? 'Inapatikana' : 'Haipatikani';

        if (confirm('Je, una uhakika unataka kubadilisha status ya bidhaa kuwa "' + statusText + '"?')) {
            // Implement status toggle functionality
            alert('Toggle product status ' + productId + ' to ' + newStatus + ' - To be implemented');
        }
    }

    function deleteProduct(productId) {
        if (confirm('Je, una uhakika unataka kufuta bidhaa hii? Kitendo hiki hakiwezi kurekebishwa.')) {
            // Implement delete product functionality
            alert('Delete product ' + productId + ' - To be implemented');
        }
    }
</script>