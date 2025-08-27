<?php
require_once '../../config/init.php';
require_once '../../config/database.php';

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header('Location: ../../auth/login.php');
    exit;
}

// Initialize database
$database = new Database();
$conn = $database->getConnection();

$userId = $_SESSION['user_id'];
$pageTitle = "Profile Yangu - Panda Market";

include '../../includes/header.php';

// Get user information
$userQuery = "SELECT * FROM users WHERE id = ?";
$userStmt = $conn->prepare($userQuery);
$userStmt->execute([$userId]);
$user = $userStmt->fetch();

// Get user's order history
$ordersQuery = "SELECT s.*, p.name as product_name, p.image as product_image, p.amount as product_price 
                FROM sales s 
                LEFT JOIN products p ON s.productId = p.id 
                WHERE s.buyersId = ? 
                ORDER BY s.date DESC";
$ordersStmt = $conn->prepare($ordersQuery);
$ordersStmt->execute([$userId]);
$orders = $ordersStmt->fetchAll();

// Get user's ratings
$ratingsQuery = "SELECT r.*, p.name as product_name 
                 FROM ratings r 
                 LEFT JOIN products p ON r.productId = p.id 
                 WHERE r.userId = ? 
                 ORDER BY r.date DESC";
$ratingsStmt = $conn->prepare($ratingsQuery);
$ratingsStmt->execute([$userId]);
$ratings = $ratingsStmt->fetchAll();
?>

<!-- Page Header -->
<section class="page-header" style="background-image: url('<?= asset('images/banner/market-banner.jpg') ?>'); background-size: cover; background-position: center; background-repeat: no-repeat; position: relative;">
    <div class="overlay" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(0, 0, 0, 0.6);"></div>
    <div class="container" style="position: relative; z-index: 2;">
        <div class="row align-items-center">
            <div class="col-lg-12" data-aos="fade-right">
                <h1 class="page-title" style="color: #fff;">Profile Yangu</h1>
                <p class="page-subtitle">Dhibiti maelezo yako na uone historia ya orders</p>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= app_url() ?>">Nyumbani</a></li>
                        <li class="breadcrumb-item"><a href="<?= app_url('panda-market.php') ?>">Panda Market</a></li>
                        <li class="breadcrumb-item active">Profile</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</section>

