<?php
require_once __DIR__ . "/../config/init.php";
require_once __DIR__ . "/../middleware/AuthMiddleware.php";
require_once __DIR__ . "/../models/Business.php";

$auth = new AuthMiddleware();
$auth->requireRole('user');

$currentUser = $auth->getCurrentUser();
$businessModel = new Business();

$businessId = $_GET['business_id'] ?? null;
$message = '';
$messageType = '';
$showSuccessActions = false;

// Validate business ownership
if ($businessId) {
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
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productName = trim($_POST['product_name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = trim($_POST['price'] ?? '');
    $category = trim($_POST['category'] ?? '');
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
        $imagePath = null;
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
                }
            }
        }

        if ($businessModel->addProduct($businessId, $productName, $description, $price, $category ?: null, $imagePath, $isOffered, $offer)) {
            $message = 'Bidhaa yako imeongezwa kwa mafanikio!';
            $messageType = 'success';
            $showSuccessActions = true;

            // Clear form data
            $productName = $description = $price = $category = '';
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
    <title>Ongeza Bidhaa - Panda Digital</title>

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
    </style>
</head>

<body>
    <!-- Sidebar and Main Content Layout -->
    <div class="dashboard-container">
        <?php include __DIR__ . '/../includes/user_sidebar.php'; ?>

        <!-- Main Content -->
        <div class="main-content">
            <?php
            $page_title = 'Ongeza Bidhaa';
            include __DIR__ . '/../includes/user_top_nav.php';
            ?>

            <div class="content-wrapper">
                <!-- Header -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h1 class="h3 mb-0">
                            Ongeza Bidhaa
                        </h1>
                        <p class="text-muted">Ongeza bidhaa mpya kwenye biashara yako</p>
                    </div>
                </div>

                <!-- Message Display -->
                <?php if ($message): ?>
                    <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                        <?php echo htmlspecialchars($message); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>

                    <!-- Success Actions -->
                    <?php if ($showSuccessActions): ?>
                        <div class="alert alert-success border-0">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <h6 class="mb-1">Bidhaa Imeongezwa!</h6>
                                    <p class="mb-0">Bidhaa yako imeongezwa kwa mafanikio kwenye biashara yako.</p>
                                </div>
                                <div class="d-flex gap-2">
                                    <a href="<?= app_url('user/business.php?business_id=' . $businessId . '&tab=products') ?>" class="btn btn-primary">
                                        Tazama Bidhaa Zangu
                                    </a>
                                    <a href="<?= app_url('user/add-product.php?business_id=' . $businessId) ?>" class="btn btn-outline-primary">
                                        Ongeza Bidhaa Nyingine
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>

                <!-- Add Product Form -->
                <?php if (!$showSuccessActions): ?>
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
                                                    value="<?php echo htmlspecialchars($productName ?? ''); ?>"
                                                    placeholder="Jina la bidhaa yako" required>
                                            </div>

                                            <!-- Description -->
                                            <div class="col-md-12 mb-3">
                                                <label for="description" class="form-label">Maelezo ya Bidhaa *</label>
                                                <textarea class="form-control" id="description" name="description" rows="4"
                                                    placeholder="Eleza bidhaa yako, vipengele, na faida zake..." required><?php echo htmlspecialchars($description ?? ''); ?></textarea>
                                            </div>

                                            <!-- Product Image -->
                                            <div class="col-md-12 mb-3">
                                                <label for="product_image" class="form-label">Picha ya Bidhaa</label>
                                                <input type="file" class="form-control" id="product_image" name="product_image"
                                                    accept="image/*">
                                                <small class="text-muted">Aina za picha zinazokubalika: JPG, PNG, GIF, WEBP. Ukubwa wa juu: 5MB</small>
                                            </div>

                                            <!-- Price -->
                                            <div class="col-md-6 mb-3">
                                                <label for="price" class="form-label">Bei (TSh) *</label>
                                                <input type="number" class="form-control" id="price" name="price"
                                                    value="<?php echo htmlspecialchars($price ?? ''); ?>"
                                                    placeholder="0" min="0" step="100" required>
                                            </div>

                                            <!-- Category -->
                                            <div class="col-md-6 mb-3">
                                                <label for="category" class="form-label">Kategoria</label>
                                                <select class="form-select" id="category" name="category">
                                                    <option value="">Chagua Kategoria</option>
                                                    <option value="1" <?php echo ($category ?? '') == '1' ? 'selected' : ''; ?>>Chakula na Vinywaji</option>
                                                    <option value="2" <?php echo ($category ?? '') == '2' ? 'selected' : ''; ?>>Mavazi</option>
                                                    <option value="3" <?php echo ($category ?? '') == '3' ? 'selected' : ''; ?>>Vifaa vya Nyumbani</option>
                                                    <option value="4" <?php echo ($category ?? '') == '4' ? 'selected' : ''; ?>>Teknolojia</option>
                                                    <option value="5" <?php echo ($category ?? '') == '5' ? 'selected' : ''; ?>>Afya na Urembo</option>
                                                    <option value="6" <?php echo ($category ?? '') == '6' ? 'selected' : ''; ?>>Nyingine</option>
                                                </select>
                                            </div>

                                            <!-- Offer Settings -->
                                            <div class="col-md-12 mb-4">
                                                <div class="card border-0 bg-light">
                                                    <div class="card-body">
                                                        <h6 class="card-title mb-3">Mpango wa Punguzo (Si lazima)</h6>

                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-check mb-2">
                                                                    <input class="form-check-input" type="checkbox" id="isOffered" name="isOffered" value="1">
                                                                    <label class="form-check-label" for="isOffered">
                                                                        Weka punguzo kwenye bidhaa hii
                                                                    </label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label for="offer" class="form-label">Asilimia ya Punguzo (%)</label>
                                                                <input type="number" class="form-control" id="offer" name="offer"
                                                                    placeholder="10" min="1" max="99" disabled>
                                                                <small class="text-muted">Weka asilimia ya punguzo (1-99%)</small>
                                                            </div>
                                                        </div>

                                                        <div class="mt-3 p-3 bg-white rounded border">
                                                            <div class="row">
                                                                <div class="col-md-4">
                                                                    <strong>Bei ya Kawaida:</strong><br>
                                                                    <span class="text-muted" id="originalPrice">TSh 0</span>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <strong>Punguzo:</strong><br>
                                                                    <span class="text-success" id="discountAmount">TSh 0</span>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <strong>Bei ya Punguzo:</strong><br>
                                                                    <span class="text-primary fw-bold" id="finalPrice">TSh 0</span>
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
                                                        Ongeza Bidhaa
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>

    <script>
        // Handle offer checkbox interaction
        document.getElementById('isOffered').addEventListener('change', function() {
            const offerInput = document.getElementById('offer');
            const priceInput = document.getElementById('price');

            if (this.checked) {
                offerInput.disabled = false;
                offerInput.focus();
                updateOfferCalculation();
            } else {
                offerInput.disabled = true;
                offerInput.value = '';
                updateOfferCalculation();
            }
        });

        // Handle price and offer input changes
        document.getElementById('price').addEventListener('input', updateOfferCalculation);
        document.getElementById('offer').addEventListener('input', updateOfferCalculation);

        function updateOfferCalculation() {
            const priceInput = document.getElementById('price');
            const offerInput = document.getElementById('offer');
            const originalPrice = document.getElementById('originalPrice');
            const discountAmount = document.getElementById('discountAmount');
            const finalPrice = document.getElementById('finalPrice');

            const price = parseFloat(priceInput.value) || 0;
            const offer = parseFloat(offerInput.value) || 0;

            originalPrice.textContent = 'TSh ' + price.toLocaleString();

            if (offer > 0 && price > 0) {
                const discount = price * offer / 100;
                const final = price - discount;

                discountAmount.textContent = 'TSh ' + discount.toLocaleString();
                finalPrice.textContent = 'TSh ' + final.toLocaleString();
            } else {
                discountAmount.textContent = 'TSh 0';
                finalPrice.textContent = 'TSh ' + price.toLocaleString();
            }
        }

        // Initialize calculation on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateOfferCalculation();
        });
    </script>
</body>

</html>