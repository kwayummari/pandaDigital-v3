<?php
require_once __DIR__ . "/../config/init.php";
require_once __DIR__ . "/../middleware/AuthMiddleware.php";

$auth = new AuthMiddleware();
$auth->requireRole('user');

$currentUser = $auth->getCurrentUser();

// Mock data for skill levels - in real app this would come from database
$skillLevels = [
    [
        'skill' => 'Digital Marketing',
        'level' => 85,
        'grade' => 'A',
        'courses_completed' => 3,
        'last_updated' => '2024-03-15',
        'trend' => 'up'
    ],
    [
        'skill' => 'Social Media Management',
        'level' => 72,
        'grade' => 'B+',
        'courses_completed' => 2,
        'last_updated' => '2024-03-10',
        'trend' => 'up'
    ],
    [
        'skill' => 'E-commerce',
        'level' => 45,
        'grade' => 'C+',
        'courses_completed' => 1,
        'last_updated' => '2024-02-28',
        'trend' => 'stable'
    ],
    [
        'skill' => 'Content Creation',
        'level' => 90,
        'grade' => 'A+',
        'courses_completed' => 4,
        'last_updated' => '2024-03-12',
        'trend' => 'up'
    ],
    [
        'skill' => 'SEO',
        'level' => 38,
        'grade' => 'C',
        'courses_completed' => 1,
        'last_updated' => '2024-02-20',
        'trend' => 'stable'
    ]
];

$overallLevel = array_sum(array_column($skillLevels, 'level')) / count($skillLevels);
$overallGrade = $overallLevel >= 90 ? 'A+' : ($overallLevel >= 80 ? 'A' : ($overallLevel >= 70 ? 'B+' : ($overallLevel >= 60 ? 'B' : ($overallLevel >= 50 ? 'C+' : 'C'))));
$totalCourses = array_sum(array_column($skillLevels, 'courses_completed'));
$improvingSkills = count(array_filter($skillLevels, function ($s) {
    return $s['trend'] == 'up';
}));
?>

