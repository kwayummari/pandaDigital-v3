<?php
require_once __DIR__ . "/../config/init.php";
require_once __DIR__ . "/../middleware/AuthMiddleware.php";

$auth = new AuthMiddleware();
$auth->requireRole('user');

$currentUser = $auth->getCurrentUser();

// Get power ranking data - same logic as old system
try {
    require_once __DIR__ . "/../config/database.php";
    $database = new Database();
    $conn = $database->getConnection();

    if (!$conn) {
        throw new Exception("Database connection failed");
    }

    $query = "SELECT 
                  CONCAT(users.first_name, ' ', users.last_name) AS fullname,
                  COUNT(algorithm.id) AS total_correct_answers
              FROM 
                  algorithm
              JOIN 
                  answers ON algorithm.ans_id = answers.id
              JOIN 
                  users ON algorithm.user_id = users.id
              WHERE 
                  answers.status = 'true'
              GROUP BY 
                  fullname, users.id
              ORDER BY 
                  total_correct_answers DESC";

    $stmt = $conn->prepare($query);
    $stmt->execute();
    $rankings = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Find current user's rank
    $currentUserRank = 0;
    $currentUserScore = 0;
    foreach ($rankings as $index => $ranking) {
        if (strtolower($ranking['fullname']) === strtolower($currentUser['first_name'] . ' ' . $currentUser['last_name'])) {
            $currentUserRank = $index + 1;
            $currentUserScore = $ranking['total_correct_answers'];
            break;
        }
    }
} catch (Exception $e) {
    error_log("Database error: " . $e->getMessage());
    $rankings = [];
    $currentUserRank = 0;
    $currentUserScore = 0;
}
?>

<!DOCTYPE html>
<html lang="sw">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daraja la Uwezo - Panda Digital</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= app_url('assets/css/style.css') ?>?v=9">
    <style>
        .ranking-card {
            background: white !important;
            color: var(--secondary-color) !important;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            border: 1px solid #e9ecef !important;
            border-top: 3px solid var(--primary-color) !important;
            border-radius: 15px;
            transition: transform 0.2s ease;
            margin-bottom: 20px;
        }

        .ranking-card:hover {
            transform: translateY(-2px);
        }

        .stats-card {
            background: white !important;
            color: var(--secondary-color) !important;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            border: 1px solid #e9ecef !important;
            border-top: 3px solid var(--primary-color) !important;
            border-radius: 15px;
            transition: transform 0.2s ease;
        }

        .stats-card:hover {
            transform: translateY(-2px);
        }

        .stats-card.success {
            border-top: 3px solid var(--secondary-color) !important;
        }

        .stats-card.info {
            border-top: 3px solid var(--primary-color) !important;
        }

        .ranking-table {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .ranking-table th {
            background: #f8f9fa;
            border: none;
            color: var(--secondary-color);
            font-weight: 600;
            padding: 15px;
        }

        .ranking-table td {
            padding: 15px;
            border: none;
            border-bottom: 1px solid #e9ecef;
            vertical-align: middle;
        }

        .ranking-table tbody tr:hover {
            background: rgba(255, 188, 59, 0.05);
        }

        .rank-badge {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-weight: bold;
            color: var(--secondary-color);
        }

        .crown-icon {
            width: 30px;
            height: 30px;
            object-fit: contain;
        }

        .current-user-row {
            background: rgba(255, 188, 59, 0.1) !important;
            border-left: 4px solid var(--primary-color);
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
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
                            <h1 class="h3 mb-0">Daraja la Uwezo</h1>
                            <p class="text-muted">Orodha ya wajibu maswali bora - Power Ranking</p>
                        </div>
                    </div>
                </div>

                <!-- Current User Stats -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card stats-card">
                            <div class="card-body text-center">
                                <h3 class="mb-1"><?php echo $currentUserRank > 0 ? $currentUserRank : 'N/A'; ?></h3>
                                <p class="mb-0">Cheo Chako</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card stats-card success">
                            <div class="card-body text-center">
                                <h3 class="mb-1"><?php echo $currentUserScore; ?></h3>
                                <p class="mb-0">Maswali Sahihi</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card stats-card info">
                            <div class="card-body text-center">
                                <h3 class="mb-1"><?php echo count($rankings); ?></h3>
                                <p class="mb-0">Washiriki Wote</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Power Ranking Table -->
                <div class="card ranking-card">
                    <div class="card-header border-0 pb-0">
                        <h4 class="mb-0">Orodha ya Wajibu Maswali Bora</h4>
                    </div>
                    <div class="card-body p-0">
                        <?php if (empty($rankings)): ?>
                            <div class="empty-state">
                                <h5>Hakuna data ya power ranking</h5>
                                <p class="text-muted">Hakuna washiriki waliopima uwezo wao bado</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table ranking-table mb-0">
                                    <thead>
                                        <tr>
                                            <th>Cheo</th>
                                            <th>Jina</th>
                                            <th>Maswali Sahihi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($rankings as $index => $ranking): ?>
                                            <?php
                                            $rank = $index + 1;
                                            $isCurrentUser = strtolower($ranking['fullname']) === strtolower($currentUser['first_name'] . ' ' . $currentUser['last_name']);
                                            $rowClass = $isCurrentUser ? 'current-user-row' : '';
                                            ?>
                                            <tr class="<?php echo $rowClass; ?>">
                                                <td>
                                                    <div class="rank-badge">
                                                        <?php echo $rank; ?>
                                                        <?php if ($rank <= 3): ?>
                                                            <img src="<?= app_url('assets/images/crowns/gold.png') ?>"
                                                                alt="Gold Crown" class="crown-icon">
                                                        <?php elseif ($rank <= 6): ?>
                                                            <img src="<?= app_url('assets/images/crowns/silver.png') ?>"
                                                                alt="Silver Crown" class="crown-icon">
                                                        <?php elseif ($rank <= 9): ?>
                                                            <img src="<?= app_url('assets/images/crowns/bronze.png') ?>"
                                                                alt="Bronze Crown" class="crown-icon">
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($ranking['fullname']); ?></strong>
                                                    <?php if ($isCurrentUser): ?>
                                                        <span class="badge bg-primary ms-2">Wewe</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <span class="fw-bold"><?php echo $ranking['total_correct_answers']; ?></span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- How It Works -->
                <div class="card ranking-card">
                    <div class="card-header border-0 pb-0">
                        <h4 class="mb-0">Jinsi ya Kupata Cheo Cha Juu</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <h6>Vipengele Muhimu</h6>
                                <ul class="list-unstyled">
                                    <li>Jibu maswali sahihi zaidi</li>
                                    <li>Kamilisha kozi na mazoezi</li>
                                    <li>Fanya quiz na mtihani</li>
                                </ul>
                            </div>
                            <div class="col-md-6 mb-3">
                                <h6>Viwango vya Tuzo</h6>
                                <ul class="list-unstyled">
                                    <li>Cheo 1-3: Tuzo ya Dhahabu</li>
                                    <li>Cheo 4-6: Tuzo ya Fedha</li>
                                    <li>Cheo 7-9: Tuzo ya Shaba</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
</body>

</html>