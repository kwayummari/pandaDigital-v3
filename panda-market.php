<?php
require_once 'config/init.php';
require_once 'config/database.php';

// Initialize database connection
$database = new Database();
$conn = $database->getConnection();

// Get categories for the banner
$categoriesQuery = "SELECT * FROM categories ORDER BY id";
$categoriesStmt = $conn->prepare($categoriesQuery);
$categoriesStmt->execute();
$categories = $categoriesStmt->fetchAll();

// Get top rated products (most sold)
$topRatedQuery = "
    SELECT p.*, IFNULL(AVG(r.rating), 0) AS avg_rating, COUNT(s.productId) AS sold_count
    FROM products p
    LEFT JOIN sales s ON p.id = s.productId
    LEFT JOIN ratings r ON p.id = r.productId
    WHERE p.status = 1
    GROUP BY p.id
    ORDER BY sold_count DESC
    LIMIT 12
";
$topRatedStmt = $conn->prepare($topRatedQuery);
$topRatedStmt->execute();
$topRatedProducts = $topRatedStmt->fetchAll();

// Get most sold products
$mostSoldQuery = "
    SELECT p.*, IFNULL(AVG(r.rating), 0) AS avg_rating, COUNT(s.productId) AS sold_count
    FROM products p
    LEFT JOIN sales s ON p.id = s.productId
    LEFT JOIN ratings r ON p.id = r.productId
    WHERE p.status = 1
    GROUP BY p.id
    ORDER BY sold_count DESC
    LIMIT 12
";
$mostSoldStmt = $conn->prepare($mostSoldQuery);
$mostSoldStmt->execute();
$mostSoldProducts = $mostSoldStmt->fetchAll();

// Get products by categories
$categoryProductsQuery = "SELECT * FROM categories ORDER BY id ASC LIMIT 3";
$categoryProductsStmt = $conn->prepare($categoryProductsQuery);
$categoryProductsStmt->execute();
$categoryProducts = $categoryProductsStmt->fetchAll();

$allProducts = [];
foreach ($categoryProducts as $category) {
    $categoryId = $category['id'];

    $productsQuery = "
        SELECT p.*, IFNULL(AVG(r.rating), 0) AS avg_rating
        FROM products p
        LEFT JOIN ratings r ON p.id = r.productId
        WHERE p.categoryId = ? AND p.status = '1'
        GROUP BY p.id
        ORDER BY p.id DESC
        LIMIT 12
    ";

    $productsStmt = $conn->prepare($productsQuery);
    $productsStmt->execute([$categoryId]);
    $products = $productsStmt->fetchAll();

    $allProducts[] = [
        'category' => $category,
        'products' => $products
    ];
}

$pageTitle = 'Panda Market - ' . env('APP_NAME', 'Panda Digital');

include 'includes/header.php';
?>

<!-- Page Header -->
<section class="page-header" style="background-image: url('<?= asset('images/banner/market-banner.jpg') ?>'); background-size: cover; background-position: center; background-repeat: no-repeat; position: relative;">
    <div class="overlay" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(0, 0, 0, 0.6);"></div>
    <div class="container" style="position: relative; z-index: 2;">
        <div class="row align-items-center">
            <div class="col-lg-12" data-aos="fade-right">
                <h1 class="page-title" style="color: #fff;">Panda Market</h1>
                <p class="page-subtitle">Gundua bidhaa bora na bei nafuu kutoka kwa wauzaji wetu wa kujiamini</p>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= app_url() ?>">Nyumbani</a></li>
                        <li class="breadcrumb-item active">Panda Market</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</section>

<!-- Categories Banner -->
<section class="categories-banner py-5 bg-light">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="d-flex flex-row overflow-auto">
                    <?php foreach ($categories as $category): ?>
                        <a href="market/productsByCategories.php?categoryId=<?php echo $category['id']; ?>" class="text-decoration-none">
                            <div class="align-items-center text-center p-3 me-3 border rounded shadow-sm bg-white" style="min-width: 150px;">
                                <div class="thumb mb-2">
                                    <img src="market/assets/images/<?php echo htmlspecialchars($category['image']); ?>" alt="<?php echo htmlspecialchars($category['name']); ?>"
                                        style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;">
                                </div>
                                <h6 style="font-weight: 600; margin: 0; color: #333; font-size: 14px;"><?php echo htmlspecialchars($category['name']); ?></h6>
                                <div class="mt-2">
                                    <small class="text-primary">Gundua Zaidi</small>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Top Rated Products -->
