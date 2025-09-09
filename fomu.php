<?php
require_once 'config/init.php';

// Initialize message variable
$message = '';
$messageType = '';

// Handle reset to go back to initial question
if (isset($_GET['reset'])) {
    unset($show_login);
    unset($show_register);
}

// Display session message if exists
if (isset($_SESSION['success_message'])) {
    $message = $_SESSION['success_message'];
    $messageType = 'success';
    unset($_SESSION['success_message']);
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['has_account'])) {
        if ($_POST['has_account'] === 'yes') {
            // Show login form
            $show_login = true;
            $show_register = false;
        } else {
            // Show registration form
            $show_login = false;
            $show_register = true;
        }
    } elseif (isset($_POST['login'])) {
        // Handle login and expert application
        $email = trim($_POST['email']);
        $password = $_POST['password'];
        $bio = trim($_POST['bio']);

        try {
            $conn = Database::getInstance()->getConnection();
            $stmt = $conn->prepare("SELECT id, pass FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $result = $stmt->fetch();

            if ($result && password_verify($password, $result['pass'])) {
                // Update role to expert and add bio
                $updateStmt = $conn->prepare("UPDATE users SET role = 'expert', bio = ?, expert_authorization = 0 WHERE id = ?");
                if ($updateStmt->execute([$bio, $result['id']])) {
                    $_SESSION['success_message'] = "Ombi lako la kuwa mtaalam limetumwa kikamilifu! Subiri kuidhinishwa.";
                    header("Location: uliza-swali.php");
                    exit();
                } else {
                    $message = "Kuna hitilafu, jaribu tena";
                    $messageType = 'danger';
                }
            } else {
                $message = "Barua pepe au nywila sio sahihi";
                $messageType = 'danger';
            }
        } catch (PDOException $e) {
            error_log("Login error: " . $e->getMessage());
            $message = "Kuna hitilafu, jaribu tena";
            $messageType = 'danger';
        }
    } elseif (isset($_POST['register'])) {
        // Handle new registration
        $email = trim($_POST['email']);
        $password = $_POST['password'];
        $firstName = trim($_POST['first_name']);
        $lastName = trim($_POST['last_name']);
        $bio = trim($_POST['bio']);
        $role = 'expert';
        $status = 'free';

        // Handle file upload if provided
        $profile_photo = '';
        if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] == 0) {
            $target_dir = "uploads/ProfilePhotos/";
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0755, true);
            }

            $file_extension = pathinfo($_FILES['profile_photo']['name'], PATHINFO_EXTENSION);
            $profile_photo = "expert_" . time() . "." . $file_extension;
            $target_file = $target_dir . $profile_photo;

            if (move_uploaded_file($_FILES['profile_photo']['tmp_name'], $target_file)) {
                // File uploaded successfully
            } else {
                $message = "Hitilafu ya kupakia picha, jaribu tena";
                $messageType = 'danger';
            }
        }

        try {
            $conn = Database::getInstance()->getConnection();
            $stmt = $conn->prepare("INSERT INTO users (email, pass, first_name, last_name, role, status, profile_photo, bio, expert_authorization) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0)");
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            if ($stmt->execute([$email, $hashedPassword, $firstName, $lastName, $role, $status, $profile_photo, $bio])) {
                $_SESSION['success_message'] = "Ombi lako la kuwa mtaalam limetumwa kikamilifu! Subiri kuidhinishwa.";
                header("Location: uliza-swali.php");
                exit();
            } else {
                $message = "Kuna hitilafu, jaribu tena";
                $messageType = 'danger';
            }
        } catch (PDOException $e) {
            error_log("Registration error: " . $e->getMessage());
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                $message = "Barua pepe tayari imesajiliwa";
            } else {
                $message = "Kuna hitilafu, jaribu tena";
            }
            $messageType = 'danger';
        }
    }
}

$pageTitle = 'Jisajili Kama Mtaalamu - ' . $appConfig['name'];

include 'includes/header.php';
?>