<!-- Profile Content -->
<section class="profile-content py-5">
    <div class="container">
        <div class="row">
            <!-- Profile Sidebar -->
            <div class="col-lg-3 mb-4">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="profile-avatar mb-3">
                            <img src="<?= asset('images/profiles/' . ($user['profile_photo'] ?: 'Profile-01.jpg')) ?>"
                                alt="Profile Photo" class="rounded-circle" style="width: 100px; height: 100px; object-fit: cover;">
                        </div>
                        <h5 class="card-title"><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></h5>
                        <p class="text-muted"><?= htmlspecialchars($user['email']) ?></p>
                        <p class="text-muted small">Mwanachama tangu: <?= date('d/m/Y', strtotime($user['date_created'])) ?></p>

                        <div class="profile-stats mt-3">
                            <div class="row text-center">
                                <div class="col-6">
                                    <h6 class="text-primary"><?= count($orders) ?></h6>
                                    <small class="text-muted">Orders</small>
                                </div>
                                <div class="col-6">
                                    <h6 class="text-success"><?= count($ratings) ?></h6>
                                    <small class="text-muted">Ratings</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h6 class="mb-0">Vitendo vya Haraka</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            <a href="#profile-info" class="list-group-item list-group-item-action">
                                <i class="fas fa-user me-2"></i>Maelezo ya Profile
                            </a>
                            <a href="#order-history" class="list-group-item list-group-item-action">
                                <i class="fas fa-shopping-bag me-2"></i>Historia ya Orders
                            </a>
                            <a href="#ratings" class="list-group-item list-group-item-action">
                                <i class="fas fa-star me-2"></i>Ratings Zangu
                            </a>
                            <a href="../../auth/logout.php" class="list-group-item list-group-item-action text-danger">
                                <i class="fas fa-sign-out-alt me-2"></i>Logout
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-lg-9">
                <!-- Profile Information -->
                <div class="card mb-4" id="profile-info">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-user me-2"></i>Maelezo ya Profile
                        </h5>
                    </div>
                    <div class="card-body">
                        <form id="profileForm">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="first_name" class="form-label">Jina la Kwanza</label>
                                    <input type="text" class="form-control" id="first_name" name="first_name"
                                        value="<?= htmlspecialchars($user['first_name']) ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="last_name" class="form-label">Jina la Mwisho</label>
                                    <input type="text" class="form-control" id="last_name" name="last_name"
                                        value="<?= htmlspecialchars($user['last_name']) ?>" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Barua Pepe</label>
                                    <input type="email" class="form-control" id="email" name="email"
                                        value="<?= htmlspecialchars($user['email']) ?>" readonly>
                                    <small class="text-muted">Barua pepe haiwezi kubadilishwa</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label">Namba ya Simu</label>
                                    <input type="tel" class="form-control" id="phone" name="phone"
                                        value="<?= htmlspecialchars($user['phone']) ?>" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="region" class="form-label">Mkoa</label>
                                    <input type="text" class="form-control" id="region" name="region"
                                        value="<?= htmlspecialchars($user['region']) ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="business" class="form-label">Biashara</label>
                                    <input type="text" class="form-control" id="business" name="business"
                                        value="<?= htmlspecialchars($user['business']) ?>">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="gender" class="form-label">Jinsia</label>
                                    <select class="form-select" id="gender" name="gender">
                                        <option value="">Chagua jinsia</option>
                                        <option value="Mwanaume" <?= $user['gender'] == 'Mwanaume' ? 'selected' : '' ?>>Mwanaume</option>
                                        <option value="Mwanamke" <?= $user['gender'] == 'Mwanamke' ? 'selected' : '' ?>>Mwanamke</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="date_of_birth" class="form-label">Tarehe ya Kuzaliwa</label>
                                    <input type="date" class="form-control" id="date_of_birth" name="date_of_birth"
                                        value="<?= $user['date_of_birth'] ?>">
                                </div>
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Hifadhi Mabadiliko
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Order History -->
                <div class="card mb-4" id="order-history">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-shopping-bag me-2"></i>Historia ya Orders
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($orders)): ?>
                            <div class="text-center py-4">
                                <i class="fas fa-shopping-bag fa-3x text-muted mb-3"></i>
                                <h6 class="text-muted">Huna orders bado</h6>
                                <p class="text-muted">Nunua bidhaa kutoka Panda Market na uone orders zako hapa</p>
                                <a href="../../panda-market.php" class="btn btn-primary">
                                    <i class="fas fa-shopping-cart me-2"></i>Nenda Panda Market
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Bidhaa</th>
                                            <th>Bei</th>
                                            <th>Idadi</th>
                                            <th>Jumla</th>
                                            <th>Status</th>
                                            <th>Tarehe</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($orders as $order): ?>
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <img src="../../assets/images/<?= htmlspecialchars($order['product_image']) ?>"
                                                            alt="<?= htmlspecialchars($order['product_name']) ?>"
                                                            class="rounded me-3" style="width: 50px; height: 50px; object-fit: cover;">
                                                        <div>
                                                            <h6 class="mb-0"><?= htmlspecialchars($order['product_name']) ?></h6>
                                                            <small class="text-muted">Ref: <?= htmlspecialchars($order['reference_no']) ?></small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>Tsh.<?= number_format($order['product_price'], 0) ?></td>
                                                <td><?= htmlspecialchars($order['quantity']) ?></td>
                                                <td>Tsh.<?= number_format($order['amount'], 0) ?></td>
                                                <td>
                                                    <?php if ($order['status'] == '1'): ?>
                                                        <span class="badge bg-success">Imekamilika</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-warning">Inasubiri</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?= date('d/m/Y', strtotime($order['date'])) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Ratings -->
                <div class="card mb-4" id="ratings">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-star me-2"></i>Ratings Zangu
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($ratings)): ?>
                            <div class="text-center py-4">
                                <i class="fas fa-star fa-3x text-muted mb-3"></i>
                                <h6 class="text-muted">Huna ratings bado</h6>
                                <p class="text-muted">Rudia bidhaa unazozinunua na uone ratings zako hapa</p>
                            </div>
                        <?php else: ?>
                            <div class="row">
                                <?php foreach ($ratings as $rating): ?>
                                    <div class="col-md-6 mb-3">
                                        <div class="card border">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div>
                                                        <h6 class="mb-1"><?= htmlspecialchars($rating['product_name']) ?></h6>
                                                        <div class="stars mb-2">
                                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                                <i class="fas fa-star <?= $i <= $rating['rating'] ? 'text-warning' : 'text-muted' ?>"></i>
                                                            <?php endfor; ?>
                                                        </div>
                                                        <small class="text-muted">Tarehe: <?= date('d/m/Y', strtotime($rating['date'])) ?></small>
                                                    </div>
                                                </div>
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
    </div>
</section>

<?php include '../../includes/footer.php'; ?>

<style>
    .profile-avatar img {
        border: 3px solid #007bff;
    }

    .profile-stats h6 {
        font-size: 1.5rem;
        font-weight: 600;
    }

    .list-group-item {
        border: none;
        border-bottom: 1px solid #e9ecef;
    }

    .list-group-item:last-child {
        border-bottom: none;
    }

    .list-group-item:hover {
        background-color: #f8f9fa;
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

    .table th {
        border-top: none;
        font-weight: 600;
        color: #333;
    }

    .badge {
        font-size: 12px;
        padding: 6px 12px;
    }
</style>

<script>
    // Profile form submission
    document.getElementById('profileForm').addEventListener('submit', function(e) {
        e.preventDefault();

        // Get form data
        const formData = new FormData(this);

        // Show loading state
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Inahifadhi...';
        submitBtn.disabled = true;

        // Submit form data (you would implement this endpoint)
        fetch('update_profile.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Profile imehifadhiwa kwa mafanikio!');
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

    // Smooth scrolling for navigation links
    document.querySelectorAll('a[href^="#"]').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            const targetSection = document.querySelector(targetId);

            if (targetSection) {
                targetSection.scrollIntoView({
                    behavior: 'smooth'
                });
            }
        });
    });
</script>