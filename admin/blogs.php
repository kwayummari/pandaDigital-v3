<?php
require_once __DIR__ . "/../middleware/AuthMiddleware.php";
require_once __DIR__ . "/../models/Blog.php";

$auth = new AuthMiddleware();
$auth->requireRole('admin');

$currentUser = $auth->getCurrentUser();
$blogModel = new Blog();

// Handle blog actions
if ($_POST && isset($_POST['action'])) {
    $blogId = $_POST['blog_id'];
    $action = $_POST['action'];

    if ($action === 'delete') {
        $result = $blogModel->deleteBlog($blogId);
        if ($result) {
            $success = "Blog imefutwa kikamilifu!";
        } else {
            $error = "Imefeli kufuta blog. Tafadhali jaribu tena.";
        }
    } elseif ($action === 'toggle_status') {
        $result = $blogModel->toggleBlogStatus($blogId);
        if ($result) {
            $success = "Hali ya blog imebadilishwa!";
        } else {
            $error = "Imefeli kubadilisha hali ya blog. Tafadhali jaribu tena.";
        }
    }
}

// Get all blogs with pagination
$page = $_GET['page'] ?? 1;
$perPage = 20;
$blogs = $blogModel->getAllBlogsForAdmin($page, $perPage);
$totalBlogs = $blogModel->getTotalBlogs();
$totalPages = ceil($totalBlogs / $perPage);

// Get blog statistics
$blogStats = $blogModel->getOverallBlogStats();
?>

