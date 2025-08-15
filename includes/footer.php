<!-- Modern Professional Footer -->
<footer class="footer">
    <!-- Alert Container for Messages -->
    <div id="alertContainer" class="position-fixed top-0 start-50 translate-middle-x p-3" style="z-index: 9999;"></div>
    
    <!-- Decorative floating elements -->
    <div class="footer-decoration">
        <div class="floating-element element-1"></div>
        <div class="floating-element element-2"></div>
        <div class="floating-element element-3"></div>
        <div class="floating-element element-4"></div>
    </div>

    <div class="container">
        <!-- Main Footer Content -->
        <div class="footer-main">
            <div class="row">
                <!-- Brand Section -->
                <div class="col-lg-4 col-md-6 mb-5">
                    <div class="footer-brand">
                        <div class="footer-logo mb-4">
                            <img src="<?= asset('images/logo/logo.png') ?>" alt="<?= htmlspecialchars($appConfig['name']) ?>" height="60">
                            <!-- <span class="ms-3 brand-text"><?= htmlspecialchars($appConfig['name']) ?></span> -->
                        </div>
                        <p class="footer-description">
                            <?= htmlspecialchars($appConfig['name']) ?> ni jukwaa la kujifunza na kuendeleza biashara ya kidijitali kwa wanawake Tanzania. Tunakupa fursa za kujifunza ujuzi wa kidijitali na kuendeleza biashara yako.
                        </p>
                        <div class="footer-social mt-4">
                            <?php if ($socialConfig['facebook']): ?>
                                <a href="<?= htmlspecialchars($socialConfig['facebook']) ?>" target="_blank" class="social-link" title="Facebook">
                                    <i class="fab fa-facebook-f"></i>
                                </a>
                            <?php endif; ?>
                            <?php if ($socialConfig['twitter']): ?>
                                <a href="<?= htmlspecialchars($socialConfig['twitter']) ?>" target="_blank" class="social-link" title="Twitter">
                                    <i class="fab fa-twitter"></i>
                                </a>
                            <?php endif; ?>
                            <?php if ($socialConfig['instagram']): ?>
                                <a href="<?= htmlspecialchars($socialConfig['instagram']) ?>" target="_blank" class="social-link" title="Instagram">
                                    <i class="fab fa-instagram"></i>
                                </a>
                            <?php endif; ?>
                            <?php if ($socialConfig['linkedin']): ?>
                                <a href="<?= htmlspecialchars($socialConfig['linkedin']) ?>" target="_blank" class="social-link" title="LinkedIn">
                                    <i class="fab fa-linkedin-in"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Quick Links -->
                <div class="col-lg-2 col-md-6 mb-5">
                    <div class="footer-section">
                        <h5 class="footer-title">Viungo vya Haraka</h5>
                        <ul class="footer-links">
                            <li><a href="<?= app_url() ?>">Nyumbani</a></li>
                            <li><a href="<?= app_url('about.php') ?>">Kuhusu Sisi</a></li>
                            <li><a href="<?= app_url('kozi.php') ?>">Kozi</a></li>
                            <li><a href="<?= app_url('fursa.php') ?>">Fursa</a></li>
                            <li><a href="<?= app_url('habari.php') ?>">Habari</a></li>
                            <li><a href="<?= app_url('contact.php') ?>">Wasiliana Nasi</a></li>
                        </ul>
                    </div>
                </div>

                <!-- Services -->
                <div class="col-lg-3 col-md-6 mb-5">
                    <div class="footer-section">
                        <h5 class="footer-title">Huduma Zetu</h5>
                        <ul class="footer-links">
                            <li><a href="<?= app_url('kozi.php') ?>">Kozi za Kidijitali</a></li>
                            <li><a href="<?= app_url('mentorship.php') ?>">Ushauri wa Biashara</a></li>
                            <li><a href="<?= app_url('networking.php') ?>">Mtandao wa Wajasiriamali</a></li>
                            <li><a href="<?= app_url('funding.php') ?>">Fursa za Ufadhili</a></li>
                            <li><a href="<?= app_url('soko.php') ?>">Soko la Kidijitali</a></li>
                            <li><a href="<?= app_url('chat.php') ?>">Panda Chat</a></li>
                        </ul>
                    </div>
                </div>

                <!-- Contact Info -->
                <div class="col-lg-3 col-md-6 mb-5">
                    <div class="footer-section">
                        <h5 class="footer-title">Wasiliana Nasi</h5>
                        <div class="contact-info">
                            <div class="contact-item">
                                <div class="contact-icon">
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                                <div class="contact-details">
                                    <span>Dar es Salaam, Tanzania</span>
                                </div>
                            </div>
                            <div class="contact-item">
                                <div class="contact-icon">
                                    <i class="fas fa-phone"></i>
                                </div>
                                <div class="contact-details">
                                    <a href="tel:<?= htmlspecialchars($contactConfig['phone']) ?>"><?= htmlspecialchars($contactConfig['phone']) ?></a>
                                </div>
                            </div>
                            <div class="contact-item">
                                <div class="contact-icon">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <div class="contact-details">
                                    <a href="mailto:<?= htmlspecialchars($contactConfig['email']) ?>"><?= htmlspecialchars($contactConfig['email']) ?></a>
                                </div>
                            </div>
                            <div class="contact-item">
                                <div class="contact-icon">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div class="contact-details">
                                    <span>Jumatano - Ijumaa: 8:00 AM - 6:00 PM</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer Bottom -->
        <div class="footer-bottom">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="copyright">
                        &copy; <?= date('Y') ?> <?= htmlspecialchars($appConfig['name']) ?>. Haki zote zimehifadhiwa.
                    </p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="credits">
                        Imeundwa na <a href="#" class="team-link"><?= htmlspecialchars($appConfig['name']) ?> Team</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- Login Modal -->
