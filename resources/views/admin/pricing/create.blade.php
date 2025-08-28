@extends('master.master')

@section('content')
    <div class="page-content">
        <nav class="page-breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.pricing.index') }}">Pricing</a></li>
                <li class="breadcrumb-item active" aria-current="page">Create Pricing Plan</li>
            </ol>
        </nav>

        <div class="row">
            <div class="col-lg-8 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="card-title mb-0">
                                <i data-feather="dollar-sign" class="me-2"></i>Create New Pricing Plan
                            </h4>
                            <a href="{{ route('admin.pricing.index') }}" class="btn btn-outline-secondary btn-sm">
                                <i data-feather="arrow-left" class="me-1"></i>Back to Pricing
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

                        <form action="{{ route('admin.pricing.store') }}" method="POST" enctype="multipart/form-data" id="pricingForm">
                            @csrf

                            <!-- Basic Information -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="mb-0"><i data-feather="info" class="me-2"></i>Basic Information</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="mb-3">
                                                <label for="name" class="form-label">Plan Name <span class="text-danger">*</span></label>
                                                <input id="name" class="form-control @error('name') is-invalid @enderror"
                                                       name="name" type="text" value="{{ old('name') }}"
                                                       placeholder="e.g., Basic Plan, Pro Plan, Enterprise" required>
                                                @error('name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="slug" class="form-label">URL Slug</label>
                                                <input type="text" class="form-control @error('slug') is-invalid @enderror"
                                                       name="slug" id="slug" value="{{ old('slug') }}"
                                                       placeholder="auto-generated">
                                                <div class="form-text">Leave empty to auto-generate</div>
                                                @error('slug')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="description" class="form-label">Plan Description <span class="text-danger">*</span></label>
                                        <textarea class="form-control @error('description') is-invalid @enderror"
                                                  name="description" id="description" rows="3"
                                                  placeholder="Brief description of what this plan offers" required>{{ old('description') }}</textarea>
                                        @error('description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="price" class="form-label">Price <span class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <span class="input-group-text">$</span>
                                                    <input type="number" class="form-control @error('price') is-invalid @enderror"
                                                           name="price" id="price" value="{{ old('price') }}"
                                                           placeholder="0.00" step="0.01" min="0" required>
                                                </div>
                                                @error('price')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="billing_cycle" class="form-label">Billing Cycle <span class="text-danger">*</span></label>
                                                <select class="form-select @error('billing_cycle') is-invalid @enderror" name="billing_cycle" id="billing_cycle" required>
                                                    <option value="">Select Billing Cycle</option>
                                                    <option value="monthly" {{ old('billing_cycle') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                                    <option value="quarterly" {{ old('billing_cycle') == 'quarterly' ? 'selected' : '' }}>Quarterly</option>
                                                    <option value="yearly" {{ old('billing_cycle') == 'yearly' ? 'selected' : '' }}>Yearly</option>
                                                    <option value="one-time" {{ old('billing_cycle') == 'one-time' ? 'selected' : '' }}>One Time</option>
                                                </select>
                                                @error('billing_cycle')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="original_price" class="form-label">Original Price</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">$</span>
                                                    <input type="number" class="form-control @error('original_price') is-invalid @enderror"
                                                           name="original_price" id="original_price" value="{{ old('original_price') }}"
                                                           placeholder="0.00" step="0.01" min="0">
                                                </div>
                                                <div class="form-text">For showing discounts</div>
                                                @error('original_price')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="currency" class="form-label">Currency</label>
                                                <select class="form-select @error('currency') is-invalid @enderror" name="currency" id="currency">
                                                    <option value="USD" {{ old('currency', 'USD') == 'USD' ? 'selected' : '' }}>USD ($)</option>
                                                    <option value="EUR" {{ old('currency') == 'EUR' ? 'selected' : '' }}>EUR (€)</option>
                                                    <option value="GBP" {{ old('currency') == 'GBP' ? 'selected' : '' }}>GBP (£)</option>
                                                    <option value="CAD" {{ old('currency') == 'CAD' ? 'selected' : '' }}>CAD (C$)</option>
                                                    <option value="AUD" {{ old('currency') == 'AUD' ? 'selected' : '' }}>AUD (A$)</option>
                                                </select>
                                                @error('currency')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Features -->
                            <div class="card mb-4">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0"><i data-feather="list" class="me-2"></i>Plan Features</h6>
                                    <button type="button" class="btn btn-sm btn-primary" onclick="addFeature()">
                                        <i data-feather="plus" class="me-1"></i>Add Feature
                                    </button>
                                </div>
                                <div class="card-body">
                                    <div id="features-container">
                                        <!-- Features will be added here dynamically -->
                                    </div>
                                    <div class="text-center mt-3" id="no-features-message">
                                        <p class="text-muted">No features added yet. Click "Add Feature" to get started.</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Plan Settings -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="mb-0"><i data-feather="settings" class="me-2"></i>Plan Settings</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="max_users" class="form-label">Maximum Users</label>
                                                <input type="number" class="form-control @error('max_users') is-invalid @enderror"
                                                       name="max_users" id="max_users" value="{{ old('max_users') }}"
                                                       placeholder="Unlimited" min="1">
                                                <div class="form-text">Leave empty for unlimited</div>
                                                @error('max_users')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="max_projects" class="form-label">Maximum Projects</label>
                                                <input type="number" class="form-control @error('max_projects') is-invalid @enderror"
                                                       name="max_projects" id="max_projects" value="{{ old('max_projects') }}"
                                                       placeholder="Unlimited" min="1">
                                                <div class="form-text">Leave empty for unlimited</div>
                                                @error('max_projects')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="storage_limit" class="form-label">Storage Limit (GB)</label>
                                                <input type="number" class="form-control @error('storage_limit') is-invalid @enderror"
                                                       name="storage_limit" id="storage_limit" value="{{ old('storage_limit') }}"
                                                       placeholder="Unlimited" min="1" step="0.1">
                                                <div class="form-text">Leave empty for unlimited</div>
                                                @error('storage_limit')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="trial_days" class="form-label">Trial Days</label>
                                                <input type="number" class="form-control @error('trial_days') is-invalid @enderror"
                                                       name="trial_days" id="trial_days" value="{{ old('trial_days', 0) }}"
                                                       placeholder="0" min="0">
                                                <div class="form-text">0 = No trial</div>
                                                @error('trial_days')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" name="is_featured" id="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="is_featured">
                                                        <i data-feather="star" class="me-1"></i>Featured Plan
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" name="is_popular" id="is_popular" value="1" {{ old('is_popular') ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="is_popular">
                                                        <i data-feather="trending-up" class="me-1"></i>Popular Plan
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', '1') ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="is_active">
                                                        <i data-feather="check-circle" class="me-1"></i>Active Plan
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
                                               name="meta_title" id="meta_title" value="{{ old('meta_title') }}"
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
                                                  placeholder="SEO description for search engines">{{ old('meta_description') }}</textarea>
                                        <div class="form-text">Keep it between 150-160 characters for optimal SEO</div>
                                        @error('meta_description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
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
                                        <i data-feather="check-circle" class="me-1"></i>Create Pricing Plan
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Plan Preview -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0"><i data-feather="eye" class="me-2"></i>Plan Preview</h6>
                    </div>
                    <div class="card-body">
                        <div id="plan-preview">
                            <div class="text-center p-3 border rounded">
                                <h5 class="mb-2">Plan Name</h5>
                                <div class="h3 mb-2">$0.00</div>
                                <p class="text-muted small">Billing Cycle</p>
                                <p class="small">Plan description...</p>
                                <ul class="list-unstyled text-start small">
                                    <li><i data-feather="check" class="me-2 text-success"></i>Feature 1</li>
                                    <li><i data-feather="check" class="me-2 text-success"></i>Feature 2</li>
                                </ul>
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
                                <i data-feather="copy" class="me-1"></i>Duplicate Last Plan
                            </button>
                            <button type="button" class="btn btn-outline-warning btn-sm" onclick="generateSeo()">
                                <i data-feather="search" class="me-1"></i>Generate SEO Content
                            </button>
                            <button type="button" class="btn btn-outline-info btn-sm" onclick="addSampleFeatures()">
                                <i data-feather="list" class="me-1"></i>Add Sample Features
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
.feature-item {
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 15px;
    background: #f8f9fa;
    transition: all 0.3s ease;
}

.feature-item:hover {
    border-color: #007bff;
    background: #e3f2fd;
}

.feature-item.featured {
    border-color: #28a745;
    background: #d4edda;
}

.feature-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}

.feature-title {
    font-weight: 600;
    color: #495057;
}

.feature-actions {
    display: flex;
    gap: 5px;
}

.feature-actions .btn {
    padding: 2px 6px;
    font-size: 12px;
}

.form-progress {
    transition: width 0.3s ease;
}

.invalid-feedback {
    display: block;
}

.plan-preview {
    transition: all 0.3s ease;
}

.feature-check {
    color: #28a745;
}

.feature-uncheck {
    color: #dc3545;
}
</style>
@endsection

@section('js')
<script>
// Global variables
let featureCount = 0;
let formData = {};
let isFormValid = false;

// Initialize when page loads
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, setting up pricing form...');
    setupForm();
    setupAutoSave();
    updateFormProgress();
    console.log('Pricing form setup complete');
});

// Form setup
function setupForm() {
    const form = document.getElementById('pricingForm');

    // Form submit listener
    form.addEventListener('submit', function(e) {
        console.log('Form submit event triggered');
        if (!validateForm()) {
            e.preventDefault();
            console.log('Form validation failed, preventing submit');
            showToast('Please fix the validation errors before submitting.', 'error');
        } else {
            console.log('Form validation passed, allowing submit');
            showToast('Creating pricing plan...', 'info');
        }
    });

    // Auto-generate slug from name
    document.getElementById('name').addEventListener('input', function() {
        const name = this.value;
        const slug = document.getElementById('slug');
        if (!slug.value || slug.dataset.autoGenerated === 'true') {
            slug.value = generateSlug(name);
            slug.dataset.autoGenerated = 'true';
        }
        updateFormProgress();
        updatePreview();
    });

    // Update preview on field changes
    ['description', 'price', 'billing_cycle'].forEach(fieldId => {
        document.getElementById(fieldId).addEventListener('input', function() {
            updatePreview();
            updateFormProgress();
        });
    });

    // Price validation
    document.getElementById('price').addEventListener('input', function() {
        const price = parseFloat(this.value);
        const originalPrice = document.getElementById('original_price');

        if (price > 0 && originalPrice.value && parseFloat(originalPrice.value) <= price) {
            originalPrice.classList.add('is-invalid');
            showToast('Original price must be higher than current price for discounts.', 'warning');
        } else {
            originalPrice.classList.remove('is-invalid');
        }
    });
}

// Feature management
function addFeature() {
    featureCount++;
    const container = document.getElementById('features-container');
    const noFeaturesMessage = document.getElementById('no-features-message');

    if (noFeaturesMessage) {
        noFeaturesMessage.style.display = 'none';
    }

    const featureDiv = document.createElement('div');
    featureDiv.className = 'feature-item';
    featureDiv.id = `feature-${featureCount}`;

    featureDiv.innerHTML = `
        <div class="feature-header">
            <div class="feature-title">Feature ${featureCount}</div>
            <div class="feature-actions">
                <button type="button" class="btn btn-sm btn-outline-success" onclick="toggleFeatureStatus(${featureCount})" title="Toggle Featured">
                    <i data-feather="star" class="feature-icon"></i>
                </button>
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeFeature(${featureCount})" title="Remove Feature">
                    <i data-feather="trash-2"></i>
                </button>
            </div>
        </div>
        <div class="row">
            <div class="col-md-8">
                <div class="mb-2">
                    <label class="form-label">Feature Name</label>
                    <input type="text" class="form-control feature-name" name="features[${featureCount}][name]"
                           placeholder="Enter feature name" required>
                </div>
            </div>
            <div class="col-md-4">
                <div class="mb-2">
                    <label class="form-label">Status</label>
                    <select class="form-select feature-status" name="features[${featureCount}][status]">
                        <option value="included">Included</option>
                        <option value="not-included">Not Included</option>
                        <option value="limited">Limited</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="mb-2">
            <label class="form-label">Description</label>
            <textarea class="form-control feature-description" name="features[${featureCount}][description]"
                      rows="2" placeholder="Brief description of this feature"></textarea>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="mb-2">
                    <label class="form-label">Icon</label>
                    <input type="text" class="form-control feature-icon" name="features[${featureCount}][icon]"
                           placeholder="e.g., check, star, zap" value="check">
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-2">
                    <label class="form-label">Order</label>
                    <input type="number" class="form-control feature-order" name="features[${featureCount}][order]"
                           value="${featureCount}" min="1">
                </div>
            </div>
        </div>
        <input type="hidden" name="features[${featureCount}][is_featured]" value="0" class="feature-featured">
    `;

    container.appendChild(featureDiv);

    // Reinitialize Feather icons
    if (typeof feather !== 'undefined') {
        feather.replace();
    }

    updatePreview();
    showToast('Feature added successfully!', 'success');
}

function removeFeature(featureId) {
    if (confirm('Are you sure you want to remove this feature?')) {
        const feature = document.getElementById(`feature-${featureId}`);
        feature.remove();

        // Check if no features left
        const container = document.getElementById('features-container');
        if (container.children.length === 0) {
            document.getElementById('no-features-message').style.display = 'block';
        }

        updatePreview();
        showToast('Feature removed successfully!', 'info');
    }
}

function toggleFeatureStatus(featureId) {
    const feature = document.getElementById(`feature-${featureId}`);
    const featuredInput = feature.querySelector('.feature-featured');
    const icon = feature.querySelector('.feature-icon i');

    if (featuredInput.value === '1') {
        featuredInput.value = '0';
        feature.classList.remove('featured');
        icon.setAttribute('data-feather', 'star');
        showToast('Feature unmarked as featured', 'info');
    } else {
        featuredInput.value = '1';
        feature.classList.add('featured');
        icon.setAttribute('data-feather', 'star');
        showToast('Feature marked as featured!', 'success');
    }

    if (typeof feather !== 'undefined') {
        feather.replace();
    }

    updatePreview();
}

// Form validation
function validateForm() {
    const requiredFields = ['name', 'description', 'price', 'billing_cycle'];
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

    // Check if at least one feature is added
    const features = document.querySelectorAll('.feature-item');
    if (features.length === 0) {
        showToast('Please add at least one feature to the plan.', 'warning');
        isValid = false;
    }

    isFormValid = isValid;
    updateFormProgress();
    return isValid;
}

// Form progress
function updateFormProgress() {
    const requiredFields = ['name', 'description', 'price', 'billing_cycle'];
    let completed = 0;

    requiredFields.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field.value.trim()) {
            completed++;
        }
    });

    // Add feature bonus
    const features = document.querySelectorAll('.feature-item');
    if (features.length > 0) {
        completed += 1;
    }

    const percentage = (completed / (requiredFields.length + 1)) * 100;
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
    const name = document.getElementById('name').value || 'Plan Name';
    const description = document.getElementById('description').value || 'Plan description...';
    const price = document.getElementById('price').value || '0.00';
    const billingCycle = document.getElementById('billing_cycle').value || 'Billing Cycle';
    const features = document.querySelectorAll('.feature-item');

    const preview = document.getElementById('plan-preview');

    let featuresHtml = '';
    features.forEach((feature, index) => {
        const name = feature.querySelector('.feature-name').value || `Feature ${index + 1}`;
        const status = feature.querySelector('.feature-status').value;
        const isFeatured = feature.querySelector('.feature-featured').value === '1';

        if (status === 'included') {
            featuresHtml += `<li><i data-feather="check" class="me-2 text-success"></i>${name}</li>`;
        } else if (status === 'limited') {
            featuresHtml += `<li><i data-feather="minus" class="me-2 text-warning"></i>${name} (Limited)</li>`;
        } else {
            featuresHtml += `<li><i data-feather="x" class="me-2 text-danger"></i>${name}</li>`;
        }
    });

    if (features.length === 0) {
        featuresHtml = '<li class="text-muted">No features added yet</li>';
    }

    preview.innerHTML = `
        <div class="text-center p-3 border rounded">
            <h5 class="mb-2">${name}</h5>
            <div class="h3 mb-2">$${price}</div>
            <p class="text-muted small">${billingCycle}</p>
            <p class="small">${description}</p>
            <ul class="list-unstyled text-start small">
                ${featuresHtml}
            </ul>
        </div>
    `;

    // Reinitialize Feather icons
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
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
    if (confirm('Are you sure you want to reset the form? All data will be lost.')) {
        document.getElementById('pricingForm').reset();

        // Remove all features
        const container = document.getElementById('features-container');
        container.innerHTML = '';
        document.getElementById('no-features-message').style.display = 'block';

        featureCount = 0;
        updateFormProgress();
        updatePreview();
        showToast('Form has been reset.', 'info');
    }
}