<section class="top-rated-products py-5">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-6">
                <div class="section-heading">
                    <h2 class="section-title">Bidhaa Zinazouzwa Zaidi</h2>
                    <p class="section-subtitle">Gundua bidhaa ambazo zimeuzwa zaidi.</p>
                </div>
            </div>
        </div>
        <div class="row">
            <?php if (!empty($topRatedProducts)): ?>
                <?php foreach ($topRatedProducts as $product): ?>
                    <div class="col-6 col-md-2 mb-4">
                        <div class="card h-100 product-card" style="border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); transition: all 0.3s ease;">
                            <div class="thumb position-relative">
                                <?php if ($product['isOffered'] == 1): ?>
                                    <div class="offer-badge">
                                        <?php echo htmlspecialchars($product['offer']) . '% OFF'; ?>
                                    </div>
                                <?php endif; ?>
                                <a href="market/single-product.php?id=<?php echo $product['id']; ?>">
                                    <img src="market/assets/images/<?php echo htmlspecialchars($product['image']); ?>"
                                        alt="<?php echo htmlspecialchars($product['name']); ?>"
                                        class="img-fluid" style="width: 100%; height: 200px; object-fit: cover;">
                                </a>
                            </div>
                            <div class="card-body p-3">
                                <a href="market/single-product.php?id=<?php echo $product['id']; ?>" class="text-decoration-none">
                                    <h6 class="product-name mb-2" style="color: #333; font-size: 14px; line-height: 1.3;">
                                        <?php echo htmlspecialchars($product['name']); ?>
                                    </h6>
                                </a>
                                <div class="rating mb-2">
                                    <?php
                                    $avgRating = $product['avg_rating'];
                                    for ($i = 0; $i < 5; $i++):
                                        $starColor = ($i < round($avgRating)) ? '#FFD700' : '#D3D3D3';
                                    ?>
                                        <i class="fas fa-star" style="color: <?php echo $starColor; ?>; font-size: 12px;"></i>
                                    <?php endfor; ?>
                                </div>
                                <?php if ($product['isOffered'] == 1): ?>
                                    <?php
                                    $originalPrice = $product['amount'];
                                    $discount = $product['offer'];
                                    $discountedPrice = $originalPrice - ($originalPrice * ($discount / 100));
                                    ?>
                                    <div class="price mb-2">
                                        <del class="text-muted" style="font-size: 12px;">Tsh.<?php echo number_format($originalPrice, 0); ?></del>
                                        <div class="text-primary fw-bold">Tsh.<?php echo number_format($discountedPrice, 0); ?></div>
                                    </div>
                                <?php else: ?>
                                    <div class="price mb-2">
                                        <span class="text-primary fw-bold">Tsh.<?php echo number_format($product['amount'], 0); ?></span>
                                    </div>
                                <?php endif; ?>
                                <div class="product-actions">
                                    <div class="d-flex gap-2">
                                        <a href="market/single-product.php?id=<?php echo $product['id']; ?>"
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
                    <p class="text-muted">Hakuna bidhaa zilizouzwa zilizopatikana.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Most Sold Products -->
<section class="most-sold-products py-5 bg-light">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-6">
                <div class="section-heading">
                    <h2 class="section-title">Bidhaa Zinazouzwa Zaidi</h2>
                    <p class="section-subtitle">Gundua bidhaa ambazo zimeuzwa zaidi.</p>
                </div>
            </div>
        </div>
        <div class="row">
            <?php if (!empty($mostSoldProducts)): ?>
                <?php foreach ($mostSoldProducts as $product): ?>
                    <div class="col-6 col-md-2 mb-4">
                        <div class="card h-100 product-card" style="border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); transition: all 0.3s ease;">
                            <div class="thumb position-relative">
                                <?php if ($product['isOffered'] == 1): ?>
                                    <div class="offer-badge">
                                        <?php echo htmlspecialchars($product['offer']) . '% OFF'; ?>
                                    </div>
                                <?php endif; ?>
                                <a href="market/single-product.php?id=<?php echo $product['id']; ?>">
                                    <img src="market/assets/images/<?php echo htmlspecialchars($product['image']); ?>"
                                        alt="<?php echo htmlspecialchars($product['name']); ?>"
                                        class="img-fluid" style="width: 100%; height: 200px; object-fit: cover;">
                                </a>
                            </div>
                            <div class="card-body p-3">
                                <a href="market/single-product.php?id=<?php echo $product['id']; ?>" class="text-decoration-none">
                                    <h6 class="product-name mb-2" style="color: #333; font-size: 14px; line-height: 1.3;">
                                        <?php echo htmlspecialchars($product['name']); ?>
                                    </h6>
                                </a>
                                <div class="rating mb-2">
                                    <?php
                                    $avgRating = $product['avg_rating'];
                                    for ($i = 0; $i < 5; $i++):
                                        $starColor = ($i < round($avgRating)) ? '#FFD700' : '#D3D3D3';
                                    ?>
                                        <i class="fas fa-star" style="color: <?php echo $starColor; ?>; font-size: 12px;"></i>
                                    <?php endfor; ?>
                                </div>
                                <?php if ($product['isOffered'] == 1): ?>
                                    <?php
                                    $originalPrice = $product['amount'];
                                    $discount = $product['offer'];
                                    $discountedPrice = $originalPrice - ($originalPrice * ($discount / 100));
                                    ?>
                                    <div class="price mb-2">
                                        <del class="text-muted" style="font-size: 12px;">Tsh.<?php echo number_format($originalPrice, 0); ?></del>
                                        <div class="text-primary fw-bold">Tsh.<?php echo number_format($discountedPrice, 0); ?></div>
                                    </div>
                                <?php else: ?>
                                    <div class="price mb-2">
                                        <span class="text-primary fw-bold">Tsh.<?php echo number_format($product['amount'], 0); ?></span>
                                    </div>
                                <?php endif; ?>
                                <div class="product-actions">
                                    <div class="d-flex gap-2">
                                        <a href="market/single-product.php?id=<?php echo $product['id']; ?>"
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
                    <p class="text-muted">Hakuna bidhaa zilizouzwa zilizopatikana.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Products by Categories -->
