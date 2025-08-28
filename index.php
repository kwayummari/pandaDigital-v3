<?php
require_once 'config/init.php';
require_once 'models/Blog.php';
require_once 'models/Fursa.php';
require_once 'models/Wanufaika.php';
require_once 'models/Expert.php';
require_once 'models/User.php';

// Initialize models
$blogModel = new Blog();
$fursaModel = new Fursa();
$wanufaikaModel = new Wanufaika();
$expertModel = new Expert();

// Fetch data from database
$latestBlogPosts = $blogModel->getLatestPosts(6);
$latestOpportunities = $fursaModel->getLatestOpportunities(6);
$featuredWanufaika = $wanufaikaModel->getLatestWanufaika(6);
$featuredExperts = array_slice($expertModel->getAllExperts(), 0, 4);
?>

<?php include 'includes/header.php'; ?>

<!-- Modern Hero Section -->
<section class="hero-section">
    <div class="hero-container">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10 text-center" data-aos="fade-up">
                    <div class="hero-content">
                        <div class="hero-badge" style="text-transform: capitalize;">
                            Pata fursa, ujuzi wa kidijitali, anzisha biashara, <br> na jijengee uhuru wa kiuchumi kupitia Panda Digital.
                        </div>
                        <h1 class="hero-title">Kuwa Mjasiriamali wa Kidijitali</h1>
                        <p class="hero-subtitle">Jisajili. Chagua kozi. Jifunze. Pata cheti. Kutana na fursa. <?= htmlspecialchars($appConfig['name']) ?></p>
                        <div class="hero-buttons">
                            <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#signupModal">
                                Jisajili
                            </a>
                            <a href="#" class="btn btn-outline-primary">
                                PandaSMS
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="hero-scroll-indicator">
        <a href="#stats" class="scroll-down">
            <i class="fas fa-chevron-down"></i>
        </a>
    </div>
</section>

<!-- Tunachokupa Section -->
<section class="features-section py-5">
    <div class="container">
        <div class="row text-center mb-5">
            <div class="col-lg-8 mx-auto" data-aos="fade-up">
                <h2 class="section-title">Tunachokupa</h2>
                <p class="section-subtitle">Jukwaa kamili la kujifunza, kuendeleza biashara, na kupata msaada wa kitaalamu</p>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="100">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <h3 class="feature-title">Masomo ya Kidijitali kwa Kiswahili</h3>
                    <p class="feature-description">
                        Masoko mtandaoni, mbinu za biashara hadi uanzishaji biashara ya vipodozi asili.
                        Kozi zote kwa lugha ya Kiswahili ili uweze kujifunza kwa urahisi.
                    </p>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="200">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h3 class="feature-title">Kozi za Bure na za Kulipia</h3>
                    <p class="feature-description">
                        Zinazopatikana muda wowote, mahali popote – mijini na vijijini.
                        Jifunze kwa kasi yako mwenyewe na upate cheti cha kusadikika.
                    </p>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="300">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-comments"></i>
                    </div>
                    <h3 class="feature-title">Msaada kupitia Panda Chat</h3>
                    <p class="feature-description">
                        Uliza maswali na pata ushauri wa kitaalamu kutoka kwa wataalamu wetu.
                        Panda Chat inakupa msaada wa moja kwa moja na majibu ya haraka.
                    </p>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="400">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-store"></i>
                    </div>
                    <h3 class="feature-title">Onesha na Tangaza Biashara</h3>
                    <p class="feature-description">
                        Kupitia jukwaa letu, unaweza kutangaza biashara yako na kufikia wateja wengi.
                        Jukwaa salama na la kusadikika kwa biashara za kidijitali.
                    </p>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="500">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-sms"></i>
                    </div>
                    <h3 class="feature-title">Jifunze kwa SMS</h3>
                    <p class="feature-description">
                        Bila intaneti au smartphone. PandaSMS inakupa fursa ya kujifunza
                        kupitia ujumbe wa SMS, hata kwenye simu za kawaida.
                    </p>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="600">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3 class="feature-title">Ongea Hub</h3>
                    <p class="feature-description">
                        Mahali salama pa kuripoti unyanyasaji wa kijinsia na rushwa ya ngono.
                        Tunakupa msaada na usaidizi wa kisheria na kijamii.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Wasemavyo Wanufaika Section -->