<!DOCTYPE html>
<html lang="sw">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usimamizi wa Blog - Panda Digital</title>

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

        .stats-card {
            background: linear-gradient(135deg, var(--accent-color), #c0392b);
            color: white;
        }

        .stats-card.success {
            background: linear-gradient(135deg, var(--success-color), #229954);
        }

        .stats-card.info {
            background: linear-gradient(135deg, var(--info-color), #2980b9);
        }

        .stats-card.warning {
            background: linear-gradient(135deg, var(--warning-color), #d68910);
        }

        .btn-primary-custom {
            background: var(--primary-color);
            border: none;
            border-radius: 25px;
            padding: 10px 20px;
            font-weight: 600;
        }

        .btn-primary-custom:hover {
            background: var(--secondary-color);
            transform: translateY(-1px);
        }

        .blog-table {
            background: white;
            border-radius: 15px;
            overflow: hidden;
        }

        .blog-table th {
            background: var(--primary-color);
            color: white;
            border: none;
            font-weight: 600;
        }

        .blog-table td {
            vertical-align: middle;
        }

        .badge-status {
            font-size: 0.8rem;
            padding: 6px 12px;
            border-radius: 15px;
        }

        .search-box {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .alert {
            border-radius: 10px;
            border: none;
        }

        .pagination .page-link {
            border-radius: 10px;
            margin: 0 2px;
            border: none;
            color: var(--primary-color);
        }

        .pagination .page-item.active .page-link {
            background: var(--primary-color);
            border-color: var(--primary-color);
        }

        .blog-image {
            width: 80px;
            height: 60px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
        }

        .category-badge {
            background: var(--info-color);
            color: white;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.8rem;
        }

        .blog-title {
            max-width: 300px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .blog-excerpt {
            max-width: 250px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
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
                <a class="nav-link" href="/admin/videos.php">
                    <i class="fas fa-video me-1"></i> Video
                </a>
                <a class="nav-link" href="/admin/questions.php">
                    <i class="fas fa-question-circle me-1"></i> Maswali
                </a>
                <a class="nav-link active" href="/admin/blogs.php">
                    <i class="fas fa-blog me-1"></i> Blog
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
                        <i class="fas fa-blog text-primary me-2"></i>
                        Usimamizi wa Blog
                    </h1>
                    <p class="text-muted">Udhibiti blog zote za mfumo</p>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card stats-card">
                        <div class="card-body text-center">
                            <i class="fas fa-blog fa-2x mb-2"></i>
                            <h3 class="mb-1"><?php echo $totalBlogs; ?></h3>
                            <p class="mb-0">Jumla ya Blog</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stats-card success">
                        <div class="card-body text-center">
                            <i class="fas fa-eye fa-2x mb-2"></i>
                            <h3 class="mb-1"><?php echo $blogStats['total_views'] ?? 0; ?></h3>
                            <p class="mb-0">Jumla ya Matazamo</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stats-card info">
                        <div class="card-body text-center">
                            <i class="fas fa-comments fa-2x mb-2"></i>
                            <h3 class="mb-1"><?php echo $blogStats['total_comments'] ?? 0; ?></h3>
                            <p class="mb-0">Jumla ya Maoni</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stats-card warning">
                        <div class="card-body text-center">
                            <i class="fas fa-users fa-2x mb-2"></i>
                            <h3 class="mb-1"><?php echo $blogStats['total_authors'] ?? 0; ?></h3>
                            <p class="mb-0">Waandishi</p>
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

            <!-- Search and Filters -->
            <div class="search-box">
                <div class="row">
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" class="form-control" id="searchInput" placeholder="Tafuta blog...">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="categoryFilter">
                            <option value="">Kategoria Zote</option>
                            <option value="technology">Teknolojia</option>
                            <option value="business">Biashara</option>
                            <option value="education">Elimu</option>
                            <option value="lifestyle">Maisha</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="statusFilter">
                            <option value="">Hali Zote</option>
                            <option value="published">Iliyochapishwa</option>
                            <option value="draft">Draft</option>
                            <option value="archived">Iliyohifadhiwa</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Blogs Table -->
            <div class="card blog-table">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-list me-2"></i>
                        Orodha ya Blog
                    </h5>
                    <a href="/admin/add-blog.php" class="btn btn-primary-custom text-white">
                        <i class="fas fa-plus me-2"></i>
                        Ongeza Blog
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Blog</th>
                                    <th>Kategoria</th>
                                    <th>Mwandishi</th>
                                    <th>Matazamo</th>
                                    <th>Maoni</th>
                                    <th>Hali</th>
                                    <th>Vitendo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($blogs as $blog): ?>
                                    <tr>
                                        <td>
                                            <span class="badge bg-secondary">#<?php echo $blog['id']; ?></span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="blog-image me-3">
                                                    <i class="fas fa-newspaper"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-bold blog-title" title="<?php echo htmlspecialchars($blog['title']); ?>">
                                                        <?php echo htmlspecialchars($blog['title']); ?>
                                                    </div>
                                                    <div class="blog-excerpt text-muted" title="<?php echo htmlspecialchars($blog['excerpt'] ?? ''); ?>">
                                                        <?php echo htmlspecialchars($blog['excerpt'] ?? 'Hakuna maelezo'); ?>
                                                    </div>
                                                    <small class="text-muted">
                                                        <i class="fas fa-calendar me-1"></i>
                                                        <?php echo date('d M Y', strtotime($blog['date_created'])); ?>
                                                    </small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="category-badge">
                                                <?php
                                                switch ($blog['category'] ?? 'general') {
                                                    case 'technology':
                                                        echo 'Teknolojia';
                                                        break;
                                                    case 'business':
                                                        echo 'Biashara';
                                                        break;
                                                    case 'education':
                                                        echo 'Elimu';
                                                        break;
                                                    case 'lifestyle':
                                                        echo 'Maisha';
                                                        break;
                                                    default:
                                                        echo 'Jumla';
                                                }
                                                ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-user-circle text-primary me-2"></i>
                                                <span><?php echo htmlspecialchars($blog['author_name'] ?? 'Admin'); ?></span>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">
                                                <?php echo $blog['views'] ?? 0; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-success">
                                                <?php echo $blog['comments'] ?? 0; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge badge-status bg-<?php
                                                                                switch ($blog['status'] ?? 'published') {
                                                                                    case 'published':
                                                                                        echo 'success';
                                                                                        break;
                                                                                    case 'draft':
                                                                                        echo 'warning';
                                                                                        break;
                                                                                    case 'archived':
                                                                                        echo 'secondary';
                                                                                        break;
                                                                                    default:
                                                                                        echo 'success';
                                                                                }
                                                                                ?>">
                                                <?php
                                                switch ($blog['status'] ?? 'published') {
                                                    case 'published':
                                                        echo 'Iliyochapishwa';
                                                        break;
                                                    case 'draft':
                                                        echo 'Draft';
                                                        break;
                                                    case 'archived':
                                                        echo 'Iliyohifadhiwa';
                                                        break;
                                                    default:
                                                        echo 'Iliyochapishwa';
                                                }
                                                ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="/admin/edit-blog.php?id=<?php echo $blog['id']; ?>"
                                                    class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="/admin/blog-details.php?id=<?php echo $blog['id']; ?>"
                                                    class="btn btn-sm btn-outline-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-warning"
                                                    onclick="toggleBlogStatus(<?php echo $blog['id']; ?>)">
                                                    <i class="fas fa-pause"></i>
                                                </button>
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-danger"
                                                    onclick="deleteBlog(<?php echo $blog['id']; ?>, '<?php echo htmlspecialchars(substr($blog['title'], 0, 50)); ?>')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <nav aria-label="Blog pagination" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <?php if ($page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?php echo $page - 1; ?>">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                            <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($page < $totalPages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?php echo $page + 1; ?>">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Search functionality
        document.getElementById('searchInput').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('tbody tr');

            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });

        // Filter functionality
        document.getElementById('categoryFilter').addEventListener('change', filterBlogs);
        document.getElementById('statusFilter').addEventListener('change', filterBlogs);

        function filterBlogs() {
            const categoryFilter = document.getElementById('categoryFilter').value;
            const statusFilter = document.getElementById('statusFilter').value;
            const rows = document.querySelectorAll('tbody tr');

            rows.forEach(row => {
                const category = row.querySelector('td:nth-child(3)').textContent.trim();
                const status = row.querySelector('td:nth-child(7)').textContent.trim();

                const categoryMatch = !categoryFilter || category.includes(categoryFilter);
                const statusMatch = !statusFilter || status.includes(statusFilter);

                row.style.display = (categoryMatch && statusMatch) ? '' : 'none';
            });
        }

        // Toggle blog status
        function toggleBlogStatus(blogId) {
            if (confirm('Je, una uhakika unataka kubadilisha hali ya blog hii?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="blog_id" value="${blogId}">
                    <input type="hidden" name="action" value="toggle_status">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        // Delete blog
        function deleteBlog(blogId, blogTitle) {
            if (confirm(`Je, una uhakika unataka kufuta blog "${blogTitle}..."? Kitendo hiki hakiwezi kubatilishwa!`)) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="blog_id" value="${blogId}">
                    <input type="hidden" name="action" value="delete">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</body>

</html>