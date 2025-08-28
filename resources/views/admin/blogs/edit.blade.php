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

                        <form action="{{ route('admin.blogs.update', $blog->id) }}" method="Post" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <label for="title" class="form-label">Title</label>
                                <input id="title" class="form-control" name="title" type="text" value="{{ $blog->title }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="category_id" class="form-label">Category</label>
                                <select class="form-control" name="category_id" id="category_id" required>
                                    <option value="">Select Category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ $blog->category_id == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="excerpt" class="form-label">Excerpt</label>
                                <textarea class="form-control" name="excerpt" id="excerpt" rows="3" required>{{ $blog->excerpt }}</textarea>
                            </div>

                            <div class="mb-3">
                                <label for="content" class="form-label">Content</label>
                                <textarea class="form-control" name="content" id="easyMdeExample" rows="10" required>{{ $blog->content }}</textarea>
                            </div>

                            <div class="mb-3">
                                <label for="featured_image" class="form-label">Featured Image</label>
                                @if($blog->featured_image)
                                    <div class="mb-2">
                                        <img src="{{ asset($blog->featured_image) }}" alt="Current Image" style="max-width: 200px; height: auto;" class="img-thumbnail">
                                    </div>
                                @endif
                                <input type="file" class="form-control" name="featured_image" id="featured_image" accept="image/*">
                            </div>

                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-control" name="status" id="status" required>
                                    <option value="draft" {{ $blog->status == 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="published" {{ $blog->status == 'published' ? 'selected' : '' }}>Published</option>
                                    <option value="archived" {{ $blog->status == 'archived' ? 'selected' : '' }}>Archived</option>
                                </select>
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
                                <input id="meta_title" class="form-control" name="meta_title" type="text" value="{{ $blog->meta_title }}">
                            </div>

                            <div class="mb-3">
                                <label for="meta_description" class="form-label">Meta Description</label>
                                <textarea class="form-control" name="meta_description" id="meta_description" rows="3">{{ $blog->meta_description }}</textarea>
                            </div>

                            <input class="btn btn-primary" type="submit" value="Update">
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