<section class="testimonials-section py-5 bg-light">
    <div class="container">
        <div class="row text-center mb-5">
            <div class="col-lg-8 mx-auto" data-aos="fade-up">
                <h2 class="section-title">Wasemavyo Wanufaika</h2>
                <p class="section-subtitle">Sikia kutoka kwa wanawake wajasiriamali waliofanikiwa kupitia jukwaa letu</p>
            </div>
        </div>

        <div class="row">
            <?php if (!empty($featuredWanufaika)): ?>
                <?php foreach (array_slice($featuredWanufaika, 0, 3) as $index => $testimonial): ?>
                    <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="<?= ($index + 1) * 100 ?>">
                        <div class="testimonial-card">
                            <div class="testimonial-image mb-3">
                                <?php if (!empty($testimonial['photo'])): ?>
                                    <img src="<?= $wanufaikaModel->getImageUrl($testimonial['photo']) ?>"
                                        alt="<?= htmlspecialchars($testimonial['name']) ?>"
                                        class="img-fluid rounded-circle"
                                        style="width: 80px; height: 80px; object-fit: cover;">
                                <?php else: ?>
                                    <img src="<?= upload_url('Wanufaika/1.jpeg') ?>"
                                        alt="Default Wanufaika Image"
                                        class="img-fluid rounded-circle"
                                        style="width: 80px; height: 80px; object-fit: cover;">
                                <?php endif; ?>
                            </div>
                            <div class="testimonial-content">
                                <div class="testimonial-quote">
                                    <i class="fas fa-quote-left"></i>
                                    <p style="color: #000000;">"<?= htmlspecialchars($wanufaikaModel->truncateText($testimonial['description'], 120)) ?>"</p>
                                </div>
                                <div class="testimonial-author">
                                    <div class="author-info">
                                        <h5><?= htmlspecialchars($testimonial['name']) ?></h5>
                                        <span><?= htmlspecialchars($testimonial['title']) ?></span>
                                        <div class="success-metrics">
                                            <small><i class="fas fa-chart-line text-success me-1"></i>Mafanikio</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Fallback content if no testimonials found -->
                <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="testimonial-card">
                        <div class="testimonial-image mb-3">
                            <img src="<?= upload_url('Wanufaika/1.jpeg') ?>"
                                alt="Default Wanufaika Image"
                                class="img-fluid rounded-circle"
                                style="width: 80px; height: 80px; object-fit: cover;">
                        </div>
                        <div class="testimonial-content">
                            <div class="testimonial-quote">
                                <i class="fas fa-quote-left"></i>
                                <p>"Kupitia Panda Digital, nimejifunza ujuzi wa masoko mtandaoni na sasa biashara yangu inaendelea vizuri. Nimepata wateja wengi na mapato yangu yameongezeka mara tatu!"</p>
                            </div>
                            <div class="testimonial-author">
                                <div class="author-info">
                                    <h5>Sarah Mwambene</h5>
                                    <span>Mjasiriamali wa Vipodozi Asili</span>
                                    <div class="success-metrics">
                                        <small><i class="fas fa-chart-line text-success me-1"></i>Mapato yameongezeka 300%</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="testimonial-card">
                        <div class="testimonial-image mb-3">
                            <img src="<?= upload_url('Wanufaika/1.jpeg') ?>"
                                alt="Default Wanufaika Image"
                                class="img-fluid rounded-circle"
                                style="width: 80px; height: 80px; object-fit: cover;">
                        </div>
                        <div class="testimonial-content">
                            <div class="testimonial-quote">
                                <i class="fas fa-quote-left"></i>
                                <p>"Kozi za Panda Digital zimenisaidia kujifunza usimamizi wa fedha na sasa ninaweza kuendesha biashara yangu kwa njia ya kitaalamu. Nimepata ufadhili wa milioni 2!"</p>
                            </div>
                            <div class="testimonial-author">
                                <div class="author-info">
                                    <h5>Fatima Hassan</h5>
                                    <span>Mjasiriamali wa Teknolojia</span>
                                    <div class="success-metrics">
                                        <small><i class="fas fa-handshake text-success me-1"></i>Ufadhili wa TZS 2M</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="testimonial-card">
                        <div class="testimonial-image mb-3">
                            <img src="<?= upload_url('Wanufaika/1.jpeg') ?>"
                                alt="Default Wanufaika Image"
                                class="img-fluid rounded-circle"
                                style="width: 80px; height: 80px; object-fit: cover;">
                        </div>
                        <div class="testimonial-content">
                            <div class="testimonial-quote">
                                <i class="fas fa-quote-left"></i>
                                <p>"Panda Chat inanisaidia kupata ushauri wa haraka kutoka kwa wataalamu. Sikuwa na ujuzi wa kufanya masoko mtandaoni, lakini sasa ninafahamu kila kitu!"</p>
                            </div>
                            <div class="testimonial-author">
                                <div class="author-info">
                                    <h5>Grace Mushi</h5>
                                    <span>Mjasiriamali wa Mavazi</span>
                                    <div class="success-metrics">
                                        <small><i class="fas fa-users text-success me-1"></i>Wateja 500+ mtandaoni</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>


    </div>
