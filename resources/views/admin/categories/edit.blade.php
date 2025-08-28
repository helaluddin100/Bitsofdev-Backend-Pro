@extends('master.master')

@section('content')
    <div class="page-content">

        <nav class="page-breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Forms</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edit Category</li>
            </ol>
        </nav>

        <div class="row">
            <div class="col-lg-6 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Edit Category</h4>

                        <form action="{{ route('admin.categories.update', $category->id) }}" method="Post">
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input id="name" class="form-control" name="name" type="text" value="{{ $category->name }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" name="description" id="easyMdeExample" rows="5">{{ $category->description }}</textarea>
                            </div>

                            <div class="mb-3">
                                <label for="color" class="form-label">Color</label>
                                <input type="color" class="form-control" name="color" id="color" value="{{ $category->color }}" required>
                            </div>

                            <div class="mb-3">
                                <div class="form-check">
                                    <label class="form-check-label" for="is_active">
                                        Active
                                    </label>
                                    <input type="checkbox" class="form-check-input" name="is_active" id="is_active" value="1" {{ $category->is_active ? 'checked' : '' }}>
                                </div>
                            </div>

                            <input class="btn btn-primary" type="submit" value="Update">
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