function saveDraft() {
    // Save form data to localStorage
    const formData = new FormData(document.getElementById('pricingForm'));
    const data = {};
    for (let [key, value] of formData.entries()) {
        data[key] = value;
    }

    // Save features
    const features = [];
    document.querySelectorAll('.feature-item').forEach(feature => {
        features.push({
            name: feature.querySelector('.feature-name').value,
            status: feature.querySelector('.feature-status').value,
            description: feature.querySelector('.feature-description').value,
            icon: feature.querySelector('.feature-icon').value,
            order: feature.querySelector('.feature-order').value,
            is_featured: feature.querySelector('.feature-featured').value
        });
    });
    data.features = features;

    localStorage.setItem('pricingDraft', JSON.stringify(data));
    showToast('Draft saved successfully!', 'success');
}

function duplicateFromLast() {
    // Load last saved pricing data
    const lastPricing = localStorage.getItem('lastPricing');
    if (lastPricing) {
        const data = JSON.parse(lastPricing);
        Object.keys(data).forEach(key => {
            const field = document.getElementById(key);
            if (field && key !== 'features') {
                field.value = data[key];
            }
        });

        // Load features
        if (data.features) {
            data.features.forEach(feature => {
                addFeature();
                const lastFeature = document.querySelector(`#feature-${featureCount}`);
                lastFeature.querySelector('.feature-name').value = feature.name || '';
                lastFeature.querySelector('.feature-status').value = feature.status || 'included';
                lastFeature.querySelector('.feature-description').value = feature.description || '';
                lastFeature.querySelector('.feature-icon').value = feature.icon || 'check';
                lastFeature.querySelector('.feature-order').value = feature.order || featureCount;
                lastFeature.querySelector('.feature-featured').value = feature.is_featured || '0';

                if (feature.is_featured === '1') {
                    lastFeature.classList.add('featured');
                }
            });
        }

        updateFormProgress();
        updatePreview();
        showToast('Last pricing data loaded!', 'success');
    } else {
        showToast('No previous pricing data found.', 'info');
    }
}

