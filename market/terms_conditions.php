<?php
require_once '../config/init.php';
require_once '../config/database.php';

// Initialize database connection
$database = new Database();
$conn = $database->getConnection();

$pageTitle = "Sheria na Masharti - Panda Market";

include '../includes/header.php';
?>

<!-- Page Header -->
<section class="page-header" style="background-image: url('<?= asset('images/banner/market-banner.jpg') ?>'); background-size: cover; background-position: center; background-repeat: no-repeat; position: relative;">
    <div class="overlay" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(0, 0, 0, 0.6);"></div>
    <div class="container" style="position: relative; z-index: 2;">
        <div class="row align-items-center">
            <div class="col-lg-12" data-aos="fade-right">
                <h1 class="page-title" style="color: #fff;">Sheria na Masharti</h1>
                <p class="page-subtitle">Soma na uelewe sheria na masharti ya kutumia Panda Market</p>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= app_url() ?>">Nyumbani</a></li>
                        <li class="breadcrumb-item"><a href="<?= app_url('panda-market.php') ?>">Panda Market</a></li>
                        <li class="breadcrumb-item active">Sheria na Masharti</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</section>

<!-- Terms and Conditions Content -->
<section class="terms-conditions-content py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-3">
                <!-- Table of Contents -->
                <div class="card sticky-top" style="top: 100px;">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0">
                            <i class="fas fa-list me-2"></i>Orodha ya Maudhui
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        <nav class="nav flex-column">
                            <a class="nav-link" href="#introduction">Utangulizi</a>
                            <a class="nav-link" href="#definitions">Ufafanuzi</a>
                            <a class="nav-link" href="#account">Akaunti na Usajili</a>
                            <a class="nav-link" href="#products">Bidhaa na Huduma</a>
                            <a class="nav-link" href="#pricing">Bei na Malipo</a>
                            <a class="nav-link" href="#shipping">Usafiri na Uwasilishaji</a>
                            <a class="nav-link" href="#returns">Kurudisha na Kubadilisha</a>
                            <a class="nav-link" href="#privacy">Usalama na Privacy</a>
                            <a class="nav-link" href="#prohibited">Vitu Visivyoruhusiwa</a>
                            <a class="nav-link" href="#liability">Uwajibikaji</a>
                            <a class="nav-link" href="#termination">Kukatwa</a>
                            <a class="nav-link" href="#changes">Mabadiliko</a>
                            <a class="nav-link" href="#contact">Uhusiano</a>
                        </nav>
                    </div>
                </div>
            </div>

            <div class="col-lg-9">
                <!-- Introduction -->
                <div class="terms-section" id="introduction">
                    <div class="card mb-4">
                        <div class="card-body">
                            <h4 class="card-title text-primary">
                                <i class="fas fa-info-circle me-2"></i>1. Utangulizi
                            </h4>
                            <p>
                                Karibu kwenye Panda Market. Kwa kutumia jukwaa hili, unakubaliana na sheria na
                                masharti yafuatayo. Tafadhali soma kwa makini kabla ya kutumia huduma zetu.
                            </p>
                            <p>
                                Panda Market ni jukwaa la biashara mtandaoni linalowezesha watumiaji kununua
                                na kuuza bidhaa mbalimbali. Tunafanya kazi kwa mujibu wa sheria za Tanzania
                                na tunazingatia kanuni za biashara za haki.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Definitions -->
                <div class="terms-section" id="definitions">
                    <div class="card mb-4">
                        <div class="card-body">
                            <h4 class="card-title text-primary">
                                <i class="fas fa-book me-2"></i>2. Ufafanuzi
                            </h4>
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>"Jukwaa"</h6>
                                    <p class="text-muted">Panda Market website na programu zake zote</p>

                                    <h6>"Mtumiaji"</h6>
                                    <p class="text-muted">Mtu yeyote anayetumia jukwaa hili</p>

                                    <h6>"Muuzaji"</h6>
                                    <p class="text-muted">Mtumiaji anayeuzisha bidhaa kwenye jukwaa</p>
                                </div>
                                <div class="col-md-6">
                                    <h6>"Mnunuzi"</h6>
                                    <p class="text-muted">Mtumiaji anayenunua bidhaa kwenye jukwaa</p>

                                    <h6>"Bidhaa"</h6>
                                    <p class="text-muted">Vitu vyote vinavyouzwa kwenye jukwaa</p>

                                    <h6>"Huduma"</h6>
                                    <p class="text-muted">Huduma zote zinazotolewa na Panda Market</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Account and Registration -->
                <div class="terms-section" id="account">
                    <div class="card mb-4">
                        <div class="card-body">
                            <h4 class="card-title text-primary">
                                <i class="fas fa-user me-2"></i>3. Akaunti na Usajili
                            </h4>
                            <h6>3.1 Usajili wa Akaunti</h6>
                            <ul>
                                <li>Unaweza kutumia jukwaa bila kujisajili, lakini kwa baadhi ya huduma utahitaji akaunti</li>
                                <li>Unahitaji kutoa maelezo sahihi na ya kweli unapojisajili</li>
                                <li>Unaweza kuuza bidhaa tu baada ya kujisajili kama muuzaji</li>
                                <li>Unahitaji kuhifadhi password yako kwa usalama</li>
                            </ul>

                            <h6>3.2 Uwajibikaji wa Akaunti</h6>
                            <ul>
                                <li>Unahusika na vitendo vyote vinavyofanywa kwenye akaunti yako</li>
                                <li>Usishiriki maelezo ya akaunti yako na mtu mwingine</li>
                                <li>Ripoti mara moja kama unaona shughuli za kushtuka kwenye akaunti yako</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Products and Services -->
                <div class="terms-section" id="products">
                    <div class="card mb-4">
                        <div class="card-body">
                            <h4 class="card-title text-primary">
                                <i class="fas fa-box me-2"></i>4. Bidhaa na Huduma
                            </h4>
                            <h6>4.1 Maelezo ya Bidhaa</h6>
                            <ul>
                                <li>Muuzaji anahusika na kutoa maelezo sahihi ya bidhaa</li>
                                <li>Picha za bidhaa lazima ziwe za kweli na za sasa</li>
                                <li>Bei lazima ionyeshe kwa wazi na kwa shilingi za Tanzania</li>
                                <li>Maelezo ya warranty na huduma lazima ziwe wazi</li>
                            </ul>

                            <h6>4.2 Ubora wa Bidhaa</h6>
                            <ul>
                                <li>Bidhaa zote lazima ziwe na ubora wa kawaida</li>
                                <li>Bidhaa hazipaswi kuwa za bandia au za kuharibu</li>
                                <li>Muuzaji anahusika na ubora wa bidhaa zake</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Pricing and Payments -->
                <div class="terms-section" id="pricing">
                    <div class="card mb-4">
                        <div class="card-body">
                            <h4 class="card-title text-primary">
                                <i class="fas fa-money-bill me-2"></i>5. Bei na Malipo
                            </h4>
                            <h6>5.1 Bei</h6>
                            <ul>
                                <li>Bei zote zinaonyeshwa kwa shilingi za Tanzania (TSh)</li>
                                <li>Bei ya bidhaa haijumuishwa bei ya usafiri</li>
                                <li>Bei zinaweza kubadilika bila taarifa ya awali</li>
                                <li>Punguzo na ofa zinaweza kutumika kwa masharti fulani</li>
                            </ul>

                            <h6>5.2 Malipo</h6>
                            <ul>
                                <li>Tunakubali malipo kupitia M-Pesa, Airtel Money, na benki</li>
                                <li>Malipo yanahitaji kuthibitishwa kabla ya kusafirisha bidhaa</li>
                                <li>Bei ya usafiri itaongezwa kwenye bei ya bidhaa</li>
                                <li>Unaweza kupata receipt ya malipo yako</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Shipping and Delivery -->
                <div class="terms-section" id="shipping">
                    <div class="card mb-4">
                        <div class="card-body">
                            <h4 class="card-title text-primary">
                                <i class="fas fa-shipping-fast me-2"></i>6. Usafiri na Uwasilishaji
                            </h4>
                            <h6>6.1 Muda wa Usafiri</h6>
                            <ul>
                                <li>Dar es Salaam: 1-2 siku za kazi</li>
                                <li>Miji mingine: 2-3 siku za kazi</li>
                                <li>Vijiji: 3-5 siku za kazi</li>
                                <li>Muda unaweza kubadilika kutegemea hali ya hewa na usafiri</li>
                            </ul>

                            <h6>6.2 Bei ya Usafiri</h6>
                            <ul>
                                <li>Bei ya usafiri hutegemea umbali na ukubwa wa bidhaa</li>
                                <li>Unaweza kupata makadirio ya bei ya usafiri kabla ya kununua</li>
                                <li>Bei ya usafiri itaongezwa kwenye bei ya bidhaa</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Returns and Exchanges -->
                <div class="terms-section" id="returns">
                    <div class="card mb-4">
                        <div class="card-body">
                            <h4 class="card-title text-primary">
                                <i class="fas fa-undo me-2"></i>7. Kurudisha na Kubadilisha
                            </h4>
                            <h6>7.1 Masharti ya Kurudisha</h6>
                            <ul>
                                <li>Unaweza kurudisha bidhaa ndani ya siku 7 za kununua</li>
                                <li>Bidhaa lazima iwe katika hali ya awali</li>
                                <li>Unaweza kupata refund au kubadilisha bidhaa</li>
                                <li>Bei ya usafiri haitarefundishwa</li>
                            </ul>

                            <h6>7.2 Bidhaa Zisizorudishwa</h6>
                            <ul>
                                <li>Bidhaa za chakula na vinywaji</li>
                                <li>Bidhaa za kibinafsi (mavazi ya ndani, nguo za kuogea)</li>
                                <li>Bidhaa zilizotengenezwa kwa maagizo maalum</li>
                                <li>Bidhaa za kidijitali zilizofunguliwa</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Privacy and Security -->
                <div class="terms-section" id="privacy">
                    <div class="card mb-4">
                        <div class="card-body">
                            <h4 class="card-title text-primary">
                                <i class="fas fa-shield-alt me-2"></i>8. Usalama na Privacy
                            </h4>
                            <h6>8.1 Ulinzi wa Data</h6>
                            <ul>
                                <li>Tunatumia teknolojia za usalama za hali ya juu</li>
                                <li>Data yako haifikiwi na watu wasio na ruhusa</li>
                                <li>Hatushiriki data yako na watu wengine</li>
                                <li>Unaweza kufuta data yako wakati wowote</li>
                            </ul>

                            <h6>8.2 Cookies na Tracking</h6>
                            <ul>
                                <li>Tunatumia cookies kwa ajili ya kuboresha huduma</li>
                                <li>Unaweza kuzima cookies kutoka kwenye browser yako</li>
                                <li>Hatutrack shughuli zako nje ya jukwaa letu</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Prohibited Items -->
                <div class="terms-section" id="prohibited">
                    <div class="card mb-4">
                        <div class="card-body">
                            <h4 class="card-title text-primary">
                                <i class="fas fa-ban me-2"></i>9. Vitu Visivyoruhusiwa
                            </h4>
                            <p class="text-danger">
                                <strong>Hakuna kuuza vitu vifuatazo kwenye Panda Market:</strong>
                            </p>
                            <ul>
                                <li>Silaha na vifaa vya kujihami</li>
                                <li>Dawa za kulevya na vifaa vya kuvuta</li>
                                <li>Vifaa vya pornografia na vya kijinsia</li>
                                <li>Bidhaa za bandia au za kuharibu</li>
                                <li>Vifaa vya kuharibu mazingira</li>
                                <li>Bidhaa zilizopigwa marufuku na serikali</li>
                            </ul>
                            <p>
                                Kuuza vitu hivi kunaweza kusababisha kufutwa kwa akaunti yako na
                                kufikishwa kwa mamlaka husika.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Liability -->
                <div class="terms-section" id="liability">
                    <div class="card mb-4">
                        <div class="card-body">
                            <h4 class="card-title text-primary">
                                <i class="fas fa-balance-scale me-2"></i>10. Uwajibikaji
                            </h4>
                            <h6>10.1 Uwajibikaji wa Panda Market</h6>
                            <ul>
                                <li>Hatuhusiki na ubora wa bidhaa zinazouzwa</li>
                                <li>Hatuhusiki na ugomvi kati ya muuzaji na mnunuzi</li>
                                <li>Uwajibikaji wetu umepunguzwa kwa kiwango cha juu cha sheria</li>
                            </ul>

                            <h6>10.2 Uwajibikaji wa Mtumiaji</h6>
                            <ul>
                                <li>Unahusika na kufuata sheria na masharti</li>
                                <li>Unahusika na ubora wa bidhaa unazouza</li>
                                <li>Unahusika na malipo na usafiri</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Termination -->
                <div class="terms-section" id="termination">
                    <div class="card mb-4">
                        <div class="card-body">
                            <h4 class="card-title text-primary">
                                <i class="fas fa-times-circle me-2"></i>11. Kukatwa
                            </h4>
                            <p>
                                Panda Market inaweza kukata au kufuta akaunti yako ikiwa:
                            </p>
                            <ul>
                                <li>Unakiuka sheria na masharti</li>
                                <li>Unafanya vitendo vya kuharibu</li>
                                <li>Unafanya biashara ya bandia</li>
                                <li>Unafanya vitendo vya kujihami</li>
                                <li>Unafanya vitendo vya kuharibu watumiaji wengine</li>
                            </ul>
                            <p>
                                Unaweza pia kufuta akaunti yako wakati wowote kwa kutuma barua pepe
                                au ujumbe kupitia WhatsApp.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Changes to Terms -->
                <div class="terms-section" id="changes">
                    <div class="card mb-4">
                        <div class="card-body">
                            <h4 class="card-title text-primary">
                                <i class="fas fa-edit me-2"></i>12. Mabadiliko
                            </h4>
                            <p>
                                Panda Market inaweza kubadilisha sheria na masharti wakati wowote.
                                Mabadiliko yatatangazwa kwenye jukwaa na kutumwa kwa watumiaji kupitia
                                barua pepe au ujumbe.
                            </p>
                            <p>
                                Kuendelea kutumia jukwaa baada ya mabadiliko kunamaanisha kuwa
                                unakubaliana na sheria na masharti mapya.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="terms-section" id="contact">
                    <div class="card mb-4">
                        <div class="card-body">
                            <h4 class="card-title text-primary">
                                <i class="fas fa-envelope me-2"></i>13. Uhusiano
                            </h4>
                            <p>
                                Kama una maswali yoyote kuhusu sheria na masharti, tafadhali
                                wasiliana nasi kupitia njia zifuatazo:
                            </p>
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>WhatsApp:</h6>
                                    <p>+255 767 680 463</p>

                                    <h6>Simu:</h6>
                                    <p>+255 767 680 463</p>
                                </div>
                                <div class="col-md-6">
                                    <h6>Barua Pepe:</h6>
                                    <p>info@pandadigital.co.tz</p>

                                    <h6>Maelezo:</h6>
                                    <p>Panda Digital, Dar es Salaam, Tanzania</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Last Updated -->
                <div class="card bg-light">
                    <div class="card-body text-center">
                        <p class="mb-0">
                            <strong>Sheria na Masharti yaliyorekebishwa:</strong>
                            <?php echo date('d/m/Y'); ?>
                        </p>
                        <small class="text-muted">
                            Toleo la 1.0 - Panda Market Terms and Conditions
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>

