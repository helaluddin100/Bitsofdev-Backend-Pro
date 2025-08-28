@extends('master.master')

@section('content')
    <div class="page-content">
        <nav class="page-breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.teams.index') }}">Team</a></li>
                <li class="breadcrumb-item active" aria-current="page">Add Team Member</li>
            </ol>
        </nav>

        <div class="row">
            <div class="col-lg-8 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="card-title mb-0">
                                <i data-feather="user-plus" class="me-2"></i>Add New Team Member
                            </h4>
                            <a href="{{ route('admin.teams.index') }}" class="btn btn-outline-secondary btn-sm">
                                <i data-feather="arrow-left" class="me-1"></i>Back to Team
                            </a>
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

                        <form action="{{ route('admin.teams.store') }}" method="POST" enctype="multipart/form-data" id="teamForm">
                            @csrf

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
                                                       name="name" type="text" value="{{ old('name') }}"
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
                                                       name="position" type="text" value="{{ old('position') }}"
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
                                                       name="email" id="email" value="{{ old('email') }}"
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
                                                       name="phone" id="phone" value="{{ old('phone') }}"
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
                                                  placeholder="Brief description about the team member" required>{{ old('bio') }}</textarea>
                                        <div class="form-text">Keep it concise but informative</div>
                                        @error('bio')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Professional Details -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="mb-0"><i data-feather="briefcase" class="me-2"></i>Professional Details</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="department" class="form-label">Department</label>
                                                <select class="form-select @error('department') is-invalid @enderror" name="department" id="department">
                                                    <option value="">Select Department</option>
                                                    <option value="development" {{ old('department') == 'development' ? 'selected' : '' }}>Development</option>
                                                    <option value="design" {{ old('department') == 'design' ? 'selected' : '' }}>Design</option>
                                                    <option value="marketing" {{ old('department') == 'marketing' ? 'selected' : '' }}>Marketing</option>
                                                    <option value="sales" {{ old('department') == 'sales' ? 'selected' : '' }}>Sales</option>
                                                    <option value="support" {{ old('department') == 'support' ? 'selected' : '' }}>Support</option>
                                                    <option value="management" {{ old('department') == 'management' ? 'selected' : '' }}>Management</option>
                                                </select>
                                                @error('department')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="experience_years" class="form-label">Years of Experience</label>
                                                <select class="form-select @error('experience_years') is-invalid @enderror" name="experience_years" id="experience_years">
                                                    <option value="">Select Experience</option>
                                                    <option value="0-1" {{ old('experience_years') == '0-1' ? 'selected' : '' }}>0-1 years</option>
                                                    <option value="1-3" {{ old('experience_years') == '1-3' ? 'selected' : '' }}>1-3 years</option>
                                                    <option value="3-5" {{ old('experience_years') == '3-5' ? 'selected' : '' }}>3-5 years</option>
                                                    <option value="5-10" {{ old('experience_years') == '5-10' ? 'selected' : '' }}>5-10 years</option>
                                                    <option value="10+" {{ old('experience_years') == '10+' ? 'selected' : '' }}>10+ years</option>
                                                </select>
                                                @error('experience_years')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="skills" class="form-label">Skills & Expertise</label>
                                        <input type="text" class="form-control @error('skills') is-invalid @enderror"
                                               name="skills" id="skills" value="{{ old('skills') }}"
                                               placeholder="e.g., Laravel, React, UI/UX, Project Management">
                                        <div class="form-text">Separate multiple skills with commas</div>
                                        @error('skills')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="education" class="form-label">Education</label>
                                        <textarea class="form-control @error('education') is-invalid @enderror"
                                                  name="education" id="education" rows="3"
                                                  placeholder="Educational background and certifications">{{ old('education') }}</textarea>
                                        @error('education')
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
                                        <div class="drop-zone @error('avatar') is-invalid @enderror" id="dropZone">
                                            <div class="drop-zone-content">
                                                <i data-feather="user" class="drop-zone-icon"></i>
                                                <h6 class="drop-zone-title">Drag & Drop Photo Here</h6>
                                                <p class="drop-zone-text">or click to browse</p>
                                                <input type="file" class="drop-zone-input" name="avatar" id="avatar"
                                                       accept="image/*" onchange="handleImageSelect(this)">
                                            </div>
                                        </div>
                                        <div id="image-preview" class="mt-3" style="display: none;">
                                            <div class="position-relative d-inline-block">
                                                <img id="preview-img" src="" alt="Preview" class="img-thumbnail rounded-circle" style="width: 150px; height: 150px; object-fit: cover;">
                                                <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1"
                                                        onclick="removeImage()">
                                                    <i data-feather="x"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="form-text">Recommended size: 400x400px. Will be converted to WebP format.</div>
                                        @error('avatar')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="order" class="form-label">Display Order</label>
                                                <input type="number" class="form-control @error('order') is-invalid @enderror"
                                                       name="order" id="order" value="{{ old('order', 0) }}" min="0">
                                                <div class="form-text">Lower numbers appear first</div>
                                                @error('order')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" name="is_featured" id="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="is_featured">
                                                        <i data-feather="star" class="me-1"></i>Featured Member
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', '1') ? 'checked' : '' }}>
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
                                                           name="linkedin_url" id="linkedin_url" value="{{ old('linkedin_url') }}"
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
                                                           name="github_url" id="github_url" value="{{ old('github_url') }}"
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
                                                           name="twitter_url" id="twitter_url" value="{{ old('twitter_url') }}"
                                                           placeholder="https://twitter.com/username">
                                                </div>
                                                @error('twitter_url')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="portfolio_url" class="form-label">Portfolio Website</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i data-feather="globe"></i></span>
                                                    <input type="url" class="form-control @error('portfolio_url') is-invalid @enderror"
                                                           name="portfolio_url" id="portfolio_url" value="{{ old('portfolio_url') }}"
                                                           placeholder="https://portfolio.com">
                                                </div>
                                                @error('portfolio_url')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="d-flex justify-content-between">
                                <button type="button" class="btn btn-outline-secondary" onclick="resetForm()">
                                    <i data-feather="refresh-cw" class="me-1"></i>Reset Form
                                </button>
                                <div>
                                    <button type="button" class="btn btn-outline-info me-2" onclick="saveDraft()">
                                        <i data-feather="save" class="me-1"></i>Save Draft
                                    </button>
                                    <button type="submit" class="btn btn-primary" onclick="return validateForm()">
                                        <i data-feather="check-circle" class="me-1"></i>Add Team Member
                                    </button>
                                </div>
                            </div>
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
                                <div class="avatar-placeholder mb-3">
                                    <i data-feather="user" style="width: 80px; height: 80px; color: #6c757d;"></i>
                                </div>
                                <h6 class="fw-bold">Team Member Name</h6>
                                <p class="text-muted small">Position</p>
                                <p class="text-muted small">Bio description...</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Progress -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0"><i data-feather="bar-chart-2" class="me-2"></i>Form Progress</h6>
                    </div>
                    <div class="card-body">
                        <div class="progress mb-3" style="height: 8px;">
                            <div class="progress-bar" id="form-progress" role="progressbar" style="width: 0%"></div>
                        </div>
                        <small class="text-muted">Complete all required fields to submit</small>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0"><i data-feather="zap" class="me-2"></i>Quick Actions</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-outline-success btn-sm" onclick="duplicateFromLast()">
                                <i data-feather="copy" class="me-1"></i>Duplicate Last Member
                            </button>
                            <button type="button" class="btn btn-outline-warning btn-sm" onclick="generateBio()">
                                <i data-feather="edit-3" class="me-1"></i>Generate Bio
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
<style>
.drop-zone {
    border: 2px dashed #dee2e6;
    border-radius: 8px;
    padding: 40px 20px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
    background: #f8f9fa;
}

