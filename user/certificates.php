<?php
require_once __DIR__ . "/../config/init.php";
require_once __DIR__ . "/../middleware/AuthMiddleware.php";

$auth = new AuthMiddleware();
$auth->requireRole('user');

$currentUser = $auth->getCurrentUser();

// Mock data for certificates - in real app this would come from database
$certificates = [
    [
        'id' => 1,
        'course_name' => 'Mafundisho ya Biashara ya Mtandaoni',
        'issue_date' => '2024-01-15',
        'completion_date' => '2024-01-10',
        'grade' => 'A+',
        'certificate_number' => 'CERT-001-2024',
        'status' => 'issued'
    ],
    [
        'id' => 2,
        'course_name' => 'Ufundi wa Digital Marketing',
        'issue_date' => '2024-02-20',
        'completion_date' => '2024-02-18',
        'grade' => 'A',
        'certificate_number' => 'CERT-002-2024',
        'status' => 'issued'
    ],
    [
        'id' => 3,
        'course_name' => 'Ufundi wa Social Media Management',
        'issue_date' => null,
        'completion_date' => '2024-03-05',
        'grade' => 'B+',
        'certificate_number' => 'CERT-003-2024',
        'status' => 'pending'
    ]
];
?>

<!DOCTYPE html>
<html lang="sw">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vyeti Vyagu - Panda Digital</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= app_url('assets/css/style.css') ?>?v=3">
    <style>
        .certificate-card {
            border: 2px solid var(--primary-color);
            border-radius: 15px;
            transition: all 0.3s ease;
            background: white;
        }

        .certificate-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(255, 188, 59, 0.3);
        }

        .certificate-header {
            background: var(--primary-color);
            color: black;
            border-radius: 13px 13px 0 0;
            padding: 20px;
            text-align: center;
        }

        .certificate-body {
            padding: 25px;
        }

        .certificate-number {
            background: var(--secondary-color);
            color: white;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
        }

        .grade-badge {
            background: var(--primary-color);
            color: black;
            padding: 10px 20px;
            border-radius: 25px;
            font-weight: bold;
            font-size: 1.2rem;
        }

        .status-badge {
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .status-issued {
            background: var(--primary-color);
            color: black;
        }

        .status-pending {
            background: var(--secondary-color);
            color: white;
        }

        .download-btn {
            background: var(--primary-color);
            color: black;
            border: none;
            border-radius: 25px;
            padding: 12px 25px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .download-btn:hover {
            background: var(--secondary-color);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
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
            $page_title = 'Vyeti Vyagu';
            include __DIR__ . '/../includes/user_top_nav.php';
            ?>

            <div class="content-wrapper">
                <!-- Header -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h1 class="h3 mb-0">
                            <i class="fas fa-certificate me-2" style="color: var(--primary-color);"></i>
                            Vyeti Vyagu
                        </h1>
                        <p class="text-muted">Tazama vyeti vyote ulivyopata kwa kukamilisha kozi</p>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card stats-card">
                            <div class="card-body text-center">
                                <i class="fas fa-certificate fa-2x mb-2"></i>
                                <h3 class="mb-1"><?php echo count($certificates); ?></h3>
                                <p class="mb-0">Jumla ya Vyeti</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card stats-card success">
                            <div class="card-body text-center">
                                <i class="fas fa-check-circle fa-2x mb-2"></i>
                                <h3 class="mb-1"><?php echo count(array_filter($certificates, function ($c) {
                                                        return $c['status'] == 'issued';
                                                    })); ?></h3>
                                <p class="mb-0">Vyeti Vilivyotolewa</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card stats-card info">
                            <div class="card-body text-center">
                                <i class="fas fa-clock fa-2x mb-2"></i>
                                <h3 class="mb-1"><?php echo count(array_filter($certificates, function ($c) {
                                                        return $c['status'] == 'pending';
                                                    })); ?></h3>
                                <p class="mb-0">Vyeti Vinasubiri</p>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if (empty($certificates)): ?>
                    <div class="empty-state">
                        <i class="fas fa-certificate"></i>
                        <h4>Huna vyeti bado</h4>
                        <p class="text-muted">Jisajili kwenye kozi na ukamilishe ili upate vyeti vyako</p>
                        <a href="<?= app_url('user/courses.php') ?>" class="btn download-btn">
                            <i class="fas fa-book me-2"></i>
                            Tazama Kozi
                        </a>
                    </div>
                <?php else: ?>
                    <!-- Certificates Grid -->
                    <div class="row">
                        <?php foreach ($certificates as $certificate): ?>
                            <div class="col-lg-6 col-md-12 mb-4">
                                <div class="certificate-card">
                                    <div class="certificate-header">
                                        <h5 class="mb-2"><?php echo htmlspecialchars($certificate['course_name']); ?></h5>
                                        <div class="certificate-number">
                                            <?php echo htmlspecialchars($certificate['certificate_number']); ?>
                                        </div>
                                    </div>

                                    <div class="certificate-body">
                                        <div class="row mb-3">
                                            <div class="col-6">
                                                <small class="text-muted">Tarehe ya Kukamilisha</small>
                                                <div class="fw-bold"><?php echo date('d/m/Y', strtotime($certificate['completion_date'])); ?></div>
                                            </div>
                                            <div class="col-6">
                                                <small class="text-muted">Alama</small>
                                                <div class="grade-badge"><?php echo htmlspecialchars($certificate['grade']); ?></div>
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <div class="col-6">
                                                <small class="text-muted">Tarehe ya Kutolewa</small>
                                                <div class="fw-bold">
                                                    <?php
                                                    if ($certificate['issue_date']) {
                                                        echo date('d/m/Y', strtotime($certificate['issue_date']));
                                                    } else {
                                                        echo 'Bado';
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <small class="text-muted">Hali</small>
                                                <div>
                                                    <span class="status-badge status-<?php echo $certificate['status']; ?>">
                                                        <?php
                                                        if ($certificate['status'] == 'issued') {
                                                            echo 'Imetolewa';
                                                        } else {
                                                            echo 'Inasubiri';
                                                        }
                                                        ?>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="text-center">
                                            <?php if ($certificate['status'] == 'issued'): ?>
                                                <button class="btn download-btn" onclick="downloadCertificate(<?php echo $certificate['id']; ?>)">
                                                    <i class="fas fa-download me-2"></i>
                                                    Pakua Vyeti
                                                </button>
                                            <?php else: ?>
                                                <button class="btn download-btn" disabled>
                                                    <i class="fas fa-clock me-2"></i>
                                                    Inasubiri
                                                </button>
                                            <?php endif; ?>
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