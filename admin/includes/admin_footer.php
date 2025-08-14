                </div>
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
                                if (!sidebar.contains(e.target) && !e.target.closest('.sidebar-toggle')) {
                                    sidebar.classList.remove('collapsed');
                                    dashboardContainer.classList.remove('sidebar-collapsed');
                                }
                            }
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