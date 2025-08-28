@extends('master.master')

@section('content')
<div class="page-content">
    <nav class="page-breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.teams.index') }}">Team</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit Team Member</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-lg-8 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="card-title mb-0">
                            <i data-feather="user-edit" class="me-2"></i>Edit Team Member: {{ $team->name }}
                        </h4>
                        <div>
                            <a href="{{ route('admin.teams.index') }}" class="btn btn-outline-secondary btn-sm me-2">
                                <i data-feather="arrow-left" class="me-1"></i>Back to Team
                            </a>
                            <button type="button" class="btn btn-outline-danger btn-sm" onclick="deleteTeamMember()">
                                <i data-feather="trash-2" class="me-1"></i>Delete
                            </button>
                        </div>
                    </div>

                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <div class="d-flex align-items-center">
                                <i data-feather="alert-triangle" class="me-2"></i>
                                <strong>Please fix the following errors:</strong>
                            </div>
                            <ul class="mb-0 mt-2">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('admin.teams.update', $team) }}" method="POST" enctype="multipart/form-data" id="teamForm">
                        @csrf
                        @method('PUT')

                        <!-- Basic Information -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="mb-0"><i data-feather="user" class="me-2"></i>Basic Information</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                            <input id="name" class="form-control @error('name') is-invalid @enderror"
                                                   name="name" type="text" value="{{ old('name', $team->name) }}"
                                                   placeholder="Enter full name" required>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="position" class="form-label">Position <span class="text-danger">*</span></label>
                                            <input id="position" class="form-control @error('position') is-invalid @enderror"
                                                   name="position" type="text" value="{{ old('position', $team->position) }}"
                                                   placeholder="e.g., Senior Developer, Designer" required>
                                            @error('position')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="email" class="form-label">Email Address</label>
                                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                                   name="email" id="email" value="{{ old('email', $team->email) }}"
                                                   placeholder="team@example.com">
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="phone" class="form-label">Phone Number</label>
                                            <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                                                   name="phone" id="phone" value="{{ old('phone', $team->phone) }}"
                                                   placeholder="+1 (555) 123-4567">
                                            @error('phone')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="bio" class="form-label">Bio/Description <span class="text-danger">*</span></label>
                                    <textarea class="form-control @error('bio') is-invalid @enderror"
                                              name="bio" id="bio" rows="4"
                                              placeholder="Brief description about the team member" required>{{ old('bio', $team->bio) }}</textarea>
                                    <div class="form-text">Keep it concise but informative</div>
                                    @error('bio')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Media & Settings -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="mb-0"><i data-feather="image" class="me-2"></i>Media & Settings</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="avatar" class="form-label">Profile Photo</label>

                                    <!-- Current Avatar Display -->
                                    @if($team->avatar)
                                        <div class="mb-3">
                                            <label class="form-label">Current Photo:</label>
                                            <div class="position-relative d-inline-block">
                                                <img src="{{ asset($team->avatar) }}" alt="Current Avatar"
                                                     class="img-thumbnail rounded-circle"
                                                     style="width: 150px; height: 150px; object-fit: cover;">
                                            </div>
                                        </div>
                                    @endif

                                    <input type="file" class="form-control @error('avatar') is-invalid @enderror"
                                           name="avatar" id="avatar" accept="image/*">
                                    <div class="form-text">Leave empty to keep current photo. Recommended size: 400x400px.</div>
                                    @error('avatar')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="order" class="form-label">Display Order</label>
                                            <input type="number" class="form-control @error('order') is-invalid @enderror"
                                                   name="order" id="order" value="{{ old('order', $team->order) }}" min="0">
                                            <div class="form-text">Lower numbers appear first</div>
                                            @error('order')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" name="is_featured" id="is_featured" value="1" {{ old('is_featured', $team->is_featured) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="is_featured">
                                                    <i data-feather="star" class="me-1"></i>Featured Member
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $team->is_active) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="is_active">
                                                    <i data-feather="check-circle" class="me-1"></i>Active Member
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Social Media -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="mb-0"><i data-feather="share-2" class="me-2"></i>Social Media & Links</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="linkedin_url" class="form-label">LinkedIn Profile</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i data-feather="linkedin"></i></span>
                                                <input type="url" class="form-control @error('linkedin_url') is-invalid @enderror"
                                                       name="linkedin_url" id="linkedin_url" value="{{ old('linkedin_url', $team->linkedin_url) }}"
                                                       placeholder="https://linkedin.com/in/username">
                                            </div>
                                            @error('linkedin_url')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="github_url" class="form-label">GitHub Profile</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i data-feather="github"></i></span>
                                                <input type="url" class="form-control @error('github_url') is-invalid @enderror"
                                                       name="github_url" id="github_url" value="{{ old('github_url', $team->github_url) }}"
                                                       placeholder="https://github.com/username">
                                            </div>
                                            @error('github_url')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="twitter_url" class="form-label">Twitter Profile</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i data-feather="twitter"></i></span>
                                                <input type="url" class="form-control @error('twitter_url') is-invalid @enderror"
                                                       name="twitter_url" id="twitter_url" value="{{ old('twitter_url', $team->twitter_url) }}"
                                                       placeholder="https://twitter.com/username">
                                            </div>
                                            @error('twitter_url')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="website_url" class="form-label">Portfolio Website</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i data-feather="globe"></i></span>
                                                <input type="url" class="form-control @error('website_url') is-invalid @enderror"
                                                       name="website_url" id="website_url" value="{{ old('website_url', $team->website_url) }}"
                                                       placeholder="https://portfolio.com">
                                            </div>
                                            @error('website_url')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.teams.index') }}" class="btn btn-outline-secondary">
                                <i data-feather="x" class="me-1"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary" onclick="return validateForm()">
                                <i data-feather="check-circle" class="me-1"></i>Update Team Member
                            </button>
                        </div>
                    </form>

                    <!-- Delete Form -->
                    <form id="deleteForm" action="{{ route('admin.teams.destroy', $team) }}" method="POST" style="display: none;">
                        @csrf
                        @method('DELETE')
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Form Preview -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0"><i data-feather="eye" class="me-2"></i>Live Preview</h6>
                </div>
                <div class="card-body">
                    <div id="form-preview">
                        <div class="text-center">
                            @if($team->avatar)
                                <img src="{{ asset($team->avatar) }}" alt="{{ $team->name }}" class="rounded-circle mb-3" style="width: 80px; height: 80px; object-fit: cover;">
                            @else
                                <div class="avatar-placeholder mb-3">
                                    <i data-feather="user" style="width: 80px; height: 80px; color: #6c757d;"></i>
                                </div>
                            @endif
                            <h6 class="fw-bold">{{ $team->name }}</h6>
                            <p class="text-muted small">{{ $team->position }}</p>
                            <p class="text-muted small">{{ Str::limit($team->bio, 100) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i data-feather="zap" class="me-2"></i>Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.teams.create') }}" class="btn btn-outline-primary btn-sm">
                            <i data-feather="plus" class="me-1"></i>Add New Member
                        </a>
                        <a href="{{ route('admin.teams.index') }}" class="btn btn-outline-info btn-sm">
                            <i data-feather="list" class="me-1"></i>View All Members
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
function validateForm() {
    const requiredFields = ['name', 'position', 'bio'];
    let isValid = true;

    requiredFields.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        const value = field.value.trim();

        if (!value) {
            field.classList.add('is-invalid');
            isValid = false;
        } else {
            field.classList.remove('is-invalid');
        }
    });

    if (!isValid) {
        alert('Please fill in all required fields.');
        return false;
    }

    return true;
}

