@extends('master.master')

@section('title', 'Edit Testimonial')

@section('content')
    <div class="page-content">

        <nav class="page-breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Management</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.testimonials.index') }}">Testimonials</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edit Testimonial</li>
            </ol>
        </nav>

        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h6 class="card-title">Edit Testimonial - {{ $testimonial->name }}</h6>
                            <a href="{{ route('admin.testimonials.index') }}" class="btn btn-secondary">
                                <i data-feather="arrow-left"></i> Back to Testimonials
                            </a>
                        </div>

                        <form method="POST" action="{{ route('admin.testimonials.update', $testimonial) }}">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <!-- Personal Information -->
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6 class="card-title">Personal Information</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="mb-3">
                                                <label for="name" class="form-label">Full Name *</label>
                                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                                       id="name" name="name" value="{{ old('name', $testimonial->name) }}" required>
                                                @error('name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="mb-3">
                                                <label for="role" class="form-label">Role/Position *</label>
                                                <input type="text" class="form-control @error('role') is-invalid @enderror"
                                                       id="role" name="role" value="{{ old('role', $testimonial->role) }}" required>
                                                @error('role')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="mb-3">
                                                <label for="company" class="form-label">Company</label>
                                                <input type="text" class="form-control @error('company') is-invalid @enderror"
                                                       id="company" name="company" value="{{ old('company', $testimonial->company) }}">
                                                @error('company')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="mb-3">
                                                <label for="email" class="form-label">Email</label>
                                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                                       id="email" name="email" value="{{ old('email', $testimonial->email) }}">
                                                @error('email')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="mb-3">
                                                <label for="location" class="form-label">Location</label>
                                                <input type="text" class="form-control @error('location') is-invalid @enderror"
                                                       id="location" name="location" value="{{ old('location', $testimonial->location) }}">
                                                @error('location')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="mb-3">
                                                <label for="image" class="form-label">Image URL</label>
                                                <input type="url" class="form-control @error('image') is-invalid @enderror"
                                                       id="image" name="image" value="{{ old('image', $testimonial->image) }}">
                                                @error('image')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                @if($testimonial->image)
                                                    <div class="mt-2">
                                                        <img src="{{ $testimonial->image }}" alt="{{ $testimonial->name }}" class="img-thumbnail" width="100">
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Project Information -->
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6 class="card-title">Project Information</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="mb-3">
                                                <label for="project_type" class="form-label">Project Type</label>
                                                <select class="form-control @error('project_type') is-invalid @enderror"
                                                        id="project_type" name="project_type">
                                                    <option value="">Select Project Type</option>
                                                    <option value="web-development" {{ old('project_type', $testimonial->project_type) == 'web-development' ? 'selected' : '' }}>Web Development</option>
                                                    <option value="mobile-app" {{ old('project_type', $testimonial->project_type) == 'mobile-app' ? 'selected' : '' }}>Mobile App</option>
                                                    <option value="ui-ux-design" {{ old('project_type', $testimonial->project_type) == 'ui-ux-design' ? 'selected' : '' }}>UI/UX Design</option>
                                                    <option value="e-commerce" {{ old('project_type', $testimonial->project_type) == 'e-commerce' ? 'selected' : '' }}>E-commerce</option>
                                                    <option value="consulting" {{ old('project_type', $testimonial->project_type) == 'consulting' ? 'selected' : '' }}>Consulting</option>
                                                    <option value="seo" {{ old('project_type', $testimonial->project_type) == 'seo' ? 'selected' : '' }}>SEO Services</option>
                                                    <option value="digital-marketing" {{ old('project_type', $testimonial->project_type) == 'digital-marketing' ? 'selected' : '' }}>Digital Marketing</option>
                                                    <option value="other" {{ old('project_type', $testimonial->project_type) == 'other' ? 'selected' : '' }}>Other</option>
                                                </select>
                                                @error('project_type')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="mb-3">
                                                <label for="project_name" class="form-label">Project Name</label>
                                                <input type="text" class="form-control @error('project_name') is-invalid @enderror"
                                                       id="project_name" name="project_name" value="{{ old('project_name', $testimonial->project_name) }}">
                                                @error('project_name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="mb-3">
                                                <label for="rating" class="form-label">Rating *</label>
                                                <select class="form-control @error('rating') is-invalid @enderror"
                                                        id="rating" name="rating" required>
                                                    <option value="5" {{ old('rating', $testimonial->rating) == 5 ? 'selected' : '' }}>5 Stars - Excellent</option>
                                                    <option value="4" {{ old('rating', $testimonial->rating) == 4 ? 'selected' : '' }}>4 Stars - Very Good</option>
                                                    <option value="3" {{ old('rating', $testimonial->rating) == 3 ? 'selected' : '' }}>3 Stars - Good</option>
                                                    <option value="2" {{ old('rating', $testimonial->rating) == 2 ? 'selected' : '' }}>2 Stars - Fair</option>
                                                    <option value="1" {{ old('rating', $testimonial->rating) == 1 ? 'selected' : '' }}>1 Star - Poor</option>
                                                </select>
                                                @error('rating')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="mb-3">
                                                <label for="sort_order" class="form-label">Sort Order</label>
                                                <input type="number" class="form-control @error('sort_order') is-invalid @enderror"
                                                       id="sort_order" name="sort_order" value="{{ old('sort_order', $testimonial->sort_order) }}">
                                                @error('sort_order')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Testimonial Content -->
                            <div class="row">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6 class="card-title">Testimonial Content</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="mb-3">
                                                <label for="content" class="form-label">Testimonial Content *</label>
                                                <textarea class="form-control @error('content') is-invalid @enderror"
                                                          id="content" name="content" rows="6" required>{{ old('content', $testimonial->content) }}</textarea>
                                                @error('content')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Status Settings -->
                            <div class="row">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6 class="card-title">Status Settings</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-check">
                                                        <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" {{ old('is_active', $testimonial->is_active) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="is_active">
                                                            Active
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-check">
                                                        <input type="checkbox" class="form-check-input" id="is_featured" name="is_featured" value="1" {{ old('is_featured', $testimonial->is_featured) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="is_featured">
                                                            Featured
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-check">
                                                        <input type="checkbox" class="form-check-input" id="is_verified" name="is_verified" value="1" {{ old('is_verified', $testimonial->is_verified) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="is_verified">
                                                            Verified
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Buttons -->
                            <div class="row">
                                <div class="col-12">
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i data-feather="save"></i> Update Testimonial
                                        </button>
                                        <a href="{{ route('admin.testimonials.index') }}" class="btn btn-secondary">
                                            <i data-feather="x"></i> Cancel
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
