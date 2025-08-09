<!DOCTYPE html>
<html lang="sw">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CSS Test - Panda Digital V3</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">

    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <style>
        .test-section {
            padding: 2rem 0;
            border-bottom: 1px solid #eee;
        }

        .color-test {
            padding: 1rem;
            margin: 0.5rem;
            border-radius: 8px;
            color: white;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <h1 class="text-center mb-5">🎨 CSS & JavaScript Test</h1>

        <!-- Bootstrap Test -->
        <div class="test-section">
            <h3>Bootstrap Test</h3>
            <div class="row">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Bootstrap Card</h5>
                            <p class="card-text">This should be styled with Bootstrap.</p>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#testModal">
                                Test Modal
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> Bootstrap Alert
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="progress">
                        <div class="progress-bar" style="width: 75%">75%</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Custom CSS Test -->
        <div class="test-section">
            <h3>Custom CSS Test</h3>
            <div class="row">
                <div class="col-md-3">
                    <div class="color-test" style="background-color: var(--primary-color);">
                        Primary Color<br>#ffbc3b
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="color-test" style="background-color: var(--secondary-color);">
                        Secondary Color<br>#5f4594
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="color-test" style="background-color: var(--success-color);">
                        Success Color<br>#10b981
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="color-test" style="background-color: var(--danger-color);">
                        Danger Color<br>#ef4444
                    </div>
                </div>
            </div>
        </div>

        <!-- Font Awesome Test -->
        <div class="test-section">
            <h3>Font Awesome Test</h3>
            <div class="row text-center">
                <div class="col-md-2">
                    <i class="fas fa-home fa-3x text-primary"></i><br>
                    <small>Home</small>
                </div>
                <div class="col-md-2">
                    <i class="fas fa-user fa-3x text-success"></i><br>
                    <small>User</small>
                </div>
                <div class="col-md-2">
                    <i class="fas fa-cog fa-3x text-warning"></i><br>
                    <small>Settings</small>
                </div>
                <div class="col-md-2">
                    <i class="fas fa-heart fa-3x text-danger"></i><br>
                    <small>Heart</small>
                </div>
                <div class="col-md-2">
                    <i class="fas fa-star fa-3x text-info"></i><br>
                    <small>Star</small>
                </div>
                <div class="col-md-2">
                    <i class="fas fa-check fa-3x text-secondary"></i><br>
                    <small>Check</small>
                </div>
            </div>
        </div>

        <!-- AOS Animation Test -->
        <div class="test-section">
            <h3>AOS Animation Test</h3>
            <div class="row">
                <div class="col-md-4" data-aos="fade-up">
                    <div class="card">
                        <div class="card-body">
                            <h5>Fade Up</h5>
                            <p>This should animate on scroll.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4" data-aos="fade-left">
                    <div class="card">
                        <div class="card-body">
                            <h5>Fade Left</h5>
                            <p>This should animate on scroll.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4" data-aos="fade-right">
                    <div class="card">
                        <div class="card-body">
                            <h5>Fade Right</h5>
                            <p>This should animate on scroll.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- JavaScript Test -->
        <div class="test-section">
            <h3>JavaScript Test</h3>
            <button class="btn btn-primary" onclick="testAlert()">Test JavaScript Alert</button>
            <button class="btn btn-success" onclick="testConsole()">Test Console Log</button>
            <button class="btn btn-warning" data-bs-toggle="tooltip" title="This is a tooltip!">Hover for Tooltip</button>
        </div>

        <div class="text-center mt-5">
            <a href="index.php" class="btn btn-primary">🏠 Go to Homepage</a>
            <a href="debug.php" class="btn btn-secondary">🐛 Debug Page</a>
        </div>
    </div>

    <!-- Test Modal -->
    <div class="modal fade" id="testModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Test Modal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>This modal should work if Bootstrap JavaScript is loaded correctly.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="assets/js/script.js"></script>

    <script>
        // Initialize AOS
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true
        });

        // Initialize Bootstrap tooltips
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });

        // Test functions
        function testAlert() {
            alert('JavaScript is working!');
        }

        function testConsole() {
            console.log('Console logging is working!');
            alert('Check browser console for log message');
        }
    </script>
</body>

</html>