<!DOCTYPE html>
<html lang="sw">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daraja la Uwezo - Panda Digital</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= app_url('assets/css/style.css') ?>?v=2">
    <style>
        .skill-card {
            border: 2px solid var(--primary-color);
            border-radius: 15px;
            transition: all 0.3s ease;
            background: white;
            margin-bottom: 20px;
        }

        .skill-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(255, 188, 59, 0.2);
        }

        .skill-header {
            background: var(--primary-color);
            color: black;
            border-radius: 13px 13px 0 0;
            padding: 20px;
        }

        .skill-body {
            padding: 20px;
        }

        .overall-level-display {
            background: var(--secondary-color);
            color: white;
            padding: 30px;
            border-radius: 15px;
            text-align: center;
            margin-bottom: 30px;
        }

        .level-circle {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: var(--primary-color);
            color: black;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 2.5rem;
            font-weight: bold;
        }

        .skill-item {
            border-left: 4px solid var(--primary-color);
            padding: 20px;
            margin-bottom: 20px;
            background: #f8f9fa;
            border-radius: 0 10px 10px 0;
            transition: all 0.3s ease;
        }

        .skill-item:hover {
            background: white;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .skill-progress {
            background: #e9ecef;
            border-radius: 10px;
            height: 20px;
            overflow: hidden;
            margin: 15px 0;
        }

        .skill-progress-bar {
            height: 100%;
            background: var(--primary-color);
            border-radius: 10px;
            transition: width 1s ease;
        }

        .grade-badge {
            background: var(--primary-color);
            color: black;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 1.1rem;
        }

        .trend-indicator {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .trend-up {
            background: #28a745;
            color: white;
        }

        .trend-stable {
            background: var(--secondary-color);
            color: white;
        }

        .trend-down {
            background: #dc3545;
            color: white;
        }

        .improvement-tips {
            background: rgba(255, 188, 59, 0.1);
            border: 1px solid var(--primary-color);
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }

        .empty-state i {
            font-size: 4rem;
            color: var(--primary-color);
            margin-bottom: 20px;
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
            $page_title = 'Daraja la Uwezo';
            include __DIR__ . '/../includes/user_top_nav.php';
            ?>

            <div class="content-wrapper">
                <!-- Header -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h1 class="h3 mb-0">
                            <i class="fas fa-star me-2" style="color: var(--primary-color);"></i>
                            Daraja la Uwezo
                        </h1>
                        <p class="text-muted">Tazama na uone maendeleo ya uwezo wako katika fani mbalimbali</p>
                    </div>
                </div>

                <!-- Overall Level Display -->
                <div class="overall-level-display">
                    <div class="level-circle"><?php echo $overallGrade; ?></div>
                    <h2 class="mb-2">Daraja la Uwezo: <?php echo $overallGrade; ?></h2>
                    <p class="mb-0">Uwezo wako wa jumla: <?php echo round($overallLevel); ?>%</p>
                </div>

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card stats-card">
                            <div class="card-body text-center">
                                <i class="fas fa-star fa-2x mb-2"></i>
                                <h3 class="mb-1"><?php echo count($skillLevels); ?></h3>
                                <p class="mb-0">Fani Zilizopimwa</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card stats-card success">
                            <div class="card-body text-center">
                                <i class="fas fa-arrow-up fa-2x mb-2"></i>
                                <h3 class="mb-1"><?php echo $improvingSkills; ?></h3>
                                <p class="mb-0">Fani Zinazoboreshwa</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card stats-card info">
                            <div class="card-body text-center">
                                <i class="fas fa-book fa-2x mb-2"></i>
                                <h3 class="mb-1"><?php echo $totalCourses; ?></h3>
                                <p class="mb-0">Kozi Zilizokamilika</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Skill Levels -->
                <div class="skill-card">
                    <div class="skill-header">
                        <h4 class="mb-0">
                            <i class="fas fa-chart-line me-2"></i>
                            Viwango vya Uwezo
                        </h4>
                    </div>
                    <div class="skill-body">
                        <?php if (empty($skillLevels)): ?>
                            <div class="empty-state">
                                <i class="fas fa-star"></i>
                                <h5>Huna viwango vya uwezo bado</h5>
                                <p class="text-muted">Jisajili kwenye kozi na ukamilishe ili upate viwango vya uwezo</p>
                                <a href="<?= app_url('user/courses.php') ?>" class="btn btn-primary">
                                    <i class="fas fa-book me-2"></i>
                                    Tazama Kozi
                                </a>
                            </div>
                        <?php else: ?>
                            <?php foreach ($skillLevels as $skill): ?>
                                <div class="skill-item">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div>
                                            <h5 class="mb-1"><?php echo htmlspecialchars($skill['skill']); ?></h5>
                                            <div class="d-flex align-items-center">
                                                <span class="grade-badge me-3"><?php echo htmlspecialchars($skill['grade']); ?></span>
                                                <span class="trend-indicator trend-<?php echo $skill['trend']; ?>">
                                                    <?php
                                                    if ($skill['trend'] == 'up') {
                                                        echo '<i class="fas fa-arrow-up me-1"></i>Inaboreshwa';
                                                    } elseif ($skill['trend'] == 'down') {
                                                        echo '<i class="fas fa-arrow-down me-1"></i>Inapungua';
                                                    } else {
                                                        echo '<i class="fas fa-minus me-1"></i>Imara';
                                                    }
                                                    ?>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            <div class="fw-bold"><?php echo $skill['level']; ?>%</div>
                                            <small class="text-muted">
                                                <i class="fas fa-calendar me-1"></i>
                                                <?php echo date('d/m/Y', strtotime($skill['last_updated'])); ?>
                                            </small>
                                        </div>
                                    </div>

                                    <div class="skill-progress">
                                        <div class="skill-progress-bar" style="width: <?php echo $skill['level']; ?>%"></div>
                                    </div>

                                    <div class="row text-center">
                                        <div class="col-6">
                                            <div class="fw-bold"><?php echo $skill['courses_completed']; ?></div>
                                            <small class="text-muted">Kozi Zilizokamilika</small>
                                        </div>
                                        <div class="col-6">
                                            <div class="fw-bold"><?php echo $skill['level']; ?>%</div>
                                            <small class="text-muted">Uwezo wa Sasa</small>
                                        </div>
                                    </div>

                                    <?php if ($skill['level'] < 80): ?>
                                        <div class="improvement-tips">
                                            <h6><i class="fas fa-lightbulb me-2" style="color: var(--primary-color);"></i>Ushauri wa Kuboresha</h6>
                                            <ul class="mb-0">
                                                <li>Jisajili kwenye kozi zaidi za <?php echo htmlspecialchars($skill['skill']); ?></li>
                                                <li>Fanya mazoezi ya vitendo</li>
                                                <li>Uliza maswali kwa wataalam</li>
                                            </ul>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Recommendations -->
                <div class="skill-card">
                    <div class="skill-header">
                        <h4 class="mb-0">
                            <i class="fas fa-lightbulb me-2"></i>
                            Mapendekezo ya Kuboresha
                        </h4>
                    </div>
                    <div class="skill-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <h6>Fani za Kipaumbele</h6>
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-arrow-up me-2" style="color: var(--primary-color);"></i>SEO - Uwezo wa sasa: 38%</li>
                                    <li><i class="fas fa-arrow-up me-2" style="color: var(--primary-color);"></i>E-commerce - Uwezo wa sasa: 45%</li>
                                    <li><i class="fas fa-arrow-up me-2" style="color: var(--primary-color);"></i>Content Creation - Uwezo wa sasa: 90%</li>
                                </ul>
                            </div>
                            <div class="col-md-6 mb-3">
                                <h6>Vitendo vya Haraka</h6>
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-check me-2" style="color: var(--primary-color);"></i>Jisajili kwenye kozi mpya</li>
                                    <li><i class="fas fa-check me-2" style="color: var(--primary-color);"></i>Fanya mazoezi ya vitendo</li>
                                    <li><i class="fas fa-check me-2" style="color: var(--primary-color);"></i>Uliza maswali kwa wataalam</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Sidebar Toggle Script -->

</body>

</html>