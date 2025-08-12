<?php
require_once __DIR__ . "/../config/init.php";
require_once __DIR__ . "/../middleware/AuthMiddleware.php";

$auth = new AuthMiddleware();
$auth->requireRole('user');

$currentUser = $auth->getCurrentUser();

// Mock data for certificate history - in real app this would come from database
$certificateHistory = [
    [
        'id' => 1,
        'course_name' => 'Mafundisho ya Biashara ya Mtandaoni',
        'issue_date' => '2024-01-15',
        'completion_date' => '2024-01-10',
        'grade' => 'A+',
        'certificate_number' => 'CERT-001-2024',
        'status' => 'issued',
        'download_count' => 3,
        'last_download' => '2024-03-10'
    ],
    [
        'id' => 2,
        'course_name' => 'Ufundi wa Digital Marketing',
        'issue_date' => '2024-02-20',
        'completion_date' => '2024-02-18',
        'grade' => 'A',
        'certificate_number' => 'CERT-002-2024',
        'status' => 'issued',
        'download_count' => 1,
        'last_download' => '2024-02-25'
    ],
    [
        'id' => 3,
        'course_name' => 'Ufundi wa Social Media Management',
        'issue_date' => '2024-03-05',
        'completion_date' => '2024-03-01',
        'grade' => 'B+',
        'certificate_number' => 'CERT-003-2024',
        'status' => 'issued',
        'download_count' => 0,
        'last_download' => null
    ]
];

$totalIssued = count(array_filter($certificateHistory, function ($c) {
    return $c['status'] == 'issued';
}));
$totalDownloads = array_sum(array_column($certificateHistory, 'download_count'));
$averageGrade = array_sum(array_map(function ($c) {
    return ord($c['grade'][0]);
}, $certificateHistory)) / count($certificateHistory);
$averageGrade = chr(round($averageGrade));
?>