function generateSeo() {
    const name = document.getElementById('name').value;
    const description = document.getElementById('description').value;

    if (!name || !description) {
        showToast('Please fill in name and description first.', 'warning');
        return;
    }

    // Generate SEO content
    const metaTitle = name.length > 60 ? name.substring(0, 57) + '...' : name;
    const metaDescription = description.replace(/<[^>]*>/g, '').substring(0, 160);
    const slug = generateSlug(name);

    document.getElementById('meta_title').value = metaTitle;
    document.getElementById('meta_description').value = metaDescription;
    document.getElementById('slug').value = slug;
    document.getElementById('slug').dataset.autoGenerated = 'false';

    showToast('SEO content generated successfully!', 'success');
}

function addSampleFeatures() {
    const sampleFeatures = [
        { name: 'Unlimited Projects', status: 'included', description: 'Create and manage unlimited projects', icon: 'folder' },
        { name: 'Team Collaboration', status: 'included', description: 'Work together with your team members', icon: 'users' },
        { name: 'Advanced Analytics', status: 'included', description: 'Get detailed insights and reports', icon: 'bar-chart-2' },
        { name: 'Priority Support', status: 'included', description: '24/7 priority customer support', icon: 'headphones' },
        { name: 'Custom Integrations', status: 'included', description: 'Connect with your favorite tools', icon: 'link' }
    ];

    sampleFeatures.forEach(feature => {
        addFeature();
        const lastFeature = document.querySelector(`#feature-${featureCount}`);
        lastFeature.querySelector('.feature-name').value = feature.name;
        lastFeature.querySelector('.feature-status').value = feature.status;
        lastFeature.querySelector('.feature-description').value = feature.description;
        lastFeature.querySelector('.feature-icon').value = feature.icon;
    });

    updatePreview();
    showToast('Sample features added successfully!', 'success');
}

// Auto-save functionality
let autoSaveTimer;
function setupAutoSave() {
    const fields = ['name', 'description', 'price', 'billing_cycle'];
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
    const draft = localStorage.getItem('pricingDraft');
    if (draft) {
        const data = JSON.parse(draft);
        Object.keys(data).forEach(key => {
            const field = document.getElementById(key);
            if (field && key !== 'features') {
                field.value = data[key];
            }
        });

        // Load features
        if (data.features) {
            data.features.forEach(feature => {
                addFeature();
                const lastFeature = document.querySelector(`#feature-${featureCount}`);
                lastFeature.querySelector('.feature-name').value = feature.name || '';
                lastFeature.querySelector('.feature-status').value = feature.status || 'included';
                lastFeature.querySelector('.feature-description').value = feature.description || '';
                lastFeature.querySelector('.feature-icon').value = feature.icon || 'check';
                lastFeature.querySelector('.feature-order').value = feature.order || featureCount;
                lastFeature.querySelector('.feature-featured').value = feature.is_featured || '0';

                if (feature.is_featured === '1') {
                    lastFeature.classList.add('featured');
                }
            });
        }

        updateFormProgress();
        updatePreview();
    }
});
</script>
@endsection