<?php foreach ($allProducts as $categoryProducts): ?>
    <section class="products-by-category py-5">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-6">
                    <div class="section-heading">
                        <h2 class="section-title"><?php echo htmlspecialchars($categoryProducts['category']['name']); ?> Karibuni</h2>
                        <p class="section-subtitle"><?php echo $categoryProducts['category']['description']; ?></p>
                    </div>
                </div>
            </div>
            <div class="row">
                <?php if (!empty($categoryProducts['products'])): ?>
                    <?php foreach ($categoryProducts['products'] as $product): ?>
                        <div class="col-6 col-md-2 mb-4">
                            <div class="card h-100 product-card" style="border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); transition: all 0.3s ease;">
                                <div class="thumb position-relative">
                                    <?php if ($product['isOffered'] == 1): ?>
                                        <div class="offer-badge">
                                            <?php echo htmlspecialchars($product['offer']) . '% OFF'; ?>
                                        </div>
                                    <?php endif; ?>
                                    <a href="market/single-product.php?id=<?php echo $product['id']; ?>">
                                        <img src="market/assets/images/<?php echo htmlspecialchars($product['image']); ?>"
                                            alt="<?php echo htmlspecialchars($product['name']); ?>"
                                            class="img-fluid" style="width: 100%; height: 200px; object-fit: cover;">
                                    </a>
                                </div>
                                <div class="card-body p-3">
                                    <a href="market/single-product.php?id=<?php echo $product['id']; ?>" class="text-decoration-none">
                                        <h6 class="product-name mb-2" style="color: #333; font-size: 14px; line-height: 1.3;">
                                            <?php echo htmlspecialchars($product['name']); ?>
                                        </h6>
                                    </a>
                                    <div class="rating mb-2">
                                        <?php
                                        $avgRating = $product['avg_rating'];
                                        for ($i = 0; $i < 5; $i++):
                                            $starColor = ($i < round($avgRating)) ? '#FFD700' : '#D3D3D3';
                                        ?>
                                            <i class="fas fa-star" style="color: <?php echo $starColor; ?>; font-size: 12px;"></i>
                                        <?php endfor; ?>
                                    </div>
                                    <?php if ($product['isOffered'] == 1): ?>
                                        <?php
                                        $originalPrice = $product['amount'];
                                        $discount = $product['offer'];
                                        $discountedPrice = $originalPrice - ($originalPrice * ($discount / 100));
                                        ?>
                                        <div class="price mb-2">
                                            <del class="text-muted" style="font-size: 12px;">Tsh.<?php echo number_format($originalPrice, 0); ?></del>
                                            <div class="text-primary fw-bold">Tsh.<?php echo number_format($discountedPrice, 0); ?></div>
                                        </div>
                                    <?php else: ?>
                                        <div class="price mb-2">
                                            <span class="text-primary fw-bold">Tsh.<?php echo number_format($product['amount'], 0); ?></span>
                                        </div>
                                    <?php endif; ?>
                                    <div class="product-actions">
                                        <div class="d-flex gap-2">
                                            <a href="market/single-product.php?id=<?php echo $product['id']; ?>"
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
                        <p class="text-muted">Hakuna bidhaa zilizopatikana kwa kategoria hii.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
<?php endforeach; ?>

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

<?php include 'includes/footer.php'; ?>

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

    .section-heading {
        margin-bottom: 2rem;
    }

    .section-title {
        color: #333;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    .section-subtitle {
        color: #6c757d;
        margin-bottom: 0;
    }

    .categories-banner {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    }

    .categories-banner .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .overflow-auto::-webkit-scrollbar {
        height: 6px;
    }

    .overflow-auto::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 3px;
    }

    .overflow-auto::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 3px;
    }

    .overflow-auto::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
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
                fetch('market/rate_product.php', {
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