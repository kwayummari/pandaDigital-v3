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

                <!-- Switch to Signup -->
                <div class="text-center mt-2">
                    <small class="modal-switch-text">Huna akaunti? </small>
                    <button type="button" class="btn btn-link btn-sm p-0 ms-1 modal-switch-btn" onclick="switchToSignup()">
                        Jisajili hapa
                    </button>
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
                            Nakubaliana na <a href="<?= app_url('terms.php') ?>" target="_blank">Vigezo na Masharti</a>
                        </label>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <span class="btn-text">Jisajili</span>
                        <span class="btn-loading d-none">
                            <span class="loading"></span> Inasajili...
                        </span>
                    </button>
                </form>

                <!-- Switch to Login -->
                <div class="text-center mt-3">
                    <small class="modal-switch-text">Una akaunti tayari? </small>
                    <button type="button" class="btn btn-link btn-sm p-0 ms-1 modal-switch-btn" onclick="switchToLogin()">
                        Ingia hapa
                    </button>
                </div>
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

                    <div id="first_nameGroup" class="row">
                        <div class="col-md-6 mb-3">
                            <label for="profileFirstName" class="form-label">Jina la Kwanza *</label>
                            <input type="text" class="form-control" id="profileFirstName" name="first_name">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="profileLastName" class="form-label">Jina la Mwisho *</label>
                            <input type="text" class="form-control" id="profileLastName" name="last_name">
                        </div>
                    </div>

                    <div id="phoneGroup" class="mb-3">
                        <label for="profilePhone" class="form-label">Namba ya Simu *</label>
                        <input type="tel" class="form-control" id="profilePhone" name="phone" placeholder="Mfano: 0712345678">
                    </div>

                    <div id="regionGroup" class="mb-3">
                        <label for="profileRegion" class="form-label">Mkoa *</label>
                        <select class="form-select" id="profileRegion" name="region">
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

                    <div id="genderGroup" class="mb-3">
                        <label for="profileGender" class="form-label">Jinsia *</label>
                        <select class="form-select" id="profileGender" name="gender">
                            <option value="">Chagua Jinsia</option>
                            <option value="Mwanamke">Mwanamke</option>
                            <option value="Mwanaume">Mwanaume</option>
                        </select>
                    </div>

                    <div id="dateOfBirthGroup" class="mb-3">
                        <label for="profileDateOfBirth" class="form-label">Tarehe ya Kuzaliwa *</label>
                        <input type="date" class="form-control" id="profileDateOfBirth" name="date_of_birth">
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

    // Modal switching functions
    function switchToLogin() {
        // Hide signup modal
        const signupModal = bootstrap.Modal.getInstance(document.getElementById('signupModal'));
        if (signupModal) {
            signupModal.hide();
        }

        // Show login modal
        const loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
        loginModal.show();
    }

    function switchToSignup() {
        // Hide login modal
        const loginModal = bootstrap.Modal.getInstance(document.getElementById('loginModal'));
        if (loginModal) {
            loginModal.hide();
        }

        // Show signup modal
        const signupModal = new bootstrap.Modal(document.getElementById('signupModal'));
        signupModal.show();
    }

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

            console.log('Checking profile completion for user:', user);

            // Check what fields are missing and show appropriate form
            const missingFields = [];
            if (!user.first_name || user.first_name === '') missingFields.push('first_name');
            if (!user.last_name || user.last_name === '') missingFields.push('last_name');
            if (!user.phone || user.phone === 'null' || user.phone === '') missingFields.push('phone');
            if (!user.region || user.region === 'null' || user.region === '') missingFields.push('region');
            if (!user.gender || user.gender === 'null' || user.gender === '') missingFields.push('gender');
            if (!user.date_of_birth || user.date_of_birth === 'null' || user.date_of_birth === '') missingFields.push('date_of_birth');

            console.log('Missing fields:', missingFields);

            if (missingFields.length > 0) {
                console.log('Showing profile completion modal');
                // Show profile completion modal
                const profileModal = new bootstrap.Modal(document.getElementById('profileCompletionModal'));
                profileModal.show();

                // Show only the fields that are missing
                showMissingFields(missingFields);

                // Pre-fill existing data if available
                if (user.first_name && user.first_name !== '') document.getElementById('profileFirstName').value = user.first_name;
                if (user.last_name && user.last_name !== '') document.getElementById('profileLastName').value = user.last_name;
                if (user.phone && user.phone !== 'null' && user.phone !== '') document.getElementById('profilePhone').value = user.phone;
                if (user.region && user.region !== 'null' && user.region !== '') document.getElementById('profileRegion').value = user.region;
                if (user.gender && user.gender !== 'null' && user.gender !== '') document.getElementById('profileGender').value = user.gender;
                if (user.date_of_birth && user.date_of_birth !== 'null' && user.date_of_birth !== '') document.getElementById('profileDateOfBirth').value = user.date_of_birth;
            } else {
                console.log('Profile is complete, no modal needed');
            }
        <?php else: ?>
            console.log('User not logged in or currentUser not available');
        <?php endif; ?>
    }

    // Call profile completion check when page loads (only on specific pages)
    document.addEventListener('DOMContentLoaded', function() {
        // Only show profile completion modal on specific pages where it's needed
        const currentPath = window.location.pathname;
        const profileCompletionPages = [
            '/user/certificates.php',
            '/user/business.php',
            '/user/ask-questions.php',
            '/user/course-overview.php'
        ];

        // Check if current page is one of the profile completion pages
        const shouldCheckProfile = profileCompletionPages.some(page => currentPath.includes(page));

        if (shouldCheckProfile) {
            checkProfileCompletion();
        }
    });

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

    // Function to show/hide profile fields based on what's missing
    function showMissingFields(missingFields) {
        const allFields = ['first_name', 'last_name', 'phone', 'region', 'gender', 'date_of_birth'];

        allFields.forEach(field => {
            const fieldGroup = document.getElementById(field + 'Group');
            if (fieldGroup) {
                if (missingFields.includes(field)) {
                    fieldGroup.style.display = 'block';
                    const input = fieldGroup.querySelector('input, select');
                    if (input) input.required = true;
                } else {
                    fieldGroup.style.display = 'none';
                    const input = fieldGroup.querySelector('input, select');
                    if (input) input.required = false;
                }
            }
        });
    }

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

    // Automatic Language Detection and Translation Prompt
    (function() {
        // Check if user has already made a language choice
        if (localStorage.getItem('languageChoice')) {
            return;
        }

        // Detect user's preferred language
        const userLanguage = navigator.language || navigator.userLanguage;
        const isEnglish = userLanguage.startsWith('en');
        const isSwahili = userLanguage.startsWith('sw');

        // If user's language is English and page is in Swahili, show translation prompt
        if (isEnglish && !isSwahili) {
            // Wait for page to fully load
            setTimeout(() => {
                showTranslationPrompt();
            }, 2000);
        }
    })();

    function showTranslationPrompt() {
        // Create translation prompt modal
        const promptHtml = `
            <div id="translationPrompt" class="modal fade" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title">
                                <i class="fas fa-language me-2"></i>
                                Would you like to translate this page to English?
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p class="mb-3">
                                We detected that you might prefer English. This page is currently in Swahili.
                            </p>
                            <div class="d-flex align-items-center mb-3">
                                <img src="https://flagcdn.com/w20/tz.png" alt="Tanzania" class="me-2">
                                <span>Swahili (Current)</span>
                                <i class="fas fa-arrow-right mx-3 text-muted"></i>
                                <img src="https://flagcdn.com/w20/gb.png" alt="English" class="me-2">
                                <span>English</span>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                <i class="fas fa-times me-2"></i>No, keep Swahili
                            </button>
                            <button type="button" class="btn btn-primary" onclick="translateToEnglish()">
                                <i class="fas fa-language me-2"></i>Yes, translate to English
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Add modal to page
        document.body.insertAdjacentHTML('beforeend', promptHtml);

        // Show the modal
        const modal = new bootstrap.Modal(document.getElementById('translationPrompt'));
        modal.show();

        // Store user choice
        document.getElementById('translationPrompt').addEventListener('hidden.bs.modal', function() {
            localStorage.setItem('languageChoice', 'swahili');
        });
    }

    function translateToEnglish() {
        // Store user choice
        localStorage.setItem('languageChoice', 'english');

        // Close modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('translationPrompt'));
        modal.hide();

        // Show loading message
        showAlert('info', 'Translating page to English... Please wait.');

        // Initialize Google Translate
        if (typeof google !== 'undefined' && google.translate) {
            google.translate.TranslateElement({
                pageLanguage: 'sw',
                includedLanguages: 'en',
                layout: google.translate.TranslateElement.InlineLayout.SIMPLE,
                autoDisplay: false
            }, 'google_translate_element');

            // Trigger translation
            setTimeout(() => {
                const translateElement = document.querySelector('.goog-te-combo');
                if (translateElement) {
                    translateElement.value = 'en';
                    translateElement.dispatchEvent(new Event('change'));
                }
            }, 1000);
        } else {
            // Load Google Translate if not already loaded
            const script = document.createElement('script');
            script.src = '//translate.google.com/translate_a/element.js?cb=initTranslate';
            document.head.appendChild(script);
        }
    }

    // Initialize Google Translate when script loads
    function initTranslate() {
        google.translate.TranslateElement({
            pageLanguage: 'sw',
            includedLanguages: 'en',
            layout: google.translate.TranslateElement.InlineLayout.SIMPLE,
            autoDisplay: false
        }, 'google_translate_element');

        // Trigger translation
        setTimeout(() => {
            const translateElement = document.querySelector('.goog-te-combo');
            if (translateElement) {
                translateElement.value = 'en';
                translateElement.dispatchEvent(new Event('change'));
            }
        }, 1000);
    }

    // Add CSS for modal switch buttons
    const style = document.createElement('style');
    style.textContent = `
        .modal-switch-btn {
            color: var(--primary-color, #ffbc3b) !important;
            text-decoration: none !important;
            font-weight: 500 !important;
            transition: all 0.3s ease !important;
        }
        
        .modal-switch-btn:hover {
            color: var(--primary-color-dark, #e6a800) !important;
            text-decoration: underline !important;
        }
        
        .modal-switch-text {
            color: #6c757d !important;
            font-size: 14px !important;
        }
    `;
    document.head.appendChild(style);
</script>

</body>

</html>