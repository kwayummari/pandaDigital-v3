<?php
require_once __DIR__ . "/../middleware/AuthMiddleware.php";
require_once __DIR__ . "/../models/Course.php";
require_once __DIR__ . "/../models/User.php";

// Check if user is admin
$auth = new AuthMiddleware();
$auth->requireRole('admin');

// Initialize models
$courseModel = new Course();
$userModel = new User();

// Get course statistics
$totalCourses = $courseModel->getTotalCourses();
$totalEnrollments = $courseModel->getTotalEnrollments();

// Get all courses for display
$courses = $courseModel->getAllCoursesForAdmin();



// Get instructor stats
$totalInstructors = $userModel->getTotalInstructors();

// Debug: Check what we're getting
error_log("Total courses: " . $totalCourses);
error_log("Courses array count: " . count($courses));
if (!empty($courses)) {
    error_log("First course data: " . json_encode($courses[0]));
}


?>

<!DOCTYPE html>
<html lang="sw">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usimamizi wa Kozi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="<?= app_url('assets/css/style.css') ?>?v=5">
    <style>
        .stats-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border: 1px solid #e9ecef;
            transition: all 0.3s ease;
        }

        .stats-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
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
            margin: 0;
        }

        .course-table {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .course-table .table {
            margin: 0;
        }

        .course-table .table th {
            background: #f8f9fa;
            border: none;
            padding: 1rem;
            font-weight: 600;
            color: #495057;
        }

        .course-table .table td {
            padding: 1rem;
            border: none;
            border-bottom: 1px solid #e9ecef;
            margin: 0;
        }

        .course-table .table tbody tr:hover {
            background: #f8f9fa;
        }

        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .status-published {
            background: #d4edda;
            color: #155724;
        }

        .status-draft {
            background: #fff3cd;
            color: #856404;
        }

        .status-pending {
            background: #f8d7da;
            color: #721c24;
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

        .filter-tabs {
            background: white;
            border-radius: 15px;
            padding: 1rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .filter-tabs .nav-link {
            border: none;
            color: #6c757d;
            padding: 0.5rem 1rem;
            margin-right: 0.5rem;
            border-radius: 8px;
            transition: all 0.2s ease;
        }

        .filter-tabs .nav-link.active {
            background: #000;
            color: white;
        }

        .filter-tabs .nav-link:hover:not(.active) {
            background: #f8f9fa;
            color: #495057;
        }

        .search-box {
            background: white;
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .search-box .form-control {
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 0.75rem 1rem;
        }

        .search-box .form-control:focus {
            border-color: #000;
            box-shadow: 0 0 0 0.2rem rgba(0, 0, 0, 0.25);
        }

        .add-course-btn {
            background: #000;
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .add-course-btn:hover {
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
            right: 0;
            background-color: white;
            min-width: 160px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            border-radius: 8px;
            overflow: hidden;
        }

        .export-dropdown-content a {
            color: #495057;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
            transition: background-color 0.2s;
        }

        .export-dropdown-content a:hover {
            background-color: #f8f9fa;
        }

        .export-dropdown.show .export-dropdown-content {
            display: block;
        }

        /* Simple layout fixes */
        .content-wrapper {
            padding: 20px 30px;
        }

        .stats-card,
        .search-box,
        .filter-tabs,
        .course-table {
            margin-bottom: 1.5rem;
        }

        /* Course thumbnail styling */
        .course-thumbnail {
            border: 1px solid #e9ecef;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease;
        }

        .course-thumbnail:hover {
            transform: scale(1.1);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }
    </style>
</head>

<body>
    <?php include __DIR__ . '/includes/admin_header.php'; ?>

    <div class="content-wrapper">


        <!-- Statistics Cards -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="stats-number"><?= number_format($totalCourses) ?></div>
                    <div class="stats-label">Kozi Zote</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="stats-number"><?= number_format($totalEnrollments) ?></div>
                    <div class="stats-label">Jumla ya Usajili</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="stats-number"><?= number_format($totalInstructors) ?></div>
                    <div class="stats-label">Waalimu</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="stats-number"><?= number_format($totalCourses) ?></div>
                    <div class="stats-label">Video Zote</div>
                </div>
            </div>
        </div>

        <!-- Success Message -->
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                <?php if ($_GET['success'] == '1'): ?>
                    Kozi imehifadhiwa kwa mafanikio!
                <?php endif; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>





        <!-- Search and Actions -->
        <div class="search-box">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="input-group">
                        <input type="text" class="form-control" id="searchInput" placeholder="Tafuta kozi...">
                        <button class="btn btn-outline-secondary" type="button" onclick="searchCourses()">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
                <div class="col-md-6 text-end">
                    <button class="add-course-btn me-2" onclick="openAddCourseModal()">
                        <i class="fas fa-plus me-2"></i>Ongeza Kozi Mpya
                    </button>
                    <div class="export-dropdown">
                        <button class="btn btn-outline-secondary" onclick="toggleExportDropdown()">
                            <i class="fas fa-download me-2"></i>Pakua
                        </button>
                        <div class="export-dropdown-content">
                            <a href="export_courses.php?format=csv">Excel (CSV)</a>
                            <a href="export_courses.php?format=pdf">PDF</a>
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
                        Zote (<?= $totalCourses ?>)
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" onclick="filterByStatus('with_video')">
                        Zilizo na Video (<?= $totalCourses ?>)
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" onclick="filterByStatus('with_photo')">
                        Zilizo na Picha (<?= $totalCourses ?>)
                    </a>
                </li>
            </ul>
        </div>

        <!-- Courses Table -->
        <div class="course-table">
            <div class="table-responsive">
                <table class="table" id="coursesTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Picha</th>
                            <th>Jina la Kozi</th>
                            <th>Maelezo</th>
                            <th>Video</th>
                            <th>Vitendo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($courses)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <p class="text-muted mb-0">Hakuna kozi zilizopatikana</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($courses as $course): ?>
                                <tr>
                                    <td><?= $course['id'] ?></td>
                                    <td>
                                        <?php if (!empty($course['photo'])): ?>
                                            <img src="../uploads/courses/<?= htmlspecialchars($course['photo']) ?>"
                                                alt="Course Image"
                                                class="course-thumbnail"
                                                style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                                        <?php else: ?>
                                            <span class="text-muted">Hakuna picha</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div>
                                            <strong><?= htmlspecialchars($course['title'] ?? 'N/A') ?></strong>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if (!empty($course['description'])): ?>
                                            <?= htmlspecialchars(substr($course['description'], 0, 100)) ?>...
                                        <?php else: ?>
                                            <span class="text-muted">Hakuna maelezo</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($course['video'])): ?>
                                            <span class="text-success"><?= htmlspecialchars($course['video']) ?></span>
                                        <?php else: ?>
                                            <span class="text-muted">Hakuna video</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <button class="action-btn btn-view" onclick="viewCourse(<?= $course['id'] ?>)">
                                            <i class="fas fa-eye me-1"></i>Ona
                                        </button>
                                        <button class="action-btn btn-edit" onclick="editCourse(<?= $course['id'] ?>)">
                                            <i class="fas fa-edit me-1"></i>Hariri
                                        </button>
                                        <button class="action-btn btn-delete" onclick="deleteCourse(<?= $course['id'] ?>)">
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
    </div>

    <!-- Course View Modal -->
    <div class="modal fade" id="courseViewModal" tabindex="-1" aria-labelledby="courseViewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="courseViewModalLabel">Maelezo ya Kozi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="courseViewModalBody">
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

    <!-- Add Course Modal -->
    <div class="modal fade" id="addCourseModal" tabindex="-1" aria-labelledby="addCourseModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addCourseModalLabel">Ongeza Kozi Mpya</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addCourseForm">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="courseTitle" class="form-label">Jina la Kozi *</label>
                                <input type="text" class="form-control" id="courseTitle" name="title" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="courseInstructor" class="form-label">Mwalimu *</label>
                                <select class="form-select" id="courseInstructor" name="instructor_id" required>
                                    <option value="">Chagua mwalimu</option>
                                    <?php
                                    $instructors = $userModel->getAllInstructors();
                                    foreach ($instructors as $instructor) {
                                        echo '<option value="' . $instructor['id'] . '">' . htmlspecialchars($instructor['full_name']) . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="courseDescription" class="form-label">Maelezo</label>
                            <textarea class="form-control" id="courseDescription" name="description" rows="4"></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="coursePrice" class="form-label">Bei</label>
                                <input type="number" class="form-control" id="coursePrice" name="price" min="0" step="100">
                                <small class="form-text text-muted">Acha tupu kwa kozi ya bure</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="courseStatus" class="form-label">Hali</label>
                                <select class="form-select" id="courseStatus" name="status">
                                    <option value="draft">Rasimu</option>
                                    <option value="pending">Inasubiri</option>
                                    <option value="published">Imechapishwa</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="courseImage" class="form-label">Picha ya Kozi</label>
                            <input type="file" class="form-control" id="courseImage" name="image" accept="image/*">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Funga</button>
                    <button type="button" class="btn btn-primary" onclick="saveCourse()">Hifadhi Kozi</button>
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

        // Close export dropdown when clicking outside
        window.onclick = function(event) {
            if (!event.target.matches('.export-dropdown') && !event.target.matches('.export-dropdown *')) {
                var dropdowns = document.getElementsByClassName("export-dropdown");
                for (var i = 0; i < dropdowns.length; i++) {
                    var openDropdown = dropdowns[i];
                    if (openDropdown.classList.contains('show')) {
                        openDropdown.classList.remove('show');
                    }
                }
            }
        }

        // Search functionality
        function searchCourses() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const table = document.getElementById('coursesTable');
            const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

            for (let row of rows) {
                const title = row.cells[1].textContent.toLowerCase();
                const instructor = row.cells[2].textContent.toLowerCase();

                if (title.includes(searchTerm) || instructor.includes(searchTerm)) {
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
            const table = document.getElementById('coursesTable');
            const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

            for (let row of rows) {
                if (status === 'all') {
                    row.style.display = '';
                } else if (status === 'with_video') {
                    const videoCell = row.cells[3]; // Video column
                    if (videoCell && !videoCell.textContent.includes('Hakuna video')) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                } else if (status === 'with_photo') {
                    const photoCell = row.cells[4]; // Photo column
                    if (photoCell && !photoCell.textContent.includes('Hakuna picha')) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                }
            }
        }

        // View course details
        function viewCourse(courseId) {
            const modal = new bootstrap.Modal(document.getElementById('courseViewModal'));
            modal.show();

            // Fetch course details via AJAX
            fetch(`get_course_details.php?id=${courseId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const course = data.course;
                        document.getElementById('courseViewModalBody').innerHTML = `
                            <div class="row">
                                <div class="col-md-4">
                                    <img src="${course.image_url || 'assets/images/default-course.jpg'}" 
                                         class="img-fluid rounded" alt="Course Image">
                                </div>
                                <div class="col-md-8">
                                    <h5>${course.title}</h5>
                                    <p class="text-muted">${course.description || 'Hakuna maelezo'}</p>
                                    
                                    <div class="row mb-3">
                                        <div class="col-6">
                                            <strong>Mwalimu:</strong><br>
                                            ${course.instructor_name}
                                        </div>
                                        <div class="col-6">
                                            <strong>Bei:</strong><br>
                                            ${course.price > 0 ? 'TSh ' + course.price.toLocaleString() : 'Bure'}
                                        </div>
                                    </div>
                                    
                                    <div class="row mb-3">
                                        <div class="col-6">
                                            <strong>Hali:</strong><br>
                                            <span class="status-badge status-${course.status}">
                                                ${getStatusText(course.status)}
                                            </span>
                                        </div>
                                        <div class="col-6">
                                            <strong>Tarehe ya Uundaji:</strong><br>
                                            ${new Date(course.created_at).toLocaleDateString('sw-TZ')}
                                        </div>
                                    </div>
                                    
                                    <div class="row mb-3">
                                        <div class="col-6">
                                            <strong>Idadi ya Wanafunzi:</strong><br>
                                            ${course.enrollment_count || 0}
                                        </div>
                                        <div class="col-6">
                                            <strong>Idadi ya Video:</strong><br>
                                            ${course.video_count || 0}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                    } else {
                        document.getElementById('courseViewModalBody').innerHTML = `
                            <div class="alert alert-danger">
                                ${data.message || 'Kuna tatizo la kupata maelezo ya kozi'}
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('courseViewModalBody').innerHTML = `
                        <div class="alert alert-danger">
                            Kuna tatizo la mtandao. Jaribu tena.
                        </div>
                    `;
                });
        }

        // Get status text in Swahili
        function getStatusText(status) {
            switch (status) {
                case 'published':
                    return 'Imechapishwa';
                case 'draft':
                    return 'Rasimu';
                case 'pending':
                    return 'Inasubiri';
                default:
                    return 'Haijulikani';
            }
        }

        // Open add course modal
        function openAddCourseModal() {
            const modal = new bootstrap.Modal(document.getElementById('addCourseModal'));
            modal.show();
        }

        // Save new course
        function saveCourse() {
            const form = document.getElementById('addCourseForm');
            const formData = new FormData(form);

            fetch('add_course.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Kozi imehifadhiwa kwa mafanikio!');
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

        // Edit course
        function editCourse(courseId) {
            // Redirect to edit page or open edit modal
            window.location.href = `edit_course.php?id=${courseId}`;
        }

        // Delete course
        function deleteCourse(courseId) {
            if (confirm('Una uhakika unataka kufuta kozi hii? Kitendo hiki hakiwezi kurekebishwa.')) {
                fetch('delete_course.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            id: courseId
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Kozi imefutwa kwa mafanikio!');
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
                searchCourses();
            }
        });
    </script>
</body>

</html>