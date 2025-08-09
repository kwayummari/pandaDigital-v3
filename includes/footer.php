<!-- Footer -->
<footer class="footer">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 col-md-6 mb-4">
                <img src="<?= asset('images/logo/logo.png') ?>" alt="<?= htmlspecialchars($appConfig['name']) ?>" height="50" class="mb-3">
                <p class="text-muted mb-3"><?= htmlspecialchars($appConfig['name']) ?> ni jukwaa la kujifunza na kuendeleza biashara ya kidijitali kwa wanawake Tanzania.</p>
                <div class="social-links">
                    <?php if ($socialConfig['facebook']): ?>
                    <a href="<?= htmlspecialchars($socialConfig['facebook']) ?>" target="_blank" class="text-white me-2">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <?php endif; ?>
                    
                    <?php if ($socialConfig['twitter']): ?>
                    <a href="<?= htmlspecialchars($socialConfig['twitter']) ?>" target="_blank" class="text-white me-2">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <?php endif; ?>
                    
                    <?php if ($socialConfig['linkedin']): ?>
                    <a href="<?= htmlspecialchars($socialConfig['linkedin']) ?>" target="_blank" class="text-white me-2">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                    <?php endif; ?>
                    
                    <?php if ($socialConfig['instagram']): ?>
                    <a href="<?= htmlspecialchars($socialConfig['instagram']) ?>" target="_blank" class="text-white">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <?php endif; ?>
                </div>
            </div>

            <div class="col-lg-2 col-md-6 mb-4">
                <h5>Kozi</h5>
                <ul class="footer-links">
                    <li><a href="<?= app_url('courses.php') ?>">Kozi Zote</a></li>
                    <li><a href="<?= app_url('courses.php?category=digital-marketing') ?>">Masoko ya Kidijitali</a></li>
                    <li><a href="<?= app_url('courses.php?category=entrepreneurship') ?>">Ujasiriamali</a></li>
                    <li><a href="<?= app_url('courses.php?category=content-creation') ?>">Uandishi wa Maudhui</a></li>
                    <li><a href="<?= app_url('courses.php?category=web-design') ?>">Muundo wa Tovuti</a></li>
                </ul>
            </div>

            <div class="col-lg-2 col-md-6 mb-4">
                <h5>Biashara</h5>
                <ul class="footer-links">
                    <li><a href="<?= app_url('marketplace.php') ?>">Soko</a></li>
                    <li><a href="<?= app_url('business.php') ?>">Tangaza Biashara</a></li>
                    <li><a href="<?= app_url('panda-market.php') ?>">Panda Market</a></li>
                    <li><a href="<?= app_url('opportunities.php') ?>">Fursa</a></li>
                    <li><a href="<?= app_url('success-stories.php') ?>">Mafanikio</a></li>
                </ul>
            </div>

            <div class="col-lg-2 col-md-6 mb-4">
                <h5>Msaada</h5>
                <ul class="footer-links">
                    <li><a href="<?= app_url('chat.php') ?>">Ongea Hub</a></li>
                    <li><a href="<?= app_url('expert-chat.php') ?>">Uliza Mtaalamu</a></li>
                    <li><a href="<?= app_url('faq.php') ?>">Maswali Yanayoulizwa</a></li>
                    <li><a href="<?= app_url('contact.php') ?>">Wasiliana Nasi</a></li>
                    <li><a href="<?= app_url('support.php') ?>">Msaada wa Kiufundi</a></li>
                </ul>
            </div>

            <div class="col-lg-2 col-md-6 mb-4">
                <h5>Kampuni</h5>
                <ul class="footer-links">
                    <li><a href="<?= app_url('about.php') ?>">Kuhusu Sisi</a></li>
                    <li><a href="<?= app_url('blog.php') ?>">Blog</a></li>
                    <li><a href="<?= app_url('careers.php') ?>">Kazi</a></li>
                    <li><a href="<?= app_url('privacy.php') ?>">Sera ya Faragha</a></li>
                    <li><a href="<?= app_url('terms.php') ?>">Sheria na Masharti</a></li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="mb-0">&copy; <?= date('Y') ?> <?= htmlspecialchars($appConfig['name']) ?>. Haki zote zimehifadhiwa.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-0">Imeundwa na <a href="#" class="text-primary"><?= htmlspecialchars($appConfig['name']) ?> Team</a></p>
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
                    <button type="submit" class="btn btn-primary w-100">Ingia</button>
                </form>
                <div class="text-center mt-3">
                    <a href="<?= app_url('forgot-password.php') ?>" class="text-decoration-none">Umesahau nywila?</a>
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
                <h5 class="modal-title">Jisajili Sasa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="signupForm">
                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="firstName" class="form-label">Jina la Kwanza</label>
                            <input type="text" class="form-control" id="firstName" name="first_name" value="<?= old('first_name') ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="lastName" class="form-label">Jina la Mwisho</label>
                            <input type="text" class="form-control" id="lastName" name="last_name" value="<?= old('last_name') ?>" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="signupEmail" class="form-label">Barua Pepe</label>
                        <input type="email" class="form-control" id="signupEmail" name="email" value="<?= old('email') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Namba ya Simu</label>
                        <input type="tel" class="form-control" id="phone" name="phone" value="<?= old('phone') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="signupPassword" class="form-label">Nywila</label>
                        <input type="password" class="form-control" id="signupPassword" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="confirmPassword" class="form-label">Thibitisha Nywila</label>
                        <input type="password" class="form-control" id="confirmPassword" name="password_confirm" required>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="agreeTerms" name="agree_terms" required>
                        <label class="form-check-label" for="agreeTerms">
                            Nakubaliana na <a href="<?= app_url('terms.php') ?>" target="_blank">Sheria na Masharti</a>
                        </label>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Jisajili</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- AOS Animation -->
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

<!-- Custom JS -->
<script src="<?= asset('js/script.js') ?>"></script>

<script>
    // Initialize AOS
    AOS.init({
        duration: 800,
        easing: 'ease-in-out',
        once: true
    });
    
    // Facebook Pixel - Track form submissions
    <?php if (env('FACEBOOK_PIXEL_ID')): ?>
    function trackFormSubmission(formType) {
        if (typeof fbq !== 'undefined') {
            fbq('track', 'Lead', {
                content_name: formType,
                content_category: 'form_submission'
            });
        }
    }
    <?php endif; ?>
</script>

</body>
</html>