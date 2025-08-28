@extends('master.master')

@section('content')
    <div class="page-content">

        <nav class="page-breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Tables</a></li>
                <li class="breadcrumb-item active" aria-current="page">Pricing Management</li>
            </ol>
        </nav>

        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h6 class="card-title">Pricing Plans</h6>
                            <div class="create-button">
                                <a href="{{ route('admin.pricing.create') }}" class="btn btn-primary btn-icon">
                                    <i data-feather="plus-circle"></i>
                                </a>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table id="dataTableExample" class="table">
                                <thead>
                                    <tr>
                                        <th>#ID</th>
                                        <th>Name</th>
                                        <th>Price</th>
                                        <th>Billing Cycle</th>
                                        <th>Features Count</th>
                                        <th>Popular</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($plans as $key => $plan)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $plan->name }}</td>
                                            <td>
                                                <span class="badge bg-success">
                                                    {{ $plan->currency }} {{ number_format($plan->price, 2) }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">{{ ucfirst($plan->billing_cycle) }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary">{{ $plan->features->count() }}</span>
                                            </td>
                                            <td>
                                                @if ($plan->is_popular)
                                                    <span class="badge bg-warning">Popular</span>
                                                @else
                                                    <span class="badge bg-light text-dark">Regular</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($plan->is_active)
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-danger">Inactive</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.pricing.edit', $plan->id) }}"
                                                    class="btn btn-primary btn-icon">
                                                    <i data-feather="edit"></i>
                                                </a>

                                                @if (Auth::user()->role_id == 1)
                                                    <form id="delete_form_{{ $plan->id }}"
                                                        action="{{ route('admin.pricing.destroy', $plan->id) }}"
                                                        method="post" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" class="btn btn-danger btn-icon delete-button"
                                                            onclick="deleteId({{ $plan->id }})">
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
