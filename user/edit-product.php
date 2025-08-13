<?php
require_once __DIR__ . "/../config/init.php";
require_once __DIR__ . "/../middleware/AuthMiddleware.php";
require_once __DIR__ . "/../models/Business.php";

// Ensure user is logged in
$auth = new AuthMiddleware();
$auth->requireRole('user');

$currentUser = $auth->getCurrentUser();
$businessId = $_GET['business_id'] ?? null;
$productId = $_GET['product_id'] ?? null;

if (!$businessId || !$productId) {
    header('Location: ' . app_url('user/business.php'));
    exit;
}

// Initialize business model
$businessModel = new Business();

// Validate business ownership
$userBusinesses = $businessModel->getBusinessesByUserId($currentUser['id']);
$businessExists = false;

foreach ($userBusinesses as $business) {
    if ($business['id'] == $businessId) {
        $businessExists = true;
        break;
    }
}

if (!$businessExists) {
    header('Location: ' . app_url('user/business.php'));
    exit;
}

// Get product details
$product = $businessModel->getProductById($productId);

if (!$product || $product['sellerId'] != $businessId) {
    header('Location: ' . app_url('user/business.php?business_id=' . $businessId));
    exit;
}

$message = '';
$messageType = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productName = trim($_POST['product_name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = trim($_POST['price'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $status = trim($_POST['status'] ?? '1');
    $isOffered = isset($_POST['isOffered']) ? '1' : '0';
    $offer = trim($_POST['offer'] ?? '');

    // Validation
    if (empty($productName)) {
        $message = 'Jina la bidhaa ni lazima';
        $messageType = 'danger';
    } elseif (empty($description)) {
        $message = 'Maelezo ya bidhaa ni lazima';
        $messageType = 'danger';
    } elseif (empty($price) || !is_numeric($price)) {
        $message = 'Bei ya bidhaa ni lazima na iwe namba';
        $messageType = 'danger';
    } else {
        // Handle image upload
        $imagePath = $product['image']; // Keep existing image by default
        if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $fileExtension = strtolower(pathinfo($_FILES['product_image']['name'], PATHINFO_EXTENSION));
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

            if (in_array($fileExtension, $allowedExtensions)) {
                $fileName = uniqid() . '_' . time() . '.' . $fileExtension;
                $uploadPath = $uploadDir . $fileName;

                if (move_uploaded_file($_FILES['product_image']['tmp_name'], $uploadPath)) {
                    $imagePath = $fileName;

                    // Delete old image if it exists and is different
                    if ($product['image'] && $product['image'] != $fileName) {
                        $oldImagePath = __DIR__ . '/../uploads/' . $product['image'];
                        if (file_exists($oldImagePath)) {
                            unlink($oldImagePath);
                        }
                    }
                }
            }
        }

        if ($businessModel->updateProduct($productId, $productName, $description, $price, $category ?: null, $imagePath, $status, $isOffered, $offer)) {
            $message = 'Bidhaa yako imesasishwa kwa mafanikio!';
            $messageType = 'success';

            // Refresh product data
            $product = $businessModel->getProductById($productId);
        } else {
            $message = 'Kuna tatizo la kiufundi. Jaribu tena.';
            $messageType = 'danger';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="sw">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hariri Bidhaa - Panda Digital</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= app_url('assets/css/style.css') ?>?v=7">
    <style>
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease;
        }

        .form-control,
        .form-select {
            border: 1px solid var(--border-color);
            border-radius: 10px;
            padding: 12px 15px;
            font-size: 0.95rem;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(255, 188, 59, 0.25);
        }

        .form-label {
            font-weight: 600;
            color: var(--secondary-color);
            margin-bottom: 0.5rem;
        }

        .btn-primary {
            background: var(--primary-color);
            border: none;
            border-radius: 25px;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .btn-secondary {
            background: var(--gray-color);
            border: none;
            border-radius: 25px;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-secondary:hover {
            background: var(--gray-dark);
            transform: translateY(-2px);
        }

        .alert {
            border: none;
            border-radius: 10px;
            padding: 15px 20px;
        }

        .form-control[type="file"] {
            padding: 8px 12px;
            line-height: 1.5;
        }

        .form-control[type="file"]::-webkit-file-upload-button {
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 6px;
            padding: 8px 16px;
            margin-right: 10px;
            cursor: pointer;
        }

        .form-control[type="file"]::-webkit-file-upload-button:hover {
            background: var(--primary-dark);
        }

        .current-image {
            max-width: 150px;
            max-height: 150px;
            border-radius: 8px;
            border: 2px solid var(--border-color);
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php include '../includes/user_sidebar.php'; ?>

            <!-- Main Content -->
            <div class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                <!-- Page Header -->
                <div class="page-header mb-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="h3 mb-0">Hariri Bidhaa</h1>
                            <p class="text-muted">Sasisha taarifa za bidhaa yako</p>
                        </div>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="<?= app_url('user/business.php') ?>">Biashara</a></li>
                                <li class="breadcrumb-item"><a href="<?= app_url('user/business.php?business_id=' . $businessId . '&tab=products') ?>">Bidhaa</a></li>
                                <li class="breadcrumb-item active">Hariri</li>
                            </ol>
                        </nav>
                    </div>
                </div>

                <!-- Message Display -->
                <?php if ($message): ?>
                    <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                        <?php echo htmlspecialchars($message); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Edit Product Form -->
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-body p-4">
                                <form method="POST" action="" enctype="multipart/form-data">
                                    <div class="row">
                                        <!-- Product Name -->
                                        <div class="col-md-12 mb-3">
                                            <label for="product_name" class="form-label">Jina la Bidhaa *</label>
                                            <input type="text" class="form-control" id="product_name" name="product_name"
                                                value="<?php echo htmlspecialchars($product['name'] ?? ''); ?>"
                                                placeholder="Jina la bidhaa yako" required>
                                        </div>

                                        <!-- Description -->
                                        <div class="col-md-12 mb-3">
                                            <label for="description" class="form-label">Maelezo ya Bidhaa *</label>
                                            <textarea class="form-control" id="description" name="description" rows="4"
                                                placeholder="Eleza bidhaa yako, vipengele, na faida zake..." required><?php echo htmlspecialchars($product['description'] ?? ''); ?></textarea>
                                        </div>

                                        <!-- Current Image Display -->
                                        <?php if (!empty($product['image'])): ?>
                                            <div class="col-md-12 mb-3">
                                                <label class="form-label">Picha ya Sasa</label>
                                                <div class="mb-2">
                                                    <img src="<?= app_url('uploads/' . $product['image']) ?>"
                                                        alt="Picha ya sasa" class="current-image">
                                                </div>
                                                <small class="text-muted">Picha hii itabadilishwa ikiwa utachagua mpya</small>
                                            </div>
                                        <?php endif; ?>

                                        <!-- Product Image -->
                                        <div class="col-md-12 mb-3">
                                            <label for="product_image" class="form-label">Picha Mpya ya Bidhaa</label>
                                            <input type="file" class="form-control" id="product_image" name="product_image"
                                                accept="image/*">
                                            <small class="text-muted">Aina za picha zinazokubalika: JPG, PNG, GIF, WEBP. Ukubwa wa juu: 5MB</small>
                                        </div>

                                        <!-- Price -->
                                        <div class="col-md-6 mb-3">
                                            <label for="price" class="form-label">Bei (TSh) *</label>
                                            <input type="number" class="form-control" id="price" name="price"
                                                value="<?php echo htmlspecialchars($product['amount'] ?? ''); ?>"
                                                placeholder="0" min="0" step="100" required>
                                        </div>

                                        <!-- Category -->
                                        <div class="col-md-6 mb-3">
                                            <label for="category" class="form-label">Kategoria</label>
                                            <select class="form-select" id="category" name="category">
                                                <option value="">Chagua Kategoria</option>
                                                <option value="1" <?php echo ($product['categoryId'] ?? '') == '1' ? 'selected' : ''; ?>>Chakula na Vinywaji</option>
                                                <option value="2" <?php echo ($product['categoryId'] ?? '') == '2' ? 'selected' : ''; ?>>Mavazi</option>
                                                <option value="3" <?php echo ($product['categoryId'] ?? '') == '3' ? 'selected' : ''; ?>>Vifaa vya Nyumbani</option>
                                                <option value="4" <?php echo ($product['categoryId'] ?? '') == '4' ? 'selected' : ''; ?>>Teknolojia</option>
                                                <option value="5" <?php echo ($product['categoryId'] ?? '') == '5' ? 'selected' : ''; ?>>Afya na Urembo</option>
                                                <option value="6" <?php echo ($product['categoryId'] ?? '') == '6' ? 'selected' : ''; ?>>Nyingine</option>
                                            </select>
                                        </div>

                                        <!-- Product Status -->
                                        <div class="col-md-6 mb-3">
                                            <label for="status" class="form-label">Hali ya Bidhaa</label>
                                            <select class="form-select" id="status" name="status">
                                                <option value="1" <?php echo ($product['status'] ?? '1') == '1' ? 'selected' : ''; ?>>Iko Soko</option>
                                                <option value="0" <?php echo ($product['status'] ?? '1') == '0' ? 'selected' : ''; ?>>Haiko Soko</option>
                                            </select>
                                            <small class="text-muted">Chagua ikiwa bidhaa iko soko au haiko</small>
                                        </div>

                                        <!-- Offer Settings -->
                                        <div class="col-md-12 mb-4">
                                            <div class="card border-0 bg-light">
                                                <div class="card-body">
                                                    <h6 class="card-title mb-3">Mpango wa Punguzo</h6>

                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-check mb-2">
                                                                <input class="form-check-input" type="checkbox" id="isOffered" name="isOffered" value="1"
                                                                    <?php echo ($product['isOffered'] ?? '0') == '1' ? 'checked' : ''; ?>>
                                                                <label class="form-check-label" for="isOffered">
                                                                    Weka punguzo kwenye bidhaa hii
                                                                </label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label for="offer" class="form-label">Asilimia ya Punguzo (%)</label>
                                                            <input type="number" class="form-control" id="offer" name="offer"
                                                                value="<?php echo htmlspecialchars($product['offer'] ?? ''); ?>"
                                                                placeholder="10" min="1" max="99"
                                                                <?php echo ($product['isOffered'] ?? '0') == '1' ? '' : 'disabled'; ?>>
                                                            <small class="text-muted">Weka asilimia ya punguzo (1-99%)</small>
                                                        </div>
                                                    </div>

                                                    <div class="mt-3 p-3 bg-white rounded border">
                                                        <div class="row">
                                                            <div class="col-md-4">
                                                                <strong>Bei ya Kawaida:</strong><br>
                                                                <span class="text-muted">TSh <?php echo number_format($product['amount'] ?? 0); ?></span>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <strong>Punguzo:</strong><br>
                                                                <span class="text-success" id="discountAmount">
                                                                    <?php
                                                                    if (($product['isOffered'] ?? '0') == '1' && !empty($product['offer'])) {
                                                                        $discount = ($product['amount'] ?? 0) * ($product['offer'] ?? 0) / 100;
                                                                        echo 'TSh ' . number_format($discount);
                                                                    } else {
                                                                        echo 'TSh 0';
                                                                    }
                                                                    ?>
                                                                </span>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <strong>Bei ya Punguzo:</strong><br>
                                                                <span class="text-primary fw-bold" id="finalPrice">
                                                                    <?php
                                                                    if (($product['isOffered'] ?? '0') == '1' && !empty($product['offer'])) {
                                                                        $finalPrice = ($product['amount'] ?? 0) - (($product['amount'] ?? 0) * ($product['offer'] ?? 0) / 100);
                                                                        echo 'TSh ' . number_format($finalPrice);
                                                                    } else {
                                                                        echo 'TSh ' . number_format($product['amount'] ?? 0);
                                                                    }
                                                                    ?>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Submit Buttons -->
                                        <div class="col-12">
                                            <div class="d-flex gap-3 justify-content-end">
                                                <a href="<?= app_url('user/business.php?business_id=' . $businessId . '&tab=products') ?>" class="btn btn-secondary">
                                                    Ghairi
                                                </a>
                                                <button type="submit" class="btn btn-primary">
                                                    Sasisha Bidhaa
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Product Info -->
                        <div class="card mt-4">
                            <div class="card-body">
                                <h6 class="card-title">Taarifa za Bidhaa</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>ID ya Bidhaa:</strong> <?php echo $productId; ?></p>
                                        <p class="mb-1"><strong>Hali:</strong>
                                            <span class="badge bg-<?php echo ($product['status'] ?? '1') == '1' ? 'success' : 'secondary'; ?>">
                                                <?php echo ($product['status'] ?? '1') == '1' ? 'Iko Soko' : 'Haiko Soko'; ?>
                                            </span>
                                        </p>
                                        <p class="mb-1"><strong>Tarehe ya Kuongezwa:</strong>
                                            <?php echo date('d/m/Y', strtotime($product['date'] ?? 'now')); ?>
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Biashara:</strong> <?php echo htmlspecialchars($product['business_name'] ?? ''); ?></p>
                                        <p class="mb-0"><strong>Mmiliki:</strong> <?php echo htmlspecialchars($currentUser['name'] ?? ''); ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>

    <script>
        // Handle offer checkbox interaction
        document.getElementById('isOffered').addEventListener('change', function() {
            const offerInput = document.getElementById('offer');
            const discountAmount = document.getElementById('discountAmount');
            const finalPrice = document.getElementById('finalPrice');
            const originalPrice = <?php echo $product['amount'] ?? 0; ?>;

            if (this.checked) {
                offerInput.disabled = false;
                offerInput.focus();
                updateOfferCalculation();
            } else {
                offerInput.disabled = true;
                offerInput.value = '';
                discountAmount.textContent = 'TSh 0';
                finalPrice.textContent = 'TSh ' + originalPrice.toLocaleString();
            }
        });

        // Handle offer percentage input
        document.getElementById('offer').addEventListener('input', function() {
            updateOfferCalculation();
        });

        function updateOfferCalculation() {
            const offerInput = document.getElementById('offer');
            const discountAmount = document.getElementById('discountAmount');
            const finalPrice = document.getElementById('finalPrice');
            const originalPrice = <?php echo $product['amount'] ?? 0; ?>;

            if (offerInput.value && !isNaN(offerInput.value)) {
                const percentage = parseFloat(offerInput.value);
                const discount = originalPrice * percentage / 100;
                const final = originalPrice - discount;

                discountAmount.textContent = 'TSh ' + discount.toLocaleString();
                finalPrice.textContent = 'TSh ' + final.toLocaleString();
            } else {
                discountAmount.textContent = 'TSh 0';
                finalPrice.textContent = 'TSh ' + originalPrice.toLocaleString();
            }
        }

        // Initialize calculation on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateOfferCalculation();
        });
    </script>
</body>

</html>