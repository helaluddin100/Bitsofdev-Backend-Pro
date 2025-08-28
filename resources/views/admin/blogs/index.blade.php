@extends('master.master')

@section('content')
    <div class="page-content">

        <nav class="page-breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Tables</a></li>
                <li class="breadcrumb-item active" aria-current="page">Blog Management</li>
            </ol>
        </nav>

        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h6 class="card-title">Blog Posts</h6>
                            <div class="create-button">
                                <a href="{{ route('admin.blogs.create') }}" class="btn btn-primary btn-icon">
                                    <i data-feather="plus-circle"></i>
                                </a>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table id="dataTableExample" class="table">
                                <thead>
                                    <tr>
                                        <th>#ID</th>
                                        <th>Title</th>
                                        <th>Category</th>
                                        <th>Author</th>
                                        <th>Status</th>
                                        <th>Featured</th>
                                        <th>Views</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($blogs as $key => $blog)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $blog->title }}</td>
                                            <td>
                                                <span class="badge" style="background-color: {{ $blog->category->color }}">
                                                    {{ $blog->category->name }}
                                                </span>
                                            </td>
                                            <td>{{ $blog->user->name }}</td>
                                            <td>
                                                @if ($blog->status === 'published')
                                                    <span class="badge bg-success">Published</span>
                                                @elseif ($blog->status === 'draft')
                                                    <span class="badge bg-warning">Draft</span>
                                                @else
                                                    <span class="badge bg-secondary">Archived</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($blog->is_featured)
                                                    <span class="badge bg-info">Featured</span>
                                                @else
                                                    <span class="badge bg-light text-dark">Regular</span>
                                                @endif
                                            </td>
                                            <td>{{ $blog->views }}</td>
                                            <td>
                                                <a href="{{ route('admin.blogs.show', $blog->id) }}"
                                                    class="btn btn-info btn-icon">
                                                    <i data-feather="eye"></i>
                                                </a>
                                                <a href="{{ route('admin.blogs.edit', $blog->id) }}"
                                                    class="btn btn-primary btn-icon">
                                                    <i data-feather="edit"></i>
                                                </a>

                                                @if (Auth::user()->role_id == 1)
                                                    <form id="delete_form_{{ $blog->id }}"
                                                        action="{{ route('admin.blogs.destroy', $blog->id) }}"
                                                        method="post" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" class="btn btn-danger btn-icon delete-button"
                                                            onclick="deleteId({{ $blog->id }})">
                                                            <i data-feather="trash"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
