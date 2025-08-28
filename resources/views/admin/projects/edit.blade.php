@extends('master.master')

@section('content')
    <div class="page-content">
        <nav class="page-breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.projects.index') }}">Projects</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edit Project</li>
            </ol>
        </nav>

        <div class="row">
            <div class="col-lg-8 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="card-title mb-0">
                                <i data-feather="edit-3" class="me-2"></i>Edit Project: {{ $project->title }}
                            </h4>
                            <div>
                                <a href="{{ route('admin.projects.show', $project->id) }}" class="btn btn-outline-info btn-sm me-2" target="_blank">
                                    <i data-feather="eye" class="me-1"></i>View Project
                                </a>
                                <a href="{{ route('admin.projects.index') }}" class="btn btn-outline-secondary btn-sm">
                                    <i data-feather="arrow-left" class="me-1"></i>Back to Projects
                                </a>
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

                        <form action="{{ route('admin.projects.update', $project->id) }}" method="POST" enctype="multipart/form-data" id="projectForm">
                            @csrf
                            @method('PUT')

                            <!-- Basic Information -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="mb-0"><i data-feather="info" class="me-2"></i>Basic Information</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="mb-3">
                                                <label for="title" class="form-label">Project Title <span class="text-danger">*</span></label>
                                                <input id="title" class="form-control @error('title') is-invalid @enderror"
                                                       name="title" type="text" value="{{ old('title', $project->title) }}"
                                                       placeholder="Enter project title" required>
                                                @error('title')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="client" class="form-label">Client <span class="text-danger">*</span></label>
                                                <input id="client" class="form-control @error('client') is-invalid @enderror"
                                                       name="client" type="text" value="{{ old('client', $project->client) }}"
                                                       placeholder="Client name" required>
                                                @error('client')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="excerpt" class="form-label">Project Summary <span class="text-danger">*</span></label>
                                        <textarea class="form-control @error('excerpt') is-invalid @enderror"
                                                  name="excerpt" id="excerpt" rows="3"
                                                  placeholder="Brief description of the project" required>{{ old('excerpt', $project->excerpt) }}</textarea>
                                        <div class="form-text">Keep it concise but informative</div>
                                        @error('excerpt')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                                <select class="form-select @error('status') is-invalid @enderror" name="status" id="status" required>
                                                    <option value="">Select Status</option>
                                                    <option value="planning" {{ old('status', $project->status) == 'planning' ? 'selected' : '' }}>Planning</option>
                                                    <option value="in-progress" {{ old('status', $project->status) == 'in-progress' ? 'selected' : '' }}>In Progress</option>
                                                    <option value="review" {{ old('status', $project->status) == 'review' ? 'selected' : '' }}>Under Review</option>
                                                    <option value="completed" {{ old('status', $project->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                                                    <option value="on-hold" {{ old('status', $project->status) == 'on-hold' ? 'selected' : '' }}>On Hold</option>
                                                </select>
                                                @error('status')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="priority" class="form-label">Priority <span class="text-danger">*</span></label>
                                                <select class="form-select @error('priority') is-invalid @enderror" name="priority" id="priority" required>
                                                    <option value="">Select Priority</option>
                                                    <option value="low" {{ old('priority', $project->priority) == 'low' ? 'selected' : '' }}>Low</option>
                                                    <option value="medium" {{ old('priority', $project->priority) == 'medium' ? 'selected' : '' }}>Medium</option>
                                                    <option value="high" {{ old('priority', $project->priority) == 'high' ? 'selected' : '' }}>High</option>
                                                    <option value="urgent" {{ old('priority', $project->priority) == 'urgent' ? 'selected' : '' }}>Urgent</option>
                                                </select>
                                                @error('priority')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Project Details -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="mb-0"><i data-feather="file-text" class="me-2"></i>Project Details</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="content" class="form-label">Detailed Description <span class="text-danger">*</span></label>
                                        <textarea class="form-control @error('content') is-invalid @enderror"
                                                  name="content" id="content" rows="8"
                                                  placeholder="Detailed project description, requirements, and specifications" required>{{ old('content', $project->content) }}</textarea>
                                        @error('content')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="start_date" class="form-label">Start Date</label>
                                                <input type="date" class="form-control @error('start_date') is-invalid @enderror"
                                                       name="start_date" id="start_date" value="{{ old('start_date', $project->start_date ? $project->start_date->format('Y-m-d') : '') }}">
                                                @error('start_date')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="end_date" class="form-label">End Date</label>
                                                <input type="date" class="form-control @error('end_date') is-invalid @enderror"
                                                       name="end_date" id="end_date" value="{{ old('end_date', $project->end_date ? $project->end_date->format('Y-m-d') : '') }}">
                                                @error('end_date')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="technologies" class="form-label">Technologies Used</label>
                                        <input type="text" class="form-control @error('technologies') is-invalid @enderror"
                                               name="technologies" id="technologies" value="{{ old('technologies', $project->technologies) }}"
                                               placeholder="e.g., Laravel, React, MySQL, AWS">
                                        <div class="form-text">Separate multiple technologies with commas</div>
                                        @error('technologies')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="project_url" class="form-label">Project URL</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i data-feather="link"></i></span>
                                            <input type="url" class="form-control @error('project_url') is-invalid @enderror"
                                                   name="project_url" id="project_url" value="{{ old('project_url', $project->project_url) }}"
                                                   placeholder="https://example.com">
                                        </div>
                                        @error('project_url')
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
                                        <label for="featured_image" class="form-label">Featured Image</label>

                                        @if($project->featured_image)
                                            <div class="mb-3">
                                                <label class="form-label">Current Image</label>
                                                <div class="position-relative d-inline-block">
                                                    <img src="{{ asset($project->featured_image) }}" alt="Current Image"
                                                         class="img-thumbnail" style="max-width: 300px;">
                                                    <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1"
                                                            onclick="removeCurrentImage()">
                                                        <i data-feather="trash-2"></i>
                                                    </button>
                                                </div>
                                                <input type="hidden" name="remove_current_image" id="remove_current_image" value="0">
                                            </div>
                                        @endif

                                        <div class="drop-zone @error('featured_image') is-invalid @enderror" id="dropZone"
                                             style="{{ $project->featured_image ? 'display: none;' : '' }}">
                                            <div class="drop-zone-content">
                                                <i data-feather="upload-cloud" class="drop-zone-icon"></i>
                                                <h6 class="drop-zone-title">Drag & Drop New Image Here</h6>
                                                <p class="drop-zone-text">or click to browse</p>
                                                <input type="file" class="drop-zone-input" name="featured_image" id="featured_image"
                                                       accept="image/*" onchange="handleImageSelect(this)">
                                            </div>
                                        </div>

                                        <div id="image-preview" class="mt-3" style="display: none;">
                                            <div class="position-relative d-inline-block">
                                                <img id="preview-img" src="" alt="Preview" class="img-thumbnail" style="max-width: 300px;">
                                                <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1"
                                                        onclick="removeNewImage()">
                                                    <i data-feather="x"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <div class="form-text">Recommended size: 1200x800px. Will be converted to WebP format.</div>
                                        @error('featured_image')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" name="is_featured" id="is_featured"
                                                           value="1" {{ old('is_featured', $project->is_featured) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="is_featured">
                                                        <i data-feather="star" class="me-1"></i>Featured Project
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active"
                                                           value="1" {{ old('is_active', $project->is_active) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="is_active">
                                                        <i data-feather="check-circle" class="me-1"></i>Active Project
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- SEO & Meta -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="mb-0"><i data-feather="search" class="me-2"></i>SEO & Meta Information</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="meta_title" class="form-label">Meta Title</label>
                                        <input type="text" class="form-control @error('meta_title') is-invalid @enderror"
                                               name="meta_title" id="meta_title" value="{{ old('meta_title', $project->meta_title) }}"
                                               placeholder="SEO title for search engines">
                                        <div class="form-text">Keep it under 60 characters for optimal SEO</div>
                                        @error('meta_title')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="meta_description" class="form-label">Meta Description</label>
                                        <textarea class="form-control @error('meta_description') is-invalid @enderror"
                                                  name="meta_description" id="meta_description" rows="3"
                                                  placeholder="SEO description for search engines">{{ old('meta_description', $project->meta_description) }}</textarea>
                                        <div class="form-text">Keep it between 150-160 characters for optimal SEO</div>
                                        @error('meta_description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="slug" class="form-label">URL Slug</label>
                                        <div class="input-group">
                                            <span class="input-group-text">{{ url('/projects/') }}</span>
                                            <input type="text" class="form-control @error('slug') is-invalid @enderror"
                                                   name="slug" id="slug" value="{{ old('slug', $project->slug) }}"
                                                   placeholder="auto-generated-slug">
                                        </div>
                                        <div class="form-text">Leave empty to auto-generate from title</div>
                                        @error('slug')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="d-flex justify-content-between">
                                <button type="button" class="btn btn-outline-secondary" onclick="resetForm()">
                                    <i data-feather="refresh-cw" class="me-1"></i>Reset Changes
                                </button>
                                <div>
                                    <button type="button" class="btn btn-outline-info me-2" onclick="saveDraft()">
                                        <i data-feather="save" class="me-1"></i>Save Draft
                                    </button>
                                    <button type="submit" class="btn btn-primary" onclick="return validateForm()">
                                        <i data-feather="check-circle" class="me-1"></i>Update Project
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Project Info -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0"><i data-feather="info" class="me-2"></i>Project Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <small class="text-muted">Created</small>
                            <div class="fw-bold">{{ $project->created_at->format('M d, Y H:i') }}</div>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted">Last Updated</small>
                            <div class="fw-bold">{{ $project->updated_at->format('M d, Y H:i') }}</div>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted">Status</small>
                            <div>
                                <span class="badge bg-{{ $project->status === 'completed' ? 'success' : ($project->status === 'in-progress' ? 'primary' : 'secondary') }}">
                                    {{ ucfirst(str_replace('-', ' ', $project->status)) }}
                                </span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted">Priority</small>
                            <div>
                                <span class="badge bg-{{ $project->priority === 'urgent' ? 'danger' : ($project->priority === 'high' ? 'warning' : 'info') }}">
                                    {{ ucfirst($project->priority) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Preview -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0"><i data-feather="eye" class="me-2"></i>Live Preview</h6>
                    </div>
                    <div class="card-body">
                        <div id="form-preview">
                            <div class="border rounded p-3">
                                <h6 class="fw-bold">{{ $project->title }}</h6>
                                <p class="text-muted small mb-2">{{ $project->excerpt }}</p>
                                <div class="text-muted small" style="max-height: 100px; overflow: hidden;">
                                    {{ Str::limit(strip_tags($project->content), 200) }}
                                </div>
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
                            <div class="progress-bar bg-success" id="form-progress" role="progressbar" style="width: 100%"></div>
                        </div>
                        <small class="text-muted">All required fields are completed</small>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0"><i data-feather="zap" class="me-2"></i>Quick Actions</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-outline-success btn-sm" onclick="duplicateProject()">
                                <i data-feather="copy" class="me-1"></i>Duplicate Project
                            </button>
                            <button type="button" class="btn btn-outline-warning btn-sm" onclick="generateSeo()">
                                <i data-feather="search" class="me-1"></i>Generate SEO Content
                            </button>
                            <button type="button" class="btn btn-outline-danger btn-sm" onclick="deleteProject()">
                                <i data-feather="trash-2" class="me-1"></i>Delete Project
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
</style>
@endsection

@section('js')
<script>
// Global variables
let originalFormData = {};
let isFormValid = true;

// Initialize when page loads
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, setting up project edit form...');
    setupForm();
    setupDragAndDrop();
    setupAutoSave();
    captureOriginalData();
    updateFormProgress();
    console.log('Project edit form setup complete');
});

// Capture original form data
function captureOriginalData() {
    const form = document.getElementById('projectForm');
    const formData = new FormData(form);
    for (let [key, value] of formData.entries()) {
        originalFormData[key] = value;
    }
}

// Form setup
function setupForm() {
    const form = document.getElementById('projectForm');

    // Form submit listener
    form.addEventListener('submit', function(e) {
        console.log('Form submit event triggered');
        if (!validateForm()) {
            e.preventDefault();
            console.log('Form validation failed, preventing submit');
            showToast('Please fix the validation errors before submitting.', 'error');
        } else {
            console.log('Form validation passed, allowing submit');
            showToast('Updating project...', 'info');
        }
    });

    // Auto-generate slug from title
    document.getElementById('title').addEventListener('input', function() {
        const title = this.value;
        const slug = document.getElementById('slug');
        if (!slug.value || slug.dataset.autoGenerated === 'true') {
            slug.value = generateSlug(title);
            slug.dataset.autoGenerated = 'true';
        }
        updateFormProgress();
        updatePreview();
    });

    // Update preview on content change
    ['excerpt', 'content'].forEach(fieldId => {
        document.getElementById(fieldId).addEventListener('input', function() {
            updatePreview();
            updateFormProgress();
        });
    });

    // Date validation
    document.getElementById('start_date').addEventListener('change', validateDates);
    document.getElementById('end_date').addEventListener('change', validateDates);
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
    dropZone.addEventListener('click', () => document.getElementById('featured_image').click());
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
    };
    reader.readAsDataURL(file);
}

function removeNewImage() {
    document.getElementById('featured_image').value = '';
    document.getElementById('image-preview').style.display = 'none';
    document.getElementById('dropZone').style.display = 'block';
}

function removeCurrentImage() {
    if (confirm('Are you sure you want to remove the current image?')) {
        document.getElementById('remove_current_image').value = '1';
        document.querySelector('.mb-3:has(img)').style.display = 'none';
        document.getElementById('dropZone').style.display = 'block';
        showToast('Current image will be removed on save.', 'info');
    }
}

// Form validation
function validateForm() {
    const requiredFields = ['title', 'client', 'excerpt', 'content', 'status', 'priority'];
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

    // Validate dates
    if (!validateDates()) {
        isValid = false;
    }

    isFormValid = isValid;
    updateFormProgress();
    return isValid;
}

function validateDates() {
    const startDate = document.getElementById('start_date').value;
    const endDate = document.getElementById('end_date').value;

    if (startDate && endDate && startDate > endDate) {
        document.getElementById('end_date').classList.add('is-invalid');
        showToast('End date cannot be before start date.', 'error');
        return false;
    } else {
        document.getElementById('end_date').classList.remove('is-invalid');
        return true;
    }
}

// Form progress
function updateFormProgress() {
    const requiredFields = ['title', 'client', 'excerpt', 'content', 'status', 'priority'];
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
    const title = document.getElementById('title').value || 'Project Title';
    const excerpt = document.getElementById('excerpt').value || 'Project description...';
    const content = document.getElementById('content').value || 'Detailed project information...';

    const preview = document.getElementById('form-preview');
    preview.innerHTML = `
        <div class="border rounded p-3">
            <h6 class="fw-bold">${title}</h6>
            <p class="text-muted small mb-2">${excerpt}</p>
            <div class="text-muted small" style="max-height: 100px; overflow: hidden;">
                ${content.substring(0, 200)}${content.length > 200 ? '...' : ''}
            </div>
        </div>
    `;
}

// Utility functions
function generateSlug(text) {
    return text.toLowerCase()
        .replace(/[^a-z0-9 -]/g, '')
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-')
        .trim('-');
}

function resetForm() {
    if (confirm('Are you sure you want to reset all changes? All modifications will be lost.')) {
        // Reset to original values
        Object.keys(originalFormData).forEach(key => {
            const field = document.getElementById(key);
            if (field) {
                field.value = originalFormData[key];
                field.classList.remove('is-invalid');
            }
        });

        // Reset checkboxes
        document.getElementById('is_featured').checked = originalFormData.is_featured === '1';
        document.getElementById('is_active').checked = originalFormData.is_active === '1';

        // Reset image handling
        document.getElementById('remove_current_image').value = '0';
        document.querySelector('.mb-3:has(img)').style.display = 'block';
        document.getElementById('dropZone').style.display = 'none';
        document.getElementById('image-preview').style.display = 'none';

        updateFormProgress();
        updatePreview();
        showToast('Form has been reset to original values.', 'info');
    }
}

function saveDraft() {
    // Save current form data to localStorage
    const formData = new FormData(document.getElementById('projectForm'));
    const data = {};
    for (let [key, value] of formData.entries()) {
        data[key] = value;
    }
    localStorage.setItem('projectEditDraft', JSON.stringify(data));
    showToast('Draft saved successfully!', 'success');
}

function duplicateProject() {
    if (confirm('This will create a new project with the same data. Continue?')) {
        // Redirect to create page with pre-filled data
        const formData = new FormData(document.getElementById('projectForm'));
        const data = {};
        for (let [key, value] of formData.entries()) {
            data[key] = value;
        }

        // Store data for create page
        localStorage.setItem('duplicatedProject', JSON.stringify(data));

        // Redirect to create page
        window.location.href = '{{ route("admin.projects.create") }}';
    }
}

function generateSeo() {
    const title = document.getElementById('title').value;
    const content = document.getElementById('content').value;

    if (!title || !content) {
        showToast('Please fill in title and content first.', 'warning');
        return;
    }

    // Generate SEO content
    const metaTitle = title.length > 60 ? title.substring(0, 57) + '...' : title;
    const metaDescription = content.replace(/<[^>]*>/g, '').substring(0, 160);
    const slug = generateSlug(title);

    document.getElementById('meta_title').value = metaTitle;
    document.getElementById('meta_description').value = metaDescription;
    document.getElementById('slug').value = slug;
    document.getElementById('slug').dataset.autoGenerated = 'false';

    showToast('SEO content generated successfully!', 'success');
}

function deleteProject() {
    if (confirm('Are you sure you want to delete this project? This action cannot be undone.')) {
        // Create a form to submit DELETE request
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.projects.destroy", $project->id) }}';

        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';

        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';

        form.appendChild(csrfToken);
        form.appendChild(methodField);
        document.body.appendChild(form);
        form.submit();
    }
}

// Auto-save functionality
let autoSaveTimer;
function setupAutoSave() {
    const fields = ['title', 'client', 'excerpt', 'content', 'technologies'];
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
    const draft = localStorage.getItem('projectEditDraft');
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
