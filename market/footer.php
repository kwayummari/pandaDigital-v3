<footer>
        <div class="container">
            <div class="row">
                <div class="col-lg-3">
                    <div class="first-item">
                        <div class="logo">
                            <img height="150px" src="assets/images/market1.png" alt="PANDA DIGITAL">
                        </div>
                        <ul>
                            <li><a href="#">info@pandadigital.co.tz</a></li>
                            <li><a href="#">+255 734 283 34</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-3">
                    <h4>Shopping &amp; Categories</h4>
                    <ul>
                        <li><a href="#">Bidhaa Zilizokadiriwa Zaidi</a></li>
                        <li><a href="#">Bidhaa Zinazouzwa Zaidi</a></li>
                        <!-- <li><a href="#">Kid's Shopping</a></li> -->
                    </ul>
                </div>
                <div class="col-lg-3">
                    <h4>Useful Links</h4>
                    <ul>
                        <li><a href="#">Nyumbani</a></li>
                        <li><a href="#">Kuhusu sisi</a></li>
                        <li><a href="#">Msaada</a></li>
                        <li><a href="#">Wasiliana nasi</a></li>
                    </ul>
                </div>
                <div class="col-lg-3">
                    <h4>Maelezo ya &amp; Msaada</h4>
                    <ul>
                        <li><a href="#">Msaada</a></li>
                        <li><a href="#">Maswali Yanayoulizwa Mara kwa Mara</a></li>
                        <li><a href="#">Usafirishaji</a></li>
                        <li><a href="#">Kitambulisho cha Ufuatiliaji</a></li>
                    </ul>
                </div>
                <div class="col-lg-12">
                    <div class="under-footer">
                    <p class="mb-0">Copyright &copy;
                        <script>
                            var CurrentYear = new Date().getFullYear()
                            document.write(CurrentYear)
                        </script>
                        , Designed & Developed by <a target="_blank" href="https://serengetibytes.com" class="text-muted">Serengeti Bytes</a>
                    </p>
                        <ul>
                            <li><a href="https://www.facebook.com/PandaDigitalTZ/"><i class="fa fa-facebook"></i></a></li>
                            <li><a href="https://twitter.com/pandadigitaltz"><i class="fa fa-twitter"></i></a></li>
                            <li><a href="https://www.linkedin.com/company/her-initiative/"><i class="fa fa-linkedin"></i></a></li>
                            <li><a href="https://www.instagram.com/pandadigitaltz"><i class="fa fa-instagram"></i></a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    

    <div id="chatbot-trigger" class="chatbot-trigger">
    <img src="https://pandadigital.co.tz/images/chatBot.png" alt="Chat Bot" style="width: 50px; height: 50px;">
    <span>Muulize Zuri</span>
</div>