.drop-zone:hover {
    border-color: #007bff;
    background: #e3f2fd;
}

.drop-zone.dragover {
    border-color: #28a745;
    background: #d4edda;
}

.drop-zone-content {
    pointer-events: none;
}

.drop-zone-input {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    opacity: 0;
    cursor: pointer;
}

.drop-zone-icon {
    width: 48px;
    height: 48px;
    color: #6c757d;
    margin-bottom: 16px;
}

.drop-zone-title {
    font-size: 18px;
    font-weight: 600;
    margin-bottom: 8px;
    color: #495057;
}

.drop-zone-text {
    color: #6c757d;
    margin-bottom: 0;
}

.form-progress {
    transition: width 0.3s ease;
}

.invalid-feedback {
    display: block;
}

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
</style>
@endsection

@section('js')
<script>
// Global variables
let formData = {};
let isFormValid = false;

// Initialize when page loads
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, setting up team form...');
    setupForm();
    setupDragAndDrop();
    setupAutoSave();
    updateFormProgress();
    console.log('Team form setup complete');
});

// Form setup
function setupForm() {
    const form = document.getElementById('teamForm');

    // Form submit listener
    form.addEventListener('submit', function(e) {
        console.log('Form submit event triggered');
        if (!validateForm()) {
            e.preventDefault();
            console.log('Form validation failed, preventing submit');
            showToast('Please fix the validation errors before submitting.', 'error');
        } else {
            console.log('Form validation passed, allowing submit');
            showToast('Adding team member...', 'info');
        }
    });

    // Update preview on field changes
    ['name', 'position', 'bio'].forEach(fieldId => {
        document.getElementById(fieldId).addEventListener('input', function() {
            updatePreview();
            updateFormProgress();
        });
    });
}

