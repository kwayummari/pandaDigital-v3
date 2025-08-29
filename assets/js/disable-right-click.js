/**
 * Panda Digital V3 - Right Click Disabler
 * Disables right-click context menu and keyboard shortcuts
 * Mobile/Touch device compatible version
 */

(function () {
    'use strict';

    // Simple detection: disable protection for any touch device or mobile screen
    function shouldDisableProtection() {
        // Check if device has touch support
        const hasTouch = 'ontouchstart' in window || navigator.maxTouchPoints > 0;

        // Check if screen is mobile-sized
        const isMobileSize = window.innerWidth <= 1024 || window.innerHeight <= 768;

        // Check user agent for mobile devices
        const userAgent = navigator.userAgent || navigator.vendor || window.opera;
        const isMobileUserAgent = /Mobile|Android|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(userAgent);

        // Disable protection if ANY of these conditions are true
        const shouldDisable = hasTouch || isMobileSize || isMobileUserAgent;

        // Debug logging
        console.log('Protection Check:', {
            hasTouch: hasTouch,
            isMobileSize: isMobileSize,
            isMobileUserAgent: isMobileUserAgent,
            screenWidth: window.innerWidth,
            screenHeight: window.innerHeight,
            maxTouchPoints: navigator.maxTouchPoints,
            userAgent: userAgent,
            shouldDisable: shouldDisable
        });

        return shouldDisable;
    }

    // If we should disable protection, exit immediately
    if (shouldDisableProtection()) {
        console.log('Mobile/Touch device detected - Right-click protection DISABLED');
        return; // Exit early, no protection for mobile/touch devices
    }

    console.log('Desktop device detected - Right-click protection ENABLED');

    // Disable right-click context menu
    document.addEventListener('contextmenu', function (e) {
        e.preventDefault();
        showRightClickWarning();
        return false;
    });

    // Disable keyboard shortcuts
    document.addEventListener('keydown', function (e) {
        // Disable F12 key
        if (e.key === 'F12') {
            e.preventDefault();
            showRightClickWarning();
            return false;
        }

        // Disable Ctrl+Shift+I (Developer Tools)
        if (e.ctrlKey && e.shiftKey && e.key === 'I') {
            e.preventDefault();
            showRightClickWarning();
            return false;
        }

        // Disable Ctrl+Shift+J (Console)
        if (e.ctrlKey && e.shiftKey && e.key === 'J') {
            e.preventDefault();
            showRightClickWarning();
            return false;
        }

        // Disable Ctrl+Shift+C (Element Inspector)
        if (e.ctrlKey && e.shiftKey && e.key === 'C') {
            e.preventDefault();
            showRightClickWarning();
            return false;
        }

        // Disable Ctrl+U (View Source)
        if (e.ctrlKey && e.key === 'u') {
            e.preventDefault();
            showRightClickWarning();
            return false;
        }

        // Disable Ctrl+S (Save Page)
        if (e.ctrlKey && e.key === 's') {
            e.preventDefault();
            showRightClickWarning();
            return false;
        }

        // Disable Ctrl+P (Print)
        if (e.ctrlKey && e.key === 'p') {
            e.preventDefault();
            showRightClickWarning();
            return false;
        }

        // Disable Ctrl+Shift+S (Save As)
        if (e.ctrlKey && e.shiftKey && e.key === 'S') {
            e.preventDefault();
            showRightClickWarning();
            return false;
        }
    });

    // Disable drag and drop
    document.addEventListener('dragstart', function (e) {
        e.preventDefault();
        return false;
    });

    // Disable copy/cut/paste
    document.addEventListener('copy', function (e) {
        e.preventDefault();
        showRightClickWarning();
    });

    document.addEventListener('cut', function (e) {
        e.preventDefault();
        showRightClickWarning();
    });

    document.addEventListener('paste', function (e) {
        e.preventDefault();
        showRightClickWarning();
    });

    // Function to show warning message
    function showRightClickWarning() {
        // Create warning element if it doesn't exist
        let warning = document.getElementById('right-click-warning');

        if (!warning) {
            warning = document.createElement('div');
            warning.id = 'right-click-warning';
            warning.style.cssText = `
                position: fixed;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                background: #dc3545;
                color: white;
                padding: 20px;
                border-radius: 8px;
                box-shadow: 0 4px 20px rgba(0,0,0,0.3);
                z-index: 9999;
                font-family: Arial, sans-serif;
                font-size: 16px;
                text-align: center;
                max-width: 300px;
                animation: warningFadeIn 0.3s ease-in-out;
            `;

            // Add CSS animation
            const style = document.createElement('style');
            style.textContent = `
                @keyframes warningFadeIn {
                    from { opacity: 0; transform: translate(-50%, -50%) scale(0.8); }
                    to { opacity: 1; transform: translate(-50%, -50%) scale(1); }
                }
            `;
            document.head.appendChild(style);

            document.body.appendChild(warning);
        }

        // Update warning message
        warning.innerHTML = `
            <div style="margin-bottom: 15px;">
                <i class="fas fa-exclamation-triangle" style="font-size: 24px; color: #ffc107;"></i>
            </div>
            <div style="font-weight: bold; margin-bottom: 10px;">Hakuna Ruhusa!</div>
            <div style="font-size: 14px;">Huwezi kutumia hizi vitendo kwenye ukurasa huu.</div>
        `;

        // Show warning
        warning.style.display = 'block';

        // Hide warning after 3 seconds
        setTimeout(function () {
            warning.style.display = 'none';
        }, 3000);
    }

    // Additional protection against developer tools
    function detectDevTools() {
        const threshold = 160;

        const widthThreshold = window.outerWidth - window.innerWidth > threshold;
        const heightThreshold = window.outerHeight - window.innerHeight > threshold;

        if (widthThreshold || heightThreshold) {
            showRightClickWarning();
        }
    }

    // Check for dev tools periodically
    setInterval(detectDevTools, 1000);

    // Disable console logging
    console.log = function () { };
    console.info = function () { };
    console.warn = function () { };
    console.error = function () { };
    console.debug = function () { };

    // Additional protection for iframes
    if (window.self !== window.top) {
        window.top.location = window.self.location;
    }

    // Disable viewport manipulation
    if (window.visualViewport) {
        Object.defineProperty(window.visualViewport, 'height', {
            get: function () { return window.innerHeight; },
            set: function () { return false; }
        });
    }

})();
