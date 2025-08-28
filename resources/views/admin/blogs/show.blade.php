@extends('master.master')

@section('title', 'View Blog Post')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title">Blog Post Details</h4>
                        <div class="btn-group">
                            <a href="{{ route('admin.blogs.edit', $blog->id) }}" class="btn btn-primary">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <a href="{{ route('admin.blogs.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to List
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <!-- Blog Content -->
                            <div class="blog-content">
                                <h2 class="mb-3">{{ $blog->title }}</h2>
                                
                                @if($blog->featured_image)
                                <div class="featured-image mb-4">
                                    <img src="{{ asset($blog->featured_image) }}" 
                                         alt="{{ $blog->title }}" 
                                         class="img-fluid rounded" 
                                         style="max-height: 400px; width: 100%; object-fit: cover;">
                                </div>
                                @endif

                                <div class="meta-info mb-4">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>Status:</strong> 
                                                <span class="badge badge-{{ $blog->status === 'published' ? 'success' : ($blog->status === 'draft' ? 'warning' : 'secondary') }}">
                                                    {{ ucfirst($blog->status) }}
                                                </span>
                                            </p>
                                            <p><strong>Category:</strong> 
                                                <span class="badge badge-info">{{ $blog->category->name ?? 'No Category' }}</span>
                                            </p>
                                            <p><strong>Author:</strong> {{ $blog->user->name ?? 'Unknown' }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Created:</strong> {{ $blog->created_at->format('M d, Y H:i') }}</p>
                                            <p><strong>Updated:</strong> {{ $blog->updated_at->format('M d, Y H:i') }}</p>
                                            @if($blog->published_at)
                                            <p><strong>Published:</strong> {{ $blog->published_at->format('M d, Y H:i') }}</p>
                                            @endif
                                            <p><strong>Views:</strong> {{ $blog->views ?? 0 }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="excerpt mb-4">
                                    <h5>Excerpt</h5>
                                    <p class="text-muted">{{ $blog->excerpt }}</p>
                                </div>

                                <div class="content">
                                    <h5>Content</h5>
                                    <div class="content-body">
                                        {!! $blog->content !!}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <!-- Sidebar Info -->
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Post Information</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <strong>Slug:</strong>
                                        <code class="d-block mt-1">{{ $blog->slug }}</code>
                                    </div>

                                    <div class="mb-3">
                                        <strong>Featured Post:</strong>
                                        <span class="badge badge-{{ $blog->is_featured ? 'success' : 'secondary' }}">
                                            {{ $blog->is_featured ? 'Yes' : 'No' }}
                                        </span>
                                    </div>

                                    @if($blog->meta_title || $blog->meta_description)
                                    <div class="mb-3">
                                        <h6>SEO Information</h6>
                                        @if($blog->meta_title)
                                        <div class="mb-2">
                                            <strong>Meta Title:</strong>
                                            <p class="text-muted small mb-0">{{ $blog->meta_title }}</p>
                                        </div>
                                        @endif
                                        @if($blog->meta_description)
                                        <div class="mb-2">
                                            <strong>Meta Description:</strong>
                                            <p class="text-muted small mb-0">{{ $blog->meta_description }}</p>
                                        </div>
                                        @endif
                                    </div>
                                    @endif

                                    <div class="mb-3">
                                        <strong>Word Count:</strong>
                                        <span class="badge badge-info">{{ str_word_count(strip_tags($blog->content)) }} words</span>
                                    </div>

                                    <div class="mb-3">
                                        <strong>Reading Time:</strong>
                                        <span class="badge badge-info">
                                            {{ ceil(str_word_count(strip_tags($blog->content)) / 200) }} min read
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Actions</h5>
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('admin.blogs.edit', $blog->id) }}" 
                                           class="btn btn-primary btn-block">
                                            <i class="fas fa-edit"></i> Edit Post
                                        </a>
                                        
                                        @if($blog->status === 'draft')
                                        <form action="{{ route('admin.blogs.update', $blog->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="published">
                                            <button type="submit" class="btn btn-success btn-block">
                                                <i class="fas fa-publish"></i> Publish
                                            </button>
                                        </form>
                                        @elseif($blog->status === 'published')
                                        <form action="{{ route('admin.blogs.update', $blog->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="draft">
                                            <button type="submit" class="btn btn-warning btn-block">
                                                <i class="fas fa-unpublish"></i> Unpublish
                                            </button>
                                        </form>
                                        @endif

                                        <form action="{{ route('admin.blogs.destroy', $blog->id) }}" 
                                              method="POST" 
                                              class="d-inline"
                                              onsubmit="return confirm('Are you sure you want to delete this blog post?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-block">
                                                <i class="fas fa-trash"></i> Delete Post
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

<style>
.blog-content h2 {
    color: #333;
    border-bottom: 2px solid #007bff;
    padding-bottom: 10px;
}

.meta-info p {
    margin-bottom: 8px;
}

.content-body {
    line-height: 1.8;
    color: #333;
}

.content-body img {
    max-width: 100%;
    height: auto;
    border-radius: 8px;
    margin: 15px 0;
}

.badge {
    font-size: 0.875em;
}

.featured-image img {
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.card {
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    border: none;
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}
</style>
@endsection
