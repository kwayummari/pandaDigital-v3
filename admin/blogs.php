<?php
require_once __DIR__ . "/../middleware/AuthMiddleware.php";
require_once __DIR__ . "/../models/Blog.php";

$auth = new AuthMiddleware();
$auth->requireRole('admin');

$currentUser = $auth->getCurrentUser();
$blogModel = new Blog();

// Get blog statistics
$blogStats = $blogModel->getOverallBlogStats();
$totalBlogs = $blogStats['total_blogs'] ?? 0;
$totalViews = $blogStats['total_views'] ?? 0;
$totalComments = $blogStats['total_comments'] ?? 0;
$totalAuthors = $blogStats['total_authors'] ?? 0;

// Get all blogs for admin
$blogs = $blogModel->getAllBlogsForAdmin(1, 1000); // Get all blogs for now
?>

<!DOCTYPE html>
<html lang="sw">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usimamizi wa Blog - Panda Digital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="<?= app_url('assets/css/style.css') ?>?v=5">
    <style>
        /* Simple layout fixes */
        .content-wrapper {
            padding: 20px 30px;
        }

        .stats-card,
        .search-box,
        .filter-tabs,
        .blog-table {
            margin-bottom: 1.5rem;
        }

        /* Blog thumbnail styling */
        .blog-thumbnail {
            border: 1px solid #e9ecef;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease;
        }

        .blog-thumbnail:hover {
            transform: scale(1.1);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        .stats-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border: 1px solid #e9ecef;
            border-top: 3px solid #000;
            transition: transform 0.2s ease;
        }

        .stats-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
        }

        .stats-number {
            font-size: 2rem;
            font-weight: bold;
            color: #000;
            margin-bottom: 0.5rem;
        }

        .stats-label {
            color: #6c757d;
            font-size: 0.9rem;
        }

        .search-box {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .filter-tabs {
            background: white;
            border-radius: 15px;
            padding: 1rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .filter-tabs .nav-link {
            border: none;
            color: #6c757d;
            padding: 0.5rem 1rem;
            margin-right: 0.5rem;
            border-radius: 8px;
        }

        .filter-tabs .nav-link.active {
            background: #000;
            color: white;
        }

        .blog-table {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .blog-table .table {
            margin: 0;
        }

        .blog-table .table th {
            background: #f8f9fa;
            border: none;
            padding: 1rem;
            font-weight: 600;
            color: #495057;
        }

        .blog-table .table td {
            padding: 1rem;
            border: none;
            border-bottom: 1px solid #e9ecef;
            margin: 0;
        }

        .blog-table .table tbody tr:hover {
            background: #f8f9fa;
        }

        .action-btn {
            padding: 0.375rem 0.75rem;
            border-radius: 6px;
            font-size: 0.875rem;
            text-decoration: none;
            display: inline-block;
            margin: 0 0.25rem;
            transition: all 0.2s ease;
        }

        .btn-view {
            background: #000;
            color: white;
        }

        .btn-view:hover {
            background: #333;
            color: white;
        }

        .btn-edit {
            background: #6c757d;
            color: white;
        }

        .btn-edit:hover {
            background: #5a6268;
            color: white;
        }

        .btn-delete {
            background: #dc3545;
            color: white;
        }

        .btn-delete:hover {
            background: #c82333;
            color: white;
        }

        .add-blog-btn {
            background: #000;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            text-decoration: none;
            display: inline-block;
            transition: all 0.2s ease;
        }

        .add-blog-btn:hover {
            background: #333;
            color: white;
            transform: translateY(-1px);
        }

        .export-dropdown {
            position: relative;
            display: inline-block;
        }

        .export-dropdown-content {
            display: none;
            position: absolute;
            background-color: #f9f9f9;
            min-width: 160px;
            box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
            z-index: 1;
            border-radius: 6px;
            overflow: hidden;
        }

        .export-dropdown-content a {
            color: black;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
            transition: background-color 0.3s;
        }

        .export-dropdown-content a:hover {
            background-color: #f1f1f1;
        }

        .export-dropdown.show .export-dropdown-content {
            display: block;
        }
    </style>
</head>

<body>
    <?php include __DIR__ . '/includes/admin_header.php'; ?>

    <div class="content-wrapper">
        <!-- Success Message -->
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                <?php if ($_GET['success'] == '1'): ?>
                    Blog imehifadhiwa kwa mafanikio!
                <?php endif; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Statistics Cards -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="stats-number"><?= number_format($totalBlogs) ?></div>
                    <div class="stats-label">Blog Zote</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="stats-number"><?= number_format($totalViews) ?></div>
                    <div class="stats-label">Jumla ya Matazamo</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="stats-number"><?= number_format($totalComments) ?></div>
                    <div class="stats-label">Maoni Yote</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="stats-number"><?= number_format($totalAuthors) ?></div>
                    <div class="stats-label">Waandishi</div>
                </div>
            </div>
        </div>

        <!-- Search and Actions -->
        <div class="search-box">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="input-group">
                        <input type="text" class="form-control" id="searchInput" placeholder="Tafuta blog...">
                        <button class="btn btn-outline-secondary" type="button" onclick="searchBlogs()">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
                <div class="col-md-6 text-end">
                    <a href="add-blog.php" class="add-blog-btn me-2">
                        <i class="fas fa-plus me-2"></i>Ongeza Blog Mpya
                    </a>
                    <div class="export-dropdown">
                        <button class="btn btn-outline-secondary" onclick="toggleExportDropdown()">
                            <i class="fas fa-download me-2"></i>Pakua
                        </button>
                        <div class="export-dropdown-content">
                            <a href="export_blogs.php?format=csv">Excel (CSV)</a>
                            <a href="export_blogs.php?format=pdf">PDF</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Tabs -->
        <div class="filter-tabs">
            <ul class="nav nav-tabs" id="statusTabs">
                <li class="nav-item">
                    <a class="nav-link active" href="#" onclick="filterByStatus('all')">
                        Zote (<?= $totalBlogs ?>)
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" onclick="filterByStatus('published')">
                        Zilizochapishwa (<?= $totalBlogs ?>)
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" onclick="filterByStatus('draft')">
                        Rasimu (<?= $totalBlogs ?>)
                    </a>
                </li>
            </ul>
        </div>

        <!-- Blogs Table -->
        <div class="blog-table">
            <div class="table-responsive">
                <table class="table" id="blogsTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Picha</th>
                            <th>Kichwa Cha Habari</th>
                            <th>Maelezo</th>
                            <th>Muda</th>
                            <th>Vitendo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($blogs)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <p class="text-muted mb-0">Hakuna blog zilizopatikana</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($blogs as $blog): ?>
                                <tr>
                                    <td><?= $blog['id'] ?></td>
                                    <td>
                                        <?php if (!empty($blog['photo'])): ?>
                                            <img src="../uploads/Blog/<?= htmlspecialchars($blog['photo']) ?>"
                                                alt="Blog Image"
                                                class="blog-thumbnail"
                                                style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                                        <?php else: ?>
                                            <span class="text-muted">Hakuna picha</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div>
                                            <strong><?= htmlspecialchars($blog['title'] ?? $blog['name'] ?? 'N/A') ?></strong>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if (!empty($blog['excerpt'] ?? $blog['maelezo'])): ?>
                                            <?= htmlspecialchars(substr($blog['excerpt'] ?? $blog['maelezo'], 0, 100)) ?>...
                                        <?php else: ?>
                                            <span class="text-muted">Hakuna maelezo</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?= htmlspecialchars($blog['date_created'] ?? 'N/A') ?>
                                    </td>
                                    <td>
                                        <button class="action-btn btn-view" onclick="viewBlog(<?= $blog['id'] ?>)">
                                            <i class="fas fa-eye me-1"></i>Ona
                                        </button>
                                        <a href="edit-blog.php?id=<?= $blog['id'] ?>" class="action-btn btn-edit">
                                            <i class="fas fa-edit me-1"></i>Hariri
                                        </a>
                                        <button class="action-btn btn-delete" onclick="deleteBlog(<?= $blog['id'] ?>)">
                                            <i class="fas fa-trash me-1"></i>Futa
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Blog View Modal -->
    <div class="modal fade" id="blogViewModal" tabindex="-1" aria-labelledby="blogViewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="blogViewModalLabel">Maelezo ya Blog</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="blogViewModalBody">
                    <div class="text-center">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">Inapakia...</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Funga</button>
                </div>
            </div>
        </div>
    </div>

    <?php include __DIR__ . '/includes/admin_footer_common.php'; ?>

    <script>
        // Export dropdown functionality
        function toggleExportDropdown() {
            document.querySelector('.export-dropdown').classList.toggle('show');
        }

        // Close dropdown when clicking outside
        window.onclick = function(event) {
            if (!event.target.matches('.export-dropdown')) {
                const dropdowns = document.getElementsByClassName('export-dropdown');
                for (let dropdown of dropdowns) {
                    if (dropdown.classList.contains('show')) {
                        dropdown.classList.remove('show');
                    }
                }
            }
        }

        // Search functionality
        function searchBlogs() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const table = document.getElementById('blogsTable');
            const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

            for (let row of rows) {
                const title = row.cells[2].textContent.toLowerCase();
                const description = row.cells[3].textContent.toLowerCase();

                if (title.includes(searchTerm) || description.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            }
        }

        // Filter by status
        function filterByStatus(status) {
            // Update active tab
            document.querySelectorAll('#statusTabs .nav-link').forEach(tab => {
                tab.classList.remove('active');
            });
            event.target.classList.add('active');

            // Filter table rows
            const table = document.getElementById('blogsTable');
            const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

            for (let row of rows) {
                if (status === 'all') {
                    row.style.display = '';
                } else if (status === 'published') {
                    // For now, show all since we don't have status field
                    row.style.display = '';
                } else if (status === 'draft') {
                    // For now, show all since we don't have status field
                    row.style.display = '';
                }
            }
        }

        // View blog details
        function viewBlog(blogId) {
            const modal = new bootstrap.Modal(document.getElementById('blogViewModal'));
            modal.show();

            // Fetch blog details via AJAX
            fetch(`get_blog_details.php?id=${blogId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const blog = data.blog;
                        document.getElementById('blogViewModalBody').innerHTML = `
                            <div class="row">
                                <div class="col-md-4">
                                    <img src="../uploads/Blog/${blog.photo || 'default-blog.jpg'}" 
                                         class="img-fluid rounded" alt="Blog Image">
                                </div>
                                <div class="col-md-8">
                                    <h5>${blog.title || blog.name}</h5>
                                    <p class="text-muted">${blog.excerpt || blog.maelezo || 'Hakuna maelezo'}</p>
                                    
                                    <div class="row mb-3">
                                        <div class="col-6">
                                            <strong>Mwandishi:</strong><br>
                                            ${blog.author_name || 'Haijulikani'}
                                        </div>
                                        <div class="col-6">
                                            <strong>Tarehe:</strong><br>
                                            ${blog.date_created || 'Haijulikani'}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                    } else {
                        document.getElementById('blogViewModalBody').innerHTML = `
                            <div class="alert alert-danger">
                                ${data.message || 'Haikuweza kupata maelezo ya blog'}
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('blogViewModalBody').innerHTML = `
                        <div class="alert alert-danger">
                            Kuna tatizo la mtandao. Jaribu tena.
                        </div>
                    `;
                });
        }

        // Delete blog
        function deleteBlog(blogId) {
            if (confirm('Una uhakika unataka kufuta blog hii? Kitendo hiki hakiwezi kurekebishwa.')) {
                fetch('delete-blog.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            id: blogId
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Blog imefutwa kwa mafanikio!');
                            location.reload();
                        } else {
                            alert('Kuna tatizo: ' + (data.message || 'Haijulikani'));
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Kuna tatizo la mtandao. Jaribu tena.');
                    });
            }
        }

        // Initialize search on Enter key
        document.getElementById('searchInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                searchBlogs();
            }
        });
    </script>
</body>

</html>