// Drag and drop functionality
function setupDragAndDrop() {
    const dropZone = document.getElementById('dropZone');

    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, preventDefaults, false);
    });

    ['dragenter', 'dragover'].forEach(eventName => {
        dropZone.addEventListener(eventName, highlight, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, unhighlight, false);
    });

    dropZone.addEventListener('drop', handleDrop, false);
    dropZone.addEventListener('click', () => document.getElementById('avatar').click());
}

function preventDefaults(e) {
    e.preventDefault();
    e.stopPropagation();
}

function highlight(e) {
    dropZone.classList.add('dragover');
}

function unhighlight(e) {
    dropZone.classList.remove('dragover');
}

function handleDrop(e) {
    const dt = e.dataTransfer;
    const files = dt.files;
    handleFiles(files);
}

function handleFiles(files) {
    if (files.length > 0) {
        const file = files[0];
        if (file.type.startsWith('image/')) {
            previewImage(file);
        } else {
            showToast('Please select an image file.', 'error');
        }
    }
}

function handleImageSelect(input) {
    if (input.files && input.files[0]) {
        previewImage(input.files[0]);
    }
}

function previewImage(file) {
    const reader = new FileReader();
    reader.onload = function(e) {
        document.getElementById('preview-img').src = e.target.result;
        document.getElementById('image-preview').style.display = 'block';
        document.getElementById('dropZone').style.display = 'none';
        updatePreview();
    };
    reader.readAsDataURL(file);
}

function removeImage() {
    document.getElementById('avatar').value = '';
    document.getElementById('image-preview').style.display = 'none';
    document.getElementById('dropZone').style.display = 'block';
    updatePreview();
}

// Form validation
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

    isFormValid = isValid;
    updateFormProgress();
    return isValid;
}

// Form progress
function updateFormProgress() {
    const requiredFields = ['name', 'position', 'bio'];
    let completed = 0;

    requiredFields.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field.value.trim()) {
            completed++;
        }
    });

    const percentage = (completed / requiredFields.length) * 100;
    const progressBar = document.getElementById('form-progress');
    progressBar.style.width = percentage + '%';

    if (percentage === 100) {
        progressBar.className = 'progress-bar bg-success';
    } else if (percentage >= 50) {
        progressBar.className = 'progress-bar bg-warning';
    } else {
        progressBar.className = 'progress-bar bg-danger';
    }
}