function deleteTeamMember() {
    if (confirm('Are you sure you want to delete this team member? This action cannot be undone.')) {
        document.getElementById('deleteForm').submit();
    }
}

// Update preview on field changes
document.addEventListener('DOMContentLoaded', function() {
    ['name', 'position', 'bio'].forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field) {
            field.addEventListener('input', updatePreview);
        }
    });
});

function updatePreview() {
    const name = document.getElementById('name').value || '{{ $team->name }}';
    const position = document.getElementById('position').value || '{{ $team->position }}';
    const bio = document.getElementById('bio').value || '{{ $team->bio }}';
    const currentAvatar = '{{ $team->avatar ? asset($team->avatar) : "" }}';

    const preview = document.getElementById('form-preview');

    if (currentAvatar) {
        preview.innerHTML = `
            <div class="text-center">
                <img src="${currentAvatar}" alt="${name}" class="rounded-circle mb-3" style="width: 80px; height: 80px; object-fit: cover;">
                <h6 class="fw-bold">${name}</h6>
                <p class="text-muted small">${position}</p>
                <p class="text-muted small">${bio.substring(0, 100)}${bio.length > 100 ? '...' : ''}</p>
            </div>
        `;
    } else {
        preview.innerHTML = `
            <div class="text-center">
                <div class="avatar-placeholder mb-3">
                    <i data-feather="user" style="width: 80px; height: 80px; color: #6c757d;"></i>
                </div>
                <h6 class="fw-bold">${name}</h6>
                <p class="text-muted small">${position}</p>
                <p class="text-muted small">${bio.substring(0, 100)}${bio.length > 100 ? '...' : ''}</p>
            </div>
        `;
    }
}
</script>
@endsection

@section('css')
<style>
.avatar-placeholder {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: #f8f9fa;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
}

.invalid-feedback {
    display: block;
}
</style>
@endsection
