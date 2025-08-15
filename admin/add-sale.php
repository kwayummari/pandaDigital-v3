<?php
require_once __DIR__ . "/../middleware/AuthMiddleware.php";
require_once __DIR__ . "/../models/Sales.php";

// Check if user is admin
$auth = new AuthMiddleware();
$auth->requireRole('admin');

// Initialize models
$salesModel = new Sales();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $referenceNo = trim($_POST['reference_no'] ?? '');
        $productId = trim($_POST['product_id'] ?? '');
        $buyerId = trim($_POST['buyer_id'] ?? '');
        $amount = trim($_POST['amount'] ?? '');
        $quantity = trim($_POST['quantity'] ?? '');

        if (empty($referenceNo) || empty($productId) || empty($buyerId) || empty($amount) || empty($quantity)) {
            $error = 'Sehemu zote ni lazima';
        } elseif (!is_numeric($amount) || !is_numeric($quantity)) {
            $error = 'Kiasi na idadi lazima ziwe namba';
        } else {
            // Add sale using the Sales model
            $result = $salesModel->addSale([
                'reference_no' => $referenceNo,
                'productId' => $productId,
                'buyersId' => $buyerId,
                'amount' => $amount,
                'quantity' => $quantity,
                'date' => date('Y-m-d H:i:s')
            ]);

            if ($result) {
                header('Location: sales.php?success=1');
                exit;
            } else {
                $error = 'Haikuweza kuongeza muuzo';
            }
        }
    } catch (Exception $e) {
        error_log("Error adding sale: " . $e->getMessage());
        $error = 'Kuna tatizo la kuongeza muuzo';
    }
}
?>

<!DOCTYPE html>
<html lang="sw">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ongeza Muuzo Mpya - Panda Digital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="<?= app_url('assets/css/style.css') ?>?v=5">
    <style>
        .add-form {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-top: 2rem;
        }

        .form-control:focus {
            border-color: #000;
            box-shadow: 0 0 0 0.2rem rgba(0, 0, 0, 0.25);
        }

        .btn-primary {
            background: #000;
            border-color: #000;
        }

        .btn-primary:hover {
            background: #333;
            border-color: #333;
        }

        .btn-secondary {
            background: #6c757d;
            border-color: #6c757d;
        }

        .btn-secondary:hover {
            background: #5a6268;
            border-color: #5a6268;
        }

        /* Layout fixes */
        .content-wrapper {
            padding: 20px 30px;
        }

        .add-form {
            margin-top: 1rem;
        }
    </style>
</head>

<body>
    <?php include __DIR__ . '/includes/admin_header.php'; ?>

    <div class="content-wrapper">
        <div class="add-form">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="mb-0">
                    <i class="fas fa-plus me-2"></i>Ongeza Muuzo Mpya
                </h4>
                <a href="sales.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Rudi Nyuma
                </a>
            </div>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i><?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="referenceNo" class="form-label">Namba ya Rejeleo *</label>
                        <input type="text" class="form-control" id="referenceNo" name="reference_no"
                            value="<?= htmlspecialchars($_POST['reference_no'] ?? '') ?>" required>
                        <small class="form-text text-muted">Namba ya kipekee ya muuzo</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="productId" class="form-label">ID ya Bidhaa *</label>
                        <input type="number" class="form-control" id="productId" name="product_id"
                            value="<?= htmlspecialchars($_POST['product_id'] ?? '') ?>" min="1" required>
                        <small class="form-text text-muted">ID ya bidhaa kutoka database</small>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="buyerId" class="form-label">ID ya Mnunuzi *</label>
                        <input type="number" class="form-control" id="buyerId" name="buyer_id"
                            value="<?= htmlspecialchars($_POST['buyer_id'] ?? '') ?>" min="1" required>
                        <small class="form-text text-muted">ID ya mnunuzi kutoka database</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="amount" class="form-label">Kiasi *</label>
                        <input type="number" class="form-control" id="amount" name="amount"
                            value="<?= htmlspecialchars($_POST['amount'] ?? '') ?>" step="0.01" min="0" required>
                        <small class="form-text text-muted">Kiasi cha muuzo kwa TSh</small>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="quantity" class="form-label">Idadi *</label>
                    <input type="number" class="form-control" id="quantity" name="quantity"
                        value="<?= htmlspecialchars($_POST['quantity'] ?? '') ?>" min="1" required>
                    <small class="form-text text-muted">Idadi ya bidhaa zilizouzwa</small>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="sales.php" class="btn btn-secondary">Futa</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Ongeza Muuzo
                    </button>
                </div>
            </form>
        </div>
    </div>

    <?php include __DIR__ . '/includes/admin_footer_common.php'; ?>
</body>

</html>