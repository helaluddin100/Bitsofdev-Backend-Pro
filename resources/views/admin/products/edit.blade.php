@extends('master.master')

@section('content')
    <div class="page-content">
        <nav class="page-breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}">Products</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edit Product</li>
            </ol>
        </nav>

        <div class="row">
            <div class="col-lg-8 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="card-title mb-0">
                                <i data-feather="edit-3" class="me-2"></i>Edit Product: {{ $product->title }}
                            </h4>
                            <div>
                                <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary btn-sm">
                                    <i data-feather="arrow-left" class="me-1"></i>Back to Products
                                </a>
                            </div>
                        </div>

                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong>Please fix the following errors:</strong>
                                <ul class="mb-0 mt-2">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data" id="productForm">
                            @csrf
                            @method('PUT')

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
                                                       name="title" type="text" value="{{ old('title', $product->title) }}" required>
                                                @error('title')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="client" class="form-label">Client / Brand <span class="text-danger">*</span></label>
                                                <input id="client" class="form-control @error('client') is-invalid @enderror"
                                                       name="client" type="text" value="{{ old('client', $product->client) }}" required>
                                                @error('client')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="excerpt" class="form-label">Product Summary <span class="text-danger">*</span></label>
                                        <textarea class="form-control @error('excerpt') is-invalid @enderror" name="excerpt" id="excerpt" rows="3" required>{{ old('excerpt', $product->excerpt) }}</textarea>
                                        @error('excerpt')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                                <select class="form-select @error('status') is-invalid @enderror" name="status" id="status" required>
                                                    <option value="planning" {{ old('status', $product->status) == 'planning' ? 'selected' : '' }}>Planning</option>
                                                    <option value="in-progress" {{ old('status', $product->status) == 'in-progress' ? 'selected' : '' }}>In Progress</option>
                                                    <option value="review" {{ old('status', $product->status) == 'review' ? 'selected' : '' }}>Under Review</option>
                                                    <option value="completed" {{ old('status', $product->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                                                    <option value="on-hold" {{ old('status', $product->status) == 'on-hold' ? 'selected' : '' }}>On Hold</option>
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
                                                    <option value="low" {{ old('priority', $product->priority) == 'low' ? 'selected' : '' }}>Low</option>
                                                    <option value="medium" {{ old('priority', $product->priority) == 'medium' ? 'selected' : '' }}>Medium</option>
                                                    <option value="high" {{ old('priority', $product->priority) == 'high' ? 'selected' : '' }}>High</option>
                                                    <option value="urgent" {{ old('priority', $product->priority) == 'urgent' ? 'selected' : '' }}>Urgent</option>
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
                                        <textarea class="form-control @error('content') is-invalid @enderror" name="content" id="content" rows="8" required>{{ old('content', $product->content) }}</textarea>
                                        @error('content')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="start_date" class="form-label">Start Date</label>
                                                <input type="date" class="form-control" name="start_date" id="start_date"
                                                       value="{{ old('start_date', $product->start_date ? $product->start_date->format('Y-m-d') : '') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="end_date" class="form-label">End Date</label>
                                                <input type="date" class="form-control" name="end_date" id="end_date"
                                                       value="{{ old('end_date', $product->end_date ? $product->end_date->format('Y-m-d') : '') }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="technologies" class="form-label">Technologies / Tags</label>
                                        <input type="text" class="form-control" name="technologies" id="technologies"
                                               value="{{ old('technologies', $product->technologies) }}" placeholder="Comma separated">
                                    </div>
                                    <div class="mb-3">
                                        <label for="product_url" class="form-label">Product URL</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i data-feather="link"></i></span>
                                            <input type="url" class="form-control" name="product_url" id="product_url"
                                                   value="{{ old('product_url', $product->product_url) }}" placeholder="https://example.com">
                                        </div>
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
                                        @if($product->featured_image)
                                            <div class="mb-3">
                                                <label class="form-label">Current Image</label>
                                                <img src="{{ asset($product->featured_image) }}" alt="Current" class="img-thumbnail" style="max-width: 300px;">
                                            </div>
                                        @endif
                                        <input type="file" class="form-control" name="featured_image" id="featured_image" accept="image/*">
                                        <div class="form-text">Recommended: 1200x800px. Will be converted to WebP.</div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" name="is_featured" id="is_featured" value="1" {{ old('is_featured', $product->is_featured) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="is_featured">Featured Product</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="is_active">Active Product</label>
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
                                        <input type="text" class="form-control" name="meta_title" id="meta_title" value="{{ old('meta_title', $product->meta_title) }}">
                                    </div>
                                    <div class="mb-3">
                                        <label for="meta_description" class="form-label">Meta Description</label>
                                        <textarea class="form-control" name="meta_description" id="meta_description" rows="3">{{ old('meta_description', $product->meta_description) }}</textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="slug" class="form-label">URL Slug</label>
                                        <div class="input-group">
                                            <span class="input-group-text">{{ url('/products/') }}</span>
                                            <input type="text" class="form-control" name="slug" id="slug" value="{{ old('slug', $product->slug) }}">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary">
                                    <i data-feather="check-circle" class="me-1"></i>Update Product
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0"><i data-feather="info" class="me-2"></i>Product Info</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <small class="text-muted">Created</small>
                            <div class="fw-bold">{{ $product->created_at->format('M d, Y H:i') }}</div>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted">Last Updated</small>
                            <div class="fw-bold">{{ $product->updated_at->format('M d, Y H:i') }}</div>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted">Status</small>
                            <div><span class="badge bg-secondary">{{ ucfirst(str_replace('-', ' ', $product->status)) }}</span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