<style>
    .sticky-top {
        z-index: 1020;
    }

    .nav-link {
        color: #6c757d;
        border-left: 3px solid transparent;
        padding: 8px 15px;
        transition: all 0.3s ease;
    }

    .nav-link:hover {
        color: #007bff;
        border-left-color: #007bff;
        background-color: #f8f9fa;
    }

    .nav-link.active {
        color: #007bff;
        border-left-color: #007bff;
        background-color: #e7f1ff;
    }

    .terms-section {
        scroll-margin-top: 120px;
    }

    .card {
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .card-title {
        border-bottom: 2px solid #007bff;
        padding-bottom: 10px;
        margin-bottom: 20px;
    }

    .breadcrumb-item a {
        color: #fff;
        text-decoration: none;
    }

    .breadcrumb-item.active {
        color: #ffc107;
    }

    h6 {
        color: #495057;
        font-weight: 600;
        margin-top: 20px;
        margin-bottom: 10px;
    }

    ul {
        padding-left: 20px;
    }

    li {
        margin-bottom: 8px;
    }
</style>

<script>
    // Highlight active navigation item based on scroll position
    window.addEventListener('scroll', function() {
        const sections = document.querySelectorAll('.terms-section');
        const navLinks = document.querySelectorAll('.nav-link');

        let current = '';

        sections.forEach(section => {
            const sectionTop = section.offsetTop;
            const sectionHeight = section.clientHeight;
            if (pageYOffset >= sectionTop - 150) {
                current = section.getAttribute('id');
            }
        });

        navLinks.forEach(link => {
            link.classList.remove('active');
            if (link.getAttribute('href') === '#' + current) {
                link.classList.add('active');
            }
        });
    });

    // Smooth scrolling for navigation links
    document.querySelectorAll('.nav-link').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();

            const targetId = this.getAttribute('href');
            const targetSection = document.querySelector(targetId);

            if (targetSection) {
                targetSection.scrollIntoView({
                    behavior: 'smooth'
                });
            }
        });
    });
</script>