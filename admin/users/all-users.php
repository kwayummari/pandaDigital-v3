<?php
require_once __DIR__ . "/../../middleware/AuthMiddleware.php";
require_once __DIR__ . "/../models/User.php";

$auth = new AuthMiddleware();
$auth->requireRole('admin');

$currentUser = $auth->getCurrentUser();
$userModel = new User();

// Get all users
$users = $userModel->getAllUsers();
?>

<?php include __DIR__ . '/../includes/admin_header.php'; ?>

<!-- Page Title -->
<script>
    document.title = 'Watumiaji Wote - Admin Panel';
    document.getElementById('pageTitle').textContent = 'Watumiaji Wote';
</script>

<!-- Users Content -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Watumiaji Wote</h5>
                <a href="<?= app_url('admin/users/add-user.php') ?>" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Ongeza Mtumiaji
                </a>
            </div>
            <div class="card-body">
                <?php if (!empty($users)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Jina</th>
                                    <th>Barua Pepe</th>
                                    <th>Simu</th>
                                    <th>Jinsia</th>
                                    <th>Mkoa</th>
                                    <th>Jukumu</th>
                                    <th>Tarehe</th>
                                    <th>Vitendo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="user-avatar me-3">
                                                    <i class="fas fa-user"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0"><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?= htmlspecialchars($user['email']) ?></td>
                                        <td><?= htmlspecialchars($user['phone'] ?? 'N/A') ?></td>
                                        <td>
                                            <?php if ($user['gender']): ?>
                                                <span class="badge bg-info"><?= htmlspecialchars($user['gender']) ?></span>
                                            <?php else: ?>
                                                <span class="text-muted">N/A</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= htmlspecialchars($user['region'] ?? 'N/A') ?></td>
                                        <td>
                                            <?php
                                            $roleColors = [
                                                'admin' => 'danger',
                                                'expert' => 'warning',
                                                'user' => 'primary'
                                            ];
                                            $roleColor = $roleColors[$user['role']] ?? 'secondary';
                                            ?>
                                            <span class="badge bg-<?= $roleColor ?>"><?= ucfirst($user['role']) ?></span>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                <?= date('d/m/Y', strtotime($user['date_created'])) ?>
                                            </small>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="<?= app_url('admin/users/edit-user.php?id=' . $user['id']) ?>"
                                                    class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-outline-danger"
                                                    onclick="deleteUser(<?= $user['id'] ?>)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fas fa-users text-muted" style="font-size: 4rem; opacity: 0.3;"></i>
                        <h5 class="mt-3">Hakuna watumiaji bado</h5>
                        <p class="text-muted">Watumiaji wa kwanza bado hawajajisajili</p>
                        <a href="<?= app_url('admin/users/add-user.php') ?>" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Ongeza Mtumiaji wa Kwanza
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
    function deleteUser(userId) {
        if (confirm('Una uhakika unataka kufuta mtumiaji huyu?')) {
            // Add delete functionality here
            console.log('Deleting user:', userId);
        }
    }
</script>

<?php include __DIR__ . '/../includes/admin_footer.php'; ?>