</section>

<!-- Jinsi Inavyofanya Kazi Section -->
<section class="how-it-works-section py-5">
    <div class="container">
        <div class="row text-center mb-5">
            <div class="col-lg-8 mx-auto" data-aos="fade-up">
                <h2 class="section-title">Jinsi Inavyofanya Kazi</h2>
                <p class="section-subtitle">Fuata hatua hizi rahisi kuwa mwanufaika wa Panda Digital</p>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="100">
                <div class="step-card">
                    <div class="step-icon">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <h3 class="step-title">Jisajili</h3>
                    <p class="step-description">
                        Jisajili mtandaoni au kwa SMS (tuma neno SAJILI kwenda 0767-680-463).
                    </p>
                    <div class="step-actions">
                        <a href="#" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#signupModal">
                            <i class="fas fa-user-plus me-1"></i>Jisajili Mtandaoni
                        </a>
                        <div class="sms-info mt-2">
                            <small class="text-muted">
                                <i class="fas fa-sms me-1"></i>SMS: SAJILI kwenda 0767-680-463
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="200">
                <div class="step-card">
                    <div class="step-icon">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <h3 class="step-title">Chagua Kozi</h3>
                    <p class="step-description">
                        Chagua kozi unayopenda kwa Kiswahili kutoka kwenye orodha yetu ya kozi.
                    </p>
                    <div class="step-actions">
                        <a href="<?= app_url('kozi.php') ?>" class="btn btn-sm btn-outline-primary" onclick="return checkProfileCompletion('study')">
                            <i class="fas fa-search me-1"></i>Tazama Kozi
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="300">
                <div class="step-card">
                    <div class="step-icon">
                        <i class="fas fa-certificate"></i>
                    </div>
                    <h3 class="step-title">Jifunze na Upate Cheti</h3>
                    <p class="step-description">
                        Jifunze na upate cheti (mtandaoni au nakala ngumu).
                    </p>
                    <div class="step-actions">
                        <a href="<?= app_url('kozi.php') ?>" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-play me-1"></i>Anza Kujifunza
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="400">
                <div class="step-card">
                    <div class="step-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3 class="step-title">Ungana na Jumuiya</h3>
                    <p class="step-description">
                        Ungana kupitia Panda Chat, tafuta fursa, na jiunge na Ongea Hub.
                    </p>
                    <div class="step-actions">
                        <a href="<?= app_url('uliza-swali.php') ?>" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-comments me-1"></i>Panda Chat
                        </a>
                        <a href="<?= app_url('uliza-swali.php') ?>" class="btn btn-sm btn-outline-primary mt-1">
                            <i class="fas fa-shield-alt me-1"></i>Ongea Hub
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Wataalamu Wetu Section -->
<section class="experts-section py-5">
    <div class="container">
        <div class="row text-center mb-5">
            <div class="col-lg-8 mx-auto" data-aos="fade-up">
                <h2 class="section-title">Wataalamu Wetu</h2>
                <p class="section-subtitle">Wataalamu wa kitaalamu na wenye uzoefu wa kutosha katika sekta ya kidijitali na biashara</p>
            </div>
        </div>

        <div class="row">
            <?php if (!empty($featuredExperts)): ?>
                <?php foreach ($featuredExperts as $index => $expert): ?>
                    <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="<?= ($index + 1) * 100 ?>">
                        <div class="expert-card">
                            <div class="expert-image">
                                <img src="<?= $expertModel->getExpertImageUrl($expert['profile_photo']) ?>"
                                    alt="<?= htmlspecialchars($expert['first_name'] . ' ' . $expert['last_name']) ?>"
                                    class="img-fluid rounded-circle">
                            </div>
                            <div class="expert-content">
                                <h3 class="expert-name"><?= htmlspecialchars($expert['first_name'] . ' ' . $expert['last_name']) ?></h3>
                                <p class="expert-title"><?= htmlspecialchars($expert['business'] ?? 'Mtaalamu') ?></p>
                                <p class="expert-description">
                                    Mtaalamu wa kitaalamu na wenye uzoefu wa kutosha katika sekta ya kidijitali na biashara.
                                </p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Fallback content if no experts found -->
                <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="expert-card">
                        <div class="expert-image">
                            <img src="<?= asset('images/experts/default-expert.jpg') ?>"
                                alt="Default Expert Image"
                                class="img-fluid rounded-circle"
                                onerror="this.src='<?= asset('images/experts/default-expert.jpg') ?>'">
                        </div>
                        <div class="expert-content">
                            <h3 class="expert-name">Kennedy Mmari</h3>
                            <p class="expert-title">Mtaalamu wa Kidijitali</p>
                            <p class="expert-description">
                                Mtaalamu wa teknolojia na kidijitali na uzoefu wa zaidi ya miaka 10 katika sekta ya IT na biashara za kidijitali.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="expert-card">
                        <div class="expert-image">
                            <img src="<?= asset('images/experts/default-expert.jpg') ?>"
                                alt="Default Expert Image"
                                class="img-fluid rounded-circle"
                                onerror="this.src='<?= asset('images/experts/default-expert.jpg') ?>'">
                        </div>
                        <div class="expert-content">
                            <h3 class="expert-name">Michael Valentine Mallya</h3>
                            <p class="expert-title">Mtaalamu wa Biashara</p>
                            <p class="expert-description">
                                Mjasiriamali mwenye uzoefu wa kuanza na kuendesha biashara za kidijitali na mafanikio makubwa katika sekta ya teknolojia.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="expert-card">
                        <div class="expert-image">
                            <img src="<?= asset('images/experts/default-expert.jpg') ?>"
                                alt="Default Expert Image"
                                class="img-fluid rounded-circle"
                                onerror="this.src='<?= asset('images/experts/default-expert.jpg') ?>'">
                        </div>
                        <div class="expert-content">
                            <h3 class="expert-name">Getrude Joseph Mligo</h3>
                            <p class="expert-title">Mtaalamu wa Elimu</p>
                            <p class="expert-description">
                                Mtaalamu wa elimu na mafunzo ya kidijitali na uzoefu wa kutosha katika kujifunza na kufundisha teknolojia.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="400">
                    <div class="expert-card">
                        <div class="expert-image">
                            <img src="<?= asset('images/experts/default-expert.jpg') ?>"
                                alt="Default Expert Image"
                                class="img-fluid rounded-circle"
                                onerror="this.src='<?= asset('images/experts/default-expert.jpg') ?>'">
                        </div>
                        <div class="expert-content">
                            <h3 class="expert-name">Jaza na Wengine</h3>
                            <p class="expert-title">Wataalamu wa Sekta Mbalimbali</p>
                            <p class="expert-description">
                                Tuna wataalamu wengine wengi kutoka sekta mbalimbali za kidijitali, biashara, elimu, na teknolojia.
                            </p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- View All Experts Button -->
        <div class="row mt-5">
            <div class="col-12 text-center" data-aos="fade-up">
                <a href="<?= app_url('uliza-swali.php') ?>" class="btn btn-primary btn-lg">
                    <i class="fas fa-users me-2"></i>Tazama Wataalamu Wote
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section id="stats" class="stats-section">
    <div class="container">
        <div class="row text-center mb-5">
            <div class="col-lg-8 mx-auto" data-aos="fade-up">
                <h2 class="section-title">Matokeo Katika Namba</h2>
                <!-- <p class="section-subtitle">Wataalamu wa kitaalamu na wenye uzoefu wa kutosha katika sekta ya kidijitali na biashara</p> -->
            </div>
        </div>
        <div class="row">
            <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="100">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-number">5,000+</div>
                    <div class="stat-label">Wanafunzi Waliojifunza</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="200">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <div class="stat-number">200+</div>
                    <div class="stat-label">Kozi za Kidijitali</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="300">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-store"></i>
                    </div>
                    <div class="stat-number">1,500+</div>
                    <div class="stat-label">Biashara Zilizoundwa</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="400">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="stat-number">95%</div>
                    <div class="stat-label">Uwezo wa Kujitegemea</div>
                </div>
            </div>
        </div>
    </div>