<div class="modal fade" id="loginModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ingia kwenye Akaunti Yako</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Google OAuth Login -->
                <?php
                $googleOAuth = new GoogleOAuthService();
                if ($googleOAuth->isConfigured()):
                ?>
                    <div class="google-auth-section mb-3">
                        <a href="<?php echo $googleOAuth->getAuthorizationUrl(); ?>"
                            class="btn btn-outline-danger w-100 mb-2">
                            <i class="fab fa-google me-2"></i>
                            Ingia na Google
                        </a>
                        <div class="text-center">
                            <small class="text-muted">au</small>
                        </div>
                    </div>
                <?php endif; ?>

                <form id="loginForm">
                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                    <div class="mb-3">
                        <label for="loginEmail" class="form-label">Barua Pepe au Namba ya Simu</label>
                        <input type="text" class="form-control" id="loginEmail" name="email" value="<?= old('email') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="loginPassword" class="form-label">Nywila</label>
                        <input type="password" class="form-control" id="loginPassword" name="password" required>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="rememberMe" name="remember">
                        <label class="form-check-label" for="rememberMe">Nikumbuke</label>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <span class="btn-text">Ingia</span>
                        <span class="btn-loading d-none">
                            <span class="loading"></span> Inaingia...
                        </span>
                    </button>
                </form>
                <div class="text-center mt-3">
                    <a href="<?= app_url('forgot-password.php') ?>" class="text-muted">Umesahau nywila?</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Signup Modal -->
<div class="modal fade" id="signupModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Jisajili kwenye <?= htmlspecialchars($appConfig['name']) ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Google OAuth Signup -->
                <?php
                $googleOAuth = new GoogleOAuthService();
                if ($googleOAuth->isConfigured()):
                ?>
                    <div class="google-auth-section mb-3">
                        <a href="<?php echo $googleOAuth->getAuthorizationUrl(); ?>"
                            class="btn btn-outline-danger w-100 mb-2">
                            <i class="fab fa-google me-2"></i>
                            Jisajili na Google
                        </a>
                        <div class="text-center">
                            <small class="text-muted">au</small>
                        </div>
                    </div>
                <?php endif; ?>

                <form id="signupForm">
                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                    <div class="mb-3">
                        <label for="signupEmail" class="form-label">Barua Pepe</label>
                        <input type="email" class="form-control" id="signupEmail" name="email" value="<?= old('email') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="signupPassword" class="form-label">Nywila</label>
                        <input type="password" class="form-control" id="signupPassword" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="signupConfirmPassword" class="form-label">Thibitisha Nywila</label>
                        <input type="password" class="form-control" id="signupConfirmPassword" name="confirm_password" required>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="agreeTerms" name="agree_terms" required>
                        <label class="form-check-label" for="agreeTerms">
                            Nakubaliana na <a href="<?= app_url('terms.php') ?>" target="_blank">Sheria na Masharti</a>
                        </label>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <span class="btn-text">Jisajili</span>
                        <span class="btn-loading d-none">
                            <span class="loading"></span> Inasajili...
                        </span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Profile Completion Modal -->
