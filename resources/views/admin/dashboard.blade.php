@extends('master.master')

@push('styles')
    <style>
        /* Avatar Styles */
        .avatar-sm {
            width: 40px;
            height: 40px;
        }

        /* Statistics Cards Hover Effect */
        .stats-card {
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        /* Activity Item Hover */
        .activity-item {
            transition: all 0.2s ease;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 12px;
        }

        .activity-item:hover {
            background-color: #f8f9fa;
            transform: translateX(5px);
        }

        /* Quick Stats Hover */
        .quick-stat-item {
            transition: all 0.2s ease;
            padding: 15px;
            border-radius: 8px;
            cursor: pointer;
        }

        .quick-stat-item:hover {
            background-color: #f8f9fa;
            transform: scale(1.05);
        }

        /* Contact Table Row Hover */
        .table-hover tbody tr {
            transition: all 0.2s ease;
        }

        .table-hover tbody tr:hover {
            background-color: #f8f9fa;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        /* Card Enhancements */
        .card {
            border: none;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        .card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        /* Badge Animations */
        .badge {
            transition: all 0.2s ease;
        }

        .badge:hover {
            transform: scale(1.1);
        }

        /* Icon Pulse Animation */
        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.7;
            }
        }

        .icon-pulse {
            animation: pulse 2s ease-in-out infinite;
        }

        /* Empty State */
        .empty-state {
            padding: 40px 20px;
        }

        .empty-state i {
            opacity: 0.5;
        }
    </style>
@endpush

@section('content')
    <div class="page-content">
        <nav class="page-breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Analytics Overview</li>
            </ol>
        </nav>

        <!-- Welcome Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center flex-wrap">
                            <div class="mb-3 mb-md-0">
                                <h4 class="card-title mb-2">
                                    <i data-feather="smile" class="text-primary me-2"
                                        style="width: 24px; height: 24px;"></i>
                                    Welcome back, {{ Auth::user()->name }}!
                                </h4>
                                <p class="text-muted mb-3">Here's what's happening with your website today.</p>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.blogs.create') }}" class="btn btn-sm btn-outline-primary">
                                        <i data-feather="plus" style="width: 14px; height: 14px;"></i> New Blog
                                    </a>
                                    <a href="{{ route('admin.projects.create') }}" class="btn btn-sm btn-outline-success">
                                        <i data-feather="briefcase" style="width: 14px; height: 14px;"></i> New Project
                                    </a>
                                    <a href="{{ route('admin.contacts.index') }}" class="btn btn-sm btn-outline-info">
                                        <i data-feather="mail" style="width: 14px; height: 14px;"></i> Messages
                                    </a>
                                </div>
                            </div>
                            <div class="text-end">
                                <div class="mb-2">
                                    <i data-feather="calendar" class="text-muted me-1"
                                        style="width: 16px; height: 16px;"></i>
                                    <h6 class="text-muted d-inline">{{ now()->format('l, F j, Y') }}</h6>
                                </div>
                                <div>
                                    <i data-feather="clock" class="text-muted me-1" style="width: 16px; height: 16px;"></i>
                                    <p class="text-muted d-inline">{{ now()->format('g:i A') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <!-- Projects Card -->
            <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
                <a href="{{ route('admin.projects.index') }}" class="text-decoration-none">
                    <div class="card bg-primary text-white stats-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h3 class="mb-0">{{ $totalProjects }}</h3>
                                    <p class="mb-0">Total Projects</p>
                                    <small class="text-white-50">{{ $activeProjects }} Active</small>
                                </div>
                                <div class="align-self-center">
                                    <i data-feather="briefcase" class="text-white-50"
                                        style="width: 40px; height: 40px;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Blogs Card -->
            <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
                <a href="{{ route('admin.blogs.index') }}" class="text-decoration-none">
                    <div class="card bg-success text-white stats-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h3 class="mb-0">{{ $totalBlogs }}</h3>
                                    <p class="mb-0">Total Blogs</p>
                                    <small class="text-white-50">{{ $publishedBlogs }} Published</small>
                                </div>
                                <div class="align-self-center">
                                    <i data-feather="file-text" class="text-white-50"
                                        style="width: 40px; height: 40px;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Contacts Card -->
            <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
                <a href="{{ route('admin.contacts.index') }}" class="text-decoration-none">
                    <div class="card bg-info text-white stats-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h3 class="mb-0">{{ $totalContacts }}</h3>
                                    <p class="mb-0">Total Contacts</p>
                                    <small class="text-white-50">{{ $newContacts }} New</small>
                                </div>
                                <div class="align-self-center">
                                    <i data-feather="mail" class="text-white-50" style="width: 40px; height: 40px;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Visitors Card -->
            <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
                <a href="{{ route('admin.visitors.index') }}" class="text-decoration-none">
                    <div class="card bg-warning text-white stats-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h3 class="mb-0">{{ $totalVisitors }}</h3>
                                    <p class="mb-0">Total Visitors</p>
                                    <small class="text-white-50">{{ $uniqueVisitors }} Unique</small>
                                </div>
                                <div class="align-self-center">
                                    <i data-feather="users" class="text-white-50" style="width: 40px; height: 40px;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <!-- Today's Statistics -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="card-title mb-0">
                                <i data-feather="activity" class="text-primary me-2"></i>
                                Today's Activity
                            </h6>
                            <span class="badge bg-primary">Live</span>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <i data-feather="user-plus" class="text-primary"
                                            style="width: 24px; height: 24px;"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $newUsersToday }}</h6>
                                        <small class="text-muted">New Users</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <i data-feather="mail" class="text-info" style="width: 24px; height: 24px;"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $contactsToday }}</h6>
                                        <small class="text-muted">New Contacts</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <i data-feather="eye" class="text-warning"
                                            style="width: 24px; height: 24px;"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $visitorsToday }}</h6>
                                        <small class="text-muted">Total Visits</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <i data-feather="user-check" class="text-success"
                                            style="width: 24px; height: 24px;"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $uniqueVisitorsToday }}</h6>
                                        <small class="text-muted">Unique Visitors</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Row -->
        <div class="row mb-4">
            <!-- Today's Contacts Table -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h6 class="card-title">Today's Contact Messages</h6>
                            <a href="{{ route('admin.contacts.index') }}" class="btn btn-outline-primary btn-sm">
                                <i data-feather="external-link"></i> View All
                            </a>
                        </div>

                        @if ($todayContacts->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Subject</th>
                                            <th>Project Type</th>
                                            <th>Time</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($todayContacts as $contact)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="me-2">
                                                            <div
                                                                class="avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center">
                                                                <span
                                                                    class="text-white fw-bold">{{ substr($contact->name, 0, 1) }}</span>
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <h6 class="mb-0">{{ $contact->name }}</h6>
                                                            @if ($contact->company)
                                                                <small class="text-muted">{{ $contact->company }}</small>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <a href="mailto:{{ $contact->email }}" class="text-decoration-none">
                                                        {{ $contact->email }}
                                                    </a>
                                                </td>
                                                <td>
                                                    <span class="text-truncate d-inline-block" style="max-width: 150px;"
                                                        title="{{ $contact->subject }}">
                                                        {{ $contact->subject }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-secondary">
                                                        {{ ucwords(str_replace('-', ' ', $contact->project_type)) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <small class="text-muted">
                                                        {{ $contact->created_at->format('g:i A') }}
                                                    </small>
                                                </td>
                                                <td>
                                                    @if ($contact->status == 'new')
                                                        <span class="badge bg-warning">New</span>
                                                    @elseif($contact->status == 'read')
                                                        <span class="badge bg-info">Read</span>
                                                    @elseif($contact->status == 'replied')
                                                        <span class="badge bg-success">Replied</span>
                                                    @else
                                                        <span class="badge bg-secondary">Closed</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center empty-state">
                                <i data-feather="mail" class="text-muted mb-3" style="width: 64px; height: 64px;"></i>
                                <h5 class="text-muted mb-2">No contacts today</h5>
                                <p class="text-muted mb-3">Check back later for new messages.</p>
                                <a href="{{ route('admin.contacts.index') }}" class="btn btn-sm btn-outline-primary">
                                    <i data-feather="eye" style="width: 14px; height: 14px;"></i> View All Contacts
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Quick Stats & Recent Activity -->
            <div class="col-lg-4">
                <!-- Quick Stats -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h6 class="card-title mb-3">
                            <i data-feather="bar-chart-2" class="text-primary me-2"></i>
                            Quick Stats
                        </h6>
                        <div class="row">
                            <div class="col-6 mb-3">
                                <div class="text-center quick-stat-item">
                                    <h4 class="text-primary mb-1">{{ $projectsThisMonth }}</h4>
                                    <small class="text-muted">Projects This Month</small>
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="text-center quick-stat-item">
                                    <h4 class="text-success mb-1">{{ $blogsThisMonth }}</h4>
                                    <small class="text-muted">Blogs This Month</small>
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="text-center quick-stat-item">
                                    <h4 class="text-info mb-1">{{ $contactsThisMonth }}</h4>
                                    <small class="text-muted">Contacts This Month</small>
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="text-center quick-stat-item">
                                    <h4 class="text-warning mb-1">{{ $visitorsThisMonth }}</h4>
                                    <small class="text-muted">Visitors This Month</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="card-title mb-0">
                                <i data-feather="clock" class="text-info me-2"></i>
                                Recent Activity
                            </h6>
                            <a href="{{ route('admin.contacts.index') }}" class="text-muted small">View All</a>
                        </div>
                        <div class="activity-feed">
                            @foreach ($recentContacts->take(3) as $contact)
                                <div class="d-flex align-items-start activity-item">
                                    <div class="me-3">
                                        <div
                                            class="avatar-sm bg-info rounded-circle d-flex align-items-center justify-content-center">
                                            <i data-feather="mail" class="text-white"
                                                style="width: 16px; height: 16px;"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">{{ $contact->name }}</h6>
                                        <p class="text-muted mb-1">{{ Str::limit($contact->subject, 30) }}</p>
                                        <small class="text-muted">{{ $contact->created_at->diffForHumans() }}</small>
                                    </div>
                                </div>
                            @endforeach

                            @if ($recentProjects->count() > 0)
                                <div class="d-flex align-items-start activity-item">
                                    <div class="me-3">
                                        <div
                                            class="avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center">
                                            <i data-feather="briefcase" class="text-white"
                                                style="width: 16px; height: 16px;"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">New Project</h6>
                                        <p class="text-muted mb-1">{{ Str::limit($recentProjects->first()->title, 30) }}
                                        </p>
                                        <small
                                            class="text-muted">{{ $recentProjects->first()->created_at->diffForHumans() }}</small>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Visitor Analytics Row -->
        <div class="row mb-4">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title mb-4">
                            <i data-feather="trending-up" class="text-primary me-2"></i>
                            Top Visited Pages (Last 30 Days)
                        </h6>
                        @if ($topPages->count() > 0)
                            <div class="list-group list-group-flush">
                                @foreach ($topPages as $page)
                                    <div
                                        class="list-group-item d-flex justify-content-between align-items-center px-0 border-0">
                                        <div class="d-flex align-items-center">
                                            <i data-feather="file-text" class="text-muted me-2"
                                                style="width: 18px; height: 18px;"></i>
                                            <h6 class="mb-0">{{ $page->page_url }}</h6>
                                        </div>
                                        <div>
                                            <span class="badge bg-primary rounded-pill">
                                                <i data-feather="eye" style="width: 12px; height: 12px;"></i>
                                                {{ $page->visits }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center empty-state">
                                <i data-feather="bar-chart" class="text-muted mb-3"
                                    style="width: 64px; height: 64px;"></i>
                                <h6 class="text-muted mb-2">No visitor data</h6>
                                <p class="text-muted small">Page visit data will appear here</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title mb-4">
                            <i data-feather="globe" class="text-success me-2"></i>
                            Visitors by Country (Last 30 Days)
                        </h6>
                        @if ($visitorsByCountry->count() > 0)
                            <div class="list-group list-group-flush">
                                @foreach ($visitorsByCountry->take(5) as $country)
                                    <div
                                        class="list-group-item d-flex justify-content-between align-items-center px-0 border-0">
                                        <div class="d-flex align-items-center">
                                            <i data-feather="map-pin" class="text-muted me-2"
                                                style="width: 18px; height: 18px;"></i>
                                            <h6 class="mb-0">{{ $country->country }}</h6>
                                        </div>
                                        <div>
                                            <span class="badge bg-success rounded-pill">
                                                <i data-feather="users" style="width: 12px; height: 12px;"></i>
                                                {{ $country->count }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center empty-state">
                                <i data-feather="globe" class="text-muted mb-3" style="width: 64px; height: 64px;"></i>
                                <h6 class="text-muted mb-2">No location data</h6>
                                <p class="text-muted small">Visitor country data will appear here</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        // Initialize Feather icons
        feather.replace();
    </script>
@endsection
