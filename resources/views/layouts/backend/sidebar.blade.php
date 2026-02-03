<style>
    /* Force only .show dropdowns to be visible */
    .sidebar-body .collapse:not(.show) {
        display: none !important;
    }

    .sidebar-body .collapse.show {
        display: block !important;
    }
</style>

<nav class="sidebar">
    <div class="sidebar-header">
        <a href="#" class="sidebar-brand">
            Noble<span>UI</span>
        </a>
        <div class="sidebar-toggler not-active">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </div>
    <div class="sidebar-body">
        <ul class="nav" id="sidebar-accordion">
            <li class="nav-item nav-category">Main</li>
            <li class="nav-item">
                <a href="{{ route('admin.dashboard') }}"
                    class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="link-icon" data-feather="box"></i>
                    <span class="link-title">Dashboard</span>
                </a>
            </li>

            <li class="nav-item nav-category">Content Management</li>

            <!-- Blog Management -->
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#blog-management" role="button"
                    aria-expanded="{{ request()->routeIs('admin.blogs.*') || request()->routeIs('admin.categories.*') ? 'true' : 'false' }}"
                    aria-controls="blog-management">
                    <i class="link-icon" data-feather="file-text"></i>
                    <span class="link-title">Blog Management</span>
                    <i class="link-arrow" data-feather="chevron-down"></i>
                </a>
                <div class="collapse {{ request()->routeIs('admin.blogs.*') || request()->routeIs('admin.categories.*') ? 'show' : '' }}"
                    id="blog-management" data-bs-parent="#sidebar-accordion">
                    <ul class="nav sub-menu">
                        <li class="nav-item">
                            <a href="{{ route('admin.blogs.index') }}" class="nav-link">All Posts</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.blogs.create') }}" class="nav-link">Create Post</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.categories.index') }}" class="nav-link">Categories</a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Project Management -->
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#project-management" role="button"
                    aria-expanded="{{ request()->routeIs('admin.projects.*') ? 'true' : 'false' }}"
                    aria-controls="project-management">
                    <i class="link-icon" data-feather="briefcase"></i>
                    <span class="link-title">Project Management</span>
                    <i class="link-arrow" data-feather="chevron-down"></i>
                </a>
                <div class="collapse {{ request()->routeIs('admin.projects.*') ? 'show' : '' }}" id="project-management"
                    data-bs-parent="#sidebar-accordion">
                    <ul class="nav sub-menu">
                        <li class="nav-item">
                            <a href="{{ route('admin.projects.index') }}" class="nav-link">All Projects</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.projects.create') }}" class="nav-link">Create Project</a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Product Management -->
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#product-management" role="button"
                    aria-expanded="{{ request()->routeIs('admin.products.*') ? 'true' : 'false' }}"
                    aria-controls="product-management">
                    <i class="link-icon" data-feather="package"></i>
                    <span class="link-title">Product Management</span>
                    <i class="link-arrow" data-feather="chevron-down"></i>
                </a>
                <div class="collapse {{ request()->routeIs('admin.products.*') ? 'show' : '' }}" id="product-management"
                    data-bs-parent="#sidebar-accordion">
                    <ul class="nav sub-menu">
                        <li class="nav-item">
                            <a href="{{ route('admin.products.index') }}" class="nav-link">All Products</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.products.create') }}" class="nav-link">Create Product</a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Team Management -->
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#team-management" role="button"
                    aria-expanded="{{ request()->routeIs('admin.teams.*') ? 'true' : 'false' }}"
                    aria-controls="team-management">
                    <i class="link-icon" data-feather="users"></i>
                    <span class="link-title">Team Management</span>
                    <i class="link-arrow" data-feather="chevron-down"></i>
                </a>
                <div class="collapse {{ request()->routeIs('admin.teams.*') ? 'show' : '' }}" id="team-management"
                    data-bs-parent="#sidebar-accordion">
                    <ul class="nav sub-menu">
                        <li class="nav-item">
                            <a href="{{ route('admin.teams.index') }}" class="nav-link">All Members</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.teams.create') }}" class="nav-link">Add Member</a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Pricing Management -->
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#pricing-management" role="button"
                    aria-expanded="{{ request()->routeIs('admin.pricing.*') ? 'true' : 'false' }}"
                    aria-controls="pricing-management">
                    <i class="link-icon" data-feather="dollar-sign"></i>
                    <span class="link-title">Pricing Management</span>
                    <i class="link-arrow" data-feather="chevron-down"></i>
                </a>
                <div class="collapse {{ request()->routeIs('admin.pricing.*') ? 'show' : '' }}" id="pricing-management"
                    data-bs-parent="#sidebar-accordion">
                    <ul class="nav sub-menu">
                        <li class="nav-item">
                            <a href="{{ route('admin.pricing.index') }}" class="nav-link">All Plans</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.pricing.create') }}" class="nav-link">Create Plan</a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- About Management -->
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#about-management" role="button"
                    aria-expanded="{{ request()->routeIs('admin.about.*') ? 'true' : 'false' }}"
                    aria-controls="about-management">
                    <i class="link-icon" data-feather="info"></i>
                    <span class="link-title">About Management</span>
                    <i class="link-arrow" data-feather="chevron-down"></i>
                </a>
                <div class="collapse {{ request()->routeIs('admin.about.*') ? 'show' : '' }}" id="about-management"
                    data-bs-parent="#sidebar-accordion">
                    <ul class="nav sub-menu">
                        <li class="nav-item">
                            <a href="{{ route('admin.about.index') }}" class="nav-link">Company Information</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.about.edit') }}" class="nav-link">Edit Information</a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Analytics -->
            <li class="nav-item">
                <a href="{{ route('admin.analytics.index') }}"
                    class="nav-link {{ request()->routeIs('admin.analytics.*') ? 'active' : '' }}">
                    <i class="link-icon" data-feather="bar-chart-2"></i>
                    <span class="link-title">Analytics</span>
                </a>
            </li>

            <!-- Visitor Data -->
            <li class="nav-item">
                <a href="{{ route('admin.visitors.index') }}"
                    class="nav-link {{ request()->routeIs('admin.visitors.*') ? 'active' : '' }}">
                    <i class="link-icon" data-feather="users"></i>
                    <span class="link-title">Visitor Data</span>
                </a>
            </li>

            <!-- Contact Management -->
            <li class="nav-item">
                <a href="{{ route('admin.contacts.index') }}"
                    class="nav-link {{ request()->routeIs('admin.contacts.*') ? 'active' : '' }}">
                    <i class="link-icon" data-feather="mail"></i>
                    <span class="link-title">Contact Management</span>
                </a>
            </li>

            <!-- Meeting Bookings -->
            <li class="nav-item">
                <a href="{{ route('admin.meeting-bookings.index') }}"
                    class="nav-link {{ request()->routeIs('admin.meeting-bookings.*') ? 'active' : '' }}">
                    <i class="link-icon" data-feather="video"></i>
                    <span class="link-title">Meeting Bookings</span>
                </a>
            </li>

            <!-- Newsletter Subscribers -->
            <li class="nav-item">
                <a href="{{ route('admin.newsletter-subscribers.index') }}"
                    class="nav-link {{ request()->routeIs('admin.newsletter-subscribers.*') ? 'active' : '' }}">
                    <i class="link-icon" data-feather="inbox"></i>
                    <span class="link-title">Newsletter Subscribers</span>
                </a>
            </li>

            <!-- Testimonials Management -->
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#testimonials-management" role="button"
                    aria-expanded="{{ request()->routeIs('admin.testimonials.*') ? 'true' : 'false' }}"
                    aria-controls="testimonials-management">
                    <i class="link-icon" data-feather="message-square"></i>
                    <span class="link-title">Testimonials</span>
                    <i class="link-arrow" data-feather="chevron-down"></i>
                </a>
                <div class="collapse {{ request()->routeIs('admin.testimonials.*') ? 'show' : '' }}"
                    id="testimonials-management" data-bs-parent="#sidebar-accordion">
                    <ul class="nav sub-menu">
                        <li class="nav-item">
                            <a href="{{ route('admin.testimonials.index') }}" class="nav-link">All Testimonials</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.testimonials.create') }}" class="nav-link">Add Testimonial</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.testimonials.index', ['status' => 'featured']) }}"
                                class="nav-link">Featured</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.testimonials.index', ['status' => 'pending']) }}"
                                class="nav-link">Pending Review</a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- AI Chatbot Management -->
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#ai-chatbot-management" role="button"
                    aria-expanded="{{ request()->routeIs('admin.ai-*') || request()->routeIs('admin.qa-*') || request()->routeIs('admin.visitor-*') || request()->routeIs('admin.quick-*') ? 'true' : 'false' }}"
                    aria-controls="ai-chatbot-management">
                    <i class="link-icon" data-feather="message-circle"></i>
                    <span class="link-title">AI Chatbot</span>
                    <i class="link-arrow" data-feather="chevron-down"></i>
                </a>
                <div class="collapse {{ request()->routeIs('admin.ai-*') || request()->routeIs('admin.qa-*') || request()->routeIs('admin.visitor-*') || request()->routeIs('admin.quick-*') ? 'show' : '' }}"
                    id="ai-chatbot-management" data-bs-parent="#sidebar-accordion">
                    <ul class="nav sub-menu">
                        <li class="nav-item">
                            <a href="{{ route('admin.ai-dashboard') }}" class="nav-link">AI Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.ai-control') }}" class="nav-link">
                                <i class="link-icon" data-feather="cpu"></i>
                                <span class="link-title">AI Control</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.qa-management') }}" class="nav-link">Q&A Management</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.visitor-questions') }}" class="nav-link">Visitor Questions</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.quick-answers') }}" class="nav-link">Quick Answers</a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Marketing Campaign Management -->
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#marketing-management" role="button"
                    aria-expanded="{{ request()->routeIs('admin.marketing.*') ? 'true' : 'false' }}"
                    aria-controls="marketing-management">
                    <i class="link-icon" data-feather="mail"></i>
                    <span class="link-title">Marketing Campaigns</span>
                    <i class="link-arrow" data-feather="chevron-down"></i>
                </a>
                <div class="collapse {{ request()->routeIs('admin.marketing.*') ? 'show' : '' }}"
                    id="marketing-management" data-bs-parent="#sidebar-accordion">
                    <ul class="nav sub-menu">
                        <li class="nav-item">
                            <a href="{{ route('admin.marketing.dashboard') }}" class="nav-link">
                                <i class="link-icon" data-feather="trending-up"></i>
                                <span class="link-title">Dashboard</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.marketing.leads.index') }}" class="nav-link">
                                <i class="link-icon" data-feather="users"></i>
                                <span class="link-title">Leads Management</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.marketing.leads.create') }}" class="nav-link">
                                <i class="link-icon" data-feather="user-plus"></i>
                                <span class="link-title">Add Lead</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.marketing.leads.import.form') }}" class="nav-link">
                                <i class="link-icon" data-feather="upload"></i>
                                <span class="link-title">Import Leads</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.marketing.campaigns.index') }}" class="nav-link">
                                <i class="link-icon" data-feather="send"></i>
                                <span class="link-title">Campaigns</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.marketing.campaigns.create') }}" class="nav-link">
                                <i class="link-icon" data-feather="plus-circle"></i>
                                <span class="link-title">Create Campaign</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.marketing.responses.index') }}" class="nav-link">
                                <i class="link-icon" data-feather="message-circle"></i>
                                <span class="link-title">Responses</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.marketing.analytics.leads') }}" class="nav-link">
                                <i class="link-icon" data-feather="bar-chart"></i>
                                <span class="link-title">Analytics</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.marketing.jobs.index') }}" class="nav-link">
                                <i class="link-icon" data-feather="activity"></i>
                                <span class="link-title">Email Jobs Status</span>
                                <span class="badge badge-danger" id="failedJobsBadge" style="display: none;">0</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="nav-item nav-category">System</li>

            <!-- User Management -->
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#user-management" role="button"
                    aria-expanded="{{ request()->routeIs('admin.users.*') ? 'true' : 'false' }}"
                    aria-controls="user-management">
                    <i class="link-icon" data-feather="user"></i>
                    <span class="link-title">User Management</span>
                    <i class="link-arrow" data-feather="chevron-down"></i>
                </a>
                <div class="collapse {{ request()->routeIs('admin.users.*') ? 'show' : '' }}" id="user-management"
                    data-bs-parent="#sidebar-accordion">
                    <ul class="nav sub-menu">
                        <li class="nav-item">
                            <a href="#" class="nav-link">All Users</a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">Roles & Permissions</a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Settings -->
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="link-icon" data-feather="settings"></i>
                    <span class="link-title">Settings</span>
                </a>
            </li>
        </ul>
    </div>
