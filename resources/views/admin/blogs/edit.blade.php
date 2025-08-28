@extends('master.master')

@section('content')
    <div class="page-content">

        <nav class="page-breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Forms</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edit Blog Post</li>
            </ol>
        </nav>

        <div class="row">
            <div class="col-lg-8 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Edit Blog Post</h4>

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('admin.blogs.update', $blog->id) }}" method="PUT" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <label for="title" class="form-label">Title</label>
                                <input id="title" class="form-control @error('title') is-invalid @enderror" name="title" type="text" value="{{ old('title', $blog->title) }}" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="category_id" class="form-label">Category</label>
                                <select class="form-control @error('category_id') is-invalid @enderror" name="category_id" id="category_id" required>
                                    <option value="">Select Category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id', $blog->category_id) == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="excerpt" class="form-label">Excerpt</label>
                                <textarea class="form-control @error('excerpt') is-invalid @enderror" name="excerpt" id="excerpt" rows="3" required>{{ old('excerpt', $blog->excerpt) }}</textarea>
                                @error('excerpt')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="content" class="form-label">Content</label>
                                <textarea class="form-control @error('content') is-invalid @enderror" name="content" id="content" rows="10" required>{{ old('content', $blog->content) }}</textarea>
                                @error('content')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="featured_image" class="form-label">Featured Image</label>
                                @if($blog->featured_image)
                                    <div class="mb-2">
                                        <img src="{{ asset($blog->featured_image) }}" alt="Current Image" style="max-width: 200px; height: auto;" class="img-thumbnail">
                                    </div>
                                @endif
                                <input type="file" class="form-control" name="featured_image" id="featured_image" accept="image/*" onchange="previewImage(this)">
                                <div id="image-preview" class="mt-2" style="display: none;">
                                    <img id="preview-img" src="" alt="Preview" style="max-width: 300px; height: auto; border-radius: 8px;">
                                </div>
                                <small class="form-text text-muted">Image will be automatically converted to WebP format and optimized for web.</small>
                            </div>

                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-control @error('status') is-invalid @enderror" name="status" id="status" required>
                                    <option value="draft" {{ old('status', $blog->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="published" {{ old('status', $blog->status) == 'published' ? 'selected' : '' }}>Published</option>
                                    <option value="archived" {{ old('status', $blog->status) == 'archived' ? 'selected' : '' }}>Archived</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <div class="form-check">
                                    <label class="form-check-label" for="is_featured">
                                        Featured Post
                                    </label>
                                    <input type="checkbox" class="form-check-input" name="is_featured" id="is_featured" value="1" {{ $blog->is_featured ? 'checked' : '' }}>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="meta_title" class="form-label">Meta Title</label>
                                <input id="meta_title" class="form-control @error('meta_title') is-invalid @enderror" name="meta_title" type="text" value="{{ old('meta_title', $blog->meta_title) }}">
                                @error('meta_title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="meta_description" class="form-label">Meta Description</label>
                                <textarea class="form-control @error('meta_description') is-invalid @enderror" name="meta_description" id="meta_description" rows="3">{{ old('meta_description', $blog->meta_description) }}</textarea>
                                <small class="form-text text-muted">Keep it between 150-160 characters for optimal SEO.</small>
                                @error('meta_description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- SEO Analysis Section -->
                            <div class="card mt-4">
                                <div class="card-header">
                                    <h6 class="mb-0">SEO Analysis</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">SEO Score</label>
                                                <div class="progress">
                                                    <div id="seo-score-bar" class="progress-bar" role="progressbar" style="width: 0%">0%</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Character Count</label>
                                                <div class="d-flex justify-content-between">
                                                    <span>Title: <span id="title-count">{{ strlen($blog->title) }}</span>/60</span>
                                                    <span>Description: <span id="desc-count">{{ strlen($blog->meta_description ?? '') }}</span>/160</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="seo-recommendations" class="mt-3">
                                        <h6>Recommendations:</h6>
                                        <ul id="recommendations-list" class="list-unstyled">
                                            <li class="text-muted">Click "Analyze SEO" to see recommendations...</li>
                                        </ul>
                                    </div>
                                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="analyzeSEO()">
                                        <i data-feather="search"></i> Analyze SEO
                                    </button>
                                </div>
                            </div>

                            <button class="btn btn-primary" type="submit" onclick="return validateForm()">
                                <i data-feather="save"></i> Update Blog Post
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@section('js')
<script>
// Form validation
function validateForm() {
    const title = document.getElementById('title').value.trim();
    const category = document.getElementById('category_id').value;
    const excerpt = document.getElementById('excerpt').value.trim();
    const content = document.getElementById('content').value.trim();

    if (!title) {
        alert('Please enter a title');
        document.getElementById('title').focus();
        return false;
    }

    if (!category) {
        alert('Please select a category');
        document.getElementById('category_id').focus();
        return false;
    }

    if (!excerpt) {
        alert('Please enter an excerpt');
        document.getElementById('excerpt').focus();
        return false;
    }

    if (!content) {
        alert('Please enter content');
        document.getElementById('content').focus();
        return false;
    }

    console.log('Form validation passed');
    return true;
}

// Image preview functionality
function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview-img').src = e.target.result;
            document.getElementById('image-preview').style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Character counting
document.getElementById('title').addEventListener('input', function() {
    const count = this.value.length;
    console.log('Title character count:', count);
    document.getElementById('title-count').textContent = count;
    updateProgressBarColor(count, 60);
});

document.getElementById('meta_description').addEventListener('input', function() {
    const count = this.value.length;
    console.log('Meta description character count:', count);
    document.getElementById('desc-count').textContent = count;
    updateProgressBarColor(count, 160);
});

function updateProgressBarColor(count, max) {
    const percentage = (count / max) * 100;
    const progressBar = document.getElementById('seo-score-bar');

    console.log('Updating progress bar:', { count, max, percentage });

    if (percentage <= 100) {
        progressBar.style.width = percentage + '%';
        progressBar.textContent = Math.round(percentage) + '%';

        if (percentage < 50) {
            progressBar.className = 'progress-bar bg-danger';
        } else if (percentage < 80) {
            progressBar.className = 'progress-bar bg-warning';
        } else {
            progressBar.className = 'progress-bar bg-success';
        }
    }
}

// SEO Analysis
function analyzeSEO() {
    const title = document.getElementById('title').value;
    const content = document.getElementById('content').value;

    console.log('Analyzing SEO for:', { title, content });

    if (!title || !content) {
        alert('Please fill in both title and content before analyzing SEO.');
        return;
    }

    // Show loading state
    const analyzeBtn = document.querySelector('button[onclick="analyzeSEO()"]');
    const originalText = analyzeBtn.innerHTML;
    analyzeBtn.innerHTML = '<i data-feather="loader"></i> Analyzing...';
    analyzeBtn.disabled = true;

    const requestData = {
        title: title,
        content: content
    };

    const seoRoute = '{{ route("admin.blogs.seo-suggestions") }}';
    console.log('SEO route:', seoRoute);
    console.log('Request data:', requestData);

    // Check CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (csrfToken) {
        console.log('CSRF token found:', csrfToken.getAttribute('content'));
    } else {
        console.error('CSRF token not found!');
        alert('CSRF token not found. Please refresh the page.');
        return;
    }

    // Make AJAX request to get SEO suggestions
    fetch(seoRoute, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(requestData)
    })
    .then(response => {
        console.log('Response status:', response.status);
        console.log('Response headers:', response.headers);
        return response.json();
    })
    .then(data => {
        console.log('SEO Analysis response:', data);
        // Update SEO score
        const progressBar = document.getElementById('seo-score-bar');
        progressBar.style.width = data.seo_score + '%';
        progressBar.textContent = data.seo_score + '%';

        if (data.seo_score < 50) {
            progressBar.className = 'progress-bar bg-danger';
        } else if (data.seo_score < 80) {
            progressBar.className = 'progress-bar bg-warning';
        } else {
            progressBar.className = 'progress-bar bg-success';
        }

        // Update recommendations
        const recommendationsList = document.getElementById('recommendations-list');
        recommendationsList.innerHTML = '';

        if (data.recommendations.length > 0) {
            data.recommendations.forEach(rec => {
                const li = document.createElement('li');
                li.className = 'text-info mb-1';
                li.innerHTML = '<i data-feather="info" class="me-2"></i>' + rec;
                recommendationsList.appendChild(li);
            });
        } else {
            const li = document.createElement('li');
            li.className = 'text-success';
            li.innerHTML = '<i data-feather="check-circle" class="me-2"></i>Great! Your SEO looks good.';
            recommendationsList.appendChild(li);
        }

        // Auto-fill meta fields if they're empty
        if (!document.getElementById('meta_title').value) {
            document.getElementById('meta_title').value = data.meta_title;
        }
        if (!document.getElementById('meta_description').value) {
            document.getElementById('meta_description').value = data.meta_description;
        }

        // Update character counts
        document.getElementById('title-count').textContent = data.meta_title.length;
        document.getElementById('desc-count').textContent = data.meta_description.length;

        // Reinitialize Feather icons
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        console.error('Error details:', error.message);
        alert('Error analyzing SEO: ' + error.message + '. Please check console for details.');
    })
    .finally(() => {
        // Reset button state
        analyzeBtn.innerHTML = originalText;
        analyzeBtn.disabled = false;
    });
}

// Initialize when page loads
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, setting up edit form...');

    // Add form submit listener
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        console.log('Form submit event triggered');
        if (!validateForm()) {
            e.preventDefault();
            console.log('Form validation failed, preventing submit');
        } else {
            console.log('Form validation passed, allowing submit');
        }
    });

    // Initialize character counts
    document.getElementById('title-count').textContent = '{{ strlen($blog->title) }}';
    document.getElementById('desc-count').textContent = '{{ strlen($blog->meta_description ?? "") }}';

    // Initialize progress bar color
    updateProgressBarColor('{{ strlen($blog->title) }}', 60);
    updateProgressBarColor('{{ strlen($blog->meta_description ?? "") }}', 160);

    console.log('Edit form setup complete');
});
</script>
@endsection
