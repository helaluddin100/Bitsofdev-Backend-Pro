@extends('master.master')

@section('content')
    <div class="page-content">

        <nav class="page-breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Management</a></li>
                <li class="breadcrumb-item active" aria-current="page">Newsletter Subscribers</li>
            </ol>
        </nav>

        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h6 class="card-title">Newsletter Subscribers</h6>
                        </div>

                        <!-- Statistics Cards -->
                        <div class="row mb-4">
                            <div class="col-lg-3 col-6">
                                <div class="card bg-info text-white">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h3 class="mb-0">{{ $stats['total'] }}</h3>
                                                <p class="mb-0">Total</p>
                                            </div>
                                            <div class="align-self-center">
                                                <i data-feather="users" class="text-white-50"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="card bg-success text-white">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h3 class="mb-0">{{ $stats['subscribed'] }}</h3>
                                                <p class="mb-0">Subscribed</p>
                                            </div>
                                            <div class="align-self-center">
                                                <i data-feather="mail" class="text-white-50"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="card bg-secondary text-white">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h3 class="mb-0">{{ $stats['unsubscribed'] }}</h3>
                                                <p class="mb-0">Unsubscribed</p>
                                            </div>
                                            <div class="align-self-center">
                                                <i data-feather="user-x" class="text-white-50"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="card bg-warning text-white">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h3 class="mb-0">{{ $stats['today'] }}</h3>
                                                <p class="mb-0">Today</p>
                                            </div>
                                            <div class="align-self-center">
                                                <i data-feather="calendar" class="text-white-50"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Filters and Search -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <form method="GET" action="{{ route('admin.newsletter-subscribers.index') }}" class="d-flex">
                                    <input type="text" name="search" class="form-control me-2" placeholder="Search by email..." value="{{ request('search') }}">
                                    <button type="submit" class="btn btn-primary">
                                        <i data-feather="search"></i>
                                    </button>
                                </form>
                            </div>
                            <div class="col-md-6">
                                <form method="GET" action="{{ route('admin.newsletter-subscribers.index') }}" class="d-flex justify-content-end">
                                    <select name="status" class="form-control me-2" onchange="this.form.submit()">
                                        <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>All Status</option>
                                        <option value="subscribed" {{ request('status') == 'subscribed' ? 'selected' : '' }}>Subscribed</option>
                                        <option value="unsubscribed" {{ request('status') == 'unsubscribed' ? 'selected' : '' }}>Unsubscribed</option>
                                    </select>
                                </form>
                            </div>
                        </div>

                        <!-- Table (no DataTables - preserve server order) -->
                        <div class="table-responsive">
                            <table id="newsletterSubscribersTable" class="table">
                                <thead>
                                    <tr>
                                        <th>#ID</th>
                                        <th>Email</th>
                                        <th>Status</th>
                                        <th>Subscribed At</th>
                                        <th>Updated At</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($subscribers as $subscriber)
                                    <tr>
                                        <td>{{ $subscriber->id }}</td>
                                        <td>
                                            <a href="mailto:{{ $subscriber->email }}">{{ $subscriber->email }}</a>
                                        </td>
                                        <td>
                                            @if($subscriber->status === 'subscribed')
                                                <span class="badge bg-success">Subscribed</span>
                                            @else
                                                <span class="badge bg-secondary">Unsubscribed</span>
                                            @endif
                                        </td>
                                        <td>{{ $subscriber->created_at->format('M j, Y g:i A') }}</td>
                                        <td>{{ $subscriber->updated_at->format('M j, Y g:i A') }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center">No newsletter subscribers found.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-center mt-3">
                            {{ $subscribers->appends(request()->query())->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
