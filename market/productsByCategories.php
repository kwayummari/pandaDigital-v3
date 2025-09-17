<?php
require_once '../config/init.php';
require_once '../config/database.php';

// Initialize database connection
$database = Database::getInstance();
$conn = $database->getConnection();

// Get category ID from URL
$categoryId = $_GET['categoryId'] ?? null;

if (!$categoryId) {
    header('Location: ../panda-market.php');
    exit();
}

// Get category information
$categoryQuery = "SELECT * FROM categories WHERE id = ?";
$categoryStmt = $conn->prepare($categoryQuery);
$categoryStmt->execute([$categoryId]);
$category = $categoryStmt->fetch();

if (!$category) {
    header('Location: ../panda-market.php');
    exit();
}

// Get products for this category
$productsQuery = "
    SELECT p.*, IFNULL(AVG(r.rating), 0) AS avg_rating, COUNT(s.productId) AS sold_count
    FROM products p
    LEFT JOIN ratings r ON p.id = r.productId
    LEFT JOIN sales s ON p.id = s.productId
    WHERE p.categoryId = ? AND p.status = '1'
    GROUP BY p.id
    ORDER BY p.id DESC
";
$productsStmt = $conn->prepare($productsQuery);
$productsStmt->execute([$categoryId]);
$products = $productsStmt->fetchAll();

$pageTitle = $category['name'] . ' - Panda Market';

include '../includes/header.php';
?>

<!-- Page Header -->
<section class="page-header" style="background-image: url('<?= asset('images/banner/new-banner2.jpg') ?>'); background-size: cover; background-position: center; background-repeat: no-repeat; position: relative; min-height: 400px; padding: 120px 0;">
    <div class="overlay" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(0, 0, 0, 0.6);"></div>
    <div class="container" style="position: relative; z-index: 2;">
        <div class="row align-items-center">
            <div class="col-lg-12" data-aos="fade-right">
                <h1 class="page-title" style="color: #fff;"><?php echo htmlspecialchars($category['name']); ?></h1>
                <p class="page-subtitle"><?php echo htmlspecialchars($category['description'] ?? 'Gundua bidhaa bora za ' . $category['name']); ?></p>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= app_url() ?>">Nyumbani</a></li>
                        <li class="breadcrumb-item"><a href="<?= app_url('panda-market.php') ?>">Panda Market</a></li>
                        <li class="breadcrumb-item active"><?php echo htmlspecialchars($category['name']); ?></li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</section>

