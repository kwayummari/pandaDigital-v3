<?php
require_once __DIR__ . "/../config/init.php";
require_once __DIR__ . "/../middleware/AuthMiddleware.php";

$auth = new AuthMiddleware();
$auth->requireRole('user');

$currentUser = $auth->getCurrentUser();

// Get available courses for the user
try {
    require_once __DIR__ . "/../config/database.php";
    $database = new Database();
    $conn = $database->getConnection();

    if (!$conn) {
        throw new Exception("Database connection failed");
    }

    // Get user's courses
    $courseQuery = "SELECT DISTINCT c.id, c.title FROM course c 
                    INNER JOIN video v ON c.id = v.course_id 
                    WHERE c.status = '1' 
                    ORDER BY c.title ASC";
    $courseStmt = $conn->prepare($courseQuery);
    $courseStmt->execute();
    $courses = $courseStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    error_log("Database error: " . $e->getMessage());
    $courses = [];
}
?>

<!DOCTYPE html>
<html lang="sw">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Toa Maoni - Panda Digital</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= app_url('assets/css/style.css') ?>?v=10">
    <style>
        .feedback-card {
            background: white !important;
            color: var(--secondary-color) !important;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            border: 1px solid #e9ecef !important;
            border-top: 3px solid var(--primary-color) !important;
            border-radius: 15px;
            transition: transform 0.2s ease;
            margin-bottom: 20px;
        }

        .feedback-card:hover {
            transform: translateY(-2px);
        }

        .category-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .category-card {
            background: #f8f9fa;
            border: 2px solid transparent;
            border-radius: 12px;
            padding: 1.5rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .category-card:hover {
            border-color: var(--primary-color);
            background: rgba(255, 188, 59, 0.05);
        }

        .category-card.selected {
            border-color: var(--primary-color);
            background: rgba(255, 188, 59, 0.1);
        }

        .category-icon {
            font-size: 2rem;
            margin-bottom: 1rem;
            color: var(--primary-color);
        }

        .priority-badge {
            display: inline-block;
            padding: 0.5rem 1rem;
            margin: 0.25rem;
            border: 2px solid transparent;
            border-radius: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
            background: #f8f9fa;
            color: var(--secondary-color);
        }

        .priority-badge:hover {
            border-color: var(--primary-color);
        }

        .priority-badge.selected {
            border-color: var(--primary-color);
            background: var(--primary-color);
            color: white;
        }

        .file-upload-area {
            border: 2px dashed #cbd5e1;
            border-radius: 12px;
            padding: 2rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }

        .file-upload-area:hover,
        .file-upload-area.dragover {
            border-color: var(--primary-color);
            background: rgba(255, 188, 59, 0.05);
        }

        .submit-btn {
            background: var(--primary-color);
            border: none;
            border-radius: 12px;
            padding: 1rem 2rem;
            font-size: 1.1rem;
            font-weight: 600;
            color: white;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        .submit-btn:disabled {
            opacity: 0.6;
            transform: none;
            cursor: not-allowed;
        }

        .char-counter {
            font-size: 0.85rem;
            color: #6b7280;
            text-align: right;
            margin-top: 0.25rem;
        }

        .char-counter.warning {
            color: var(--warning-color);
        }

        .char-counter.danger {
            color: var(--danger-color);
        }

        .loading-spinner {
            width: 20px;
            height: 20px;
            border: 2px solid transparent;
            border-top: 2px solid white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            display: inline-block;
            margin-right: 0.5rem;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .success-animation {
            animation: successPulse 0.6s ease-out;
        }

        @keyframes successPulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }

            100% {
                transform: scale(1);
            }
        }

        @media (max-width: 768px) {
            .category-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php include '../includes/user_sidebar.php'; ?>

            <!-- Main Content -->
            <div class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                <!-- Page Header -->
                <div class="page-header mb-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="h3 mb-0">Toa Maoni Yako</h1>
                            <p class="text-muted">Tusaidie kuboresha huduma zetu kwa kutoa maoni yako muhimu</p>
                        </div>
                    </div>
                </div>

                <!-- Feedback Form -->
                <div class="card feedback-card">
                    <div class="card-header border-0 pb-0">
                        <h4 class="mb-0">Fomu ya Maoni</h4>
                    </div>
                    <div class="card-body p-4">
                        <form id="feedbackForm" enctype="multipart/form-data">
                            <!-- Category Selection -->
                            <div class="mb-4">
                                <label class="form-label">
                                    Chagua Aina ya Maoni
                                    <span class="text-danger">*</span>
                                </label>
                                <div class="category-grid" id="categoryGrid">
                                    <!-- Categories will be loaded here -->
                                </div>
                                <input type="hidden" id="selectedCategory" name="category" required>
                            </div>

                            <!-- Course Selection (Optional) -->
                            <div class="mb-4">
                                <label for="courseId" class="form-label">
                                    Kozi (si lazima)
                                </label>
                                <select class="form-select" id="courseId" name="courseId">
                                    <option value="">Chagua kozi...</option>
                                    <?php foreach ($courses as $course): ?>
                                        <option value="<?php echo $course['id']; ?>">
                                            <?php echo htmlspecialchars($course['title']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Subject -->
                            <div class="mb-4">
                                <label for="subject" class="form-label">
                                    Kichwa cha Maoni
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="subject" name="subject"
                                    placeholder="Ingiza kichwa cha maoni yako" maxlength="255" required>
                                <div class="char-counter" id="subjectCounter">0/255</div>
                            </div>

                            <!-- Priority -->
                            <div class="mb-4">
                                <label class="form-label">
                                    Kipaumbele
                                </label>
                                <div>
                                    <span class="priority-badge" data-priority="low">
                                        Chini
                                    </span>
                                    <span class="priority-badge selected" data-priority="medium">
                                        Wastani
                                    </span>
                                    <span class="priority-badge" data-priority="high">
                                        Juu
                                    </span>
                                    <span class="priority-badge" data-priority="urgent">
                                        Haraka
                                    </span>
                                </div>
                                <input type="hidden" id="selectedPriority" name="priority" value="medium">
                            </div>

                            <!-- Message -->
                            <div class="mb-4">
                                <label for="message" class="form-label">
                                    Maelezo ya Kina
                                    <span class="text-danger">*</span>
                                </label>
                                <textarea class="form-control" id="message" name="message" rows="6"
                                    placeholder="Ingiza maelezo ya kina ya maoni yako" maxlength="2000" required></textarea>
                                <div class="char-counter" id="messageCounter">0/2000</div>
                            </div>

                            <!-- File Upload -->
                            <div class="mb-4">
                                <label class="form-label">
                                    Ambatisha Faili (si lazima)
                                </label>
                                <div class="file-upload-area" id="fileUploadArea">
                                    <p class="mb-2">Bofya au buruta faili hapa</p>
                                    <small class="text-muted">PNG, JPG, PDF, DOC (Upeo: 5MB)</small>
                                    <input type="file" id="attachment" name="attachment"
                                        accept=".png,.jpg,.jpeg,.pdf,.doc,.docx" hidden>
                                </div>
                                <div id="selectedFile" class="mt-2" style="display: none;">
                                    <div class="alert alert-info">
                                        <span id="fileName"></span>
                                        <button type="button" class="btn btn-sm btn-outline-danger ms-2" id="removeFile">
                                            Ondoa
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="text-center">
                                <button type="submit" class="submit-btn" id="submitBtn">
                                    Tuma Maoni
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
    <script>
        // Initialize the feedback form
        document.addEventListener('DOMContentLoaded', function() {
            loadCategories();
            initializeEventListeners();
        });

        // Load feedback categories
        function loadCategories() {
            const categories = [{
                    id: 'technical',
                    name: 'Matatizo ya Kiufundi',
                    color: '#dc3545'
                },
                {
                    id: 'content',
                    name: 'Yaliyomo Kwenye Kozi',
                    color: '#28a745'
                },
                {
                    id: 'payment',
                    name: 'Matatizo ya Malipo',
                    color: '#ffc107'
                },
                {
                    id: 'interface',
                    name: 'Mfumo wa Kutumia',
                    color: '#17a2b8'
                },
                {
                    id: 'feature',
                    name: 'Mapendekezo Mapya',
                    color: '#6f42c1'
                },
                {
                    id: 'account',
                    name: 'Akaunti Yangu',
                    color: '#fd7e14'
                },
                {
                    id: 'general',
                    name: 'Maoni ya Jumla',
                    color: '#6c757d'
                }
            ];

            const grid = document.getElementById('categoryGrid');
            grid.innerHTML = categories.map(cat => `
                <div class="category-card" data-category="${cat.id}">
                    <div class="category-icon" style="color: ${cat.color}">
                        ●
                    </div>
                    <h6>${cat.name}</h6>
                </div>
            `).join('');
        }

        // Initialize event listeners
        function initializeEventListeners() {
            // Category selection
            document.addEventListener('click', function(e) {
                if (e.target.closest('.category-card')) {
                    const card = e.target.closest('.category-card');
                    document.querySelectorAll('.category-card').forEach(c => c.classList.remove('selected'));
                    card.classList.add('selected');
                    document.getElementById('selectedCategory').value = card.dataset.category;
                }
            });

            // Priority selection
            document.addEventListener('click', function(e) {
                if (e.target.closest('.priority-badge')) {
                    const badge = e.target.closest('.priority-badge');
                    document.querySelectorAll('.priority-badge').forEach(b => b.classList.remove('selected'));
                    badge.classList.add('selected');
                    document.getElementById('selectedPriority').value = badge.dataset.priority;
                }
            });

            // Character counters
            setupCharacterCounters();

            // File upload
            setupFileUpload();

            // Form submission
            document.getElementById('feedbackForm').addEventListener('submit', handleFormSubmission);
        }

        // Setup character counters
        function setupCharacterCounters() {
            const subject = document.getElementById('subject');
            const message = document.getElementById('message');
            const subjectCounter = document.getElementById('subjectCounter');
            const messageCounter = document.getElementById('messageCounter');

            subject.addEventListener('input', function() {
                const length = this.value.length;
                subjectCounter.textContent = `${length}/255`;
                subjectCounter.className = length > 200 ? 'char-counter warning' :
                    length > 240 ? 'char-counter danger' : 'char-counter';
            });

            message.addEventListener('input', function() {
                const length = this.value.length;
                messageCounter.textContent = `${length}/2000`;
                messageCounter.className = length > 1500 ? 'char-counter warning' :
                    length > 1800 ? 'char-counter danger' : 'char-counter';
            });
        }

        // Setup file upload
        function setupFileUpload() {
            const fileUploadArea = document.getElementById('fileUploadArea');
            const attachment = document.getElementById('attachment');
            const selectedFile = document.getElementById('selectedFile');
            const fileName = document.getElementById('fileName');
            const removeFile = document.getElementById('removeFile');

            fileUploadArea.addEventListener('click', () => attachment.click());

            fileUploadArea.addEventListener('dragover', (e) => {
                e.preventDefault();
                fileUploadArea.classList.add('dragover');
            });

            fileUploadArea.addEventListener('dragleave', () => {
                fileUploadArea.classList.remove('dragover');
            });

            fileUploadArea.addEventListener('drop', (e) => {
                e.preventDefault();
                fileUploadArea.classList.remove('dragover');
                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    attachment.files = files;
                    handleFileSelection(files[0]);
                }
            });

            attachment.addEventListener('change', (e) => {
                if (e.target.files.length > 0) {
                    handleFileSelection(e.target.files[0]);
                }
            });

            removeFile.addEventListener('click', () => {
                attachment.value = '';
                selectedFile.style.display = 'none';
                fileName.textContent = '';
            });

            function handleFileSelection(file) {
                // Validate file
                const allowedTypes = ['image/png', 'image/jpeg', 'image/jpg', 'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
                const maxSize = 5 * 1024 * 1024; // 5MB

                if (!allowedTypes.includes(file.type)) {
                    alert('Aina ya faili haikubaliki. Tumia PNG, JPG, PDF au DOC');
                    return;
                }

                if (file.size > maxSize) {
                    alert('Faili ni kubwa sana. Tumia faili la chini ya 5MB');
                    return;
                }

                fileName.textContent = file.name;
                selectedFile.style.display = 'block';
            }
        }

        // Handle form submission
        async function handleFormSubmission(e) {
            e.preventDefault();

            const submitBtn = document.getElementById('submitBtn');
            const originalText = submitBtn.innerHTML;

            // Show loading state
            submitBtn.innerHTML = '<span class="loading-spinner"></span>Inatumwa...';
            submitBtn.disabled = true;

            try {
                const formData = new FormData(e.target);

                const response = await fetch('submit-feedback.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    // Show success message
                    showAlert('success', result.message);

                    // Reset form
                    e.target.reset();
                    document.querySelectorAll('.category-card').forEach(c => c.classList.remove('selected'));
                    document.querySelectorAll('.priority-badge').forEach(b => b.classList.remove('selected'));
                    document.querySelector('.priority-badge[data-priority="medium"]').classList.add('selected');
                    document.getElementById('selectedFile').style.display = 'none';
                    document.getElementById('subjectCounter').textContent = '0/255';
                    document.getElementById('messageCounter').textContent = '0/2000';

                    // Add success animation
                    document.querySelector('.feedback-card').classList.add('success-animation');
                    setTimeout(() => {
                        document.querySelector('.feedback-card').classList.remove('success-animation');
                    }, 600);
                } else {
                    showAlert('danger', result.message);
                }
            } catch (error) {
                console.error('Error:', error);
                showAlert('danger', 'Kuna tatizo la kiufundi. Jaribu tena.');
            } finally {
                // Reset button state
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }
        }

        // Show alert message
        function showAlert(type, message) {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
            alertDiv.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;

            const form = document.getElementById('feedbackForm');
            form.parentNode.insertBefore(alertDiv, form);

            // Auto-remove after 5 seconds
            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.remove();
                }
            }, 5000);
        }
    </script>
</body>

</html>