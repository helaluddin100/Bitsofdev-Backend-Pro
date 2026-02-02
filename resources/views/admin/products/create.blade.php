@extends('master.master')

@section('content')
    <div class="page-content">
        <nav class="page-breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}">Products</a></li>
                <li class="breadcrumb-item active" aria-current="page">Create Product</li>
            </ol>
        </nav>

        <div class="row">
            <div class="col-lg-8 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="card-title mb-0">
                                <i data-feather="package" class="me-2"></i>Create New Product
                            </h4>
                            <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary btn-sm">
                                <i data-feather="arrow-left" class="me-1"></i>Back to Products
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

                        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" id="productForm">
                            @csrf

                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="mb-0"><i data-feather="info" class="me-2"></i>Basic Information</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="mb-3">
                                                <label for="title" class="form-label">Product Title <span class="text-danger">*</span></label>
                                                <input id="title" class="form-control @error('title') is-invalid @enderror"
                                                       name="title" type="text" value="{{ old('title') }}"
                                                       placeholder="Enter product title" required>
                                                @error('title')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="client" class="form-label">Client / Brand <span class="text-danger">*</span></label>
                                                <input id="client" class="form-control @error('client') is-invalid @enderror"
                                                       name="client" type="text" value="{{ old('client') }}"
                                                       placeholder="Client or brand name" required>
                                                @error('client')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="excerpt" class="form-label">Product Summary <span class="text-danger">*</span></label>
                                        <textarea class="form-control @error('excerpt') is-invalid @enderror"
                                                  name="excerpt" id="excerpt" rows="3"
                                                  placeholder="Brief description of the product" required>{{ old('excerpt') }}</textarea>
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
                                                    <option value="planning" {{ old('status') == 'planning' ? 'selected' : '' }}>Planning</option>
                                                    <option value="in-progress" {{ old('status') == 'in-progress' ? 'selected' : '' }}>In Progress</option>
                                                    <option value="review" {{ old('status') == 'review' ? 'selected' : '' }}>Under Review</option>
                                                    <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                                    <option value="on-hold" {{ old('status') == 'on-hold' ? 'selected' : '' }}>On Hold</option>
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
                                                    <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Low</option>
                                                    <option value="medium" {{ old('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                                                    <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High</option>
                                                    <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                                                </select>
                                                @error('priority')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="mb-0"><i data-feather="file-text" class="me-2"></i>Product Details</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="content" class="form-label">Detailed Description <span class="text-danger">*</span></label>
                                        <textarea class="form-control @error('content') is-invalid @enderror"
                                                  name="content" id="content" rows="8"
                                                  placeholder="Detailed product description" required>{{ old('content') }}</textarea>
                                        @error('content')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="start_date" class="form-label">Start Date</label>
                                                <input type="date" class="form-control @error('start_date') is-invalid @enderror"
                                                       name="start_date" id="start_date" value="{{ old('start_date') }}">
                                                @error('start_date')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="end_date" class="form-label">End Date</label>
                                                <input type="date" class="form-control @error('end_date') is-invalid @enderror"
                                                       name="end_date" id="end_date" value="{{ old('end_date') }}">
                                                @error('end_date')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="technologies" class="form-label">Technologies / Tags</label>
                                        <input type="text" class="form-control @error('technologies') is-invalid @enderror"
                                               name="technologies" id="technologies" value="{{ old('technologies') }}"
                                               placeholder="e.g., Laravel, React, MySQL">
                                        <div class="form-text">Separate with commas</div>
                                        @error('technologies')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="product_url" class="form-label">Product URL</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i data-feather="link"></i></span>
                                            <input type="url" class="form-control @error('product_url') is-invalid @enderror"
                                                   name="product_url" id="product_url" value="{{ old('product_url') }}"
                                                   placeholder="https://example.com">
                                        </div>
                                        @error('product_url')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="mb-0"><i data-feather="image" class="me-2"></i>Media & Settings</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="featured_image" class="form-label">Featured Image</label>
                                        <div class="drop-zone @error('featured_image') is-invalid @enderror" id="dropZone">
                                            <div class="drop-zone-content">
                                                <i data-feather="upload-cloud" class="drop-zone-icon"></i>
                                                <h6 class="drop-zone-title">Drag & Drop Image Here</h6>
                                                <p class="drop-zone-text">or click to browse</p>
                                                <input type="file" class="drop-zone-input" name="featured_image" id="featured_image"
                                                       accept="image/*" onchange="handleImageSelect(this)">
                                            </div>
                                        </div>
                                        <div id="image-preview" class="mt-3" style="display: none;">
                                            <div class="position-relative d-inline-block">
                                                <img id="preview-img" src="" alt="Preview" class="img-thumbnail" style="max-width: 300px;">
                                                <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1"
                                                        onclick="removeImage()">
                                                    <i data-feather="x"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="form-text">Recommended: 1200x800px. Will be converted to WebP.</div>
                                        @error('featured_image')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" name="is_featured" id="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="is_featured">
                                                        <i data-feather="star" class="me-1"></i>Featured Product
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', '1') ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="is_active">
                                                        <i data-feather="check-circle" class="me-1"></i>Active Product
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="mb-0"><i data-feather="search" class="me-2"></i>SEO & Meta</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="meta_title" class="form-label">Meta Title</label>
                                        <input type="text" class="form-control @error('meta_title') is-invalid @enderror"
                                               name="meta_title" id="meta_title" value="{{ old('meta_title') }}"
                                               placeholder="SEO title (max 60 chars)">
                                        @error('meta_title')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="meta_description" class="form-label">Meta Description</label>
                                        <textarea class="form-control @error('meta_description') is-invalid @enderror"
                                                  name="meta_description" id="meta_description" rows="3"
                                                  placeholder="SEO description">{{ old('meta_description') }}</textarea>
                                        @error('meta_description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="slug" class="form-label">URL Slug</label>
                                        <div class="input-group">
                                            <span class="input-group-text">{{ url('/products/') }}</span>
                                            <input type="text" class="form-control @error('slug') is-invalid @enderror"
                                                   name="slug" id="slug" value="{{ old('slug') }}"
                                                   placeholder="auto-generated-slug">
                                        </div>
                                        <div class="form-text">Leave empty to auto-generate from title</div>
                                        @error('slug')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between">
                                <button type="button" class="btn btn-outline-secondary" onclick="document.getElementById('productForm').reset();">
                                    <i data-feather="refresh-cw" class="me-1"></i>Reset
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i data-feather="check-circle" class="me-1"></i>Create Product
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0"><i data-feather="eye" class="me-2"></i>Live Preview</h6>
                    </div>
                    <div class="card-body">
                        <div id="form-preview">
                            <p class="text-muted">Start typing to see preview...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
<style>
.drop-zone { border: 2px dashed #dee2e6; border-radius: 8px; padding: 40px 20px; text-align: center; cursor: pointer; background: #f8f9fa; }
.drop-zone:hover { border-color: #007bff; background: #e3f2fd; }
.drop-zone-content { pointer-events: none; }
.drop-zone-input { position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer; }
</style>
@endsection

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('title').addEventListener('input', function() {
        var slug = document.getElementById('slug');
        if (!slug.value || slug.dataset.auto === '1') {
            slug.value = this.value.toLowerCase().replace(/[^a-z0-9 -]/g, '').replace(/\s+/g, '-').replace(/-+/g, '-').trim('-');
            slug.dataset.auto = '1';
        }
    });
});
function handleImageSelect(input) {
    if (input.files && input.files[0]) {
        var r = new FileReader();
        r.onload = function(e) {
            document.getElementById('preview-img').src = e.target.result;
            document.getElementById('image-preview').style.display = 'block';
            document.getElementById('dropZone').style.display = 'none';
        };
        r.readAsDataURL(input.files[0]);
    }
}
function removeImage() {
    document.getElementById('featured_image').value = '';
    document.getElementById('image-preview').style.display = 'none';
    document.getElementById('dropZone').style.display = 'block';
}
</script>
@endsection