<!-- Category Products -->
<section class="category-products py-5">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="section-title"><?php echo htmlspecialchars($category['name']); ?> (<?php echo count($products); ?> bidhaa)</h2>
                    <a href="<?= app_url('panda-market.php') ?>" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left me-2"></i>Rudi kwenye Market
                    </a>
                </div>
            </div>
        </div>

        <div class="row">
            <?php if (!empty($products)): ?>
                <?php foreach ($products as $product): ?>
                    <div class="col-6 col-md-3 col-lg-2 mb-4">
                        <div class="card h-100 product-card" style="border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); transition: all 0.3s ease;">
                            <div class="thumb position-relative">
                                <?php if ($product['isOffered'] == 1): ?>
                                    <div class="offer-badge">
                                        <?php echo htmlspecialchars($product['offer']) . '% OFF'; ?>
                                    </div>
                                <?php endif; ?>
                                <a href="single-product.php?id=<?php echo $product['id']; ?>">
                                    <img src="assets/images/<?php echo htmlspecialchars($product['image']); ?>"
                                        alt="<?php echo htmlspecialchars($product['name']); ?>"
                                        class="img-fluid" style="width: 100%; height: 200px; object-fit: cover;">
                                </a>
                            </div>
                            <div class="card-body p-3">
                                <a href="single-product.php?id=<?php echo $product['id']; ?>" class="text-decoration-none">
                                    <h6 class="product-name mb-2" style="color: #333; font-size: 14px; line-height: 1.3;">
                                        <?php echo htmlspecialchars($product['name']); ?>
                                    </h6>
                                </a>
                                <div class="rating mb-2">
                                    <?php
                                    $avgRating = (float)$product['avg_rating'];
                                    for ($i = 0; $i < 5; $i++):
                                        $starColor = ($i < round($avgRating)) ? '#FFD700' : '#D3D3D3';
                                    ?>
                                        <i class="fas fa-star" style="color: <?php echo $starColor; ?>; font-size: 12px;"></i>
                                    <?php endfor; ?>
                                </div>
                                <?php if ($product['isOffered'] == 1): ?>
                                    <?php
                                    $originalPrice = (float)$product['amount'];
                                    $discount = (float)$product['offer'];
                                    $discountedPrice = $originalPrice - ($originalPrice * ($discount / 100));

                                    // Debug logging
                                    error_log("Product ID: " . $product['id'] . ", Amount: " . $product['amount'] . ", Original Price: " . $originalPrice . ", Discount: " . $discount . ", Discounted Price: " . $discountedPrice);
                                    ?>
                                    <span class="price">
                                        <?php
                                        if (is_numeric($originalPrice) && is_numeric($discountedPrice)):
                                        ?>
                                            <del>Tsh.<?php echo number_format($originalPrice, 2); ?>/=</del>
                                            <br>
                                            Tsh.<?php echo number_format($discountedPrice, 2); ?>/=
                                        <?php else: ?>
                                            <span class="text-danger">Bei haipatikani</span>
                                        <?php endif; ?>
                                    </span>
                                <?php else: ?>
                                    <span class="price">Tsh.<?php echo $product['amount']; ?>/=</span>
                                <?php endif; ?>
                                <div class="product-actions">
                                    <div class="d-flex gap-2">
                                        <a href="single-product.php?id=<?php echo $product['id']; ?>"
                                            class="btn btn-outline-primary btn-sm flex-fill">
                                            <i class="fas fa-eye me-1"></i>Tazama
                                        </a>
                                        <button class="btn btn-outline-warning btn-sm"
                                            onclick="rateProduct(<?php echo $product['id']; ?>)">
                                            <i class="fas fa-star"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Hakuna bidhaa zilizopatikana kwa kategoria hii kwa sasa.
                    </div>
                    <a href="<?= app_url('panda-market.php') ?>" class="btn btn-primary">
                        <i class="fas fa-arrow-left me-2"></i>Rudi kwenye Market
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Rating Modal -->
<div class="modal fade" id="ratingModal" tabindex="-1" role="dialog" aria-labelledby="ratingModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ratingModalLabel">Kadiria bidhaa hii</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <div class="stars mb-3" id="modalStars">
                        <?php for ($i = 1; $i <= 5; $i++) : ?>
                            <i class="fas fa-star star" style="font-size: 40px; color: #D3D3D3; cursor: pointer; margin: 0 10px;"
                                data-rating="<?php echo $i; ?>" onclick="selectStar(<?php echo $i; ?>)"></i>
                        <?php endfor; ?>
                    </div>
                    <p class="text-muted">Chagua ukadiriaji wako</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Funga</button>
                <button type="button" class="btn btn-primary" id="submitRating">Wasilisha Ukadiriaji</button>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

<style>
    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }

    .offer-badge {
        position: absolute;
        top: 10px;
        right: 10px;
        background: #dc3545;
        color: white;
        padding: 4px 8px;
        border-radius: 20px;
        font-weight: bold;
        font-size: 12px;
    }

    .star.filled {
        color: #FFD700 !important;
    }

    .section-title {
        color: #333;
        font-weight: 600;
        margin-bottom: 0;
    }

    .breadcrumb-item a {
        color: #fff;
        text-decoration: none;
    }

    .price del {
        color: red;
    }

    .breadcrumb-item.active {
        color: #ffc107;
    }
</style>

<script>
    let selectedRating = 0;
    let selectedProductId = 0;

    function rateProduct(productId) {
        selectedProductId = productId;
        selectedRating = 0;

        // Reset stars
        document.querySelectorAll('.star').forEach(star => {
            star.classList.remove('filled');
            star.style.color = '#D3D3D3';
        });

        // Show modal
        const ratingModal = new bootstrap.Modal(document.getElementById('ratingModal'));
        ratingModal.show();
    }

    function selectStar(rating) {
        selectedRating = rating;

        // Update star colors
        document.querySelectorAll('.star').forEach((star, index) => {
            if (index < rating) {
                star.classList.add('filled');
                star.style.color = '#FFD700';
            } else {
                star.classList.remove('filled');
                star.style.color = '#D3D3D3';
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Handle rating submission
        document.getElementById('submitRating').addEventListener('click', function() {
            if (selectedRating > 0 && selectedProductId > 0) {
                // Submit rating via AJAX
                fetch('rate_product.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'productId=' + selectedProductId + '&rating=' + selectedRating
                    })
                    .then(response => response.text())
                    .then(data => {
                        alert("Asante kwa ukadiriaji wako!");
                        location.reload();
                    })
                    .catch(error => {
                        alert("Kosa katika kutuma ukadiriaji wako. Tafadhali jaribu tena.");
                    });
            } else {
                alert("Tafadhali chagua ukadiriaji.");
            }
        });
    });
</script>