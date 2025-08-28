@extends('master.master')

@section('content')
    <div class="page-content">

        <nav class="page-breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Tables</a></li>
                <li class="breadcrumb-item active" aria-current="page">Team Management</li>
            </ol>
        </nav>

        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h6 class="card-title">Team Members</h6>
                            <div class="create-button">
                                <a href="{{ route('admin.teams.create') }}" class="btn btn-primary btn-icon">
                                    <i data-feather="plus-circle"></i>
                                </a>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table id="dataTableExample" class="table">
                                <thead>
                                    <tr>
                                        <th>#ID</th>
                                        <th>Avatar</th>
                                        <th>Name</th>
                                        <th>Position</th>
                                        <th>Order</th>
                                        <th>Featured</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($teams as $key => $member)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>
                                                @if($member->avatar)
                                                    <img src="{{ asset($member->avatar) }}" alt="{{ $member->name }}" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                                                @else
                                                    <div style="width: 40px; height: 40px; border-radius: 50%; background-color: #e9ecef; display: flex; align-items: center; justify-content: center; color: #6c757d;">
                                                        <i data-feather="user"></i>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>{{ $member->name }}</td>
                                            <td>{{ $member->position }}</td>
                                            <td>
                                                <span class="badge bg-primary">{{ $member->order }}</span>
                                            </td>
                                            <td>
                                                @if ($member->is_featured)
                                                    <span class="badge bg-info">Featured</span>
                                                @else
                                                    <span class="badge bg-light text-dark">Regular</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($member->is_active)
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-danger">Inactive</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.teams.edit', $member->id) }}"
                                                    class="btn btn-primary btn-icon">
                                                    <i data-feather="edit"></i>
                                                </a>

                                                @if (Auth::user()->role_id == 1)
                                                    <form id="delete_form_{{ $member->id }}"
                                                        action="{{ route('admin.teams.destroy', $member->id) }}"
                                                        method="post" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" class="btn btn-danger btn-icon delete-button"
                                                            onclick="deleteId({{ $member->id }})">
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