// Preview functionality
function updatePreview() {
    const name = document.getElementById('name').value || 'Team Member Name';
    const position = document.getElementById('position').value || 'Position';
    const bio = document.getElementById('bio').value || 'Bio description...';
    const avatar = document.getElementById('preview-img');

    const preview = document.getElementById('form-preview');

    if (avatar && avatar.src) {
        preview.innerHTML = `
            <div class="text-center">
                <img src="${avatar.src}" alt="${name}" class="rounded-circle mb-3" style="width: 80px; height: 80px; object-fit: cover;">
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

// Utility functions
function resetForm() {
    if (confirm('Are you sure you want to reset the form? All data will be lost.')) {
        document.getElementById('teamForm').reset();
        document.getElementById('image-preview').style.display = 'none';
        document.getElementById('dropZone').style.display = 'block';
        updateFormProgress();
        updatePreview();
        showToast('Form has been reset.', 'info');
    }
}

function saveDraft() {
    // Save form data to localStorage
    const formData = new FormData(document.getElementById('teamForm'));
    const data = {};
    for (let [key, value] of formData.entries()) {
        data[key] = value;
    }
    localStorage.setItem('teamDraft', JSON.stringify(data));
    showToast('Draft saved successfully!', 'success');
}

function duplicateFromLast() {
    // Load last saved team member data
    const lastMember = localStorage.getItem('lastTeamMember');
    if (lastMember) {
        const data = JSON.parse(lastMember);
        Object.keys(data).forEach(key => {
            const field = document.getElementById(key);
            if (field) {
                field.value = data[key];
            }
        });
        updateFormProgress();
        updatePreview();
        showToast('Last team member data loaded!', 'success');
    } else {
        showToast('No previous team member data found.', 'info');
    }
}

function generateBio() {
    const name = document.getElementById('name').value;
    const position = document.getElementById('position').value;
    const skills = document.getElementById('skills').value;

    if (!name || !position) {
        showToast('Please fill in name and position first.', 'warning');
        return;
    }

    // Generate bio based on position and skills
    let bio = `${name} is a ${position} with expertise in `;

    if (skills) {
        const skillList = skills.split(',').map(s => s.trim());
        if (skillList.length > 0) {
            bio += skillList.slice(0, 3).join(', ');
            if (skillList.length > 3) {
                bio += ` and more.`;
            } else {
                bio += `.`;
            }
        }
    } else {
        bio += `their field.`;
    }

    bio += ` Passionate about delivering high-quality solutions and staying up-to-date with the latest industry trends.`;

    document.getElementById('bio').value = bio;
    updatePreview();
    showToast('Bio generated successfully!', 'success');
}

// Auto-save functionality
let autoSaveTimer;
function setupAutoSave() {
    const fields = ['name', 'position', 'bio', 'skills', 'education'];
    fields.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field) {
            field.addEventListener('input', () => {
                clearTimeout(autoSaveTimer);
                autoSaveTimer = setTimeout(saveDraft, 3000);
            });
        }
    });
}

// Toast notifications
function showToast(message, type = 'info') {
    // Create toast element
    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white bg-${type === 'error' ? 'danger' : type === 'success' ? 'success' : type === 'warning' ? 'warning' : 'info'} border-0`;
    toast.setAttribute('role', 'alert');
    toast.setAttribute('aria-live', 'assertive');
    toast.setAttribute('aria-atomic', 'true');

    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                ${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;

    // Add to page
    const container = document.createElement('div');
    container.className = 'toast-container position-fixed top-0 end-0 p-3';
    container.style.zIndex = '9999';
    container.appendChild(toast);
    document.body.appendChild(container);

    // Show toast
    const bsToast = new bootstrap.Toast(toast);
    bsToast.show();

    // Remove after hidden
    toast.addEventListener('hidden.bs.toast', () => {
        document.body.removeChild(container);
    });
}

// Load draft on page load
window.addEventListener('load', function() {
    const draft = localStorage.getItem('teamDraft');
    if (draft) {
        const data = JSON.parse(draft);
        Object.keys(data).forEach(key => {
            const field = document.getElementById(key);
            if (field && data[key]) {
                field.value = data[key];
            }
        });
        updateFormProgress();
        updatePreview();
    }
});
</script>
@endsection