<!-- Page Header -->
<section class="page-header" style="background-image: url('<?= asset('images/banner/new-banner2.jpg') ?>'); background-size: cover; background-position: center; background-repeat: no-repeat; position: relative;">
    <div class="overlay" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(0, 0, 0, 0.6);"></div>
    <div class="container" style="position: relative; z-index: 2;">
        <div class="row align-items-center">
            <div class="col-lg-8" data-aos="fade-right">
                <h1 class="page-title">Jisajili Kama Mtaalamu</h1>
                <p class="page-subtitle">Jiunge na timu yetu ya wataalamu na usaidie wengine kutatua changamoto zao</p>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= app_url() ?>">Nyumbani</a></li>
                        <li class="breadcrumb-item"><a href="<?= app_url('uliza-swali.php') ?>">Uliza Swali</a></li>
                        <li class="breadcrumb-item active">Jisajili Kama Mtaalamu</li>
                    </ol>
                </nav>
            </div>
            <div class="col-lg-4 text-center" data-aos="fade-left">
                <div class="header-icon">
                    <i class="fas fa-user-graduate fa-4x text-white"></i>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Form Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <?php if (!empty($message)): ?>
                    <div class="alert alert-<?= $messageType === 'success' ? 'success' : 'danger' ?> alert-dismissible fade show" role="alert">
                        <?= htmlspecialchars($message) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (!isset($show_login) && !isset($show_register)): ?>
                    <!-- Initial Question -->
                    <div class="card shadow-sm" data-aos="fade-up">
                        <div class="card-body text-center p-5">
                            <h3 class="mb-4">Je, una akaunti?</h3>
                            <p class="text-muted mb-4">Chagua njia unayotaka kujiunga na timu yetu ya wataalamu</p>
                            <form method="post" class="d-inline">
                                <button type="submit" name="has_account" value="yes" class="btn btn-primary btn-lg me-3">Ndiyo, Nina Akaunti</button>
                                <button type="submit" name="has_account" value="no" class="btn btn-outline-primary btn-lg">Hapana, Nipatie Akaunti</button>
                            </form>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (isset($show_login) && $show_login): ?>
                    <!-- Login Form -->
                    <div class="card shadow-sm" data-aos="fade-up">
                        <div class="card-header bg-primary text-white">
                            <h4 class="mb-0">Ingia na Omba Kuwa Mtaalamu</h4>
                        </div>
                        <div class="card-body p-4">
                            <form method="post">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Barua Pepe</label>
                                    <input type="email" id="email" name="email" class="form-control" placeholder="Ingiza barua pepe yako" required>
                                </div>
                                <div class="mb-3">
                                    <label for="password" class="form-label">Nywila</label>
                                    <input type="password" id="password" name="password" class="form-control" placeholder="Ingiza nywila yako" required>
                                </div>
                                <div class="mb-3">
                                    <label for="bio" class="form-label">Maelezo Yako <span class="text-danger">*</span></label>
                                    <textarea id="bio" name="bio" class="form-control" rows="5" placeholder="Andika maelezo yako kama mtaalam, uzoefu wako, na nyanja unazofahamu..." required></textarea>
                                    <div class="form-text">Maelezo haya yataonekana kwa watumiaji wengine</div>
                                </div>
                                <div class="d-grid">
                                    <button type="submit" name="login" class="btn btn-primary btn-lg">Ingia na Omba Kuwa Mtaalamu</button>
                                </div>
                            </form>
                            <div class="text-center mt-3">
                                <a href="?reset=1" class="text-decoration-none">Rudi Nyuma</a>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (isset($show_register) && $show_register): ?>
                    <!-- Registration Form -->
                    <div class="card shadow-sm" data-aos="fade-up">
                        <div class="card-header bg-primary text-white">
                            <h4 class="mb-0">Jisajili na Omba Kuwa Mtaalamu</h4>
                        </div>
                        <div class="card-body p-4">
                            <form method="post" enctype="multipart/form-data">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="first_name" class="form-label">Jina la Kwanza</label>
                                        <input type="text" id="first_name" name="first_name" class="form-control" placeholder="Ingiza jina la kwanza" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="last_name" class="form-label">Jina la Mwisho</label>
                                        <input type="text" id="last_name" name="last_name" class="form-control" placeholder="Ingiza jina la mwisho" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label">Barua Pepe</label>
                                    <input type="email" id="email" name="email" class="form-control" placeholder="Ingiza barua pepe yako" required>
                                </div>
                                <div class="mb-3">
                                    <label for="password" class="form-label">Nywila</label>
                                    <input type="password" id="password" name="password" class="form-control" placeholder="Unda nywila ya nguvu" required>
                                    <div class="form-text">Nywila lazima iwe na herufi 8 au zaidi</div>
                                </div>
                                <div class="mb-3">
                                    <label for="profile_photo" class="form-label">Picha ya Profaili</label>
                                    <input type="file" id="profile_photo" name="profile_photo" class="form-control" accept="image/*">
                                    <div class="form-text">Picha ya profaili (si lazima) - JPG, PNG, au GIF</div>
                                </div>
                                <div class="mb-3">
                                    <label for="bio" class="form-label">Maelezo Yako <span class="text-danger">*</span></label>
                                    <textarea id="bio" name="bio" class="form-control" rows="5" placeholder="Andika maelezo yako kama mtaalam, uzoefu wako, na nyanja unazofahamu..." required></textarea>
                                    <div class="form-text">Maelezo haya yataonekana kwa watumiaji wengine</div>
                                </div>
                                <div class="d-grid">
                                    <button type="submit" name="register" class="btn btn-primary btn-lg">Jisajili na Omba Kuwa Mtaalamu</button>
                                </div>
                            </form>
                            <div class="text-center mt-3">
                                <a href="?reset=1" class="text-decoration-none">Rudi Nyuma</a>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Benefits Section -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center mb-5" data-aos="fade-up">
                <h2 class="section-title">Kwa Nini Kuwa Mtaalamu?</h2>
                <p class="section-subtitle">Faidha unazopata unapokuwa mtaalamu wa Panda Digital</p>
            </div>
        </div>
        <div class="row g-4">
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="mb-3">
                            <i class="fas fa-users fa-3x text-primary"></i>
                        </div>
                        <h5 class="card-title">Ushiriki wa Jamii</h5>
                        <p class="card-text">Jiunge na jamii ya wataalamu na ufanye networking na wengine</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="mb-3">
                            <i class="fas fa-star fa-3x text-primary"></i>
                        </div>
                        <h5 class="card-title">Uthibitisho wa Uzoefu</h5>
                        <p class="card-text">Pata uthibitisho wa uzoefu wako na uweze kujenga sifa</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="mb-3">
                            <i class="fas fa-handshake fa-3x text-primary"></i>
                        </div>
                        <h5 class="card-title">Fursa za Biashara</h5>
                        <p class="card-text">Pata fursa za kusaidia wengine na kuongeza mapato yako</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Include Footer -->
