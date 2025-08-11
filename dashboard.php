<?php
require_once __DIR__ . "/services/AuthService.php";

// Require authentication
$authService = new AuthService();
$authService->requireAuth();

// Get current user
$currentUser = $authService->getCurrentUser();

// Handle logout
if (isset($_POST['logout'])) {
    $authService->logoutUser();
    header('Location: /login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Panda Digital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .navbar {
            background: #667eea;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
        }
        
        .main-content {
            padding: 40px 0;
        }
        
        .welcome-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 20px;
            padding: 40px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }
        
        .welcome-card h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 10px;
        }
        
        .welcome-card p {
            font-size: 1.2rem;
            opacity: 0.9;
            margin: 0;
        }
        
        .stats-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            border: none;
            transition: transform 0.3s ease;
        }
        
        .stats-card:hover {
            transform: translateY(-5px);
        }
        
        .stats-icon {
            width: 60px;
            height: 60px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
            margin-bottom: 20px;
        }
        
        .stats-icon.primary { background: #667eea; }
        .stats-icon.success { background: #28a745; }
        .stats-icon.warning { background: #ffc107; }
        .stats-icon.info { background: #17a2b8; }
        
        .stats-number {
            font-size: 2rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 5px;
        }
        
        .stats-label {
            color: #666;
            font-size: 1rem;
        }
        
        .profile-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            border: none;
        }
        
        .profile-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: #667eea;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            color: white;
            margin: 0 auto 20px;
        }
        
        .btn-logout {
            background: #dc3545;
            border: none;
            border-radius: 10px;
            padding: 10px 20px;
            color: white;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-logout:hover {
            background: #c82333;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="/">
                <i class="fas fa-paw me-2"></i>Panda Digital
            </a>
            
            <div class="navbar-nav ms-auto">
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user me-2"></i><?php echo htmlspecialchars($currentUser['first_name']); ?>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="/profile.php"><i class="fas fa-user-edit me-2"></i>Edit Profile</a></li>
                        <li><a class="dropdown-item" href="/change-password.php"><i class="fas fa-key me-2"></i>Change Password</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" style="display: inline;">
                                <button type="submit" name="logout" class="dropdown-item text-danger">
                                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        <div class="container">
            <!-- Welcome Section -->
            <div class="welcome-card text-center">
                <h1>Welcome back, <?php echo htmlspecialchars($currentUser['first_name']); ?>!</h1>
                <p>Here's what's happening with your Panda Digital account</p>
            </div>

            <!-- Stats Row -->
            <div class="row">
                <div class="col-md-3">
                    <div class="stats-card text-center">
                        <div class="stats-icon primary">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div class="stats-number">0</div>
                        <div class="stats-label">Total Views</div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="stats-card text-center">
                        <div class="stats-icon success">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stats-number">0</div>
                        <div class="stats-label">Connections</div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="stats-card text-center">
                        <div class="stats-icon warning">
                            <i class="fas fa-star"></i>
                        </div>
                        <div class="stats-number">0</div>
                        <div class="stats-label">Reviews</div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="stats-card text-center">
                        <div class="stats-icon info">
                            <i class="fas fa-calendar"></i>
                        </div>
                        <div class="stats-number">0</div>
                        <div class="stats-label">Events</div>
                    </div>
                </div>
            </div>

            <!-- Profile and Quick Actions -->
            <div class="row">
                <div class="col-md-6">
                    <div class="profile-card">
                        <h4 class="mb-4"><i class="fas fa-user me-2"></i>Profile Information</h4>
                        
                        <div class="profile-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        
                        <div class="text-center mb-4">
                            <h5><?php echo htmlspecialchars($currentUser['first_name'] . ' ' . $currentUser['last_name']); ?></h5>
                            <p class="text-muted"><?php echo htmlspecialchars(ucfirst($currentUser['role'])); ?></p>
                        </div>
                        
                        <div class="row">
                            <div class="col-6">
                                <p><strong>Email:</strong><br><?php echo htmlspecialchars($currentUser['email']); ?></p>
                            </div>
                            <div class="col-6">
                                <p><strong>Phone:</strong><br><?php echo htmlspecialchars($currentUser['phone']); ?></p>
                            </div>
                        </div>
                        
                        <?php if ($currentUser['region']): ?>
                            <p><strong>Region:</strong> <?php echo htmlspecialchars($currentUser['region']); ?></p>
                        <?php endif; ?>
                        
                        <?php if ($currentUser['business']): ?>
                            <p><strong>Business:</strong> <?php echo htmlspecialchars($currentUser['business']); ?></p>
                        <?php endif; ?>
                        
                        <div class="text-center">
                            <a href="/profile.php" class="btn btn-primary">
                                <i class="fas fa-edit me-2"></i>Edit Profile
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="profile-card">
                        <h4 class="mb-4"><i class="fas fa-bolt me-2"></i>Quick Actions</h4>
                        
                        <div class="d-grid gap-3">
                            <a href="/biashara.php" class="btn btn-outline-primary btn-lg">
                                <i class="fas fa-store me-2"></i>Browse Businesses
                            </a>
                            
                            <a href="/fursa.php" class="btn btn-outline-success btn-lg">
                                <i class="fas fa-lightbulb me-2"></i>View Opportunities
                            </a>
                            
                            <a href="/kozi.php" class="btn btn-outline-warning btn-lg">
                                <i class="fas fa-graduation-cap me-2"></i>Take Courses
                            </a>
                            
                            <a href="/chat/" class="btn btn-outline-info btn-lg">
                                <i class="fas fa-comments me-2"></i>Chat with Experts
                            </a>
                        </div>
                        
                        <hr class="my-4">
                        
                        <h6><i class="fas fa-clock me-2"></i>Recent Activity</h6>
                        <p class="text-muted">No recent activity to show.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
