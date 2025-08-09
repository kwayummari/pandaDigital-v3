<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Color Test - Panda Digital V3</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    
    <style>
        .color-test {
            padding: 2rem;
            margin: 1rem;
            border-radius: 8px;
            color: white;
            text-align: center;
            font-weight: 600;
        }
        .test-section {
            margin: 2rem 0;
            padding: 2rem;
            border: 1px solid #ddd;
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-5">🎨 Color Test - Panda Digital V3</h1>
        
        <!-- Brand Colors Test -->
        <div class="test-section">
            <h3>Brand Colors Test</h3>
            <div class="row">
                <div class="col-md-6">
                    <div class="color-test" style="background-color: var(--primary-color);">
                        Primary Color<br>#ffbc3b
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="color-test" style="background-color: var(--secondary-color);">
                        Secondary Color<br>#5f4594
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Universal Colors Test -->
        <div class="test-section">
            <h3>Universal Colors Test</h3>
            <div class="row">
                <div class="col-md-6">
                    <div class="color-test" style="background-color: var(--white-color); color: var(--black-color);">
                        White Color<br>#ffffff
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="color-test" style="background-color: var(--black-color);">
                        Black Color<br>#000000
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Button Test -->
        <div class="test-section">
            <h3>Button Test</h3>
            <div class="row">
                <div class="col-md-4">
                    <button class="btn btn-primary btn-lg w-100">Primary Button</button>
                </div>
                <div class="col-md-4">
                    <button class="btn btn-outline-primary btn-lg w-100">Outline Primary</button>
                </div>
                <div class="col-md-4">
                    <button class="btn btn-secondary btn-lg w-100">Secondary Button</button>
                </div>
            </div>
        </div>
        
        <!-- Navbar Test -->
        <div class="test-section">
            <h3>Navbar Test</h3>
            <nav class="navbar navbar-expand-lg navbar-light">
                <div class="container">
                    <a class="navbar-brand" href="#">Panda Digital</a>
                    <div class="navbar-nav ms-auto">
                        <a class="nav-link active" href="#">NYUMBANI</a>
                        <a class="nav-link" href="#">KOZI ZOTE</a>
                        <a class="nav-link" href="#">FURSA</a>
                    </div>
                </div>
            </nav>
        </div>
        
        <!-- Hero Section Test -->
        <div class="test-section">
            <h3>Hero Section Test</h3>
            <div class="hero-section" style="min-height: 300px;">
                <div class="hero-container">
                    <div class="container">
                        <div class="row justify-content-center">
                            <div class="col-lg-8 text-center">
                                <div class="hero-content">
                                    <h1 class="hero-title">Test Hero Title</h1>
                                    <p class="hero-subtitle">This is a test subtitle to check colors</p>
                                    <div class="hero-buttons">
                                        <a href="#" class="btn btn-primary btn-lg">Primary Button</a>
                                        <a href="#" class="btn btn-outline-light btn-lg">Outline Button</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-5">
            <a href="index.php" class="btn btn-primary">🏠 Go to Homepage</a>
            <a href="preview.php" class="btn btn-secondary">👀 Preview Page</a>
        </div>
    </div>
    
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 