<?php include 'includes/footer.php'; ?>

<!-- Custom CSS -->
<style>
    /* Page header styles - same as fursa.php */
    .page-header {
        padding: 120px 0 80px;
        color: white;
    }

    .page-title {
        font-size: 3rem;
        font-weight: 700;
        margin-bottom: 1rem;
    }

    .page-subtitle {
        font-size: 1.2rem;
        margin-bottom: 2rem;
        opacity: 0.9;
    }

    .header-icon {
        opacity: 0.8;
    }

    /* Section title styles */
    .section-title {
        margin-bottom: 3rem;
    }

    .section-title h2 {
        color: #333;
        font-weight: 600;
    }

    .section-subtitle {
        color: #6c757d;
        font-size: 1.1rem;
    }

    /* Breadcrumb styles */
    .breadcrumb {
        background: transparent;
        padding: 0;
        margin: 0;
    }

    .breadcrumb-item a {
        color: rgba(255, 255, 255, 0.8);
        text-decoration: none;
    }

    .breadcrumb-item.active {
        color: white;
    }

    .breadcrumb-item+.breadcrumb-item::before {
        color: rgba(255, 255, 255, 0.6);
    }

    /* Form styles */
    .card {
        border: none;
        border-radius: 15px;
    }

    .card-header {
        border-radius: 15px 15px 0 0 !important;
        border: none;
    }

    .form-control {
        border-radius: 10px;
        border: 1px solid #dee2e6;
        padding: 12px 15px;
    }

    .form-control:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
    }

    .btn {
        border-radius: 10px;
        padding: 12px 30px;
        font-weight: 600;
    }

    .btn-lg {
        padding: 15px 40px;
    }
</style>