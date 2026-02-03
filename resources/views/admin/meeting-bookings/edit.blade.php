@extends('master.master')

@section('content')
    <div class="page-content">

        <nav class="page-breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.meeting-bookings.index') }}">Meeting Bookings</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.meeting-bookings.show', $meeting_booking) }}">#{{ $meeting_booking->id }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edit</li>
            </ol>
        </nav>

        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h6 class="card-title">Edit Meeting Booking #{{ $meeting_booking->id }}</h6>
                            <a href="{{ route('admin.meeting-bookings.show', $meeting_booking) }}" class="btn btn-secondary btn-icon">
                                <i data-feather="arrow-left"></i> Back
                            </a>
                        </div>

                        @if($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-md-8">
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <p class="text-muted mb-0"><strong>{{ $meeting_booking->name }}</strong> — {{ $meeting_booking->email }} — {{ $meeting_booking->preferred_date->format('M j, Y') }} at {{ $meeting_booking->preferred_time }}</p>
                                    </div>
                                </div>

                                <form action="{{ route('admin.meeting-bookings.update', $meeting_booking) }}" method="POST">
                                    @csrf
                                    @method('PUT')

                                    <div class="form-group mb-3">
                                        <label for="status"><strong>Status:</strong></label>
                                        <select name="status" id="status" class="form-control" required>
                                            <option value="new" {{ old('status', $meeting_booking->status) == 'new' ? 'selected' : '' }}>New</option>
                                            <option value="read" {{ old('status', $meeting_booking->status) == 'read' ? 'selected' : '' }}>Read</option>
                                            <option value="confirmed" {{ old('status', $meeting_booking->status) == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                            <option value="replied" {{ old('status', $meeting_booking->status) == 'replied' ? 'selected' : '' }}>Replied</option>
                                            <option value="closed" {{ old('status', $meeting_booking->status) == 'closed' ? 'selected' : '' }}>Closed</option>
                                        </select>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="admin_notes"><strong>Admin Notes:</strong></label>
                                        <textarea name="admin_notes" id="admin_notes" rows="5" class="form-control" placeholder="Add internal notes about this booking...">{{ old('admin_notes', $meeting_booking->admin_notes) }}</textarea>
                                    </div>

                                    <button type="submit" class="btn btn-primary">
                                        <i data-feather="save"></i> Update Booking
                                    </button>
                                    <a href="{{ route('admin.meeting-bookings.show', $meeting_booking) }}" class="btn btn-secondary">Cancel</a>
                                </form>
                            </div>

                            <div class="col-md-4">
                                <div class="card border-danger">
                                    <div class="card-header bg-danger text-white">
                                        <h6 class="card-title mb-0">Danger Zone</h6>
                                    </div>
                                    <div class="card-body">
                                        <p class="small text-muted">Deleting this booking cannot be undone.</p>
                                        <form action="{{ route('admin.meeting-bookings.destroy', $meeting_booking) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger w-100" onclick="return confirm('Are you sure you want to delete this meeting booking? This action cannot be undone.')">
                                                <i data-feather="trash"></i> Delete Booking
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
