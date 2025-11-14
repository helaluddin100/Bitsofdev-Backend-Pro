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
        <ul class="nav">
            <li class="nav-item nav-category">Main</li>
            <li class="nav-item">
                <a href="{{ route('admin.dashboard') }}" class="nav-link">
                    <i class="link-icon" data-feather="box"></i>
                    <span class="link-title">Dashboard</span>
                </a>
            </li>

            <li class="nav-item nav-category">Content Management</li>

            <!-- Blog Management -->
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#blog-management" role="button"
                    aria-expanded="false" aria-controls="blog-management">
                    <i class="link-icon" data-feather="file-text"></i>
                    <span class="link-title">Blog Management</span>
                    <i class="link-arrow" data-feather="chevron-down"></i>
                </a>
                <div class="collapse" id="blog-management">
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
                    aria-expanded="false" aria-controls="project-management">
                    <i class="link-icon" data-feather="briefcase"></i>
                    <span class="link-title">Project Management</span>
                    <i class="link-arrow" data-feather="chevron-down"></i>
                </a>
                <div class="collapse" id="project-management">
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

            <!-- Team Management -->
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#team-management" role="button"
                    aria-expanded="false" aria-controls="team-management">
                    <i class="link-icon" data-feather="users"></i>
                    <span class="link-title">Team Management</span>
                    <i class="link-arrow" data-feather="chevron-down"></i>
                </a>
                <div class="collapse" id="team-management">
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
                    aria-expanded="false" aria-controls="pricing-management">
                    <i class="link-icon" data-feather="dollar-sign"></i>
                    <span class="link-title">Pricing Management</span>
                    <i class="link-arrow" data-feather="chevron-down"></i>
                </a>
                <div class="collapse" id="pricing-management">
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
                    aria-expanded="false" aria-controls="about-management">
                    <i class="link-icon" data-feather="info"></i>
                    <span class="link-title">About Management</span>
                    <i class="link-arrow" data-feather="chevron-down"></i>
                </a>
                <div class="collapse" id="about-management">
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
                <a href="{{ route('admin.analytics.index') }}" class="nav-link">
                    <i class="link-icon" data-feather="bar-chart-2"></i>
                    <span class="link-title">Analytics</span>
                </a>
            </li>

            <!-- Visitor Data -->
            <li class="nav-item">
                <a href="{{ route('admin.visitors.index') }}" class="nav-link">
                    <i class="link-icon" data-feather="users"></i>
                    <span class="link-title">Visitor Data</span>
                </a>
            </li>

            <!-- Contact Management -->
            <li class="nav-item">
                <a href="{{ route('admin.contacts.index') }}" class="nav-link">
                    <i class="link-icon" data-feather="mail"></i>
                    <span class="link-title">Contact Management</span>
                </a>
            </li>

            <!-- Testimonials Management -->
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#testimonials-management" role="button"
                    aria-expanded="false" aria-controls="testimonials-management">
                    <i class="link-icon" data-feather="message-square"></i>
                    <span class="link-title">Testimonials</span>
                    <i class="link-arrow" data-feather="chevron-down"></i>
                </a>
                <div class="collapse" id="testimonials-management">
                    <ul class="nav sub-menu">
                        <li class="nav-item">
                            <a href="{{ route('admin.testimonials.index') }}" class="nav-link">All Testimonials</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.testimonials.create') }}" class="nav-link">Add Testimonial</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.testimonials.index', ['status' => 'featured']) }}" class="nav-link">Featured</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.testimonials.index', ['status' => 'pending']) }}" class="nav-link">Pending Review</a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- AI Chatbot Management -->
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#ai-chatbot-management" role="button"
                    aria-expanded="false" aria-controls="ai-chatbot-management">
                    <i class="link-icon" data-feather="message-circle"></i>
                    <span class="link-title">AI Chatbot</span>
                    <i class="link-arrow" data-feather="chevron-down"></i>
                </a>
                <div class="collapse" id="ai-chatbot-management">
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
                    aria-expanded="false" aria-controls="marketing-management">
                    <i class="link-icon" data-feather="mail"></i>
                    <span class="link-title">Marketing Campaigns</span>
                    <i class="link-arrow" data-feather="chevron-down"></i>
                </a>
                <div class="collapse" id="marketing-management">
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
                    aria-expanded="false" aria-controls="user-management">
                    <i class="link-icon" data-feather="user"></i>
                    <span class="link-title">User Management</span>
                    <i class="link-arrow" data-feather="chevron-down"></i>
                </a>
                <div class="collapse" id="user-management">
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
    // Update failed jobs badge count
    function updateFailedJobsBadge() {
        $.ajax({
            url: '{{ route("admin.marketing.jobs.data") }}',
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