</section>




<!-- Washirika Wetu Section -->
<section class="partners-section">
    <div class="container">
        <div class="row text-center mb-5">
            <div class="col-lg-8 mx-auto" data-aos="fade-up">
                <h2 class="section-title">Washirika Wetu</h2>
                <p class="section-subtitle">Tunafanya kazi na washirika wa kusadikika katika sekta ya maendeleo</p>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-2 col-md-4 col-6 mb-4" data-aos="fade-up" data-aos-delay="100">
                <div class="partner-logo">
                    <img src="<?= asset('images/Logo EKN high resolution (1) (1).jpg') ?>" alt="EKN Partner">
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-6 mb-4" data-aos="fade-up" data-aos-delay="200">
                <div class="partner-logo">
                    <img src="<?= asset('images/roddenberry (1).png') ?>" alt="Roddenberry Partner">
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-6 mb-4" data-aos="fade-up" data-aos-delay="300">
                <div class="partner-logo">
                    <img src="<?= asset('images/SFFlogolong.jpeg') ?>" alt="SFF Partner">
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-6 mb-4" data-aos="fade-up" data-aos-delay="400">
                <div class="partner-logo">
                    <img src="<?= asset('images/Serengeti-Bytes-logo-1-ai-q46m1eri9z6ztzpd7mnkhscwf4hfmgddieelg3l534.png') ?>" alt="Serengeti Bytes Partner">
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-6 mb-4" data-aos="fade-up" data-aos-delay="500">
                <div class="partner-logo">
                    <img src="<?= asset('images/WFT-Trust-Logo-q46m1eri9z6ztzpd7mnkhscwf4hfmgddieelg3l534.jpg') ?>" alt="WFT Trust Partner">
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-6 mb-4" data-aos="fade-up" data-aos-delay="600">
                <div class="partner-logo">
                    <img src="<?= asset('images/women-first-q46m1eri9z6ztzpd7mnkhscwf4hfmgddieelg3l534.png') ?>" alt="Women First Partner">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8" data-aos="fade-right">
                <h2 class="cta-title">Jisajili na unufaike leo</h2>
                <p class="cta-subtitle">Kuwa mshirika na kusaidia wanawake kunufaika na uchumi wa kidigitali.</p>
                <p class="cta-subtitle">Jiunge na #PandaUwezavyo campaign.</p>
            </div>
            <div class="col-lg-4 text-lg-end" data-aos="fade-left">
                <a href="#" class="btn btn-light btn-lg" data-bs-toggle="modal" data-bs-target="#signupModal">
                    <i class="fas fa-rocket me-2"></i>Anza Sasa
                </a>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>

