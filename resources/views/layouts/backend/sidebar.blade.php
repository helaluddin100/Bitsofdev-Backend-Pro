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
