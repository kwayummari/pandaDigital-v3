<?php
require_once __DIR__ . "/../middleware/AuthMiddleware.php";
require_once __DIR__ . "/../models/Sales.php";

// Check if user is admin
$auth = new AuthMiddleware();
$auth->requireRole('admin');

$saleId = $_GET['id'] ?? null;
if (!$saleId) {
    header('Location: sales.php');
    exit;
}

// Initialize models
$salesModel = new Sales();

// Get sale details
$sale = $salesModel->getSaleById($saleId);
if (!$sale) {
    header('Location: sales.php');
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $amount = trim($_POST['amount'] ?? '');
        $quantity = trim($_POST['quantity'] ?? '');
        $status = trim($_POST['status'] ?? '');

        if (empty($amount) || empty($quantity)) {
            $error = 'Kiasi na idadi ni lazima';
        } elseif (!is_numeric($amount) || !is_numeric($quantity)) {
            $error = 'Kiasi na idadi lazima ziwe namba';
        } else {
            // Update sale using the Sales model
            $result = $salesModel->updateSale($saleId, [
                'amount' => $amount,
                'quantity' => $quantity,
                'status' => $status
            ]);

            if ($result) {
                header('Location: sales.php?success=1');
                exit;
            } else {
                $error = 'Haikuweza kuhifadhi mabadiliko';
            }
        }
    } catch (Exception $e) {
        error_log("Error updating sale: " . $e->getMessage());
        $error = 'Kuna tatizo la kuhifadhi sale';
    }
}
?>

<!DOCTYPE html>
<html lang="sw">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hariri Muuzo - <?= htmlspecialchars($sale['reference_no'] ?? 'N/A') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="<?= app_url('assets/css/style.css') ?>?v=5">
    <style>
        .edit-form {
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

        .edit-form {
            margin-top: 1rem;
        }
    </style>
</head>

<body>
    <?php include __DIR__ . '/includes/admin_header.php'; ?>

    <div class="content-wrapper">
        <div class="edit-form">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="mb-0">
                    <i class="fas fa-edit me-2"></i>Hariri Muuzo
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
                        <label for="referenceNo" class="form-label">Namba ya Rejeleo</label>
                        <input type="text" class="form-control" id="referenceNo" 
                            value="<?= htmlspecialchars($sale['reference_no'] ?? '') ?>" readonly>
                        <small class="form-text text-muted">Haiwezi kubadilishwa</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="productName" class="form-label">Bidhaa</label>
                        <input type="text" class="form-control" id="productName" 
                            value="<?= htmlspecialchars($sale['product_name'] ?? '') ?>" readonly>
                        <small class="form-text text-muted">Haiwezi kubadilishwa</small>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="amount" class="form-label">Kiasi *</label>
                        <input type="number" class="form-control" id="amount" name="amount" 
                            value="<?= htmlspecialchars($sale['amount'] ?? '') ?>" step="0.01" min="0" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="quantity" class="form-label">Idadi *</label>
                        <input type="number" class="form-control" id="quantity" name="quantity" 
                            value="<?= htmlspecialchars($sale['quantity'] ?? '') ?>" min="1" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="sellerName" class="form-label">Muuzaji</label>
                        <input type="text" class="form-control" id="sellerName" 
                            value="<?= htmlspecialchars(($sale['first_name'] ?? '') . ' ' . ($sale['last_name'] ?? '')) ?>" readonly>
                        <small class="form-text text-muted">Haiwezi kubadilishwa</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="status" class="form-label">Hali</label>
                        <select class="form-control" id="status" name="status">
                            <option value="pending" <?= ($sale['sale_status'] ?? '') === 'pending' ? 'selected' : '' ?>>Inasubiri</option>
                            <option value="completed" <?= ($sale['sale_status'] ?? '') === 'completed' ? 'selected' : '' ?>>Imekamilika</option>
                            <option value="cancelled" <?= ($sale['sale_status'] ?? '') === 'cancelled' ? 'selected' : '' ?>>Imebatilishwa</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="saleDate" class="form-label">Tarehe ya Muuzo</label>
                    <input type="text" class="form-control" id="saleDate" 
                        value="<?= htmlspecialchars($sale['sale_date'] ? date('d/m/Y H:i', strtotime($sale['sale_date'])) : 'N/A' ?>" readonly>
                    <small class="form-text text-muted">Haiwezi kubadilishwa</small>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="sales.php" class="btn btn-secondary">Futa</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Hifadhi Mabadiliko
                    </button>
                </div>
            </form>
        </div>
    </div>

    <?php include __DIR__ . '/includes/admin_footer_common.php'; ?>
</body>

</html>
