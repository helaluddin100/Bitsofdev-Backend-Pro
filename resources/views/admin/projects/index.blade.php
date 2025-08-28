@extends('master.master')

@section('content')
    <div class="page-content">

        <nav class="page-breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Tables</a></li>
                <li class="breadcrumb-item active" aria-current="page">Project Management</li>
            </ol>
        </nav>

        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h6 class="card-title">Portfolio Projects</h6>
                            <div class="create-button">
                                <a href="{{ route('admin.projects.create') }}" class="btn btn-primary btn-icon">
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
                                        <th>Client</th>
                                        <th>Status</th>
                                        <th>Priority</th>
                                        <th>Featured</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($projects as $key => $project)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $project->title }}</td>
                                            <td>{{ $project->client ?? 'N/A' }}</td>
                                            <td>
                                                @if ($project->status === 'completed')
                                                    <span class="badge bg-success">Completed</span>
                                                @elseif ($project->status === 'in_progress')
                                                    <span class="badge bg-warning">In Progress</span>
                                                @elseif ($project->status === 'planning')
                                                    <span class="badge bg-info">Planning</span>
                                                @else
                                                    <span class="badge bg-secondary">On Hold</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-primary">{{ $project->priority }}</span>
                                            </td>
                                            <td>
                                                @if ($project->is_featured)
                                                    <span class="badge bg-info">Featured</span>
                                                @else
                                                    <span class="badge bg-light text-dark">Regular</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.projects.edit', $project->id) }}"
                                                    class="btn btn-primary btn-icon">
                                                    <i data-feather="edit"></i>
                                                </a>

                                                @if (Auth::user()->role_id == 1)
                                                    <form id="delete_form_{{ $project->id }}"
                                                        action="{{ route('admin.projects.destroy', $project->id) }}"
                                                        method="post" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" class="btn btn-danger btn-icon delete-button"
                                                            onclick="deleteId({{ $project->id }})">
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
