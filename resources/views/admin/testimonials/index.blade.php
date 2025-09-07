@extends('master.master')

@section('title', 'Testimonials Management')

@section('content')
    <div class="page-content">

        <nav class="page-breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Management</a></li>
                <li class="breadcrumb-item active" aria-current="page">Testimonials Management</li>
            </ol>
        </nav>

        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h6 class="card-title">Testimonials Management</h6>
                            <a href="{{ route('admin.testimonials.create') }}" class="btn btn-primary">
                                <i data-feather="plus"></i> Add New Testimonial
                            </a>
                        </div>

                        <!-- Statistics Cards -->
                        <div class="row mb-4">
                            <div class="col-md-2">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <div class="bg-primary rounded-3 p-3">
                                            <i data-feather="message-square" class="text-white"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <p class="mb-0 text-muted">Total</p>
                                        <h4 class="my-1">{{ $stats['total'] }}</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <div class="bg-success rounded-3 p-3">
                                            <i data-feather="check-circle" class="text-white"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <p class="mb-0 text-muted">Active</p>
                                        <h4 class="my-1">{{ $stats['active'] }}</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <div class="bg-warning rounded-3 p-3">
                                            <i data-feather="star" class="text-white"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <p class="mb-0 text-muted">Featured</p>
                                        <h4 class="my-1">{{ $stats['featured'] }}</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <div class="bg-info rounded-3 p-3">
                                            <i data-feather="shield" class="text-white"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <p class="mb-0 text-muted">Verified</p>
                                        <h4 class="my-1">{{ $stats['verified'] }}</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <div class="bg-secondary rounded-3 p-3">
                                            <i data-feather="clock" class="text-white"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <p class="mb-0 text-muted">Pending</p>
                                        <h4 class="my-1">{{ $stats['pending'] }}</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <div class="bg-danger rounded-3 p-3">
                                            <i data-feather="star" class="text-white"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <p class="mb-0 text-muted">Avg Rating</p>
                                        <h4 class="my-1">{{ $stats['average_rating'] }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Filters -->
                        <form method="GET" class="mb-4">
                            <div class="row">
                                <div class="col-md-3">
                                    <input type="text" name="search" class="form-control" placeholder="Search testimonials..." value="{{ request('search') }}">
                                </div>
                                <div class="col-md-2">
                                    <select name="status" class="form-control">
                                        <option value="">All Status</option>
                                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                        <option value="featured" {{ request('status') == 'featured' ? 'selected' : '' }}>Featured</option>
                                        <option value="verified" {{ request('status') == 'verified' ? 'selected' : '' }}>Verified</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select name="project_type" class="form-control">
                                        <option value="">All Projects</option>
                                        <option value="web-development" {{ request('project_type') == 'web-development' ? 'selected' : '' }}>Web Development</option>
                                        <option value="mobile-app" {{ request('project_type') == 'mobile-app' ? 'selected' : '' }}>Mobile App</option>
                                        <option value="ui-ux-design" {{ request('project_type') == 'ui-ux-design' ? 'selected' : '' }}>UI/UX Design</option>
                                        <option value="e-commerce" {{ request('project_type') == 'e-commerce' ? 'selected' : '' }}>E-commerce</option>
                                        <option value="consulting" {{ request('project_type') == 'consulting' ? 'selected' : '' }}>Consulting</option>
                                        <option value="seo" {{ request('project_type') == 'seo' ? 'selected' : '' }}>SEO</option>
                                        <option value="digital-marketing" {{ request('project_type') == 'digital-marketing' ? 'selected' : '' }}>Digital Marketing</option>
                                        <option value="other" {{ request('project_type') == 'other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select name="rating" class="form-control">
                                        <option value="">All Ratings</option>
                                        <option value="5" {{ request('rating') == '5' ? 'selected' : '' }}>5 Stars</option>
                                        <option value="4" {{ request('rating') == '4' ? 'selected' : '' }}>4 Stars</option>
                                        <option value="3" {{ request('rating') == '3' ? 'selected' : '' }}>3 Stars</option>
                                        <option value="2" {{ request('rating') == '2' ? 'selected' : '' }}>2 Stars</option>
                                        <option value="1" {{ request('rating') == '1' ? 'selected' : '' }}>1 Star</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <button type="submit" class="btn btn-primary">Filter</button>
                                    <a href="{{ route('admin.testimonials.index') }}" class="btn btn-secondary">Clear</a>
                                    <a href="{{ route('admin.testimonials.export', request()->query()) }}" class="btn btn-success">Export</a>
                                </div>
                            </div>
                        </form>

                        <!-- Bulk Actions -->
                        <form id="bulk-form" method="POST" action="{{ route('admin.testimonials.bulk-action') }}">
                            @csrf
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <select name="action" class="form-control" required>
                                        <option value="">Bulk Actions</option>
                                        <option value="activate">Activate</option>
                                        <option value="deactivate">Deactivate</option>
                                        <option value="feature">Feature</option>
                                        <option value="unfeature">Unfeature</option>
                                        <option value="delete">Delete</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <button type="submit" class="btn btn-warning" onclick="return confirm('Are you sure?')">Apply</button>
                                </div>
                            </div>

                            <!-- Testimonials Table -->
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>
                                                <input type="checkbox" id="select-all">
                                            </th>
                                            <th>ID</th>
                                            <th>Client</th>
                                            <th>Rating</th>
                                            <th>Project</th>
                                            <th>Status</th>
                                            <th>Featured</th>
                                            <th>Verified</th>
                                            <th>Created</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($testimonials as $testimonial)
                                        <tr>
                                            <td>
                                                <input type="checkbox" name="testimonials[]" value="{{ $testimonial->id }}" class="testimonial-checkbox">
                                            </td>
                                            <td>{{ $testimonial->id }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="{{ $testimonial->image }}" alt="{{ $testimonial->name }}" class="rounded-circle me-2" width="40" height="40">
                                                    <div>
                                                        <strong>{{ $testimonial->name }}</strong><br>
                                                        <small class="text-muted">{{ $testimonial->role }}</small>
                                                        @if($testimonial->company)
                                                            <br><small class="text-muted">{{ $testimonial->company }}</small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <i data-feather="star" class="{{ $i <= $testimonial->rating ? 'text-warning' : 'text-muted' }}" width="16" height="16"></i>
                                                    @endfor
                                                    <span class="ms-1">{{ $testimonial->rating }}</span>
                                                </div>
                                            </td>
                                            <td>
                                                @if($testimonial->project_name)
                                                    <span class="badge bg-info">{{ $testimonial->project_name }}</span><br>
                                                @endif
                                                @if($testimonial->project_type)
                                                    <small class="text-muted">{{ ucfirst(str_replace('-', ' ', $testimonial->project_type)) }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge {{ $testimonial->is_active ? 'bg-success' : 'bg-danger' }}">
                                                    {{ $testimonial->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge {{ $testimonial->is_featured ? 'bg-warning' : 'bg-secondary' }}">
                                                    {{ $testimonial->is_featured ? 'Featured' : 'Regular' }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge {{ $testimonial->is_verified ? 'bg-primary' : 'bg-secondary' }}">
                                                    {{ $testimonial->is_verified ? 'Verified' : 'Unverified' }}
                                                </span>
                                            </td>
                                            <td>{{ $testimonial->created_at->format('M d, Y') }}</td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.testimonials.show', $testimonial) }}" class="btn btn-sm btn-info" title="View">
                                                        <i data-feather="eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.testimonials.edit', $testimonial) }}" class="btn btn-sm btn-warning" title="Edit">
                                                        <i data-feather="edit"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm {{ $testimonial->is_active ? 'btn-secondary' : 'btn-success' }}"
                                                            onclick="toggleStatus({{ $testimonial->id }})" title="{{ $testimonial->is_active ? 'Deactivate' : 'Activate' }}">
                                                        <i data-feather="{{ $testimonial->is_active ? 'pause' : 'play' }}"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-sm {{ $testimonial->is_featured ? 'btn-secondary' : 'btn-warning' }}"
                                                            onclick="toggleFeatured({{ $testimonial->id }})" title="{{ $testimonial->is_featured ? 'Unfeature' : 'Feature' }}">
                                                        <i data-feather="star"></i>
                                                    </button>
                                                    <form method="POST" action="{{ route('admin.testimonials.destroy', $testimonial) }}" class="d-inline"
                                                          onsubmit="return confirm('Are you sure you want to delete this testimonial?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                            <i data-feather="trash-2"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="10" class="text-center">No testimonials found.</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </form>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center">
                            {{ $testimonials->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
<script>
// Select All Checkbox
document.getElementById('select-all').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.testimonial-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
});

// Toggle Status
function toggleStatus(id) {
    fetch(`/admin/testimonials/${id}/toggle-status`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred');
    });
}

// Toggle Featured
function toggleFeatured(id) {
    fetch(`/admin/testimonials/${id}/toggle-featured`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred');
    });
}
</script>
@endsection
