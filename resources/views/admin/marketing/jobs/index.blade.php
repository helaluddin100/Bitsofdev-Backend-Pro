@extends('master.master')

@section('title', 'Jobs Monitoring')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Jobs Monitoring</h1>
        <div class="btn-group">
            <button type="button" class="btn btn-primary" onclick="refreshJobs()">
                <i class="fas fa-sync-alt"></i> Refresh
            </button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Pending Jobs
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="statPending">{{ $stats['pending'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Processing
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="statProcessing">{{ $stats['processing'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-spinner fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Failed Jobs
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="statFailed">{{ $stats['failed'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Emails Sent Today
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="statEmailsSent">{{ $emailStats['sent_today'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-envelope fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Email Statistics -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Emails Sent
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $emailStats['total_sent'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Total Emails Failed
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $emailStats['total_failed'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Jobs -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Pending Jobs</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="jobsTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Job Class</th>
                            <th>Queue</th>
                            <th>Attempts</th>
                            <th>Status</th>
                            <th>Created At</th>
                            <th>Available At</th>
                        </tr>
                    </thead>
                    <tbody id="jobsTableBody">
                        @foreach($jobs as $job)
                        <tr>
                            <td>{{ $job->id }}</td>
                            <td>
                                @php
                                    $payload = json_decode($job->payload, true);
                                    $jobClass = $payload['displayName'] ?? 'Unknown';
                                @endphp
                                {{ $jobClass }}
                            </td>
                            <td>{{ $job->queue }}</td>
                            <td>{{ $job->attempts }}</td>
                            <td>
                                @if($job->reserved_at)
                                    <span class="badge badge-warning">Processing</span>
                                @else
                                    <span class="badge badge-info">Pending</span>
                                @endif
                            </td>
                            <td>{{ date('Y-m-d H:i:s', $job->created_at) }}</td>
                            <td>{{ date('Y-m-d H:i:s', $job->available_at) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Failed Emails -->
    @if(isset($failedEmails) && $failedEmails->count() > 0)
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-danger">Failed Emails ({{ $failedEmails->count() }})</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="failedEmailsTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Campaign</th>
                            <th>Lead Name</th>
                            <th>Email Address</th>
                            <th>Failed At</th>
                            <th>Failure Reason</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="failedEmailsTableBody">
                        @foreach($failedEmails as $email)
                        <tr>
                            <td>{{ $email['id'] }}</td>
                            <td>{{ $email['campaign_name'] }}</td>
                            <td>{{ $email['lead_name'] }}</td>
                            <td>{{ $email['email_address'] }}</td>
                            <td>{{ \Carbon\Carbon::parse($email['failed_at'])->format('M d, Y h:i A') }}</td>
                            <td>
                                <small class="text-danger">{{ Str::limit($email['failure_reason'], 100) }}</small>
                            </td>
                            <td>
                                <form method="POST" action="{{ route('admin.marketing.jobs.resend-failed-email', $email['id']) }}" class="d-inline" onsubmit="return confirm('Are you sure you want to resend this email?')">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success" title="Resend Email">
                                        <i data-feather="refresh-cw"></i>
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.marketing.jobs.delete-failed-email', $email['id']) }}" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this failed email record?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                        <i data-feather="trash-2"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    <!-- Failed Jobs -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-danger">Failed Jobs (Queue)</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="failedJobsTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Job Class</th>
                            <th>Queue</th>
                            <th>Failed At</th>
                            <th>Exception</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="failedJobsTableBody">
                        @if($failedJobs->count() > 0)
                        @foreach($failedJobs as $job)
                        <tr>
                            <td>{{ $job->id }}</td>
                            <td>
                                @php
                                    $payload = json_decode($job->payload, true);
                                    $jobClass = $payload['displayName'] ?? 'Unknown';
                                @endphp
                                {{ $jobClass }}
                            </td>
                            <td>{{ $job->queue }}</td>
                            <td>{{ $job->failed_at }}</td>
                            <td>
                                <small class="text-danger">{{ Str::limit($job->exception, 100) }}</small>
                            </td>
                            <td>
                                <form method="POST" action="{{ route('admin.marketing.jobs.retry', $job->id) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-warning" title="Retry">
                                        <i data-feather="refresh-cw"></i>
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.marketing.jobs.delete-failed', $job->id) }}" class="d-inline" onsubmit="return confirm('Are you sure?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                        <i data-feather="trash-2"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                        @else
                        <tr>
                            <td colspan="6" class="text-center">No failed jobs in queue</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let refreshInterval;

    $(document).ready(function() {
        // Auto refresh every 5 seconds
        refreshInterval = setInterval(refreshJobs, 5000);
        
        // Initial load
        refreshJobs();
    });

    function refreshJobs() {
        $.ajax({
            url: '{{ route("admin.marketing.jobs.data") }}',
            method: 'GET',
            success: function(response) {
                // Update stats
                $('#statPending').text(response.stats.pending || 0);
                $('#statProcessing').text(response.stats.processing || 0);
                $('#statFailed').text(response.stats.failed || 0);
                
                // Update email stats if available
                if (response.emailStats) {
                    $('#statEmailsSent').text(response.emailStats.sent_today || 0);
                }

                // Update jobs table
                let jobsHtml = '';
                if (response.jobs.length === 0) {
                    jobsHtml = '<tr><td colspan="7" class="text-center">No pending jobs</td></tr>';
                } else {
                    response.jobs.forEach(function(job) {
                        jobsHtml += `
                            <tr>
                                <td>${job.id}</td>
                                <td>${job.job_class}</td>
                                <td>${job.queue}</td>
                                <td>${job.attempts}</td>
                                <td>
                                    <span class="badge badge-${job.status === 'processing' ? 'warning' : 'info'}">
                                        ${job.status === 'processing' ? 'Processing' : 'Pending'}
                                    </span>
                                </td>
                                <td>${job.created_at}</td>
                                <td>${job.available_at}</td>
                            </tr>
                        `;
                    });
                }
                $('#jobsTableBody').html(jobsHtml);

                // Update failed emails table
                if (response.failedEmails && response.failedEmails.length > 0) {
                    let failedEmailsHtml = '';
                    response.failedEmails.forEach(function(email) {
                        failedEmailsHtml += `
                            <tr>
                                <td>${email.id}</td>
                                <td>${email.campaign_name || 'N/A'}</td>
                                <td>${email.lead_name || 'N/A'}</td>
                                <td>${email.email_address || 'N/A'}</td>
                                <td>${email.failed_at || 'N/A'}</td>
                                <td><small class="text-danger">${email.failure_reason || 'Unknown error'}</small></td>
                                <td>
                                    <form method="POST" action="/admin/marketing/jobs/failed-email/${email.id}/resend" class="d-inline" onsubmit="return confirm('Are you sure you want to resend this email?')">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <button type="submit" class="btn btn-sm btn-success" title="Resend Email">
                                            <i data-feather="refresh-cw"></i>
                                        </button>
                                    </form>
                                    <form method="POST" action="/admin/marketing/jobs/failed-email/${email.id}" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this failed email record?')">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                            <i data-feather="trash-2"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        `;
                    });
                    $('#failedEmailsTableBody').html(failedEmailsHtml);
                } else {
                    $('#failedEmailsTableBody').html('<tr><td colspan="7" class="text-center">No failed emails</td></tr>');
                }

                // Update failed jobs table
                let failedHtml = '';
                if (response.failedJobs.length === 0) {
                    failedHtml = '<tr><td colspan="6" class="text-center">No failed jobs in queue</td></tr>';
                } else {
                    response.failedJobs.forEach(function(job) {
                        failedHtml += `
                            <tr>
                                <td>${job.id}</td>
                                <td>${job.job_class}</td>
                                <td>${job.queue}</td>
                                <td>${job.failed_at}</td>
                                <td><small class="text-danger">${job.exception}</small></td>
                                <td>
                                    <form method="POST" action="/admin/marketing/jobs/${job.id}/retry" class="d-inline">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <button type="submit" class="btn btn-sm btn-warning" title="Retry">
                                            <i data-feather="refresh-cw"></i>
                                        </button>
                                    </form>
                                    <form method="POST" action="/admin/marketing/jobs/failed/${job.id}" class="d-inline" onsubmit="return confirm('Are you sure?')">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                            <i data-feather="trash-2"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        `;
                    });
                }
                $('#failedJobsTableBody').html(failedHtml);

                // Re-initialize Feather icons
                if (typeof feather !== 'undefined') {
                    feather.replace();
                }
            },
            error: function(xhr) {
                console.error('Error refreshing jobs:', xhr);
            }
        });
    }

    // Manual refresh button
    function refreshJobs() {
        refreshJobs();
    }

    // Cleanup on page unload
    $(window).on('beforeunload', function() {
        if (refreshInterval) {
            clearInterval(refreshInterval);
        }
    });
</script>
@endpush

