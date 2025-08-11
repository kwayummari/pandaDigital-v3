<?php
require_once __DIR__ . "/../middleware/AuthMiddleware.php";
require_once __DIR__ . "/../models/Blog.php";

$auth = new AuthMiddleware();
$auth->requireRole('admin');

$currentUser = $auth->getCurrentUser();
$blogModel = new Blog();

$success = $error = '';

// Handle form submission
if ($_POST && isset($_POST['add_blog'])) {
    $title = trim($_POST['title']);
    $excerpt = trim($_POST['excerpt']);
    $content = trim($_POST['content']);
    $category = $_POST['category'];
    $status = $_POST['status'];

    // Validation
    if (empty($title) || empty($excerpt) || empty($content) || empty($category)) {
        $error = "Tafadhali jaza sehemu zote muhimu (Jina, Maelezo Mafupi, Maudhui, na Kategoria).";
    } elseif (strlen($title) < 10) {
        $error = "Jina la blog lazima liwe na herufi 10 au zaidi.";
    } elseif (strlen($excerpt) < 20) {
        $error = "Maelezo mafupi lazima yawe na herufi 20 au zaidi.";
    } elseif (strlen($content) < 100) {
        $error = "Maudhui lazima yawe na herufi 100 au zaidi.";
    } else {
        // Add blog
        $result = $blogModel->addBlog($currentUser['id'], $title, $excerpt, $content, $category, $status);
        if ($result) {
            $success = "Blog imeongezwa kikamilifu!";
            // Clear form data
            $title = $excerpt = $content = '';
            $category = 'general';
            $status = 'draft';
        } else {
            $error = "Imefeli kuongeza blog. Tafadhali jaribu tena.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="sw">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ongeza Blog - Panda Digital</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Summernote CSS -->
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
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

        .blog-preview {
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

        .category-info {
            background: var(--info-color);
            color: white;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
        }

        .status-draft {
            background: var(--warning-color);
            color: white;
        }

        .status-published {
            background: var(--success-color);
            color: white;
        }

        .status-archived {
            background: var(--secondary-color);
            color: white;
        }

        .note-editor {
            border-radius: 10px;
            overflow: hidden;
        }

        .note-toolbar {
            background: #f8f9fa;
            border-bottom: 2px solid #e9ecef;
        }

        .note-editing-area {
            min-height: 300px;
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
                <a class="nav-link active" href="/admin/blogs.php">
                    <i class="fas fa-blog me-1"></i> Blog
                </a>
                <a class="nav-link" href="/admin/courses.php">
                    <i class="fas fa-book me-1"></i> Kozi
                </a>
                <a class="nav-link" href="/admin/videos.php">
                    <i class="fas fa-video me-1"></i> Video
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
                        Ongeza Blog Mpya
                    </h1>
                    <p class="text-muted">Ongeza blog mpya kwenye mfumo</p>
                </div>
            </div>

            <!-- Blog Preview Section -->
            <div class="blog-preview">
                <i class="fas fa-newspaper fa-3x mb-3"></i>
                <h3>Ongeza Blog Mpya</h3>
                <p class="mb-0">Jaza maelezo ya blog ili watumiaji waweze kusoma</p>
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

            <!-- Add Blog Form -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-edit me-2"></i>
                        Maelezo ya Blog
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <div class="row">
                            <!-- Blog Title -->
                            <div class="col-md-8 mb-3">
                                <label for="title" class="form-label">
                                    Jina la Blog <span class="required">*</span>
                                </label>
                                <input type="text" class="form-control" id="title" name="title"
                                    value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>"
                                    placeholder="Mfano: Jinsi ya Kuanza Biashara ya Mtandaoni" required>
                                <div class="help-text">Jina la blog linaloelezea yaliyomo</div>
                            </div>

                            <!-- Blog Category -->
                            <div class="col-md-4 mb-3">
                                <label for="category" class="form-label">
                                    Kategoria <span class="required">*</span>
                                </label>
                                <select class="form-select" id="category" name="category" required>
                                    <option value="">-- Chagua Kategoria --</option>
                                    <option value="technology" <?php echo (isset($_POST['category']) && $_POST['category'] == 'technology') ? 'selected' : ''; ?>>
                                        Teknolojia
                                    </option>
                                    <option value="business" <?php echo (isset($_POST['category']) && $_POST['category'] == 'business') ? 'selected' : ''; ?>>
                                        Biashara
                                    </option>
                                    <option value="education" <?php echo (isset($_POST['category']) && $_POST['category'] == 'education') ? 'selected' : ''; ?>>
                                        Elimu
                                    </option>
                                    <option value="lifestyle" <?php echo (isset($_POST['category']) && $_POST['category'] == 'lifestyle') ? 'selected' : ''; ?>>
                                        Maisha
                                    </option>
                                </select>
                                <div class="help-text">Chagua kategoria inayofaa blog yako</div>
                            </div>
                        </div>

                        <!-- Blog Excerpt -->
                        <div class="mb-4">
                            <label for="excerpt" class="form-label">
                                Maelezo Mafupi <span class="required">*</span>
                            </label>
                            <textarea class="form-control" id="excerpt" name="excerpt" rows="3"
                                placeholder="Andika maelezo mafupi ya blog hii..." required><?php echo htmlspecialchars($_POST['excerpt'] ?? ''); ?></textarea>
                            <div class="help-text">Maelezo mafupi ya yaliyomo kwenye blog (itakionekana kwenye orodha)</div>
                        </div>

                        <!-- Blog Content -->
                        <div class="mb-4">
                            <label for="content" class="form-label">
                                Maudhui ya Blog <span class="required">*</span>
                            </label>
                            <textarea class="form-control" id="content" name="content" rows="10"
                                placeholder="Andika maudhui kamili ya blog hii..."><?php echo htmlspecialchars($_POST['content'] ?? ''); ?></textarea>
                            <div class="help-text">Maudhui kamili ya blog. Tumia chombo cha uhariri hapo juu kwa muonekano mzuri.</div>
                        </div>

                        <!-- Blog Status -->
                        <div class="mb-4">
                            <label for="status" class="form-label">
                                Hali ya Blog
                            </label>
                            <select class="form-select" id="status" name="status">
                                <option value="draft" <?php echo (isset($_POST['status']) && $_POST['status'] == 'draft') ? 'selected' : ''; ?>>
                                    Draft (Haijachapishwa)
                                </option>
                                <option value="published" <?php echo (isset($_POST['status']) && $_POST['status'] == 'published') ? 'selected' : ''; ?>>
                                    Chapishwa (Inaonekana kwa Wote)
                                </option>
                                <option value="archived" <?php echo (isset($_POST['status']) && $_POST['status'] == 'archived') ? 'selected' : ''; ?>>
                                    Imewekwa kwenye Arkivu
                                </option>
                            </select>
                            <div class="help-text">Chagua hali ya blog. Draft haitaonekana kwa watumiaji.</div>
                        </div>

                        <!-- Category Information -->
                        <div class="category-info">
                            <h6 class="mb-2"><i class="fas fa-info-circle me-2"></i>Maelezo ya Kategoria</h6>
                            <div class="row">
                                <div class="col-md-3">
                                    <strong>Teknolojia:</strong> Habari za teknolojia, programu, na mitandao
                                </div>
                                <div class="col-md-3">
                                    <strong>Biashara:</strong> Mbinu za biashara, uchumi, na uwekezaji
                                </div>
                                <div class="col-md-3">
                                    <strong>Elimu:</strong> Mambo ya elimu, mafunzo, na ujuzi
                                </div>
                                <div class="col-md-3">
                                    <strong>Maisha:</strong> Hali ya maisha, afya, na burudani
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="/admin/blogs.php" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>
                                Rudi kwenye Blog
                            </a>
                            <button type="submit" name="add_blog" class="btn btn-primary-custom">
                                <i class="fas fa-plus me-2"></i>
                                Ongeza Blog
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
                        Vidokezo vya Kuongeza Blog
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6><i class="fas fa-lightbulb text-warning me-2"></i>Jinsi ya Kuongeza Blog</h6>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-check text-success me-2"></i>Jina la blog liwe wazi na linaeleweka</li>
                                <li><i class="fas fa-check text-success me-2"></i>Maelezo mafupi yaeleze yaliyomo</li>
                                <li><i class="fas fa-check text-success me-2"></i>Maudhui yawe na habari muhimu</li>
                                <li><i class="fas fa-check text-success me-2"></i>Chagua kategoria sahihi</li>
                                <li><i class="fas fa-check text-success me-2"></i>Fikiria hali ya blog (draft au chapishwa)</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="fas fa-info-circle text-info me-2"></i>Msaada wa Uhariri</h6>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-bold text-primary me-2"></i><strong>Bold:</strong> Tumia kwa maneno muhimu</li>
                                <li><i class="fas fa-italic text-primary me-2"></i><em>Italic:</em> Tumia kwa maneno ya kigeni</li>
                                <li><i class="fas fa-list text-primary me-2"></i>Orodha: Tumia kwa maelezo ya hatua</li>
                                <li><i class="fas fa-link text-primary me-2"></i>Links: Ongeza viungo muhimu</li>
                                <li><i class="fas fa-image text-primary me-2"></i>Picha: Ongeza picha za msaada</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Summernote JS -->
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>

    <script>
        // Initialize Summernote editor
        $(document).ready(function() {
            $('#content').summernote({
                height: 300,
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'underline', 'italic', 'clear']],
                    ['fontname', ['fontname']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['height', ['height']],
                    ['insert', ['link', 'picture', 'video']],
                    ['view', ['fullscreen', 'codeview', 'help']]
                ],
                placeholder: 'Andika maudhui ya blog hapa...',
                callbacks: {
                    onImageUpload: function(files) {
                        // Handle image upload if needed
                        console.log('Image upload:', files);
                    }
                }
            });
        });

        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const title = document.getElementById('title').value.trim();
            const excerpt = document.getElementById('excerpt').value.trim();
            const content = document.getElementById('content').value.trim();
            const category = document.getElementById('category').value;

            if (!title || title.length < 10) {
                e.preventDefault();
                alert('Jina la blog lazima liwe na herufi 10 au zaidi.');
                document.getElementById('title').focus();
                return false;
            }

            if (!excerpt || excerpt.length < 20) {
                e.preventDefault();
                alert('Maelezo mafupi lazima yawe na herufi 20 au zaidi.');
                document.getElementById('excerpt').focus();
                return false;
            }

            if (!content || content.length < 100) {
                e.preventDefault();
                alert('Maudhui lazima yawe na herufi 100 au zaidi.');
                document.getElementById('content').focus();
                return false;
            }

            if (!category) {
                e.preventDefault();
                alert('Tafadhali chagua kategoria.');
                document.getElementById('category').focus();
                return false;
            }
        });

        // Character counter for excerpt
        document.getElementById('excerpt').addEventListener('input', function() {
            const length = this.value.length;
            const minLength = 20;
            const maxLength = 200;

            if (length < minLength) {
                this.style.borderColor = '#dc3545';
            } else if (length > maxLength) {
                this.style.borderColor = '#ffc107';
            } else {
                this.style.borderColor = '#28a745';
            }
        });

        // Status change handler
        document.getElementById('status').addEventListener('change', function() {
            const status = this.value;
            const statusInfo = document.createElement('div');

            // Remove existing status info
            const existingInfo = document.querySelector('.status-info');
            if (existingInfo) {
                existingInfo.remove();
            }

            // Add new status info
            statusInfo.className = 'alert status-info mt-2';
            statusInfo.style.borderRadius = '10px';

            if (status === 'draft') {
                statusInfo.className += ' alert-warning';
                statusInfo.innerHTML = '<i class="fas fa-info-circle me-2"></i>Blog itakuwa kwenye draft na haitaonekana kwa watumiaji. Unaweza kuichapisha baadaye.';
            } else if (status === 'published') {
                statusInfo.className += ' alert-success';
                statusInfo.innerHTML = '<i class="fas fa-check-circle me-2"></i>Blog itaonekana kwa watumiaji wote mara moja.';
            } else if (status === 'archived') {
                statusInfo.className += ' alert-secondary';
                statusInfo.innerHTML = '<i class="fas fa-archive me-2"></i>Blog itawekwa kwenye arkivu na haitaonekana kwa watumiaji.';
            }

            this.parentNode.appendChild(statusInfo);
        });
    </script>
</body>

</html>