<div class="modal fade" id="profileCompletionModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Kamilisha Profaili Yako</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" id="closeProfileModal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Tafadhali kamilisha maelezo yako ya msingi ili uweze kutumia huduma zote za jukwaa.
                </div>

                <form id="profileCompletionForm">
                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="profileFirstName" class="form-label">Jina la Kwanza *</label>
                            <input type="text" class="form-control" id="profileFirstName" name="first_name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="profileLastName" class="form-label">Jina la Mwisho *</label>
                            <input type="text" class="form-control" id="profileLastName" name="last_name" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="profilePhone" class="form-label">Namba ya Simu *</label>
                        <input type="tel" class="form-control" id="profilePhone" name="phone" placeholder="Mfano: 0712345678" required>
                    </div>
                    <div class="mb-3">
                        <label for="profileRegion" class="form-label">Mkoa *</label>
                        <select class="form-select" id="profileRegion" name="region" required>
                            <option value="">Chagua Mkoa</option>
                            <option value="Arusha">Arusha</option>
                            <option value="Dar es Salaam">Dar es Salaam</option>
                            <option value="Dodoma">Dodoma</option>
                            <option value="Geita">Geita</option>
                            <option value="Iringa">Iringa</option>
                            <option value="Kagera">Kagera</option>
                            <option value="Katavi">Katavi</option>
                            <option value="Kigoma">Kigoma</option>
                            <option value="Kilimanjaro">Kilimanjaro</option>
                            <option value="Lindi">Lindi</option>
                            <option value="Manyara">Manyara</option>
                            <option value="Mara">Mara</option>
                            <option value="Mbeya">Mbeya</option>
                            <option value="Morogoro">Morogoro</option>
                            <option value="Mtwara">Mtwara</option>
                            <option value="Mwanza">Mwanza</option>
                            <option value="Njombe">Njombe</option>
                            <option value="Pemba North">Pemba North</option>
                            <option value="Pemba South">Pemba South</option>
                            <option value="Pwani">Pwani</option>
                            <option value="Rukwa">Rukwa</option>
                            <option value="Ruvuma">Ruvuma</option>
                            <option value="Shinyanga">Shinyanga</option>
                            <option value="Simiyu">Simiyu</option>
                            <option value="Singida">Singida</option>
                            <option value="Songwe">Songwe</option>
                            <option value="Tabora">Tabora</option>
                            <option value="Tanga">Tanga</option>
                            <option value="Unguja North">Unguja North</option>
                            <option value="Unguja South">Unguja South</option>
                            <option value="Zanzibar Central">Zanzibar Central</option>
                            <option value="Zanzibar Urban">Zanzibar Urban</option>
                            <option value="Zanzibar West">Zanzibar West</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <span class="btn-text">Hifadhi Maelezo</span>
                        <span class="btn-loading d-none">
                            <span class="loading"></span> Inahifadhi...
                        </span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Alert Container -->
<div id="alertContainer" class="position-fixed top-0 end-0 p-3" style="z-index: 9999;"></div>

<!-- Scripts -->
<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- AOS Animation JS -->
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

<!-- Custom Scripts -->
<script>
    // Pass PHP configuration to JavaScript
    window.PANDA_CONFIG = {
        baseUrl: '<?= app_url() ?>',
        apiUrl: '<?= app_url('api') ?>',
        assetsUrl: '<?= asset('') ?>'
    };
</script>
<script src="<?= asset('js/script.js') ?>?v=<?= time() ?>&cb=<?= uniqid() ?>"></script>

