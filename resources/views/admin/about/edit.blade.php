@extends('master.master')

@section('content')
<div class="page-content">
    <nav class="page-breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.about.index') }}">About Page Management</a></li>
            <li class="breadcrumb-item active">Edit Company Information</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Edit Company Information</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.about.update') }}" method="POST">
                        @csrf
                        
                        <!-- Company Basic Information -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="mb-3">Basic Information</h5>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="company_name" class="form-label">Company Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('company_name') is-invalid @enderror" 
                                           id="company_name" name="company_name" 
                                           value="{{ old('company_name', $about->company_name ?? '') }}" required>
                                    @error('company_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="is_active" name="is_active" 
                                               {{ old('is_active', $about->is_active ?? true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">Active</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Hero Section -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="mb-3">Hero Section</h5>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="hero_title" class="form-label">Hero Title <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('hero_title') is-invalid @enderror" 
                                           id="hero_title" name="hero_title" 
                                           value="{{ old('hero_title', $about->hero_title ?? '') }}" required>
                                    @error('hero_title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="hero_description" class="form-label">Hero Description <span class="text-danger">*</span></label>
                                    <textarea class="form-control @error('hero_description') is-invalid @enderror" 
                                              id="hero_description" name="hero_description" rows="3" required>{{ old('hero_description', $about->hero_description ?? '') }}</textarea>
                                    @error('hero_description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Story Section -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="mb-3">Story Section</h5>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="story_title" class="form-label">Story Title <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('story_title') is-invalid @enderror" 
                                           id="story_title" name="story_title" 
                                           value="{{ old('story_title', $about->story_title ?? '') }}" required>
                                    @error('story_title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="story_content" class="form-label">Story Content <span class="text-danger">*</span></label>
                                    <textarea class="form-control @error('story_content') is-invalid @enderror" 
                                              id="story_content" name="story_content" rows="5" required>{{ old('story_content', $about->story_content ?? '') }}</textarea>
                                    @error('story_content')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Mission & Vision -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="mb-3">Mission & Vision</h5>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="mission_title" class="form-label">Mission Title <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('mission_title') is-invalid @enderror" 
                                           id="mission_title" name="mission_title" 
                                           value="{{ old('mission_title', $about->mission_title ?? '') }}" required>
                                    @error('mission_title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="vision_title" class="form-label">Vision Title <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('vision_title') is-invalid @enderror" 
                                           id="vision_title" name="vision_title" 
                                           value="{{ old('vision_title', $about->vision_title ?? '') }}" required>
                                    @error('vision_title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="mission_description" class="form-label">Mission Description <span class="text-danger">*</span></label>
                                    <textarea class="form-control @error('mission_description') is-invalid @enderror" 
                                              id="mission_description" name="mission_description" rows="3" required>{{ old('mission_description', $about->mission_description ?? '') }}</textarea>
                                    @error('mission_description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="vision_description" class="form-label">Vision Description <span class="text-danger">*</span></label>
                                    <textarea class="form-control @error('vision_description') is-invalid @enderror" 
                                              id="vision_description" name="vision_description" rows="3" required>{{ old('vision_description', $about->vision_description ?? '') }}</textarea>
                                    @error('vision_description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Statistics -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="mb-3">Statistics</h5>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="years_experience" class="form-label">Years Experience <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('years_experience') is-invalid @enderror" 
                                           id="years_experience" name="years_experience" 
                                           value="{{ old('years_experience', $about->years_experience ?? 5) }}" min="1" required>
                                    @error('years_experience')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="projects_delivered" class="form-label">Projects Delivered <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('projects_delivered') is-invalid @enderror" 
                                           id="projects_delivered" name="projects_delivered" 
                                           value="{{ old('projects_delivered', $about->projects_delivered ?? 100) }}" min="1" required>
                                    @error('projects_delivered')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="happy_clients" class="form-label">Happy Clients <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('happy_clients') is-invalid @enderror" 
                                           id="happy_clients" name="happy_clients" 
                                           value="{{ old('happy_clients', $about->happy_clients ?? 50) }}" min="1" required>
                                    @error('happy_clients')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="support_availability" class="form-label">Support Availability <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('support_availability') is-invalid @enderror" 
                                           id="support_availability" name="support_availability" 
                                           value="{{ old('support_availability', $about->support_availability ?? '24/7') }}" required>
                                    @error('support_availability')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Section Titles -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="mb-3">Section Titles & Descriptions</h5>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="values_title" class="form-label">Values Section Title <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('values_title') is-invalid @enderror" 
                                           id="values_title" name="values_title" 
                                           value="{{ old('values_title', $about->values_title ?? '') }}" required>
                                    @error('values_title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="process_title" class="form-label">Process Section Title <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('process_title') is-invalid @enderror" 
                                           id="process_title" name="process_title" 
                                           value="{{ old('process_title', $about->process_title ?? '') }}" required>
                                    @error('process_title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="team_title" class="form-label">Team Section Title <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('team_title') is-invalid @enderror" 
                                           id="team_title" name="team_title" 
                                           value="{{ old('team_title', $about->team_title ?? '') }}" required>
                                    @error('team_title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="cta_title" class="form-label">CTA Section Title <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('cta_title') is-invalid @enderror" 
                                           id="cta_title" name="cta_title" 
                                           value="{{ old('cta_title', $about->cta_title ?? '') }}" required>
                                    @error('cta_title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Section Descriptions -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="mb-3">Section Descriptions</h5>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="values_description" class="form-label">Values Description <span class="text-danger">*</span></label>
                                    <textarea class="form-control @error('values_description') is-invalid @enderror" 
                                              id="values_description" name="values_description" rows="3" required>{{ old('values_description', $about->values_description ?? '') }}</textarea>
                                    @error('values_description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="process_description" class="form-label">Process Description <span class="text-danger">*</span></label>
                                    <textarea class="form-control @error('process_description') is-invalid @enderror" 
                                              id="process_description" name="process_description" rows="3" required>{{ old('process_description', $about->process_description ?? '') }}</textarea>
                                    @error('process_description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="team_description" class="form-label">Team Description <span class="text-danger">*</span></label>
                                    <textarea class="form-control @error('team_description') is-invalid @enderror" 
                                              id="team_description" name="team_description" rows="3" required>{{ old('team_description', $about->team_description ?? '') }}</textarea>
                                    @error('team_description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="cta_description" class="form-label">CTA Description <span class="text-danger">*</span></label>
                                    <textarea class="form-control @error('cta_description') is-invalid @enderror" 
                                              id="cta_description" name="cta_description" rows="3" required>{{ old('cta_description', $about->cta_description ?? '') }}</textarea>
                                    @error('cta_description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('admin.about.index') }}" class="btn btn-secondary">
                                        <i data-feather="arrow-left"></i> Back to List
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i data-feather="save"></i> Save Changes
                                    </button>
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

