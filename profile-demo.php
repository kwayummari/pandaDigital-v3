<?php

/**
 * Profile Completion Demo Page
 * Demonstrates how the profile completion system works
 */

require_once __DIR__ . '/config/init.php';

$pageTitle = 'Profile Completion Demo - Panda Digital';

include 'includes/header.php';
?>

<!-- Page Header -->
<section class="page-header" style="background-image: url('<?= asset('images/banner/new-banner2.jpg') ?>'); background-size: cover; background-position: center; background-repeat: no-repeat; position: relative; min-height: 400px; padding: 120px 0;">
    <div class="overlay" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(0, 0, 0, 0.6);"></div>
    <div class="container" style="position: relative; z-index: 2;">
        <div class="row align-items-center">
            <div class="col-lg-12" data-aos="fade-right">
                <h1 class="page-title" style="color: #fff;">Profile Completion Demo</h1>
                <p class="page-subtitle">Jifunze jinsi ya kutumia mfumo wa kukamilisha wasifu</p>
            </div>
        </div>
    </div>
</section>

<!-- Demo Content -->
<section class="demo-content py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <!-- Profile Status -->
                <?php if (isset($_SESSION['userId'])): ?>
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-user-check me-2"></i>
                                Hali ya Wasifu Wako
                            </h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted">Profile completion system demo - not active on this page</p>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Action Buttons -->
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-play-circle me-2"></i>
                            Jaribu Vitendo Mbalimbali
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <!-- Download Certificate -->
                            <div class="col-md-6">
                                <div class="action-card text-center p-3 border rounded">
                                    <i class="fas fa-certificate fa-3x text-warning mb-3"></i>
                                    <h6>Pakua Cheti</h6>
                                    <p class="text-muted small">Unahitaji wasifu kamili</p>
                                    <button class="btn btn-warning btn-sm" onclick="tryAction('download_certificate', 'Kupakua Cheti')">
                                        Jaribu Sasa
                                    </button>
                                </div>
                            </div>

                            <!-- Contact Expert -->
                            <div class="col-md-6">
                                <div class="action-card text-center p-3 border rounded">
                                    <i class="fas fa-user-tie fa-3x text-info mb-3"></i>
                                    <h6>Wasiliana na Mtaalamu</h6>
                                    <p class="text-muted small">Unahitaji wasifu kamili</p>
                                    <button class="btn btn-info btn-sm" onclick="tryAction('contact_expert', 'Kuwasiliana na Mtaalamu')">
                                        Jaribu Sasa
                                    </button>
                                </div>
                            </div>

                            <!-- Sell Product -->
                            <div class="col-md-6">
                                <div class="action-card text-center p-3 border rounded">
                                    <i class="fas fa-store fa-3x text-success mb-3"></i>
                                    <h6>Uza Bidhaa</h6>
                                    <p class="text-muted small">Unahitaji wasifu kamili + biashara</p>
                                    <button class="btn btn-success btn-sm" onclick="tryAction('sell_product', 'Kuuza Bidhaa')">
                                        Jaribu Sasa
                                    </button>
                                </div>
                            </div>

                            <!-- Buy Product -->
                            <div class="col-md-6">
                                <div class="action-card text-center p-3 border rounded">
                                    <i class="fas fa-shopping-cart fa-3x text-primary mb-3"></i>
                                    <h6>Nunua Bidhaa</h6>
                                    <p class="text-muted small">Unahitaji wasifu kamili</p>
                                    <button class="btn btn-primary btn-sm" onclick="tryAction('buy_product', 'Kununua Bidhaa')">
                                        Jaribu Sasa
                                    </button>
                                </div>
                            </div>

                            <!-- Study Course -->
                            <div class="col-md-6">
                                <div class="action-card text-center p-3 border rounded">
                                    <i class="fas fa-graduation-cap fa-3x text-danger mb-3"></i>
                                    <h6>Soma Kozi</h6>
                                    <p class="text-muted small">Unahitaji wasifu wa msingi</p>
                                    <button class="btn btn-danger btn-sm" onclick="tryAction('study_course', 'Kusoma Kozi')">
                                        Jaribu Sasa
                                    </button>
                                </div>
                            </div>

                            <!-- View Profile -->
                            <div class="col-md-6">
                                <div class="action-card text-center p-3 border rounded">
                                    <i class="fas fa-user-edit fa-3x text-secondary mb-3"></i>
                                    <h6>Ona Wasifu</h6>
                                    <p class="text-muted small">Ona sehemu zilizokamilika</p>
                                    <button class="btn btn-secondary btn-sm" onclick="viewProfile()">
                                        Tazama Sasa
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Instructions -->
                <div class="card mt-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            Maelezo ya Matumizi
                        </h5>
                    </div>
                    <div class="card-body">
                        <h6>Jinsi ya Kutumia:</h6>
                        <ol>
                            <li><strong>Bofya kitufe chochote</strong> cha vitendo hapo juu</li>
                            <li><strong>Endapo wasifu haujakamilishwa</strong>, modal itaonekana</li>
                            <li><strong>Jaza sehemu zinazohitajika</strong> na uhifadhi</li>
                            <li><strong>Baada ya kukamilisha</strong>, utaweza kutekeleza kitendo</li>
                        </ol>

                        <h6>Sehemu Zinazohitajika:</h6>
                        <ul>
                            <li><strong>Kusoma Kozi:</strong> Jina la kwanza, Jina la mwisho, Nambari ya simu</li>
                            <li><strong>Kununua Bidhaa:</strong> Jina la kwanza, Jina la mwisho, Nambari ya simu, Mkoa</li>
                            <li><strong>Kuuza Bidhaa:</strong> Jina la kwanza, Jina la mwisho, Nambari ya simu, Mkoa, Biashara</li>
                            <li><strong>Kupakua Cheti:</strong> Jina la kwanza, Jina la mwisho, Nambari ya simu, Mkoa</li>
                            <li><strong>Kuwasiliana na Mtaalamu:</strong> Jina la kwanza, Jina la mwisho, Nambari ya simu, Mkoa</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- JavaScript -->
<script>
    function tryAction(action, actionName) {
        console.log('Trying action:', action, actionName);

        // Show success message (profile completion not active on this page)
        showAlert(`Demo action: ${actionName.toLowerCase()}. Profile completion system is not active on this demo page.`, 'info');
    }

    function viewProfile() {
        showAlert('Profile view demo - profile completion system not active on this page', 'info');
    }

    // Function to show alerts
    function showAlert(message, type = 'info') {
        const alertContainer = document.getElementById('alertContainer') || createAlertContainer();

        const alert = document.createElement('div');
        alert.className = `alert alert-${type} alert-dismissible fade show`;
        alert.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;

        alertContainer.appendChild(alert);

        // Auto-remove after 5 seconds
        setTimeout(() => {
            if (alert.parentNode) {
                alert.remove();
            }
        }, 5000);
    }

    function createAlertContainer() {
        const container = document.createElement('div');
        container.id = 'alertContainer';
        container.className = 'position-fixed top-0 end-0 p-3';
        container.style.zIndex = '9999';
        document.body.appendChild(container);
        return container;
    }
</script>

<?php include 'includes/footer.php'; ?>