// Panda Digital V3 - Modern JavaScript

document.addEventListener('DOMContentLoaded', function () {

    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const href = this.getAttribute('href');

            // Skip if href is just "#" (dropdown menu items)
            if (href === '#') {
                return;
            }

            e.preventDefault();
            const target = document.querySelector(href);
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Modern navbar scroll effect
    const navbar = document.querySelector('.navbar');
    if (navbar) {
        window.addEventListener('scroll', function () {
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
    }

    // Hero stats animation
    const heroStats = document.querySelectorAll('.hero-stat-number');
    heroStats.forEach(stat => {
        const target = parseInt(stat.textContent.replace(/[^\d]/g, ''));
        const suffix = stat.textContent.replace(/[\d]/g, '');
        let current = 0;
        const increment = target / 50;

        const updateCounter = () => {
            if (current < target) {
                current += increment;
                stat.textContent = Math.ceil(current) + suffix;
                requestAnimationFrame(updateCounter);
            } else {
                stat.textContent = target + suffix;
            }
        };

        // Start animation when element is in view
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    updateCounter();
                    observer.unobserve(entry.target);
                }
            });
        });

        observer.observe(stat);
    });

    // Form handling
    const loginForm = document.getElementById('loginForm');
    const signupForm = document.getElementById('signupForm');

    console.log('Login form found:', loginForm);
    console.log('Signup form found:', signupForm);

    if (loginForm) {
        console.log('Adding event listener to login form');
        loginForm.addEventListener('submit', function (e) {
            console.log('Login form submitted!');
            e.preventDefault();
            handleLogin();
        });
    } else {
        console.error('Login form not found!');
    }

    if (signupForm) {
        signupForm.addEventListener('submit', function (e) {
            e.preventDefault();
            handleSignup();
        });
    }

    // Login handler
    function handleLogin() {
        console.log('handleLogin function called!');

        const email = document.getElementById('loginEmail').value;
        const password = document.getElementById('loginPassword').value;
        const rememberMe = document.getElementById('rememberMe').checked;

        console.log('Form values:', { email, password, rememberMe });

        if (!email || !password) {
            showAlert('Tafadhali jaza sehemu zote', 'danger');
            return;
        }

        // Show loading state
        const submitBtn = loginForm.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<span class="loading"></span> Inaingia...';
        submitBtn.disabled = true;

        // Make actual API call
        console.log('Making login API call to:', '/panda/pandadigitalV3/api/auth/login.php');
        console.log('Login data:', { email, password, remember: rememberMe });

        fetch('/panda/pandadigitalV3/api/auth/login.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                email: email,
                password: password,
                remember: rememberMe
            })
        })
            .then(response => {
                console.log('Login API response status:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('Login API response data:', data);

                // Reset button
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;

                if (data.success) {
                    // Login successful
                    showAlert(data.message || 'Umeingia kwa mafanikio!', 'success');

                    // Close modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('loginModal'));
                    modal.hide();

                    // Redirect to dashboard
                    if (data.redirect_url) {
                        // Construct full URL with proper prefix
                        const baseUrl = '/panda/pandadigitalV3';
                        const redirectUrl = baseUrl + data.redirect_url;
                        console.log('Redirecting to:', redirectUrl);
                        window.location.href = redirectUrl;
                    } else {
                        // Default redirect to user dashboard
                        window.location.href = '/panda/pandadigitalV3/user/dashboard.php';
                    }
                } else {
                    // Login failed
                    showAlert(data.message || 'Kuna tatizo na kuingia. Jaribu tena.', 'danger');
                }
            })
            .catch(error => {
                console.error('Login error:', error);

                // Reset button
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;

                // Show error message
                showAlert('Kuna tatizo na mtandao. Jaribu tena.', 'danger');
            });
    }

    // Signup handler
    function handleSignup() {
        const firstName = document.getElementById('firstName').value;
        const lastName = document.getElementById('lastName').value;
        const email = document.getElementById('signupEmail').value;
        const phone = document.getElementById('phone').value;
        const password = document.getElementById('signupPassword').value;
        const agreeTerms = document.getElementById('agreeTerms').checked;

        if (!firstName || !lastName || !email || !phone || !password) {
            showAlert('Tafadhali jaza sehemu zote', 'danger');
            return;
        }

        if (!agreeTerms) {
            showAlert('Tafadhali kubaliana na sheria na masharti', 'danger');
            return;
        }

        // Show loading state
        const submitBtn = signupForm.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<span class="loading"></span> Inasajili...';
        submitBtn.disabled = true;

        // Simulate API call
        setTimeout(() => {
            // Here you would make actual API call
            console.log('Signup attempt:', { firstName, lastName, email, phone, password });

            // Reset button
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;

            // Show success message
            showAlert('Akaunti yako imeundwa kwa mafanikio!', 'success');

            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('signupModal'));
            modal.hide();
        }, 2000);
    }

    // Alert system
    function showAlert(message, type = 'info') {
        // Remove existing alerts
        const existingAlert = document.querySelector('.alert');
        if (existingAlert) {
            existingAlert.remove();
        }

        // Create alert element
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
        alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

        // Add to page
        document.body.appendChild(alertDiv);

        // Auto remove after 5 seconds
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 5000);
    }

    // Counter animation
    function animateCounters() {
        const counters = document.querySelectorAll('.stat-number');
        counters.forEach(counter => {
            const target = parseInt(counter.textContent.replace(/[^\d]/g, ''));
            const suffix = counter.textContent.replace(/[\d]/g, '');
            let current = 0;
            const increment = target / 100;

            const timer = setInterval(() => {
                current += increment;
                if (current >= target) {
                    current = target;
                    clearInterval(timer);
                }
                counter.textContent = Math.floor(current) + suffix;
            }, 20);
        });
    }

    // Intersection Observer for counter animation
    const statsSection = document.querySelector('.stats-section');
    if (statsSection) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    animateCounters();
                    observer.unobserve(entry.target);
                }
            });
        });
        observer.observe(statsSection);
    }

    // Mobile menu toggle
    const navbarToggler = document.querySelector('.navbar-toggler');
    const navbarCollapse = document.querySelector('.navbar-collapse');

    if (navbarToggler && navbarCollapse) {
        // Close mobile menu when clicking on a link
        navbarCollapse.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth < 992) {
                    const bsCollapse = new bootstrap.Collapse(navbarCollapse);
                    bsCollapse.hide();
                }
            });
        });
    }

    // Search functionality
    const searchInput = document.querySelector('.search-input');
    if (searchInput) {
        searchInput.addEventListener('input', function (e) {
            const query = e.target.value.toLowerCase();
            // Implement search logic here
            console.log('Searching for:', query);
        });
    }

    // Lazy loading for images
    const images = document.querySelectorAll('img[data-src]');
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.classList.remove('lazy');
                    imageObserver.unobserve(img);
                }
            });
        });

        images.forEach(img => imageObserver.observe(img));
    }

    // Back to top button
    const backToTopBtn = document.createElement('button');
    backToTopBtn.innerHTML = '<i class="fas fa-arrow-up"></i>';
    backToTopBtn.className = 'btn btn-primary position-fixed';
    backToTopBtn.style.cssText = 'bottom: 20px; right: 20px; z-index: 1000; width: 50px; height: 50px; border-radius: 50%; display: none;';
    backToTopBtn.addEventListener('click', () => {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
    document.body.appendChild(backToTopBtn);

    // Show/hide back to top button
    window.addEventListener('scroll', () => {
        if (window.scrollY > 300) {
            backToTopBtn.style.display = 'block';
        } else {
            backToTopBtn.style.display = 'none';
        }
    });

    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Initialize popovers
    const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });

    console.log('Panda Digital V3 loaded successfully!');
}); 