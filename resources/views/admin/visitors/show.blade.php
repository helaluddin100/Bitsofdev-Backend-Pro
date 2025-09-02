@extends('master.master')

@section('title', 'Visitor Details')

@section('content')
    <div class="page-wrapper">
        <div class="page-content">
            <div class="row">
                <div class="col-12">
                    <!-- Back Button -->
                    <div class="mb-3">
                        <a href="{{ route('admin.visitors.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Back to Visitors
                        </a>
                    </div>

                    <!-- Visitor Details Card -->
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">
                                <i class="fas fa-user me-2"></i>Visitor Details
                            </h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <!-- Basic Information -->
                                <div class="col-md-6">
                                    <h5 class="text-primary mb-3">
                                        <i class="fas fa-info-circle me-1"></i>Basic Information
                                    </h5>
                                    <table class="table table-borderless">
                                        <tr>
                                            <td class="fw-bold">ID:</td>
                                            <td><span class="badge bg-primary">{{ $visitor->id }}</span></td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Visitor ID:</td>
                                            <td><code>{{ $visitor->visitor_id }}</code></td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Session ID:</td>
                                            <td><code>{{ $visitor->session_id ?? 'N/A' }}</code></td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">IP Address:</td>
                                            <td><code>{{ $visitor->ip }}</code></td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Created At:</td>
                                            <td>{{ $visitor->created_at->format('M d, Y H:i:s') }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Updated At:</td>
                                            <td>{{ $visitor->updated_at->format('M d, Y H:i:s') }}</td>
                                        </tr>
                                    </table>
                                </div>

                                <!-- Device Information -->
                                <div class="col-md-6">
                                    <h5 class="text-success mb-3">
                                        <i class="fas fa-mobile-alt me-1"></i>Device Information
                                    </h5>
                                    <table class="table table-borderless">
                                        <tr>
                                            <td class="fw-bold">Device Type:</td>
                                            <td>
                                                <span class="badge bg-primary">{{ $visitor->device ?? 'Unknown' }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Browser:</td>
                                            <td>{{ $visitor->browser ?? 'Unknown' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Operating System:</td>
                                            <td>{{ $visitor->os ?? 'Unknown' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">ISP:</td>
                                            <td>{{ $visitor->isp ?? 'Unknown' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <hr class="my-4">

                            <!-- Location Information -->
                            <div class="row">
                                <div class="col-12">
                                    <h5 class="text-info mb-3">
                                        <i class="fas fa-map-marker-alt me-1"></i>Location Information
                                    </h5>
                                    @if ($visitor->location && is_array($visitor->location))
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="card bg-info text-white">
                                                    <div class="card-body text-center">
                                                        <h6 class="card-title">Country</h6>
                                                        <h4>{{ $visitor->location['country'] ?? 'Unknown' }}</h4>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="card bg-success text-white">
                                                    <div class="card-body text-center">
                                                        <h6 class="card-title">City</h6>
                                                        <h4>{{ $visitor->location['city'] ?? 'Unknown' }}</h4>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="card bg-warning text-white">
                                                    <div class="card-body text-center">
                                                        <h6 class="card-title">Region</h6>
                                                        <h4>{{ $visitor->location['region'] ?? 'Unknown' }}</h4>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="card bg-secondary text-white">
                                                    <div class="card-body text-center">
                                                        <h6 class="card-title">Timezone</h6>
                                                        <h4>{{ $visitor->location['timezone'] ?? 'Unknown' }}</h4>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        @if (isset($visitor->location['lat']) && isset($visitor->location['lon']))
                                            <div class="mt-3">
                                                <h6 class="text-muted">Coordinates:</h6>
                                                <code>{{ $visitor->location['lat'] }},
                                                    {{ $visitor->location['lon'] }}</code>
                                            </div>
                                        @endif
                                    @else
                                        <div class="alert alert-warning">
                                            <i class="fas fa-exclamation-triangle me-1"></i>
                                            No location data available for this visitor.
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <hr class="my-4">

                            <!-- Page Information -->
                            <div class="row">
                                <div class="col-12">
                                    <h5 class="text-warning mb-3">
                                        <i class="fas fa-globe me-1"></i>Page Information
                                    </h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6>Page URL:</h6>
                                            <div class="p-3 bg-light rounded">
                                                <a href="{{ $visitor->page_url }}" target="_blank" class="text-break">
                                                    {{ $visitor->page_url }}
                                                </a>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <h6>Referrer:</h6>
                                            <div class="p-3 bg-light rounded">
                                                @if ($visitor->referrer)
                                                    <a href="{{ $visitor->referrer }}" target="_blank" class="text-break">
                                                        {{ $visitor->referrer }}
                                                    </a>
                                                @else
                                                    <span class="text-muted">Direct Visit</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">

                            <!-- Time Information -->
                            <div class="row">
                                <div class="col-md-6">
                                    <h5 class="text-danger mb-3">
                                        <i class="fas fa-clock me-1"></i>Time Information
                                    </h5>
                                    <table class="table table-borderless">
                                        <tr>
                                            <td class="fw-bold">Page Entered:</td>
                                            <td>{{ $visitor->page_entered_at?->format('M d, Y H:i:s') ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Page Exited:</td>
                                            <td>{{ $visitor->page_exited_at?->format('M d, Y H:i:s') ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Time Spent:</td>
                                            <td>
                                                @if ($visitor->time_spent)
                                                    <span class="badge bg-warning fs-6">
                                                        {{ gmdate('H:i:s', $visitor->time_spent) }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </div>

                                <!-- Actions Information -->
                                <div class="col-md-6">
                                    <h5 class="text-purple mb-3">
                                        <i class="fas fa-mouse-pointer me-1"></i>User Actions
                                    </h5>
                                    @if ($visitor->actions && is_array($visitor->actions) && count($visitor->actions) > 0)
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>Action</th>
                                                        <th>Details</th>
                                                        <th>Time</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($visitor->actions as $action)
                                                        <tr>
                                                            <td>
                                                                <span
                                                                    class="badge bg-info">{{ $action['type'] ?? 'Unknown' }}</span>
                                                            </td>
                                                            <td>
                                                                @if (isset($action['text']))
                                                                    {{ $action['text'] }}
                                                                @elseif(isset($action['url']))
                                                                    <a href="{{ $action['url'] }}" target="_blank">
                                                                        {{ Str::limit($action['url'], 30) }}
                                                                    </a>
                                                                @else
                                                                    <span class="text-muted">No details</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if (isset($action['timestamp']))
                                                                    {{ \Carbon\Carbon::createFromTimestampMs($action['timestamp'])->format('H:i:s') }}
                                                                @else
                                                                    <span class="text-muted">N/A</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle me-1"></i>
                                            No user actions recorded for this visitor.
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('admin.visitors.index') }}" class="btn btn-secondary">
                                            <i class="fas fa-arrow-left me-1"></i> Back to List
                                        </a>
                                        <form action="{{ route('admin.visitors.destroy', $visitor) }}" method="POST"
                                            class="d-inline delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger"
                                                onclick="return confirm('Are you sure you want to delete this visitor record?')">
                                                <i class="fas fa-trash me-1"></i> Delete Record
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