<!-- Main Chatbot Container -->
<div id="chatbot-container" class="chatbot-wrapper" style="display: none;">
    <!-- Main Content Area -->
    <div id="chatbot-content" class="content-area">
        <!-- Home Tab Content -->
        <div id="home-content" class="tab-content active">
            <!-- Logo -->
            <div class="logo-section">
                <img src="https://pandadigital.co.tz/images/chatBot.png" style="width: 50px; height: 50px;" alt="Panda Digital" class="logo">
            </div>

            <!-- Welcome Text -->
            <div class="welcome-text">
            <h1>Habari 👋</h1>
            <h1>Tunawezaje kusaidia?</h1>
            </div>
            
            <!-- Message Card -->
            <div class="message-card" onclick="window.location.href='https://pandadigital.co.tz/ongea-hub/'">
                <div class="card-content">
                    <h2>Tutumie ujumbe</h2>
                    <p class="subtitle">Kwa kawaida tunajibu baada ya saa chache</p>
                </div>
                <div class="arrow">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M5 12H19M19 12L12 5M19 12L12 19" stroke="#5F4594" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
            </div>

            <!-- Search Section -->
            <div class="search-section">
                <div class="search-container">
                    <input 
                        type="text" 
                        id="faq-search" 
                        placeholder="Tafuta msaada"
                        autocomplete="off"
                    >
                    <div class="search-icon">
                        <svg viewBox="0 0 24 24" width="20" height="20">
                            <path fill="#666" d="M15.5 14h-.79l-.28-.27a6.5 6.5 0 0 0 1.48-5.34c-.47-2.78-2.79-5-5.59-5.34a6.505 6.505 0 0 0-7.27 7.27c.34 2.8 2.56 5.12 5.34 5.59a6.5 6.5 0 0 0 5.34-1.48l.27.28v.79l4.25 4.25c.41.41 1.08.41 1.49 0 .41-.41.41-1.08 0-1.49L15.5 14z"/>
                        </svg>
                    </div>
                </div>
                
                <!-- Search Results Dropdown -->
                <div id="search-suggestions" class="search-suggestions"></div>

                <!-- Featured FAQs -->
                <div class="featured-faqs">
                    <h3>Maswali ya kawaida</h3>
                    <div id="featured-faq-list"></div>
                </div>
            </div>
        </div>

        <!-- Messages Tab Content -->
        <div id="messages-content" class="tab-content">
            <div class="messages-header">Ujumbe</div>
            <div id="chat-messages" class="chat-messages">
                <div class="empty-state">
                    <div class="empty-icon">💬</div>
                    <h2>Hakuna Ujumbe</h2>
                    <p>Ujumbe kutoka kwa timu utaonyeshwa hapa</p>
                </div>
            </div>
            <div class="message-input-container">
                <input 
                    type="text" 
                    id="message-input" 
                    placeholder="Andika ujumbe wako..."
                    autocomplete="off"
                >
                <button id="send-message">Send</button>
            </div>
        </div>

        <!-- Help Tab Content -->
        <div id="help-content" class="tab-content">
            <div class="help-header">
                <h1>Msaada</h1>
            </div>
            <!-- Help Search -->
            <!-- <div class="help-search">
                <div class="search-container">
                    <input 
                        type="text" 
                        id="help-search" 
                        placeholder="Tafuta nakala za usaidizi"
                        autocomplete="off"
                    >
                    <div class="search-icon">
                        <svg viewBox="0 0 24 24" width="20" height="20">
                            <path fill="#666" d="M15.5 14h-.79l-.28-.27a6.5 6.5 0 0 0 1.48-5.34c-.47-2.78-2.79-5-5.59-5.34a6.505 6.505 0 0 0-7.27 7.27c.34 2.8 2.56 5.12 5.34 5.59a6.5 6.5 0 0 0 5.34-1.48l.27.28v.79l4.25 4.25c.41.41 1.08.41 1.49 0 .41-.41.41-1.08 0-1.49L15.5 14z"/>
                        </svg>
                    </div>
                </div>
            </div> -->
            <!-- FAQ Categories -->
            <div class="faq-categories">
                <!-- Categories will be populated by JavaScript -->
            </div>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="tab-navigation">
        <button class="tab-button active" data-tab="home">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M9 22V12h6v10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            Nyumbani
        </button>
        <button class="tab-button" data-tab="messages">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                <path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2v10z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            Ujumbe
        </button>
        <button class="tab-button" data-tab="help">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                <path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M9.09 9a3 3 0 015.83 1c0 2-3 3-3 3M12 17h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            Msaada
        </button>
    </div>
</div>

<!-- Chat Toggle Button (Only shown when chat is minimized) -->
<div id="chatbot-toggle" style="display: none;">
    <div class="toggle-button">
        <img src="https://pandadigital.co.tz/images/chatBot.png" alt="Chat Icon" class="chat-icon">
        <span>Muulize Zuri</span>
    </div>
</div>

<!-- Scripts -->
<script src="assets/js/chatBot.js"></script>