@extends('master.master')

@section('content')
    <div class="page-content">

        <nav class="page-breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Management</a></li>
                <li class="breadcrumb-item active" aria-current="page">Meeting Bookings</li>
            </ol>
        </nav>

        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h6 class="card-title">Meeting Booking Requests</h6>
                        </div>

                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                {{ session('success') }}
                            </div>
                        @endif

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
                                                <i data-feather="calendar" class="text-white-50"></i>
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
                                                <h3 class="mb-0">{{ $stats['new'] }}</h3>
                                                <p class="mb-0">New</p>
                                            </div>
                                            <div class="align-self-center">
                                                <i data-feather="bell" class="text-white-50"></i>
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
                                                <h3 class="mb-0">{{ $stats['replied'] }}</h3>
                                                <p class="mb-0">Replied</p>
                                            </div>
                                            <div class="align-self-center">
                                                <i data-feather="message-circle" class="text-white-50"></i>
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
                                                <h3 class="mb-0">{{ $stats['today'] }}</h3>
                                                <p class="mb-0">Today</p>
                                            </div>
                                            <div class="align-self-center">
                                                <i data-feather="clock" class="text-white-50"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Filters and Search -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <form method="GET" action="{{ route('admin.meeting-bookings.index') }}" class="d-flex">
                                    <input type="text" name="search" class="form-control me-2" placeholder="Search name, email, phone..." value="{{ request('search') }}">
                                    <button type="submit" class="btn btn-primary">
                                        <i data-feather="search"></i>
                                    </button>
                                </form>
                            </div>
                            <div class="col-md-6">
                                <form method="GET" action="{{ route('admin.meeting-bookings.index') }}" class="d-flex justify-content-end">
                                    <select name="status" class="form-control me-2" onchange="this.form.submit()">
                                        <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>All Status</option>
                                        <option value="new" {{ request('status') == 'new' ? 'selected' : '' }}>New</option>
                                        <option value="read" {{ request('status') == 'read' ? 'selected' : '' }}>Read</option>
                                        <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                        <option value="replied" {{ request('status') == 'replied' ? 'selected' : '' }}>Replied</option>
                                        <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                                    </select>
                                </form>
                            </div>
                        </div>

                        <!-- Table -->
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>#ID</th>
                                        <th>Name</th>
                                        <th>Email / Phone</th>
                                        <th>Preferred Date</th>
                                        <th>Preferred Time</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($bookings as $booking)
                                    <tr>
                                        <td>{{ $booking->id }}</td>
                                        <td>
                                            <strong>{{ $booking->name }}</strong>
                                            @if($booking->company)
                                                <br><small class="text-muted">{{ $booking->company }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="mailto:{{ $booking->email }}">{{ $booking->email }}</a><br>
                                            <small>{{ $booking->phone }}</small>
                                        </td>
                                        <td>{{ $booking->preferred_date->format('M j, Y') }}</td>
                                        <td>{{ $booking->preferred_time }}</td>
                                        <td>
                                            @switch($booking->status)
                                                @case('new')
                                                    <span class="badge bg-warning">New</span>
                                                    @break
                                                @case('read')
                                                    <span class="badge bg-info">Read</span>
                                                    @break
                                                @case('confirmed')
                                                    <span class="badge bg-primary">Confirmed</span>
                                                    @break
                                                @case('replied')
                                                    <span class="badge bg-success">Replied</span>
                                                    @break
                                                @case('closed')
                                                    <span class="badge bg-secondary">Closed</span>
                                                    @break
                                            @endswitch
                                        </td>
                                        <td>{{ $booking->created_at->format('M j, Y g:i A') }}</td>
                                        <td>
                                            <a href="{{ route('admin.meeting-bookings.show', $booking) }}" class="btn btn-info btn-icon" title="View">
                                                <i data-feather="eye"></i>
                                            </a>
                                            <a href="{{ route('admin.meeting-bookings.edit', $booking) }}" class="btn btn-warning btn-icon" title="Edit">
                                                <i data-feather="edit"></i>
                                            </a>
                                            <form action="{{ route('admin.meeting-bookings.destroy', $booking) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-icon" title="Delete" onclick="return confirm('Are you sure you want to delete this meeting booking?')">
                                                    <i data-feather="trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="8" class="text-center">No meeting bookings found.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-center mt-3">
                            {{ $bookings->appends(request()->query())->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
