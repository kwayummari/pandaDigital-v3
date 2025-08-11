<?php
require_once __DIR__ . "/../middleware/AuthMiddleware.php";
require_once __DIR__ . "/../models/Course.php";

$auth = new AuthMiddleware();
$auth->requireRole('admin');

$currentUser = $auth->getCurrentUser();
$courseModel = new Course();

$success = $error = '';

// Handle form submission
if ($_POST && isset($_POST['add_video'])) {
    $courseId = $_POST['course_id'];
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $videoUrl = trim($_POST['video_url']);
    $duration = trim($_POST['duration']);

    // Validation
    if (empty($courseId) || empty($title) || empty($videoUrl)) {
        $error = "Tafadhali jaza sehemu zote muhimu (Kozi, Jina la Video, na URL ya Video).";
    } elseif (strlen($title) < 3) {
        $error = "Jina la video lazima liwe na herufi 3 au zaidi.";
    } elseif (strlen($videoUrl) < 10) {
        $error = "URL ya video si sahihi.";
    } else {
        // Add video
        $result = $courseModel->addVideo($courseId, $title, $description, $videoUrl, $duration);
        if ($result) {
            $success = "Video imeongezwa kikamilifu!";
            // Clear form data
            $title = $description = $videoUrl = $duration = '';
            $courseId = '';
        } else {
            $error = "Imefeli kuongeza video. Tafadhali jaribu tena.";
        }
    }
}

// Get all courses for selection
$courses = $courseModel->getAllCourses();
?>

