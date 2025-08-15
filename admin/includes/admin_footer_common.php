<?php
// This file contains the reusable footer for admin pages
?>
</div>
</div>
</div>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Sidebar Toggle Script -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sidebarToggles = document.querySelectorAll('.sidebar-toggle');
        const sidebar = document.querySelector('.sidebar');
        const dashboardContainer = document.querySelector('.dashboard-container');

        sidebarToggles.forEach(toggle => {
            toggle.addEventListener('click', function() {
                sidebar.classList.toggle('collapsed');
                dashboardContainer.classList.toggle('sidebar-collapsed');
            });
        });

        // Close sidebar on mobile when clicking outside
        document.addEventListener('click', function(e) {
            if (window.innerWidth < 992) {
                // Don't close sidebar when clicking on menu items or navigation
                if (!sidebar.contains(e.target) &&
                    !e.target.closest('.sidebar-toggle') &&
                    !e.target.closest('.sidebar-nav') &&
                    !e.target.closest('.nav-link')) {
                    sidebar.classList.remove('collapsed');
                    dashboardContainer.classList.remove('sidebar-collapsed');
                }
            }
        });

        // Update page title
        const pageTitle = document.getElementById('pageTitle');
        if (pageTitle) {
            const title = document.title.replace(' - Admin Panel', '');
            pageTitle.textContent = title;
        }

        // Set active navigation item based on current page
        const currentPath = window.location.pathname;
        const navItems = document.querySelectorAll('.sidebar-nav .nav-item');

        navItems.forEach(item => {
            const link = item.querySelector('a');
            if (link && currentPath.includes(link.getAttribute('href').split('/').pop())) {
                // Remove active class from all items
                navItems.forEach(navItem => navItem.classList.remove('active'));
                // Add active class to current item
                item.classList.add('active');
            }
        });
    });

    // Chart.js configuration for admin dashboard
    if (typeof Chart !== 'undefined') {
        Chart.defaults.font.family = 'Inter, sans-serif';
        Chart.defaults.color = '#6c757d';
        Chart.defaults.plugins.legend.position = 'bottom';
    }
</script>
</body>

</html>