</nav>

@push('scripts')
    <script>
        // Sidebar Accordion Fix - Execute immediately
        (function() {
            // On page load, keep only the first .show dropdown visible
            var allDropdowns = document.querySelectorAll('.sidebar-body .collapse');
            var hasActiveDropdown = false;

            allDropdowns.forEach(function(dropdown) {
                if (dropdown.classList.contains('show')) {
                    if (hasActiveDropdown) {
                        // Remove show class from additional dropdowns
                        dropdown.classList.remove('show');
                        var toggle = document.querySelector('[href="#' + dropdown.id + '"]');
                        if (toggle) {
                            toggle.setAttribute('aria-expanded', 'false');
                        }
                    } else {
                        hasActiveDropdown = true;
                    }
                }
            });
        })();

        // jQuery ready function for event handlers
        $(document).ready(function() {
            // When clicking on a dropdown toggle
            $('.sidebar-body [data-bs-toggle="collapse"]').on('click', function(e) {
                e.preventDefault();
                var target = $(this).attr('href');
                var targetCollapse = $(target);
                var isCurrentlyOpen = targetCollapse.hasClass('show');

                // Close all dropdowns first
                $('.sidebar-body .collapse').removeClass('show');
                $('.sidebar-body [data-bs-toggle="collapse"]').attr('aria-expanded', 'false');

                // Open the clicked dropdown if it was closed
                if (!isCurrentlyOpen) {
                    targetCollapse.addClass('show');
                    $(this).attr('aria-expanded', 'true');
                }
            });

            // Handle Bootstrap collapse events
            $('.sidebar-body .collapse').on('show.bs.collapse', function(e) {
                e.stopPropagation();
                // Close all other dropdowns
                $('.sidebar-body .collapse').not(this).removeClass('show');
                $('.sidebar-body [data-bs-toggle="collapse"]').attr('aria-expanded', 'false');
                var toggle = $('[href="#' + this.id + '"]');
                toggle.attr('aria-expanded', 'true');
            });
        });

        // Update failed jobs badge count
        function updateFailedJobsBadge() {
            $.ajax({
                url: '{{ route('admin.marketing.jobs.data') }}',
                method: 'GET',
                success: function(response) {
                    const failedCount = (response.stats && response.stats.failed) ? response.stats.failed : 0;
                    const badge = $('#failedJobsBadge');
                    if (failedCount > 0) {
                        badge.text(failedCount).show();
                    } else {
                        badge.hide();
                    }
                },
                error: function() {
                    // Silently fail - don't show badge if API fails
                    $('#failedJobsBadge').hide();
                }
            });
        }

        // Update badge every 30 seconds
        $(document).ready(function() {
            // Only update if we're on a page that has the badge
            if ($('#failedJobsBadge').length > 0) {
                updateFailedJobsBadge();
                setInterval(updateFailedJobsBadge, 30000);
            }
        });
    </script>
@endpush
