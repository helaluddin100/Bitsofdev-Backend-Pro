@extends('master.master')

@section('title', 'View Testimonial')

@section('content')
    <div class="page-content">

        <nav class="page-breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Management</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.testimonials.index') }}">Testimonials</a></li>
                <li class="breadcrumb-item active" aria-current="page">View Testimonial</li>
            </ol>
        </nav>

        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h6 class="card-title">Testimonial Details - {{ $testimonial->name }}</h6>
                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.testimonials.edit', $testimonial) }}" class="btn btn-warning">
                                    <i data-feather="edit"></i> Edit
                                </a>
                                <a href="{{ route('admin.testimonials.index') }}" class="btn btn-secondary">
                                    <i data-feather="arrow-left"></i> Back to Testimonials
                                </a>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Client Information -->
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="card-title">Client Information</h6>
                                    </div>
                                    <div class="card-body text-center">
                                        <img src="{{ $testimonial->image }}" alt="{{ $testimonial->name }}"
                                             class="img-thumbnail rounded-circle mb-3" width="120" height="120">
                                        <h4>{{ $testimonial->name }}</h4>
                                        <p class="text-muted">{{ $testimonial->role }}</p>
                                        @if($testimonial->company)
                                            <p class="text-muted">{{ $testimonial->company }}</p>
                                        @endif
                                        @if($testimonial->location)
                                            <p class="text-muted"><i data-feather="map-pin"></i> {{ $testimonial->location }}</p>
                                        @endif
                                        @if($testimonial->email)
                                            <p class="text-muted"><i data-feather="mail"></i> {{ $testimonial->email }}</p>
                                        @endif

                                        <!-- Rating -->
                                        <div class="mt-3">
                                            <h6>Rating</h6>
                                            <div class="d-flex justify-content-center">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i data-feather="star" class="{{ $i <= $testimonial->rating ? 'text-warning' : 'text-muted' }}" width="20" height="20"></i>
                                                @endfor
                                            </div>
                                            <span class="badge bg-primary mt-2">{{ $testimonial->rating }}/5 Stars</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Testimonial Content -->
                            <div class="col-md-8">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="card-title">Testimonial Content</h6>
                                    </div>
                                    <div class="card-body">
                                        <blockquote class="blockquote">
                                            <p class="mb-0">"{{ $testimonial->content }}"</p>
                                        </blockquote>

                                        @if($testimonial->project_name || $testimonial->project_type)
                                            <div class="mt-4">
                                                <h6>Project Information</h6>
                                                @if($testimonial->project_name)
                                                    <p><strong>Project Name:</strong> {{ $testimonial->project_name }}</p>
                                                @endif
                                                @if($testimonial->project_type)
                                                    <p><strong>Project Type:</strong> {{ ucfirst(str_replace('-', ' ', $testimonial->project_type)) }}</p>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Status Information -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="card-title">Status & Settings</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        <div class="rounded-3 p-3 {{ $testimonial->is_active ? 'bg-success' : 'bg-danger' }}">
                                                            <i data-feather="{{ $testimonial->is_active ? 'check' : 'x' }}" class="text-white"></i>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <p class="mb-0 text-muted">Status</p>
                                                        <h6 class="my-1">{{ $testimonial->is_active ? 'Active' : 'Inactive' }}</h6>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        <div class="rounded-3 p-3 {{ $testimonial->is_featured ? 'bg-warning' : 'bg-secondary' }}">
                                                            <i data-feather="star" class="text-white"></i>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <p class="mb-0 text-muted">Featured</p>
                                                        <h6 class="my-1">{{ $testimonial->is_featured ? 'Yes' : 'No' }}</h6>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        <div class="rounded-3 p-3 {{ $testimonial->is_verified ? 'bg-primary' : 'bg-secondary' }}">
                                                            <i data-feather="shield" class="text-white"></i>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <p class="mb-0 text-muted">Verified</p>
                                                        <h6 class="my-1">{{ $testimonial->is_verified ? 'Yes' : 'No' }}</h6>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        <div class="rounded-3 p-3 bg-info">
                                                            <i data-feather="sort-numeric-up" class="text-white"></i>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <p class="mb-0 text-muted">Sort Order</p>
                                                        <h6 class="my-1">{{ $testimonial->sort_order }}</h6>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Timestamps -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="card-title">Timestamps</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <p><strong>Created:</strong> {{ $testimonial->created_at->format('M d, Y H:i:s') }}</p>
                                                <p><strong>Last Updated:</strong> {{ $testimonial->updated_at->format('M d, Y H:i:s') }}</p>
                                            </div>
                                            <div class="col-md-6">
                                                @if($testimonial->submitted_at)
                                                    <p><strong>Submitted:</strong> {{ $testimonial->submitted_at->format('M d, Y H:i:s') }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="card-title">Quick Actions</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-flex gap-2">
                                            <button type="button" class="btn {{ $testimonial->is_active ? 'btn-secondary' : 'btn-success' }}"
                                                    onclick="toggleStatus({{ $testimonial->id }})">
                                                <i data-feather="{{ $testimonial->is_active ? 'pause' : 'play' }}"></i>
                                                {{ $testimonial->is_active ? 'Deactivate' : 'Activate' }}
                                            </button>
                                            <button type="button" class="btn {{ $testimonial->is_featured ? 'btn-secondary' : 'btn-warning' }}"
                                                    onclick="toggleFeatured({{ $testimonial->id }})">
                                                <i data-feather="star"></i>
                                                {{ $testimonial->is_featured ? 'Unfeature' : 'Feature' }}
                                            </button>
                                            <a href="{{ route('admin.testimonials.edit', $testimonial) }}" class="btn btn-warning">
                                                <i data-feather="edit"></i> Edit
                                            </a>
                                            <form method="POST" action="{{ route('admin.testimonials.destroy', $testimonial) }}" class="d-inline"
                                                  onsubmit="return confirm('Are you sure you want to delete this testimonial?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger">
                                                    <i data-feather="trash-2"></i> Delete
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
<script>
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