<script>
    // Initialize AOS
    AOS.init({
        duration: 800,
        easing: 'ease-in-out',
        once: true
    });

    // Initialize Bootstrap tooltips and popovers
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Initialize popovers
        var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
        var popoverList = popoverTriggerList.map(function(popoverTriggerEl) {
            return new bootstrap.Popover(popoverTriggerEl);
        });

        // Check if user profile is complete and show modal if needed
        checkProfileCompletion();
    });

    // Function to check if user profile is complete
    function checkProfileCompletion() {
        // Only check if user is logged in
        <?php if ($isLoggedIn && $currentUser): ?>
            const user = <?= json_encode($currentUser) ?>;

            // Check if required profile fields are missing
            if (!user.first_name || !user.last_name || !user.phone || !user.region) {
                // Show profile completion modal
                const profileModal = new bootstrap.Modal(document.getElementById('profileCompletionModal'));
                profileModal.show();

                // Pre-fill existing data if available
                if (user.first_name) document.getElementById('profileFirstName').value = user.first_name;
                if (user.last_name) document.getElementById('profileLastName').value = user.last_name;
                if (user.phone) document.getElementById('profilePhone').value = user.phone;
                if (user.region) document.getElementById('profileRegion').value = user.region;
            }
        <?php endif; ?>
    }

    // Handle signup form submission
    document.addEventListener('DOMContentLoaded', function() {
        const signupForm = document.getElementById('signupForm');
        if (signupForm) {
            signupForm.addEventListener('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(this);
                const submitBtn = this.querySelector('button[type="submit"]');
                const btnText = submitBtn.querySelector('.btn-text');
                const btnLoading = submitBtn.querySelector('.btn-loading');

                // Show loading state
                btnText.classList.add('d-none');
                btnLoading.classList.remove('d-none');
                submitBtn.disabled = true;

                // Submit form data
                fetch('<?= app_url("api/signup.php") ?>', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Show success message
                            showAlert('success', 'Usajili umekamilika kwa mafanikio! Sasa unaweza kuingia.');
                            
                            // Close signup modal
                            const signupModal = bootstrap.Modal.getInstance(document.getElementById('signupModal'));
                            signupModal.hide();
                            
                            // Clear form
                            signupForm.reset();
                            
                            // Show login modal after delay
                            setTimeout(() => {
                                const loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
                                loginModal.show();
                            }, 2000);
                        } else {
                            showAlert('danger', data.message || 'Kuna tatizo, jaribu tena.');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showAlert('danger', 'Kuna tatizo la mtandao, jaribu tena.');
                    })
                    .finally(() => {
                        // Reset button state
                        btnText.classList.remove('d-none');
                        btnLoading.classList.add('d-none');
                        submitBtn.disabled = false;
                    });
            });
        }
    });

    // Handle profile completion form submission
    document.addEventListener('DOMContentLoaded', function() {
        const profileForm = document.getElementById('profileCompletionForm');
        if (profileForm) {
            profileForm.addEventListener('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(this);
                const submitBtn = this.querySelector('button[type="submit"]');
                const btnText = submitBtn.querySelector('.btn-text');
                const btnLoading = submitBtn.querySelector('.btn-loading');

                // Show loading state
                btnText.classList.add('d-none');
                btnLoading.classList.remove('d-none');
                submitBtn.disabled = true;

                // Submit form data
                fetch('<?= app_url("api/update-profile.php") ?>', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Show success message
                            showAlert('success', 'Maelezo yako yamehifadhiwa kwa mafanikio!');

                            // Close modal after delay
                            setTimeout(() => {
                                const profileModal = bootstrap.Modal.getInstance(document.getElementById('profileCompletionModal'));
                                profileModal.hide();
                            }, 2000);

                            // Reload page to reflect changes
                            setTimeout(() => {
                                location.reload();
                            }, 2500);
                        } else {
                            showAlert('danger', data.message || 'Kuna tatizo, jaribu tena.');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showAlert('danger', 'Kuna tatizo la mtandao, jaribu tena.');
                    })
                    .finally(() => {
                        // Reset button state
                        btnText.classList.remove('d-none');
                        btnLoading.classList.add('d-none');
                        submitBtn.disabled = false;
                    });
            });
        }
    });

    // Function to show alerts
    function showAlert(type, message) {
        const alertContainer = document.getElementById('alertContainer');
        const alertId = 'alert-' + Date.now();

        const alertHtml = `
            <div id="${alertId}" class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;

        alertContainer.insertAdjacentHTML('beforeend', alertHtml);

        // Auto-remove after 5 seconds
        setTimeout(() => {
            const alert = document.getElementById(alertId);
            if (alert) {
                alert.remove();
            }
        }, 5000);
    }
</script>
</body>

</html>