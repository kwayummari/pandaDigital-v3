<!-- Modern Minimalistic Footer -->
<footer class="footer">
    <div class="container">
        <div class="footer-content">
            <!-- About Section -->
            <div class="footer-section">
                <img src="<?= asset('images/logo/logo.png') ?>" alt="<?= htmlspecialchars($appConfig['name']) ?>" height="50" class="mb-3">
                <p><?= htmlspecialchars($appConfig['name']) ?> ni jukwaa la kujifunza na kuendeleza biashara ya kidijitali kwa wanawake Tanzania. Tunakupa fursa za kujifunza ujuzi wa kidijitali na kuendeleza biashara yako.</p>
                <div class="footer-social">
                    <?php if ($socialConfig['facebook']): ?>
                        <a href="<?= htmlspecialchars($socialConfig['facebook']) ?>" target="_blank" title="Facebook">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                    <?php endif; ?>
                    <?php if ($socialConfig['twitter']): ?>
                        <a href="<?= htmlspecialchars($socialConfig['twitter']) ?>" target="_blank" title="Twitter">
                            <i class="fab fa-twitter"></i>
                        </a>
                    <?php endif; ?>
                    <?php if ($socialConfig['instagram']): ?>
                        <a href="<?= htmlspecialchars($socialConfig['instagram']) ?>" target="_blank" title="Instagram">
                            <i class="fab fa-instagram"></i>
                        </a>
                    <?php endif; ?>
                    <?php if ($socialConfig['linkedin']): ?>
                        <a href="<?= htmlspecialchars($socialConfig['linkedin']) ?>" target="_blank" title="LinkedIn">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="footer-section">
                <h5>Viungo vya Haraka</h5>
                <ul class="footer-links">
                    <li><a href="<?= app_url() ?>">Nyumbani</a></li>
                    <li><a href="<?= app_url('about.php') ?>">Kuhusu Sisi</a></li>
                    <li><a href="<?= app_url('courses.php') ?>">Kozi</a></li>
                    <li><a href="<?= app_url('opportunities.php') ?>">Fursa</a></li>
                    <li><a href="<?= app_url('habari.php') ?>">Habari</a></li>
                    <li><a href="<?= app_url('contact.php') ?>">Wasiliana Nasi</a></li>
                </ul>
            </div>

            <!-- Services -->
            <div class="footer-section">
                <h5>Huduma Zetu</h5>
                <ul class="footer-links">
                    <li><a href="<?= app_url('courses.php') ?>">Kozi za Kidijitali</a></li>
                    <li><a href="<?= app_url('mentorship.php') ?>">Ushauri wa Biashara</a></li>
                    <li><a href="<?= app_url('networking.php') ?>">Mtandao wa Wajasiriamali</a></li>
                    <li><a href="<?= app_url('funding.php') ?>">Fursa za Ufadhili</a></li>
                    <li><a href="<?= app_url('marketplace.php') ?>">Soko la Kidijitali</a></li>
                    <li><a href="<?= app_url('chat.php') ?>">Panda Chat</a></li>
                </ul>
            </div>

            <!-- Contact Info -->
            <div class="footer-section">
                <h5>Wasiliana Nasi</h5>
                <ul class="footer-links">
                    <li>
                        <i class="fas fa-map-marker-alt me-2"></i>
                        Dar es Salaam, Tanzania
                    </li>
                    <li>
                        <i class="fas fa-phone me-2"></i>
                        <a href="tel:<?= htmlspecialchars($contactConfig['phone']) ?>"><?= htmlspecialchars($contactConfig['phone']) ?></a>
                    </li>
                    <li>
                        <i class="fas fa-envelope me-2"></i>
                        <a href="mailto:<?= htmlspecialchars($contactConfig['email']) ?>"><?= htmlspecialchars($contactConfig['email']) ?></a>
                    </li>
                    <li>
                        <i class="fas fa-clock me-2"></i>
                        Jumatano - Ijumaa: 8:00 AM - 6:00 PM
                    </li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; <?= date('Y') ?> <?= htmlspecialchars($appConfig['name']) ?>. Haki zote zimehifadhiwa. | Imeundwa na <a href="#"><?= htmlspecialchars($appConfig['name']) ?> Team</a></p>
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
                <form id="signupForm">
                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="signupFirstName" class="form-label">Jina la Kwanza</label>
                            <input type="text" class="form-control" id="signupFirstName" name="first_name" value="<?= old('first_name') ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="signupLastName" class="form-label">Jina la Mwisho</label>
                            <input type="text" class="form-control" id="signupLastName" name="last_name" value="<?= old('last_name') ?>" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="signupEmail" class="form-label">Barua Pepe</label>
                        <input type="email" class="form-control" id="signupEmail" name="email" value="<?= old('email') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="signupPhone" class="form-label">Namba ya Simu</label>
                        <input type="tel" class="form-control" id="signupPhone" name="phone" value="<?= old('phone') ?>" required>
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

<!-- Alert Container -->
<div id="alertContainer" class="position-fixed top-0 end-0 p-3" style="z-index: 9999;"></div>

<!-- Scripts -->
<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- AOS Animation JS -->
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

<!-- Custom Scripts -->
<script src="<?= asset('js/script.js') ?>"></script>

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
    });
</script>
</body>

</html>