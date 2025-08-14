                </div>
                </div>
                </div>
                </div>

                <!-- Bootstrap 5 JS -->
                <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

                <!-- Admin Scripts -->
                <script>
                    console.log('Admin footer script loaded successfully');

                    // Sidebar toggle functionality
                    document.addEventListener('DOMContentLoaded', function() {
                        console.log('DOM loaded, initializing admin functionality');
                        const sidebarToggle = document.getElementById('sidebarToggle');
                        const adminContainer = document.querySelector('.admin-container');
                        const sidebar = document.querySelector('.sidebar');

                        if (sidebarToggle) {
                            sidebarToggle.addEventListener('click', function() {
                                adminContainer.classList.toggle('sidebar-collapsed');
                                sidebar.classList.toggle('collapsed');
                            });
                        }

                        // Close sidebar on mobile when clicking outside
                        document.addEventListener('click', function(e) {
                            if (window.innerWidth < 992) {
                                if (!sidebar.contains(e.target) && !e.target.closest('.sidebar-toggle')) {
                                    adminContainer.classList.remove('sidebar-collapsed');
                                    sidebar.classList.remove('collapsed');
                                }
                            }
                        });

                        // Auto-expand submenu for current page
                        const currentPage = window.location.pathname.split('/').pop().replace('.php', '');
                        const submenus = document.querySelectorAll('.submenu');

                        submenus.forEach(submenu => {
                            const submenuItems = submenu.querySelectorAll('a');
                            submenuItems.forEach(item => {
                                if (item.getAttribute('href') && item.getAttribute('href').includes(currentPage)) {
                                    submenu.classList.add('show');
                                    const parentLink = submenu.previousElementSibling;
                                    if (parentLink) {
                                        parentLink.classList.add('active');
                                    }
                                }
                            });
                        });

                        // Update page title and breadcrumb
                        const pageTitle = document.getElementById('pageTitle');
                        const breadcrumbCurrent = document.getElementById('breadcrumbCurrent');

                        if (pageTitle && breadcrumbCurrent) {
                            const title = document.title.replace(' - Admin Panel', '');
                            pageTitle.textContent = title;
                            breadcrumbCurrent.textContent = title;
                        }
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