@extends('master.master')

@section('content')
    <div class="page-content">

        <nav class="page-breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.meeting-bookings.index') }}">Meeting Bookings</a></li>
                <li class="breadcrumb-item active" aria-current="page">Booking #{{ $meeting_booking->id }}</li>
            </ol>
        </nav>

        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h6 class="card-title">Meeting Booking Details</h6>
                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.meeting-bookings.edit', $meeting_booking) }}" class="btn btn-warning btn-icon">
                                    <i data-feather="edit"></i> Edit
                                </a>
                                <a href="{{ route('admin.meeting-bookings.index') }}" class="btn btn-secondary btn-icon">
                                    <i data-feather="arrow-left"></i>
                                </a>
                            </div>
                        </div>

                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                {{ session('success') }}
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-md-8">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title">Booking Information</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label><strong>Name:</strong></label>
                                                    <p class="form-control-static mb-0">{{ $meeting_booking->name }}</p>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label><strong>Email:</strong></label>
                                                    <p class="form-control-static mb-0">
                                                        <a href="mailto:{{ $meeting_booking->email }}">{{ $meeting_booking->email }}</a>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label><strong>Phone:</strong></label>
                                                    <p class="form-control-static mb-0">
                                                        <a href="tel:{{ $meeting_booking->phone }}">{{ $meeting_booking->phone }}</a>
                                                    </p>
                                                </div>
                                            </div>
                                            @if($meeting_booking->company)
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label><strong>Company:</strong></label>
                                                    <p class="form-control-static mb-0">{{ $meeting_booking->company }}</p>
                                                </div>
                                            </div>
                                            @endif
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label><strong>Preferred Date:</strong></label>
                                                    <p class="form-control-static mb-0">{{ $meeting_booking->preferred_date->format('l, F j, Y') }}</p>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label><strong>Preferred Time:</strong></label>
                                                    <p class="form-control-static mb-0">{{ $meeting_booking->preferred_time }}</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group mb-3">
                                            <label><strong>Subject:</strong></label>
                                            <p class="form-control-static mb-0">{{ $meeting_booking->subject }}</p>
                                        </div>
                                        <div class="form-group mb-3">
                                            <label><strong>Message / Topic:</strong></label>
                                            <div class="form-control-static" style="white-space: pre-wrap; padding: 15px; border-radius: 5px;">
                                                {{ $meeting_booking->message }}
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label><strong>Submitted:</strong></label>
                                                <p class="form-control-static mb-0">{{ $meeting_booking->created_at->format('F j, Y \a\t g:i A') }}</p>
                                            </div>
                                            @if($meeting_booking->replied_at)
                                            <div class="col-md-6">
                                                <label><strong>Replied:</strong></label>
                                                <p class="form-control-static mb-0">{{ $meeting_booking->replied_at->format('F j, Y \a\t g:i A') }}</p>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title">Status</h4>
                                    </div>
                                    <div class="card-body">
                                        @switch($meeting_booking->status)
                                            @case('new')
                                                <span class="badge bg-warning fs-6">New</span>
                                                @break
                                            @case('read')
                                                <span class="badge bg-info fs-6">Read</span>
                                                @break
                                            @case('confirmed')
                                                <span class="badge bg-primary fs-6">Confirmed</span>
                                                @break
                                            @case('replied')
                                                <span class="badge bg-success fs-6">Replied</span>
                                                @break
                                            @case('closed')
                                                <span class="badge bg-secondary fs-6">Closed</span>
                                                @break
                                        @endswitch

                                        @if($meeting_booking->admin_notes)
                                        <div class="mt-3">
                                            <label><strong>Admin Notes:</strong></label>
                                            <p class="form-control-static small text-muted" style="white-space: pre-wrap;">{{ $meeting_booking->admin_notes }}</p>
                                        </div>
                                        @endif

                                        <hr>
                                        <a href="mailto:{{ $meeting_booking->email }}?subject=Re: Meeting Request - {{ $meeting_booking->preferred_date->format('M j') }} at {{ $meeting_booking->preferred_time }}" class="btn btn-success btn-block w-100 mb-2">
                                            <i data-feather="mail"></i> Reply via Email
                                        </a>
                                        <a href="{{ route('admin.meeting-bookings.edit', $meeting_booking) }}" class="btn btn-primary btn-block w-100 mb-2">
                                            <i data-feather="edit"></i> Edit Status
                                        </a>
                                        <form action="{{ route('admin.meeting-bookings.destroy', $meeting_booking) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-block w-100" onclick="return confirm('Are you sure you want to delete this meeting booking?')">
                                                <i data-feather="trash"></i> Delete
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
@endsection