<style>
    /* Enhanced Hero Section Styles */
    .hero-section {
        position: relative;
        min-height: 100vh;
        display: flex;
        align-items: center;
        background: linear-gradient(135deg, var(--secondary-color, #5f4594) 0%, var(--secondary-dark, #4a3675) 100%);
        overflow: hidden;
        padding: 4rem 0;
    }

    .hero-container {
        position: relative;
        z-index: 3;
        width: 100%;
    }

    .hero-content {
        color: white;
        padding: 2rem 0;
    }

    .hero-badge {
        display: inline-block;
        background: rgba(255, 188, 59, 0.2);
        padding: 0.75rem 2rem;
        border-radius: 50px;
        font-size: 0.9rem;
        font-weight: 600;
        margin-bottom: 2rem;
        border: 2px solid var(--primary-color, #ffbc3b);
        color: var(--primary-color, #ffbc3b);
        text-transform: uppercase;
        letter-spacing: 1px;
        backdrop-filter: blur(10px);
    }

    .hero-title {
        font-size: 3rem;
        font-weight: 700;
        line-height: 1.2;
        margin-bottom: 1.5rem;
        color: white;
    }

    .hero-subtitle {
        font-size: 1.25rem;
        font-weight: 500;
        margin-bottom: 2rem;
        color: rgba(255, 255, 255, 0.9);
        line-height: 1.6;
    }

    .hero-buttons {
        margin-bottom: 2rem;
    }

    .hero-buttons .btn {
        padding: 1rem 2rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-radius: 50px;
        transition: all 0.3s ease;
    }

    .hero-buttons .btn-primary {
        background: var(--primary-color, #ffbc3b);
        border-color: var(--primary-color, #ffbc3b);
        color: #333;
    }

    .hero-buttons .btn-primary:hover {
        background: #e6a800;
        border-color: #e6a800;
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(255, 188, 59, 0.3);
    }

    .hero-buttons .btn-outline-primary {
        border: 2px solid var(--primary-color, #ffbc3b);
        color: var(--primary-color, #ffbc3b);
        background: transparent;
    }

    .hero-buttons .btn-outline-primary:hover {
        background: var(--primary-color, #ffbc3b);
        color: #333;
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(255, 188, 59, 0.3);
    }

    .hero-features {
        margin-top: 2rem;
    }

    .hero-feature {
        display: flex;
        align-items: center;
        margin-bottom: 1rem;
        font-size: 1.1rem;
        color: rgba(255, 255, 255, 0.9);
    }

    .hero-feature i {
        font-size: 1.2rem;
        margin-right: 0.75rem;
        color: #28a745;
    }

    .hero-image {
        position: relative;
        text-align: center;
    }

    .hero-image img {
        max-width: 100%;
        height: auto;
        border-radius: 20px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
    }

    .hero-image-overlay {
        position: absolute;
        bottom: 20px;
        left: 50%;
        transform: translateX(-50%);
        background: rgba(255, 255, 255, 0.95);
        padding: 1.5rem;
        border-radius: 15px;
        text-align: center;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        backdrop-filter: blur(10px);
    }

    .overlay-content h4 {
        color: var(--secondary-color, #5f4594);
        font-weight: 700;
        margin-bottom: 0.5rem;
        font-size: 1.1rem;
    }

    .overlay-content p {
        color: #666;
        margin: 0;
        font-size: 0.9rem;
    }

    .hero-scroll-indicator {
        position: absolute;
        bottom: 2rem;
        left: 50%;
        transform: translateX(-50%);
        z-index: 3;
    }

    .scroll-down {
        display: inline-block;
        color: white;
        font-size: 1.5rem;
        animation: bounce 2s infinite;
        text-decoration: none;
    }

    .scroll-down:hover {
        color: var(--primary-color, #ffbc3b);
    }

    @keyframes bounce {

        0%,
        20%,
        50%,
        80%,
        100% {
            transform: translateY(0);
        }

        40% {
            transform: translateY(-10px);
        }

        60% {
            transform: translateY(-5px);
        }
    }

    /* Responsive adjustments */
    @media (max-width: 991.98px) {
        .hero-title {
            font-size: 2.5rem;
        }

        .hero-subtitle {
            font-size: 1.1rem;
        }

        .hero-buttons .btn {
            display: block;
            width: 100%;
            margin-bottom: 1rem;
        }

        .hero-image {
            margin-top: 2rem;
        }
    }

    @media (max-width: 767.98px) {
        .hero-title {
            font-size: 2rem;
        }

        .hero-badge {
            font-size: 0.8rem;
            padding: 0.5rem 1.5rem;
        }

        .hero-feature {
            font-size: 1rem;
        }
    }

    /* Feature Cards Styles */
    .feature-card {
        background: white;
        border-radius: 20px;
        padding: 2rem;
        text-align: center;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        height: 100%;
        border: 1px solid #f0f0f0;
        position: relative;
        overflow: hidden;
    }

    .feature-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--primary-color, #ffbc3b), var(--secondary-color, #5f4594));
        transform: scaleX(0);
        transition: transform 0.3s ease;
    }

    .feature-card:hover::before {
        transform: scaleX(1);
    }

    .feature-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    }

    .feature-icon {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, var(--primary-color, #ffbc3b), #e6a800);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
        font-size: 2rem;
        color: white;
        transition: all 0.3s ease;
    }

    .feature-card:hover .feature-icon {
        transform: scale(1.1);
        box-shadow: 0 10px 25px rgba(255, 188, 59, 0.4);
    }

    .feature-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--secondary-color, #5f4594);
        margin-bottom: 1rem;
        line-height: 1.3;
    }

    .feature-description {
        color: #666;
        line-height: 1.6;
        margin-bottom: 0;
        font-size: 0.95rem;
    }

    /* Section Titles */
    .section-title {
        font-size: 2.5rem;
        font-weight: 700;
        color: var(--secondary-color, #5f4594);
        margin-bottom: 1rem;
        text-align: center;
    }

    .section-subtitle {
        font-size: 1.1rem;
        color: #666;
        text-align: center;
        max-width: 600px;
        margin: 0 auto;
        line-height: 1.6;
    }

    /* Feature Cards Styles */
    .feature-card {
        background: white;
        border-radius: 20px;
        padding: 2rem;
        text-align: center;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        height: 100%;
        border: 1px solid #f0f0f0;
        position: relative;
        overflow: hidden;
    }

    .feature-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--primary-color, #ffbc3b), var(--secondary-color, #5f4594));
        transform: scaleX(0);
        transition: transform 0.3s ease;
    }

    .feature-card:hover::before {
        transform: scaleX(1);
    }

    .feature-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    }

    .feature-icon {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, var(--primary-color, #ffbc3b), #e6a800);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
        font-size: 2rem;
        color: white;
        transition: all 0.3s ease;
    }

    .feature-card:hover .feature-icon {
        transform: scale(1.1);
        box-shadow: 0 10px 25px rgba(255, 188, 59, 0.4);
    }

    .feature-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--secondary-color, #5f4594);
        margin-bottom: 1rem;
        line-height: 1.3;
    }

    .feature-description {
        color: #666;
        line-height: 1.6;
        margin-bottom: 0;
        font-size: 0.95rem;
    }

    /* Testimonials Section Styles */
    .testimonials-section {
        background: white;
    }

    .testimonial-card {
        background: white;
        border-radius: 20px;
        padding: 2rem;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        height: 100%;
        border: 1px solid #f0f0f0;
        position: relative;
        overflow: hidden;
    }

    .testimonial-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--primary-color, #ffbc3b), var(--secondary-color, #5f4594));
        transform: scaleX(0);
        transition: transform 0.3s ease;
    }

    .testimonial-card:hover::before {
        transform: scaleX(1);
    }

    .testimonial-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    }

    .testimonial-quote {
        margin-bottom: 1.5rem;
        position: relative;
    }

    .testimonial-quote i {
        color: var(--primary-color, #ffbc3b);
        font-size: 2rem;
        margin-bottom: 1rem;
        display: block;
    }

    .testimonial-quote p {
        font-style: italic;
        color: #555;
        line-height: 1.6;
        margin: 0;
        font-size: 1rem;
    }

    .testimonial-image {
        text-align: center;
    }

    .testimonial-image img {
        border: 3px solid var(--primary-color, #ffbc3b);
        box-shadow: 0 5px 15px rgba(255, 188, 59, 0.3);
        transition: all 0.3s ease;
    }

    .testimonial-card:hover .testimonial-image img {
        transform: scale(1.05);
        box-shadow: 0 8px 25px rgba(255, 188, 59, 0.4);
    }

    .testimonial-author {
        display: flex;
        align-items: center;
        gap: 1rem;
        justify-content: center;
    }

    .author-info h5 {
        color: var(--secondary-color, #5f4594);
        font-weight: 700;
        margin: 0 0 0.25rem 0;
        font-size: 1.1rem;
        text-align: center;
    }

    .author-info span {
        color: #666;
        font-size: 0.9rem;
        display: block;
        margin-bottom: 0.5rem;
        text-align: center;
    }

    .success-metrics {
        color: var(--primary-color, #ffbc3b);
        font-weight: 600;
    }

    /* Success Stories Styles */
    .success-stories-title {
        color: var(--secondary-color, #5f4594);
        font-weight: 700;
        margin-bottom: 2rem;
        font-size: 2rem;
    }

    .success-story-card {
        background: white;
        border-radius: 20px;
        padding: 2rem;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        border: 1px solid #f0f0f0;
        height: 100%;
        position: relative;
        overflow: hidden;
    }

    .success-story-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--primary-color, #ffbc3b), var(--secondary-color, #5f4594));
        transform: scaleX(0);
        transition: transform 0.3s ease;
    }

    .success-story-card:hover::before {
        transform: scaleX(1);
    }

    .success-story-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    }

    .story-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .story-icon {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, var(--primary-color, #ffbc3b), #e6a800);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.5rem;
        flex-shrink: 0;
    }

    .story-header h4 {
        color: var(--secondary-color, #5f4594);
        font-weight: 700;
        margin: 0;
        font-size: 1.3rem;
    }

    .story-content p {
        color: #555;
        line-height: 1.6;
        margin-bottom: 1rem;
        font-size: 0.95rem;
    }

    .story-content p strong {
        color: var(--secondary-color, #5f4594);
    }

    .story-impact {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-top: 1rem;
    }

    .impact-badge {
        padding: 0.5rem 1rem;
        border-radius: 25px;
        font-size: 0.8rem;
        font-weight: 600;
        color: white;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .impact-badge.success {
        background: var(--primary-color, #ffbc3b);
        color: #333;
    }

    .impact-badge.primary {
        background: var(--primary-color, #ffbc3b);
        color: #333;
    }

    .impact-badge.info {
        background: var(--secondary-color, #5f4594);
    }

    /* Jinsi Inavyofanya Kazi Section Styles */
    .how-it-works-section {
        background: #f8f9fa;
        padding: 5rem 0;
    }

    .step-card {
        background: white;
        border-radius: 20px;
        padding: 2.5rem 2rem;
        text-align: center;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        border: 1px solid #f0f0f0;
        position: relative;
        overflow: hidden;
        height: 100%;
    }

    .step-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--primary-color, #ffbc3b), var(--secondary-color, #5f4594));
        transform: scaleX(0);
        transition: transform 0.3s ease;
    }

    .step-card:hover::before {
        transform: scaleX(1);
    }

    .step-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    }

    .step-icon {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, var(--primary-color, #ffbc3b), #e6a800);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
        font-size: 2.5rem;
        color: white;
        box-shadow: 0 5px 15px rgba(255, 188, 59, 0.3);
    }

    .step-title {
        font-size: 1.3rem;
        font-weight: 700;
        color: var(--secondary-color, #5f4594);
        margin-bottom: 1rem;
        line-height: 1.4;
    }

    .step-description {
        color: #666;
        line-height: 1.6;
        margin-bottom: 2rem;
        font-size: 0.95rem;
    }

    .step-actions .btn {
        padding: 0.75rem 1.5rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-radius: 50px;
        transition: all 0.3s ease;
        margin-right: 0.5rem;
    }

    .step-actions .btn-sm {
        padding: 0.5rem 1rem;
    }

    .step-actions .btn-primary {
        background: var(--primary-color, #ffbc3b);
        border-color: var(--primary-color, #ffbc3b);
        color: #333;
    }

    .step-actions .btn-primary:hover {
        background: #e6a800;
        border-color: #e6a800;
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(255, 188, 59, 0.3);
    }

    .step-actions .btn-outline-primary {
        border: 2px solid var(--primary-color, #ffbc3b);
        color: var(--primary-color, #ffbc3b);
        background: transparent;
    }

    .step-actions .btn-outline-primary:hover {
        background: var(--primary-color, #ffbc3b);
        color: #333;
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(255, 188, 59, 0.3);
    }

    .sms-info {
        margin-top: 1rem;
        font-size: 0.85rem;
        color: #666;
    }

    /* Wataalamu Wetu Section Styles */
    .experts-section {
        background: #f8f9fa;
        padding: 5rem 0;
    }

    .expert-card {
        background: white;
        border-radius: 20px;
        padding: 2rem;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        border: 1px solid #f0f0f0;
        position: relative;
        overflow: hidden;
    }

    .expert-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--primary-color, #ffbc3b), var(--secondary-color, #5f4594));
        transform: scaleX(0);
        transition: transform 0.3s ease;
    }

    .expert-card:hover::before {
        transform: scaleX(1);
    }

    .expert-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    }

    .expert-image {
        text-align: center;
        margin-bottom: 1.5rem;
    }

    .expert-image img {
        width: 120px;
        height: 120px;
        border: 3px solid var(--primary-color, #ffbc3b);
        border-radius: 50%;
        object-fit: cover;
        box-shadow: 0 5px 15px rgba(255, 188, 59, 0.3);
    }

    .expert-content {
        text-align: center;
    }

    .expert-name {
        color: var(--secondary-color, #5f4594);
        font-weight: 700;
        font-size: 1.3rem;
        margin-bottom: 0.5rem;
    }

    .expert-title {
        color: #666;
        font-size: 0.95rem;
        margin-bottom: 0.75rem;
    }

    .expert-description {
        color: #555;
        line-height: 1.6;
        margin-bottom: 1.5rem;
        font-size: 0.9rem;
    }
</style>