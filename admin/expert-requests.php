<?php
require_once __DIR__ . "/../middleware/AuthMiddleware.php";
require_once __DIR__ . "/../models/User.php";

$auth = new AuthMiddleware();
$auth->requireRole('admin');

$currentUser = $auth->getCurrentUser();
$userModel = new User();

// Handle expert approval/rejection
if ($_POST && isset($_POST['action'])) {
    $userId = $_POST['user_id'];
    $action = $_POST['action'];

    if ($action === 'approve') {
        $result = $userModel->approveExpertRole($userId);
        if ($result) {
            $success = "Mtaalam ameidhinishwa kikamilifu!";
        } else {
            $error = "Imefeli kuidhinisha mtaalam. Tafadhali jaribu tena.";
        }
    } elseif ($action === 'reject') {
        $result = $userModel->rejectExpertRole($userId);
        if ($result) {
            $success = "Ombi la mtaalam limekataliwa.";
        } else {
            $error = "Imefeli kukataa ombi. Tafadhali jaribu tena.";
        }
    }
}

// Get pending expert requests
$pendingRequests = $userModel->getPendingExpertRequests();
?>

<!DOCTYPE html>
<html lang="sw">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usimamizi wa Maombi ya Mitaalam - Panda Digital</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #34495e;
            --accent-color: #3498db;
            --success-color: #27ae60;
            --warning-color: #f39c12;
            --danger-color: #e74c3c;
        }

        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .navbar {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .main-content {
            padding: 20px;
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .request-card {
            border-left: 4px solid var(--warning-color);
            transition: all 0.3s ease;
        }

        .request-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }

        .btn-approve {
            background: var(--success-color);
            border: none;
            border-radius: 25px;
            padding: 8px 20px;
        }

        .btn-approve:hover {
            background: #229954;
            transform: translateY(-1px);
        }

        .btn-reject {
            background: var(--danger-color);
            border: none;
            border-radius: 25px;
            padding: 8px 20px;
        }

        .btn-reject:hover {
            background: #c0392b;
            transform: translateY(-1px);
        }

        .stats-card {
            background: linear-gradient(135deg, var(--accent-color), #2980b9);
            color: white;
        }

        .stats-card.success {
            background: linear-gradient(135deg, var(--success-color), #229954);
        }

        .stats-card.warning {
            background: linear-gradient(135deg, var(--warning-color), #d68910);
        }

        .alert {
            border-radius: 10px;
            border: none;
        }

        .table {
            border-radius: 10px;
            overflow: hidden;
        }

        .table th {
            background: var(--primary-color);
            color: white;
            border: none;
            font-weight: 600;
        }

        .badge-pending {
            background: var(--warning-color);
            color: white;
        }

        .badge-approved {
            background: var(--success-color);
            color: white;
        }

        .badge-rejected {
            background: var(--danger-color);
            color: white;
        }
    </style>
</head>

<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="/admin/dashboard.php">
                <i class="fas fa-shield-alt me-2"></i>
                Panda Digital - Admin
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="/admin/dashboard.php">
                    <i class="fas fa-tachometer-alt me-1"></i> Dashboard
                </a>
                <a class="nav-link active" href="/admin/expert-requests.php">
                    <i class="fas fa-user-graduate me-1"></i> Maombi ya Mitaalam
                </a>
                <a class="nav-link" href="/logout.php">
                    <i class="fas fa-sign-out-alt me-1"></i> Toka
                </a>
            </div>
        </div>
    </nav>

    <div class="main-content">
        <div class="container">
            <!-- Header -->
            <div class="row mb-4">
                <div class="col-12">
                    <h1 class="h3 mb-0">
                        <i class="fas fa-user-graduate text-primary me-2"></i>
                        Usimamizi wa Maombi ya Mitaalam
                    </h1>
                    <p class="text-muted">Idhinisha au kataa maombi ya watumiaji kuwa mitaalam</p>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card stats-card">
                        <div class="card-body text-center">
                            <i class="fas fa-users fa-2x mb-2"></i>
                            <h3 class="mb-1"><?php echo count($pendingRequests); ?></h3>
                            <p class="mb-0">Maombi Yaliyosubiri</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card stats-card success">
                        <div class="card-body text-center">
                            <i class="fas fa-check-circle fa-2x mb-2"></i>
                            <h3 class="mb-1">
                                <?php
                                // This would need to be implemented in the User model
                                echo "0"; // Placeholder for approved experts count
                                ?>
                            </h3>
                            <p class="mb-0">Mitaalam Waliyoidhinishwa</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card stats-card warning">
                        <div class="card-body text-center">
                            <i class="fas fa-clock fa-2x mb-2"></i>
                            <h3 class="mb-1">
                                <?php
                                // This would need to be implemented in the User model
                                echo "0"; // Placeholder for total experts count
                                ?>
                            </h3>
                            <p class="mb-0">Jumla ya Mitaalam</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Alerts -->
            <?php if (isset($success)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    <?php echo htmlspecialchars($success); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <?php echo htmlspecialchars($error); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Pending Requests -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-clock me-2"></i>
                        Maombi Yaliyosubiri (<?php echo count($pendingRequests); ?>)
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (empty($pendingRequests)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                            <h5>Hakuna maombi yaliyosubiri!</h5>
                            <p class="text-muted">Watu wote wanaoomba kuwa mitaalam wameidhinishwa au kukataliwa.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Jina</th>
                                        <th>Barua Pepe</th>
                                        <th>Simu</th>
                                        <th>Mkoa</th>
                                        <th>Biashara</th>
                                        <th>Bio</th>
                                        <th>Tarehe ya Ombi</th>
                                        <th>Vitendo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($pendingRequests as $request): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center me-3">
                                                        <span class="text-white fw-bold">
                                                            <?php echo strtoupper(substr($request['first_name'], 0, 1) . substr($request['last_name'], 0, 1)); ?>
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold">
                                                            <?php echo htmlspecialchars($request['first_name'] . ' ' . $request['last_name']); ?>
                                                        </div>
                                                        <small class="text-muted">ID: <?php echo $request['id']; ?></small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <a href="mailto:<?php echo htmlspecialchars($request['email']); ?>"
                                                    class="text-decoration-none">
                                                    <?php echo htmlspecialchars($request['email']); ?>
                                                </a>
                                            </td>
                                            <td>
                                                <?php if ($request['phone'] && $request['phone'] !== 'null'): ?>
                                                    <a href="tel:<?php echo htmlspecialchars($request['phone']); ?>"
                                                        class="text-decoration-none">
                                                        <?php echo htmlspecialchars($request['phone']); ?>
                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($request['region'] && $request['region'] !== 'null'): ?>
                                                    <?php echo htmlspecialchars($request['region']); ?>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($request['business'] && $request['business'] !== 'null'): ?>
                                                    <span class="badge bg-info">
                                                        <?php echo htmlspecialchars($request['business']); ?>
                                                    </span>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($request['bio'] && $request['bio'] !== 'none'): ?>
                                                    <button type="button"
                                                        class="btn btn-sm btn-outline-primary"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#bioModal"
                                                        data-bio="<?php echo htmlspecialchars($request['bio']); ?>"
                                                        data-name="<?php echo htmlspecialchars($request['first_name'] . ' ' . $request['last_name']); ?>">
                                                        <i class="fas fa-eye me-1"></i> Tazama
                                                    </button>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    <?php echo date('d M Y H:i', strtotime($request['date_created'])); ?>
                                                </small>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button type="button"
                                                        class="btn btn-approve text-white btn-sm"
                                                        onclick="approveExpert(<?php echo $request['id']; ?>, '<?php echo htmlspecialchars($request['first_name'] . ' ' . $request['last_name']); ?>')">
                                                        <i class="fas fa-check me-1"></i> Idhinisha
                                                    </button>
                                                    <button type="button"
                                                        class="btn btn-reject text-white btn-sm"
                                                        onclick="rejectExpert(<?php echo $request['id']; ?>, '<?php echo htmlspecialchars($request['first_name'] . ' ' . $request['last_name']); ?>')">
                                                        <i class="fas fa-times me-1"></i> Kataa
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Bio Modal -->
    <div class="modal fade" id="bioModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-user me-2"></i>
                        Bio ya Mtaalam
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">
                            <strong>Jina:</strong>
                        </label>
                        <div class="form-control-plaintext" id="modalExpertName"></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">
                            <strong>Bio:</strong>
                        </label>
                        <div class="form-control-plaintext" id="modalExpertBio"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i> Funga
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Handle bio modal
        document.addEventListener('DOMContentLoaded', function() {
            const bioModal = document.getElementById('bioModal');
            if (bioModal) {
                bioModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    const bio = button.getAttribute('data-bio');
                    const name = button.getAttribute('data-name');

                    document.getElementById('modalExpertName').textContent = name;
                    document.getElementById('modalExpertBio').textContent = bio;
                });
            }
        });

        // Approve expert
        function approveExpert(userId, userName) {
            if (confirm('Je, una uhakika unataka kuidhinisha ' + userName + ' kuwa mtaalam?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="user_id" value="${userId}">
                    <input type="hidden" name="action" value="approve">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        // Reject expert
        function rejectExpert(userId, userName) {
            if (confirm('Je, una uhakika unataka kukataa ombi la ' + userName + ' kuwa mtaalam?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="user_id" value="${userId}">
                    <input type="hidden" name="action" value="reject">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</body>

</html>