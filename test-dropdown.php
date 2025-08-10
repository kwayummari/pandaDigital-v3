<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dropdown Test</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h1>Dropdown Test Page</h1>

        <!-- Test Dropdown -->
        <div class="dropdown">
            <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                Test Dropdown
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="#">Action</a></li>
                <li><a class="dropdown-item" href="#">Another action</a></li>
                <li><a class="dropdown-item" href="#">Something else here</a></li>
            </ul>
        </div>

        <!-- Navigation Test -->
        <nav class="navbar navbar-expand-lg navbar-light bg-light mt-4">
            <div class="container">
                <a class="navbar-brand" href="#">Brand</a>

                <div class="navbar-nav ms-auto">
                    <div class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            SOKO
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#">SOKO</a></li>
                            <li><a class="dropdown-item" href="#">TANGAZA BIASHARA</a></li>
                            <li><a class="dropdown-item" href="#">PANDA MARKET</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>

        <div class="mt-4">
            <p>If the dropdowns work on this page but not on kozi.php, there's a conflict in the main page.</p>
            <p>If they don't work here either, there's a Bootstrap JavaScript issue.</p>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Test if Bootstrap is loaded
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof bootstrap !== 'undefined') {
                console.log('✅ Bootstrap is loaded');
                console.log('Bootstrap version:', bootstrap.VERSION);
            } else {
                console.log('❌ Bootstrap is NOT loaded');
            }

            // Test dropdown functionality
            const dropdowns = document.querySelectorAll('.dropdown-toggle');
            dropdowns.forEach(dropdown => {
                dropdown.addEventListener('click', function() {
                    console.log('Dropdown clicked:', this.textContent);
                });
            });
        });
    </script>
</body>

</html>