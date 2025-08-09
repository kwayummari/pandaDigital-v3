<!-- Footer -->
<footer class="footer">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 col-md-6 mb-4">
                <img src="assets/images/logo/logo.png" alt="Panda Digital" height="50" class="mb-3">
                <p class="text-muted mb-3">Panda Digital ni jukwaa la kujifunza na kuendeleza biashara ya kidijitali kwa wanawake Tanzania.</p>
                <div class="social-links">
                    <a href="https://www.facebook.com/PandaDigitalTZ/" target="_blank" class="text-white me-2">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="https://twitter.com/pandadigitaltz" target="_blank" class="text-white me-2">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="https://www.linkedin.com/company/her-initiative/" target="_blank" class="text-white me-2">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                    <a href="https://www.instagram.com/pandadigitaltz" target="_blank" class="text-white">
                        <i class="fab fa-instagram"></i>
                    </a>
                </div>
            </div>

            <div class="col-lg-2 col-md-6 mb-4">
                <h5>Kozi</h5>
                <ul class="footer-links">
                    <li><a href="courses.php">Kozi Zote</a></li>
                    <li><a href="courses.php?category=digital-marketing">Masoko ya Kidijitali</a></li>
                    <li><a href="courses.php?category=entrepreneurship">Ujasiriamali</a></li>
                    <li><a href="courses.php?category=content-creation">Uandishi wa Maudhui</a></li>
                    <li><a href="courses.php?category=web-design">Muundo wa Tovuti</a></li>
                </ul>
            </div>

            <div class="col-lg-2 col-md-6 mb-4">
                <h5>Biashara</h5>
                <ul class="footer-links">
                    <li><a href="marketplace.php">Soko</a></li>
                    <li><a href="business.php">Tangaza Biashara</a></li>
                    <li><a href="panda-market.php">Panda Market</a></li>
                    <li><a href="opportunities.php">Fursa</a></li>
                    <li><a href="success-stories.php">Mafanikio</a></li>
                </ul>
            </div>

            <div class="col-lg-2 col-md-6 mb-4">
                <h5>Msaada</h5>
                <ul class="footer-links">
                    <li><a href="chat.php">Ongea Hub</a></li>
                    <li><a href="expert-chat.php">Uliza Mtaalamu</a></li>
                    <li><a href="faq.php">Maswali Yanayoulizwa</a></li>
                    <li><a href="contact.php">Wasiliana Nasi</a></li>
                    <li><a href="support.php">Msaada wa Kiufundi</a></li>
                </ul>
            </div>

            <div class="col-lg-2 col-md-6 mb-4">
                <h5>Kampuni</h5>
                <ul class="footer-links">
                    <li><a href="about.php">Kuhusu Sisi</a></li>
                    <li><a href="blog.php">Blog</a></li>
                    <li><a href="careers.php">Kazi</a></li>
                    <li><a href="privacy.php">Sera ya Faragha</a></li>
                    <li><a href="terms.php">Sheria na Masharti</a></li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="mb-0">&copy; 2024 Panda Digital. Haki zote zimehifadhiwa.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-0">Imeundwa na <a href="#" class="text-primary">Panda Digital Team</a></p>
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
                    <div class="mb-3">
                        <label for="loginEmail" class="form-label">Barua Pepe au Namba ya Simu</label>
                        <input type="text" class="form-control" id="loginEmail" required>
                    </div>
                    <div class="mb-3">
                        <label for="loginPassword" class="form-label">Nywila</label>
                        <input type="password" class="form-control" id="loginPassword" required>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="rememberMe">
                        <label class="form-check-label" for="rememberMe">Nikumbuke</label>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Ingia</button>
                </form>
                <div class="text-center mt-3">
                    <a href="#" class="text-decoration-none">Umesahau nywila?</a>
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
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="firstName" class="form-label">Jina la Kwanza</label>
                            <input type="text" class="form-control" id="firstName" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="lastName" class="form-label">Jina la Mwisho</label>
                            <input type="text" class="form-control" id="lastName" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="signupEmail" class="form-label">Barua Pepe</label>
                        <input type="email" class="form-control" id="signupEmail" required>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Namba ya Simu</label>
                        <input type="tel" class="form-control" id="phone" required>
                    </div>
                    <div class="mb-3">
                        <label for="signupPassword" class="form-label">Nywila</label>
                        <input type="password" class="form-control" id="signupPassword" required>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="agreeTerms" required>
                        <label class="form-check-label" for="agreeTerms">
                            Nakubaliana na <a href="terms.php" target="_blank">Sheria na Masharti</a>
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
<script src="assets/js/script.js"></script>

<script>
    // Initialize AOS
    AOS.init({
        duration: 800,
        easing: 'ease-in-out',
        once: true
    });
</script>

</body>

</html>