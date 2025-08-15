<?php
require_once __DIR__ . "/../middleware/AuthMiddleware.php";
require_once __DIR__ . "/../models/Video.php";

$auth = new AuthMiddleware();
$auth->requireRole('admin');

$currentUser = $auth->getCurrentUser();
$videoModel = new Video();

// Get video statistics
$videoStats = $videoModel->getOverallVideoStats();
$totalVideos = $videoStats['total_videos'] ?? 0;
$totalCourses = $videoStats['total_courses'] ?? 0;
$thisMonth = $videoStats['this_month'] ?? 0;
$lastMonth = $videoStats['last_month'] ?? 0;

// Get all videos for admin
$videos = $videoModel->getAllVideosForAdmin();
?>

<!DOCTYPE html>
<html lang="sw">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usimamizi wa Video - Panda Digital</title>
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
        .video-table {
            margin-bottom: 1.5rem;
        }

        /* Video thumbnail styling */
        .video-thumbnail {
            border: 1px solid #e9ecef;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease;
        }

        .video-thumbnail:hover {
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
            background: rgba(255, 193, 11, 0.8);
            color: #000;
        }

        .video-table {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .video-table .table {
            margin: 0;
        }

        .video-table .table th {
            background: #f8f9fa;
            border: none;
            padding: 1rem;
            font-weight: 600;
            color: #495057;
        }

        .video-table .table td {
            padding: 1rem;
            border: none;
            border-bottom: 1px solid #e9ecef;
            margin: 0;
        }

        .video-table .table tbody tr:hover {
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

        .add-video-btn {
            background: #000;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            text-decoration: none;
            display: inline-block;
            transition: all 0.2s ease;
        }

        .add-video-btn:hover {
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
            border-radius: 8px;
            right: 0;
        }

        .export-dropdown-content a {
            color: black;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
            border-radius: 8px;
        }

        .export-dropdown-content a:hover {
            background-color: #f1f1f1;
        }

        .export-dropdown:hover .export-dropdown-content {
            display: block;
        }

        .export-dropdown:hover .export-btn {
            background-color: #333;
        }

        .export-btn {
            background: #000;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .export-btn:hover {
            background: #333;
        }

        .course-badge {
            background: #6c757d;
            color: white;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.8rem;
        }

        .video-url {
            max-width: 200px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
    </style>
</head>

<body>
    <?php include __DIR__ . '/includes/admin_header.php'; ?>

    <div class="content-wrapper">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 mb-0">
                            Usimamizi wa Video
                        </h1>
                        <p class="text-muted">Udhibiti video zote za mfumo</p>
                    </div>
                    <div class="d-flex gap-2">
                        <div class="export-dropdown">
                            <button class="export-btn">
                                <i class="fas fa-download me-2"></i>Pakua
                            </button>
                            <div class="export-dropdown-content">
                                <a href="#" onclick="exportToCSV()">
                                    <i class="fas fa-file-csv me-2"></i>CSV
                                </a>
                                <a href="#" onclick="exportToExcel()">
                                    <i class="fas fa-file-excel me-2"></i>Excel
                                </a>
                            </div>
                        </div>
                        <a href="add-video.php" class="add-video-btn">
                            <i class="fas fa-plus me-2"></i>Ongeza Video
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="text-center">
                        <div class="stats-number"><?= $totalVideos ?></div>
                        <div class="stats-label">Jumla ya Video</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="text-center">
                        <div class="stats-number"><?= $totalCourses ?></div>
                        <div class="stats-label">Jumla ya Kozi</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="text-center">
                        <div class="stats-number"><?= $thisMonth ?></div>
                        <div class="stats-label">Mwezi Huu</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="text-center">
                        <div class="stats-number"><?= $lastMonth ?></div>
                        <div class="stats-label">Mwezi Uliopita</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search and Actions -->
        <div class="search-box">
            <div class="row">
                <div class="col-md-8">
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" class="form-control" id="searchInput" placeholder="Tafuta video...">
                    </div>
                </div>
                <div class="col-md-4">
                    <select class="form-select" id="courseFilter">
                        <option value="">Kozi Zote</option>
                        <?php
                        $courses = $videoModel->getAllCourses();
                        foreach ($courses as $course) {
                            echo '<option value="' . htmlspecialchars($course['name']) . '">' . htmlspecialchars($course['name']) . '</option>';
                        }
                        ?>
                    </select>
                </div>
            </div>
        </div>

        <!-- Filter Tabs -->
        <div class="filter-tabs">
            <ul class="nav nav-pills" id="filterTabs">
                <li class="nav-item">
                    <a class="nav-link active" href="#" data-filter="all">
                        Zote <span class="badge bg-secondary ms-1"><?= $totalVideos ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" data-filter="this-month">
                        Mwezi Huu <span class="badge bg-secondary ms-1"><?= $thisMonth ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" data-filter="last-month">
                        Mwezi Uliopita <span class="badge bg-secondary ms-1"><?= $lastMonth ?></span>
                    </a>
                </li>
            </ul>
        </div>

        <!-- Videos Table -->
        <div class="video-table">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Video</th>
                            <th>Kozi</th>
                            <th>Maelezo</th>
                            <th>Vitendo</th>
                        </tr>
                    </thead>
                    <tbody id="videosTableBody">
                        <?php if (empty($videos)): ?>
                            <tr>
                                <td colspan="5" class="text-center py-4">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">Hakuna video zilizopatikana</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($videos as $video): ?>
                                <tr data-course="<?= htmlspecialchars($video['course_name']) ?>">
                                    <td>
                                        <span class="badge bg-secondary">#<?= $video['id'] ?></span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="video-thumbnail me-3">
                                                <i class="fas fa-play"></i>
                                            </div>
                                            <div>
                                                <div class="fw-bold">Video #<?= $video['id'] ?></div>
                                                <div class="video-url text-muted" title="<?= htmlspecialchars($video['name']) ?>">
                                                    <?php
                                                    $videoUrl = $video['name'];
                                                    if (strpos($videoUrl, 'youtube.com') !== false || strpos($videoUrl, 'youtu.be') !== false) {
                                                        // Extract video ID from YouTube URL
                                                        $videoId = '';
                                                        if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/', $videoUrl, $matches)) {
                                                            $videoId = $matches[1];
                                                        }
                                                        if ($videoId) {
                                                            echo '<a href="' . htmlspecialchars($videoUrl) . '" target="_blank" class="text-decoration-none">';
                                                            echo '<i class="fab fa-youtube text-danger me-1"></i>';
                                                            echo 'YouTube Video';
                                                            echo '</a>';
                                                            echo '<br><small class="text-muted">ID: ' . $videoId . '</small>';
                                                        } else {
                                                            echo '<a href="' . htmlspecialchars($videoUrl) . '" target="_blank" class="text-decoration-none">';
                                                            echo '<i class="fas fa-external-link-alt me-1"></i>';
                                                            echo 'Video Link';
                                                            echo '</a>';
                                                        }
                                                    } else {
                                                        echo '<a href="' . htmlspecialchars($videoUrl) . '" target="_blank" class="text-decoration-none">';
                                                        echo '<i class="fas fa-video me-1"></i>';
                                                        echo 'Video Link';
                                                        echo '</a>';
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="course-badge">
                                            <?= htmlspecialchars($video['course_name'] ?? 'N/A') ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="text-truncate" style="max-width: 200px;" title="<?= htmlspecialchars($video['description'] ?? '') ?>">
                                            <?= htmlspecialchars($video['description'] ?? 'Hakuna maelezo') ?>
                                        </div>
                                    </td>
                                    <td>
                                        <button type="button" class="action-btn btn-view" onclick="viewVideo(<?= $video['id'] ?>)">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <a href="edit-video.php?id=<?= $video['id'] ?>" class="action-btn btn-edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="action-btn btn-delete" onclick="deleteVideo(<?= $video['id'] ?>, '<?= htmlspecialchars($video['name']) ?>')">
                                            <i class="fas fa-trash"></i>
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

    <!-- Video View Modal -->
    <div class="modal fade" id="videoViewModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-video me-2"></i>Maelezo ya Video
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="videoModalBody">
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
        // Toggle export dropdown
        function toggleExportDropdown() {
            const dropdown = document.querySelector('.export-dropdown-content');
            dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
        }

        // Search functionality
        document.getElementById('searchInput').addEventListener('input', function() {
            searchVideos();
        });

        // Course filter functionality
        document.getElementById('courseFilter').addEventListener('change', function() {
            searchVideos();
        });

        function searchVideos() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const courseFilter = document.getElementById('courseFilter').value;
            const rows = document.querySelectorAll('#videosTableBody tr');

            rows.forEach(row => {
                if (row.cells.length === 1) return; // Skip "no data" row

                const videoText = row.cells[1].textContent.toLowerCase();
                const courseText = row.cells[2].textContent.toLowerCase();

                const matchesSearch = videoText.includes(searchTerm);
                const matchesCourse = !courseFilter || courseText.includes(courseFilter.toLowerCase());

                row.style.display = (matchesSearch && matchesCourse) ? '' : 'none';
            });
        }

        // Filter by period
        document.querySelectorAll('#filterTabs .nav-link').forEach(tab => {
            tab.addEventListener('click', function(e) {
                e.preventDefault();

                // Remove active class from all tabs
                document.querySelectorAll('#filterTabs .nav-link').forEach(t => t.classList.remove('active'));

                // Add active class to clicked tab
                this.classList.add('active');

                const filter = this.getAttribute('data-filter');
                filterByPeriod(filter);
            });
        });

        function filterByPeriod(period) {
            // For now, just show all videos
            // This can be enhanced later with actual period filtering
            const rows = document.querySelectorAll('#videosTableBody tr');
            rows.forEach(row => {
                if (row.cells.length === 1) return; // Skip "no data" row
                row.style.display = '';
            });
        }

        // View video details
        function viewVideo(videoId) {
            const modal = new bootstrap.Modal(document.getElementById('videoViewModal'));
            const modalBody = document.getElementById('videoModalBody');

            modal.show();

            // Show loading
            modalBody.innerHTML = `
                <div class="text-center">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Inapakia...</span>
                    </div>
                </div>
            `;

            // Fetch video details
            fetch('get_video_details.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'id=' + videoId
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const video = data.video;
                        modalBody.innerHTML = `
                        <div class="row">
                            <div class="col-md-6">
                                <h6><strong>ID:</strong></h6>
                                <p>${video.id}</p>
                                
                                <h6><strong>Kozi:</strong></h6>
                                <p>${video.course_name || 'N/A'}</p>
                                
                                <h6><strong>Maelezo:</strong></h6>
                                <p>${video.description || 'Hakuna maelezo'}</p>
                            </div>
                            <div class="col-md-6">
                                <h6><strong>Video URL:</strong></h6>
                                <p class="text-break">
                                    ${video.name.includes('youtube.com') || video.name.includes('youtu.be') 
                                        ? `<i class="fab fa-youtube text-danger me-2"></i><a href="${video.name}" target="_blank" class="text-decoration-none">YouTube Video</a>`
                                        : `<i class="fas fa-video me-2"></i><a href="${video.name}" target="_blank" class="text-decoration-none">Video Link</a>`
                                    }
                                </p>
                                
                                <h6><strong>Video Preview:</strong></h6>
                                <div class="ratio ratio-16x9">
                                    <iframe src="${video.name}" frameborder="0" allowfullscreen></iframe>
                                </div>
                            </div>
                        </div>
                    `;
                    } else {
                        modalBody.innerHTML = `
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            ${data.message}
                        </div>
                    `;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    modalBody.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        Kuna tatizo la mtandao. Jaribu tena.
                    </div>
                `;
                });
        }

        // Delete video
        function deleteVideo(videoId, videoName) {
            if (confirm(`Je, una uhakika unataka kufuta video "${videoName}"? Kitendo hiki hakiwezi kubatilishwa!`)) {
                fetch('delete_video.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'id=' + videoId
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert(data.message);
                            location.reload();
                        } else {
                            alert(data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Kuna tatizo la mtandao. Jaribu tena.');
                    });
            }
        }

        // Export functions
        function exportToCSV() {
            // Implementation for CSV export
            alert('CSV export itaongezwa hivi karibuni');
        }

        function exportToExcel() {
            // Implementation for Excel export
            alert('Excel export itaongezwa hivi karibuni');
        }
    </script>
</body>

</html>