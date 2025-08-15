document.addEventListener('DOMContentLoaded', () => {
    // DOM Elements
    const chatbotTrigger = document.getElementById('chatbot-trigger');
    const chatbotContainer = document.getElementById('chatbot-container');
    const tabButtons = document.querySelectorAll('.tab-button');
    const tabContents = document.querySelectorAll('.tab-content');
    const searchInput = document.getElementById('faq-search');
    const featuredFaqList = document.getElementById('featured-faq-list');
    const messageInput = document.getElementById('message-input');
    const sendButton = document.getElementById('send-message');
    const chatMessages = document.getElementById('chat-messages');
    const CHATBOT_API_URL = 'https://pandadigital.co.tz/chatbot-api';
    
    let faqs = [];

    // Chatbot Trigger Functionality
    chatbotTrigger.addEventListener('click', () => {
        chatbotContainer.style.display = 'flex';
        chatbotTrigger.style.display = 'none';
    });

    // Add close button
    const closeButton = document.createElement('button');
    closeButton.className = 'chatbot-close';
    closeButton.innerHTML = '×';
    chatbotContainer.appendChild(closeButton);

    closeButton.addEventListener('click', () => {
        chatbotContainer.style.display = 'none';
        chatbotTrigger.style.display = 'flex';
    });

    // Tab Switching
    window.switchTab = function(tabName) {
        tabButtons.forEach(btn => {
            btn.classList.toggle('active', btn.dataset.tab === tabName);
        });

        tabContents.forEach(content => {
            content.classList.toggle('active', content.id === `${tabName}-content`);
        });

        if (tabName !== 'messages') {
            messageInput.value = '';
        }

        if (tabName !== 'home') {
            searchInput.value = '';
            displayFeaturedFaqs(); // Reset to default FAQs when leaving home tab
        }
    };

    tabButtons.forEach(button => {
        button.addEventListener('click', () => {
            const tab = button.dataset.tab;
            switchTab(tab);
        });
    });

    // Sample FAQ Data
    const sampleFaqs = {
        'Kuanza': [
            {
                question: "Panda Digital ni nini?",
                answer: "Panda Digital ni jukwaa la kwanza la kidigitali kwa lugha ya Kiswahili lenye lengo la kuwasaidia wasichana kupata ujuzi na rasilimali za kuanza na kuendesha biashara zao ili kunufaika uchumi wa kidigitali."
            },
            {
                question: "Ninawezaje kuanza kuuza kwenye Soko la Panda?",
                answer: "Jisajili kama muuzaji kwenye Soko la Panda, jaza taarifa za bidhaa zako, na uanze kuuza mara moja baada ya akaunti yako kuthibitishwa."
            },
            {
                question: "Vigezo gani vinahitajika kujisajili?",
                answer: "Unahitaji kuwa na vitambulisho halali, picha za bidhaa zako, na maelezo ya biashara yako."
            }
        ],
        'Maswali ya Jumla': [
            {
                question: "Ni njia gani za malipo tunazotumia?",
                answer: "Tunatumia M-Pesa, Tigo Pesa, na Airtel Money kwa malipo ya ndani ya Tanzania."
            },
            {
                question: "Je, kuna ada ya kutumia jukwaa?",
                answer: "Usajili ni bure, lakini kuna asilimia ndogo ya kamisheni kwa kila mauzo."
            },
            {
                question: "Naweza kurejeshewa fedha?",
                answer: "Ndiyo, tunakubali kurejeshewa fedha ndani ya siku 7 baada ya kununua bidhaa."
            }
        ],
        'Msaada wa Kiufundi': [
            {
                question: "Nimesahau nywila yangu",
                answer: "Unaweza kubonyeza kiungo cha 'Sahau Nywila' kwenye ukurasa wa kuingia na kufuata maelekezo."
            },
            {
                question: "Sina uwezo wa kupakia picha",
                answer: "Hakikisha picha zako ni ndogo ya MB 5 na ni katika muundo wa JPG, PNG au JPEG."
            },
            {
                question: "Simu yangu haiunganishi",
                answer: "Hakikisha una mtandao imara na umesasisha programu yako."
            }
        ],
        'Huduma': [
            {
                question: "Huduma gani mnatoa?",
                answer: "Tunatoa huduma za mafunzo ya biashara, ushauri wa kifedha, na jukwaa la kuuza bidhaa."
            },
            {
                question: "Mafunzo yanapatikana wapi?",
                answer: "Mafunzo yanapatikana mtandaoni kupitia jukwaa letu na pia ana kwa ana katika vituo vyetu."
            },
            {
                question: "Gharama za usafirishaji?",
                answer: "Gharama za usafirishaji zinatofautiana kulingana na umbali na ukubwa wa bidhaa."
            }
        ]
    };

    // Search Functionality
    function initializeSearch() {
        if (!searchInput || !featuredFaqList) return;

        let searchTimeout;

        searchInput.addEventListener('input', (e) => {
            clearTimeout(searchTimeout);
            const query = e.target.value.toLowerCase().trim();
            
            searchTimeout = setTimeout(() => {
                if (query.length < 2) {
                    displayFeaturedFaqs();
                    return;
                }

                const searchResults = faqs.filter(faq => 
                    faq.question.toLowerCase().includes(query) ||
                    faq.answer.toLowerCase().includes(query)
                ).slice(0, 5);

                displaySearchResults(searchResults, query);
            }, 300);
        });
    }

    function displaySearchResults(results, query) {
        if (!featuredFaqList) return;

        featuredFaqList.innerHTML = '';
        
        if (results.length === 0) {
            featuredFaqList.innerHTML = `
                <div class="no-results">
                    Hakuna maswali yaliyopatikana kwa utafutaji wako
                </div>
            `;
            return;
        }

        results.forEach(result => {
            const faqElement = document.createElement('div');
            faqElement.className = 'faq-item';
            
            const highlightedQuestion = result.question.replace(
                new RegExp(query, 'gi'),
                match => `<strong>${match}</strong>`
            );
            
            faqElement.innerHTML = `<h4>${highlightedQuestion}</h4>`;
            faqElement.addEventListener('click', () => showFaqAnswer(result));
            featuredFaqList.appendChild(faqElement);
        });
    }

    // Display Featured FAQs
    function displayFeaturedFaqs() {
        if (!featuredFaqList) return;
        
        const featuredFaqs = faqs.slice(0, 3);
        featuredFaqList.innerHTML = '';
        
        featuredFaqs.forEach(faq => {
            const faqElement = document.createElement('div');
            faqElement.className = 'faq-item';
            faqElement.innerHTML = `<h4>${faq.question}</h4>`;
            faqElement.addEventListener('click', () => showFaqAnswer(faq));
            featuredFaqList.appendChild(faqElement);
        });
    }

    // Show Main Categories
    window.showMainCategories = function() {
        switchTab('help');
        
        const helpContent = document.getElementById('help-content');
        if (helpContent) {
            helpContent.innerHTML = `
                <div class="help-header">
                    <p style="font-weight: 700; font-size: 18px;">Msaada</p>
                </div>
                <div class="faq-categories"></div>
            `;
            populateFaqCategories();
        }
    };

    function populateFaqCategories() {
        const faqCategories = document.querySelector('.faq-categories');
        if (!faqCategories) return;

        faqCategories.innerHTML = '';
        Object.entries(sampleFaqs).forEach(([category, categoryFaqs]) => {
            const categorySection = document.createElement('div');
            categorySection.className = 'faq-category';
            categorySection.innerHTML = `
                <h2 style="font-size: 16px; margin: 0;">${category}</h2>
                <p style="font-size: 13px; margin: 4px 0 0; color: #666;">${categoryFaqs.length} articles</p>
            `;
            categorySection.addEventListener('click', () => showCategoryFaqs(category, categoryFaqs));
            faqCategories.appendChild(categorySection);
        });
    }

    function showCategoryFaqs(category, faqs) {
        const faqCategories = document.querySelector('.faq-categories');
        if (!faqCategories) return;

        faqCategories.innerHTML = `
            <div class="category-header">
                <button class="back-button" onclick="window.showMainCategories()">←</button>
                <h2>${category}</h2>
            </div>
            <div class="category-faqs"></div>
        `;

        const categoryFaqs = faqCategories.querySelector('.category-faqs');
        if (categoryFaqs) {
            faqs.forEach(faq => {
                const faqItem = document.createElement('div');
                faqItem.className = 'faq-item';
                faqItem.innerHTML = `<h4>${faq.question}</h4>`;
                faqItem.addEventListener('click', () => showFaqAnswer(faq));
                categoryFaqs.appendChild(faqItem);
            });
        }
    }

    function showFaqAnswer(faq) {
        const helpContent = document.getElementById('help-content');
        if (!helpContent) return;

        helpContent.innerHTML = `
            <div class="help-header">
                <p style="font-weight: 700; font-size: 18px;">Msaada</p>
            </div>
            <div class="faq-detail">
                <div class="category-header">
                    <button class="back-button" onclick="window.showMainCategories()">←</button>
                    <p style="font-weight: 700; font-size: 18px;">Jibu</p>
                </div>
                <div class="faq-answer">
                    <p style="font-weight: 700; font-size: 25px; padding: 10px">${faq.question}</p>
                    <p style="font-weight: 300; font-size: 15px; padding: 10px">${faq.answer}</p>
                </div>
            </div>
            <div class="faq-categories" style="display: none;"></div>
        `;
        
        switchTab('help');
    }

    // Chat Functionality
    async function sendMessage() {
        const message = messageInput.value.trim();
        if (!message) return;
    
        if (chatMessages.querySelector('.empty-state')) {
            chatMessages.innerHTML = '';
        }
    
        appendMessage(message, 'user');
        messageInput.value = '';
    
        const faqMatch = findFaqMatch(message);
        if (faqMatch) {
            appendMessage(faqMatch.answer, 'bot');
            return;
        }
    
        try {
            const response = await fetch(CHATBOT_API_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ message: message }),
                redirect: 'follow'
            });
    
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
                
            const data = await response.json();
            appendMessage(data.response, 'bot');
        } catch (error) {
            console.error('Error:', error);
            appendMessage('Samahani, nimekumbana na hitilafu. Tafadhali jaribu tena baadae.', 'bot');
        }
    }

    function findFaqMatch(message) {
        const messageLower = message.toLowerCase();
        return faqs.find(faq => {
            const similarity = calculateSimilarity(messageLower, faq.question.toLowerCase());
            return similarity > 0.7;
        });
    }

    function calculateSimilarity(str1, str2) {
        const len1 = str1.length;
        const len2 = str2.length;
        const matrix = Array(len1 + 1).fill().map(() => Array(len2 + 1).fill(0));

        for (let i = 0; i <= len1; i++) matrix[i][0] = i;
        for (let j = 0; j <= len2; j++) matrix[0][j] = j;

        for (let i = 1; i <= len1; i++) {
            for (let j = 1; j <= len2; j++) {
                const cost = str1[i - 1] === str2[j - 1] ? 0 : 1;
                matrix[i][j] = Math.min(
                    matrix[i - 1][j] + 1,
                    matrix[i][j - 1] + 1,
                    matrix[i - 1][j - 1] + cost
                );
            }
        }

        return 1 - (matrix[len1][len2] / Math.max(len1, len2));
    }

    function appendMessage(message, type) {
        if (!chatMessages) return;
        
        const messageElement = document.createElement('div');
        messageElement.className = `message ${type}-message`;
        messageElement.textContent = message;
        chatMessages.appendChild(messageElement);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    // Event Listeners
    sendButton?.addEventListener('click', sendMessage);
    messageInput?.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            sendMessage();
        }
    });

    // Add styles for search results
    const styles = `
        .faq-item strong {
            color: #5F4594;
            font-weight: bold;
        }

        .no-results {
            text-align: center;
            padding: 20px;
            color: #666;
            font-size: 14px;
        }

        .featured-faqs {
            margin-top: 16px;
            transition: all 0.3s ease;
        }

        .faq-item {
            background: white;
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 8px;
            cursor: pointer;
            transition: transform 0.2s;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            animation: fadeIn 0.3s ease-out;
        }

        .faq-item:hover {
            transform: translateX(4px);
            background-color: #f8f9fa;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(5px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    `;

    const styleSheet = document.createElement("style");
    styleSheet.textContent = styles;
    document.head.appendChild(styleSheet);

    // Initialize
    async function initialize() {
        try {
            faqs = Object.values(sampleFaqs).flat();
            displayFeaturedFaqs();
            populateFaqCategories();
            initializeSearch();
        } catch (error) {
            console.error("Error initializing:", error);
        }
    }

    // Start initialization
    initialize();
});