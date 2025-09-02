@extends('master.master')



@section('content')
    <div class="page-content">

        <nav class="page-breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Management</a></li>
                <li class="breadcrumb-item active" aria-current="page">Contact Management</li>
            </ol>
        </nav>

        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h6 class="card-title">Contact Form Submissions</h6>
                            <div class="export-button">
                                <a href="{{ route('admin.contacts.export') }}" class="btn btn-success btn-icon">
                                    <i data-feather="download"></i>
                                </a>
                            </div>
                        </div>
                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-lg-3 col-6">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h3 class="mb-0">{{ $stats['total'] }}</h3>
                                            <p class="mb-0">Total Contacts</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i data-feather="mail" class="text-white-50"></i>
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
                                            <p class="mb-0">New Messages</p>
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
                            <form method="GET" action="{{ route('admin.contacts.index') }}" class="d-flex">
                                <input type="text" name="search" class="form-control me-2" placeholder="Search contacts..." value="{{ request('search') }}">
                                <button type="submit" class="btn btn-primary">
                                    <i data-feather="search"></i>
                                </button>
                            </form>
                        </div>
                        <div class="col-md-6">
                            <form method="GET" action="{{ route('admin.contacts.index') }}" class="d-flex justify-content-end">
                                <select name="status" class="form-control me-2" onchange="this.form.submit()">
                                    <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>All Status</option>
                                    <option value="new" {{ request('status') == 'new' ? 'selected' : '' }}>New</option>
                                    <option value="read" {{ request('status') == 'read' ? 'selected' : '' }}>Read</option>
                                    <option value="replied" {{ request('status') == 'replied' ? 'selected' : '' }}>Replied</option>
                                    <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                                </select>
                            </form>
                        </div>
                    </div>

                    <!-- Contacts Table -->
                    <div class="table-responsive">
                        <table id="dataTableExample" class="table">
                            <thead>
                                <tr>
                                    <th>#ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Subject</th>
                                    <th>Project Type</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($contacts as $contact)
                                <tr>
                                    <td>{{ $contact->id }}</td>
                                    <td>
                                        <strong>{{ $contact->name }}</strong>
                                        @if($contact->company)
                                            <br><small class="text-muted">{{ $contact->company }}</small>
                                        @endif
                                    </td>
                                    <td>{{ $contact->email }}</td>
                                    <td>{{ Str::limit($contact->subject, 50) }}</td>
                                    <td>
                                        <span class="badge bg-info">
                                            {{ ucwords(str_replace('-', ' ', $contact->project_type)) }}
                                        </span>
                                    </td>
                                    <td>
                                        @switch($contact->status)
                                            @case('new')
                                                <span class="badge bg-warning">New</span>
                                                @break
                                            @case('read')
                                                <span class="badge bg-info">Read</span>
                                                @break
                                            @case('replied')
                                                <span class="badge bg-success">Replied</span>
                                                @break
                                            @case('closed')
                                                <span class="badge bg-secondary">Closed</span>
                                                @break
                                        @endswitch
                                    </td>
                                    <td>{{ $contact->created_at->format('M j, Y g:i A') }}</td>
                                    <td>
                                        <a href="{{ route('admin.contacts.show', $contact) }}" class="btn btn-info btn-icon">
                                            <i data-feather="eye"></i>
                                        </a>
                                        <form action="{{ route('admin.contacts.destroy', $contact) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-icon" onclick="return confirm('Are you sure?')">
                                                <i data-feather="trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center">No contacts found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $contacts->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