<!DOCTYPE html>
<html lang="sw">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ongeza Video - Panda Digital</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #662e91;
            --secondary-color: #FFC10B;
            --accent-color: #e74c3c;
            --success-color: #27ae60;
            --warning-color: #f39c12;
            --info-color: #3498db;
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

        .btn-primary-custom {
            background: var(--primary-color);
            border: none;
            border-radius: 25px;
            padding: 12px 30px;
            font-weight: 600;
            font-size: 1.1rem;
        }

        .btn-primary-custom:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .form-control,
        .form-select {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 12px 15px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(102, 46, 145, 0.25);
        }

        .form-label {
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 8px;
        }

        .alert {
            border-radius: 10px;
            border: none;
            padding: 15px 20px;
        }

        .video-preview {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            margin-bottom: 30px;
        }

        .help-text {
            font-size: 0.9rem;
            color: #6c757d;
            margin-top: 5px;
        }

        .required {
            color: var(--accent-color);
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
                <a class="nav-link" href="/admin/courses.php">
                    <i class="fas fa-book me-1"></i> Kozi
                </a>
                <a class="nav-link active" href="/admin/videos.php">
                    <i class="fas fa-video me-1"></i> Video
                </a>
                <a class="nav-link" href="/admin/users.php">
                    <i class="fas fa-users me-1"></i> Watumiaji
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
                        <i class="fas fa-plus text-primary me-2"></i>
                        Ongeza Video Mpya
                    </h1>
                    <p class="text-muted">Ongeza video mpya kwenye kozi</p>
                </div>
            </div>

            <!-- Video Preview Section -->
            <div class="video-preview">
                <i class="fas fa-video fa-3x mb-3"></i>
                <h3>Ongeza Video Mpya</h3>
                <p class="mb-0">Jaza maelezo ya video ili wanafunzi waweze kujifunza</p>
            </div>

            <!-- Alerts -->
            <?php if ($success): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    <?php echo htmlspecialchars($success); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <?php echo htmlspecialchars($error); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Add Video Form -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-edit me-2"></i>
                        Maelezo ya Video
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <div class="row">
                            <!-- Course Selection -->
                            <div class="col-md-6 mb-3">
                                <label for="course_id" class="form-label">
                                    Chagua Kozi <span class="required">*</span>
                                </label>
                                <select class="form-select" id="course_id" name="course_id" required>
                                    <option value="">-- Chagua Kozi --</option>
                                    <?php foreach ($courses as $course): ?>
                                        <option value="<?php echo $course['id']; ?>"
                                            <?php echo (isset($_POST['course_id']) && $_POST['course_id'] == $course['id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($course['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="help-text">Chagua kozi ambayo video itakuwa sehemu yake</div>
                            </div>

                            <!-- Video Title -->
                            <div class="col-md-6 mb-3">
                                <label for="title" class="form-label">
                                    Jina la Video <span class="required">*</span>
                                </label>
                                <input type="text" class="form-control" id="title" name="title"
                                    value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>"
                                    placeholder="Mfano: Utangulizi wa HTML" required>
                                <div class="help-text">Jina la video linaloelezea yaliyomo</div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Video URL -->
                            <div class="col-md-8 mb-3">
                                <label for="video_url" class="form-label">
                                    URL ya Video <span class="required">*</span>
                                </label>
                                <input type="url" class="form-control" id="video_url" name="video_url"
                                    value="<?php echo htmlspecialchars($_POST['video_url'] ?? ''); ?>"
                                    placeholder="https://example.com/video.mp4" required>
                                <div class="help-text">Link ya video (YouTube, Vimeo, au faili ya video)</div>
                            </div>

                            <!-- Video Duration -->
                            <div class="col-md-4 mb-3">
                                <label for="duration" class="form-label">
                                    Muda wa Video
                                </label>
                                <input type="text" class="form-control" id="duration" name="duration"
                                    value="<?php echo htmlspecialchars($_POST['duration'] ?? ''); ?>"
                                    placeholder="Mfano: 15:30">
                                <div class="help-text">Muda wa video (dakika:sekunde)</div>
                            </div>
                        </div>

                        <!-- Video Description -->
                        <div class="mb-4">
                            <label for="description" class="form-label">
                                Maelezo ya Video
                            </label>
                            <textarea class="form-control" id="description" name="description" rows="4"
                                placeholder="Elezea kwa ufupi yaliyomo kwenye video hii..."><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                            <div class="help-text">Maelezo mafupi ya yaliyomo kwenye video</div>
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="/admin/videos.php" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>
                                Rudi kwenye Video
                            </a>
                            <button type="submit" name="add_video" class="btn btn-primary-custom">
                                <i class="fas fa-plus me-2"></i>
                                Ongeza Video
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Help Section -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-question-circle text-info me-2"></i>
                        Vidokezo vya Kuongeza Video
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6><i class="fas fa-lightbulb text-warning me-2"></i>Jinsi ya Kuongeza Video</h6>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-check text-success me-2"></i>Chagua kozi sahihi</li>
                                <li><i class="fas fa-check text-success me-2"></i>Jina la video liwe wazi</li>
                                <li><i class="fas fa-check text-success me-2"></i>URL ya video iwe sahihi</li>
                                <li><i class="fas fa-check text-success me-2"></i>Maelezo ya video yaeleweke</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="fas fa-info-circle text-info me-2"></i>Msaada wa URL</h6>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-link text-primary me-2"></i>YouTube: https://youtube.com/watch?v=...</li>
                                <li><i class="fas fa-link text-primary me-2"></i>Vimeo: https://vimeo.com/...</li>
                                <li><i class="fas fa-link text-primary me-2"></i>Faili: https://example.com/video.mp4</li>
                                <li><i class="fas fa-link text-primary me-2"></i>Embed: &lt;iframe&gt;...</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const courseId = document.getElementById('course_id').value;
            const title = document.getElementById('title').value.trim();
            const videoUrl = document.getElementById('video_url').value.trim();

            if (!courseId) {
                e.preventDefault();
                alert('Tafadhali chagua kozi.');
                document.getElementById('course_id').focus();
                return false;
            }

            if (!title || title.length < 3) {
                e.preventDefault();
                alert('Jina la video lazima liwe na herufi 3 au zaidi.');
                document.getElementById('title').focus();
                return false;
            }

            if (!videoUrl || videoUrl.length < 10) {
                e.preventDefault();
                alert('URL ya video si sahihi.');
                document.getElementById('video_url').focus();
                return false;
            }
        });

        // Auto-format duration input
        document.getElementById('duration').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length >= 2) {
                value = value.slice(0, 2) + ':' + value.slice(2, 4);
            }
            e.target.value = value;
        });
    </script>
</body>

</html>