<!DOCTYPE html>
<html lang="sw">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historia ya Vyeti - Panda Digital</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= app_url('assets/css/style.css') ?>?v=2">
    <style>
        .history-card {
            border: 2px solid var(--primary-color);
            border-radius: 15px;
            transition: all 0.3s ease;
            background: white;
            margin-bottom: 20px;
        }

        .history-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(255, 188, 59, 0.2);
        }

        .history-header {
            background: var(--primary-color);
            color: black;
            border-radius: 13px 13px 0 0;
            padding: 20px;
        }

        .history-body {
            padding: 20px;
        }

        .certificate-item {
            border-left: 4px solid var(--primary-color);
            padding: 20px;
            margin-bottom: 20px;
            background: #f8f9fa;
            border-radius: 0 10px 10px 0;
            transition: all 0.3s ease;
        }

        .certificate-item:hover {
            background: white;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .grade-badge {
            background: var(--primary-color);
            color: black;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 1.1rem;
        }

        .download-info {
            background: var(--secondary-color);
            color: white;
            padding: 10px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
            margin-top: 15px;
        }

        .download-btn {
            background: var(--primary-color);
            color: black;
            border: none;
            border-radius: 20px;
            padding: 8px 20px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .download-btn:hover {
            background: var(--secondary-color);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .timeline-item {
            position: relative;
            padding-left: 30px;
            margin-bottom: 20px;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            width: 12px;
            height: 12px;
            background: var(--primary-color);
            border-radius: 50%;
        }

        .timeline-item::after {
            content: '';
            position: absolute;
            left: 5px;
            top: 12px;
            width: 2px;
            height: calc(100% + 8px);
            background: var(--primary-color);
        }

        .timeline-item:last-child::after {
            display: none;
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
            $page_title = 'Historia ya Vyeti';
            include __DIR__ . '/../includes/user_top_nav.php';
            ?>

            <div class="content-wrapper">
                <!-- Header -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h1 class="h3 mb-0">
                            <i class="fas fa-history me-2" style="color: var(--primary-color);"></i>
                            Historia ya Upakuaji Wa Vyeti Vyako
                        </h1>
                        <p class="text-muted">Tazama historia kamili ya vyeti vyote ulivyopata na vipakuliwa</p>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card stats-card">
                            <div class="card-body text-center">
                                <i class="fas fa-certificate fa-2x mb-2"></i>
                                <h3 class="mb-1"><?php echo $totalIssued; ?></h3>
                                <p class="mb-0">Vyeti Vilivyotolewa</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stats-card success">
                            <div class="card-body text-center">
                                <i class="fas fa-download fa-2x mb-2"></i>
                                <h3 class="mb-1"><?php echo $totalDownloads; ?></h3>
                                <p class="mb-0">Jumla ya Upakuaji</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stats-card info">
                            <div class="card-body text-center">
                                <i class="fas fa-star fa-2x mb-2"></i>
                                <h3 class="mb-1"><?php echo $averageGrade; ?></h3>
                                <p class="mb-0">Wastani wa Alama</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stats-card">
                            <div class="card-body text-center">
                                <i class="fas fa-calendar fa-2x mb-2"></i>
                                <h3 class="mb-1"><?php echo count($certificateHistory); ?></h3>
                                <p class="mb-0">Kozi Zilizokamilika</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Certificate History -->
                <div class="history-card">
                    <div class="history-header">
                        <h4 class="mb-0">
                            <i class="fas fa-list me-2"></i>
                            Historia ya Vyeti
                        </h4>
                    </div>
                    <div class="history-body">
                        <?php if (empty($certificateHistory)): ?>
                            <div class="empty-state">
                                <i class="fas fa-history"></i>
                                <h5>Huna historia ya vyeti bado</h5>
                                <p class="text-muted">Jisajili kwenye kozi na ukamilishe ili upate vyeti na historia yako</p>
                                <a href="<?= app_url('user/courses.php') ?>" class="btn download-btn">
                                    <i class="fas fa-book me-2"></i>
                                    Tazama Kozi
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="timeline">
                                <?php foreach ($certificateHistory as $index => $certificate): ?>
                                    <div class="timeline-item">
                                        <div class="certificate-item">
                                            <div class="d-flex justify-content-between align-items-start mb-3">
                                                <div>
                                                    <h5 class="mb-1"><?php echo htmlspecialchars($certificate['course_name']); ?></h5>
                                                    <div class="d-flex align-items-center">
                                                        <span class="grade-badge me-3"><?php echo htmlspecialchars($certificate['grade']); ?></span>
                                                        <small class="text-muted">
                                                            <i class="fas fa-hashtag me-1"></i>
                                                            <?php echo htmlspecialchars($certificate['certificate_number']); ?>
                                                        </small>
                                                    </div>
                                                </div>
                                                <div class="text-end">
                                                    <small class="text-muted">
                                                        <i class="fas fa-calendar me-1"></i>
                                                        <?php echo date('d/m/Y', strtotime($certificate['issue_date'])); ?>
                                                    </small>
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <small class="text-muted">Tarehe ya Kukamilisha</small>
                                                    <div class="fw-bold"><?php echo date('d/m/Y', strtotime($certificate['completion_date'])); ?></div>
                                                </div>
                                                <div class="col-md-6">
                                                    <small class="text-muted">Tarehe ya Kutolewa</small>
                                                    <div class="fw-bold"><?php echo date('d/m/Y', strtotime($certificate['issue_date'])); ?></div>
                                                </div>
                                            </div>

                                            <div class="download-info">
                                                <div class="row text-center">
                                                    <div class="col-6">
                                                        <div class="fw-bold"><?php echo $certificate['download_count']; ?></div>
                                                        <small>Mara za Kupakua</small>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="fw-bold">
                                                            <?php
                                                            if ($certificate['last_download']) {
                                                                echo date('d/m/Y', strtotime($certificate['last_download']));
                                                            } else {
                                                                echo 'Hajapakuliwa';
                                                            }
                                                            ?>
                                                        </div>
                                                        <small>Upakuaji wa Mwisho</small>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="text-center mt-3">
                                                <button class="btn download-btn" onclick="downloadCertificate(<?php echo $certificate['id']; ?>)">
                                                    <i class="fas fa-download me-2"></i>
                                                    Pakua Vyeti
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Summary -->
                <div class="history-card">
                    <div class="history-header">
                        <h4 class="mb-0">
                            <i class="fas fa-chart-pie me-2"></i>
                            Muhtasari wa Historia
                        </h4>
                    </div>
                    <div class="history-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Mafanikio Yako</h6>
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-check-circle me-2" style="color: var(--primary-color);"></i>Umejikamilisha kozi <?php echo count($certificateHistory); ?></li>
                                    <li><i class="fas fa-check-circle me-2" style="color: var(--primary-color);"></i>Umeupakua vyeti <?php echo $totalDownloads; ?> mara</li>
                                    <li><i class="fas fa-check-circle me-2" style="color: var(--primary-color);"></i>Wastani wa alama: <?php echo $averageGrade; ?></li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6>Mipango ya Baadae</h6>
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-arrow-right me-2" style="color: var(--primary-color);"></i>Endelea kujifunza kozi mpya</li>
                                    <li><i class="fas fa-arrow-right me-2" style="color: var(--primary-color);"></i>Boresha alama zako</li>
                                    <li><i class="fas fa-arrow-right me-2" style="color: var(--primary-color);"></i>Shiriki vyeti vyako</li>
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


    <script>
        function downloadCertificate(certificateId) {
            // In a real app, this would generate and download the PDF
            alert('Vyeti litapakuliwa hivi karibuni!');
        }
    </script>
</body>

</html>