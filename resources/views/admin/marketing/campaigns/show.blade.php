@extends('master.master')

@section('title', 'Campaign Details')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Campaign Details</h1>
        <div class="btn-group">
            <a href="{{ route('admin.marketing.campaigns.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Campaigns
            </a>
            @if(!in_array($campaign->status, ['sending', 'sent', 'completed']))
            <form method="POST" action="{{ route('admin.marketing.campaigns.send', $campaign) }}" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-success" 
                        onclick="return confirm('Are you sure you want to send this campaign? This will send emails to all selected leads.')">
                    <i class="fas fa-paper-plane"></i> Send Campaign
                </button>
            </form>
            @endif
            <a href="{{ route('admin.marketing.campaigns.edit', $campaign) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Edit Campaign
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Campaign Information -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Campaign Information</h6>
                    <span class="badge badge-{{ $campaign->status === 'draft' ? 'secondary' : ($campaign->status === 'sent' ? 'success' : ($campaign->status === 'scheduled' ? 'info' : ($campaign->status === 'sending' ? 'warning' : ($campaign->status === 'paused' ? 'danger' : 'primary')))) }}">
                        {{ ucfirst($campaign->status) }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-primary mb-3">Basic Information</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Name:</strong></td>
                                    <td>{{ $campaign->name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Category:</strong></td>
                                    <td><span class="badge badge-info">{{ $campaign->category }}</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Description:</strong></td>
                                    <td>{{ $campaign->description ?: 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Schedule Type:</strong></td>
                                    <td>{{ ucfirst($campaign->schedule_type) }}</td>
                                </tr>
                                @if($campaign->scheduled_at)
                                <tr>
                                    <td><strong>Scheduled At:</strong></td>
                                    <td>{{ $campaign->scheduled_at->format('M d, Y h:i A') }}</td>
                                </tr>
                                @endif
                                @if($campaign->sent_at)
                                <tr>
                                    <td><strong>Sent At:</strong></td>
                                    <td>{{ $campaign->sent_at->format('M d, Y h:i A') }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <td><strong>Created:</strong></td>
                                    <td>{{ $campaign->created_at->format('M d, Y h:i A') }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-primary mb-3">Email Content</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Subject:</strong></td>
                                    <td>{{ $campaign->email_subject }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Body:</strong></td>
                                    <td>
                                        <div class="border p-2" style="max-height: 200px; overflow-y: auto;">
                                            {!! nl2br(e($campaign->email_body)) !!}
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($campaign->enable_reminders || $campaign->reminders_enabled)
                    <hr>
                    <h6 class="text-primary mb-3">Reminders</h6>
                    <div class="row">
                        @if($campaign->reminder_1_days)
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-header">
                                    <strong>First Reminder</strong>
                                </div>
                                <div class="card-body">
                                    <p><strong>Days After:</strong> {{ $campaign->reminder_1_days }}</p>
                                    <p><strong>Subject:</strong> {{ $campaign->reminder_1_subject ?: 'N/A' }}</p>
                                    <p><strong>Body:</strong></p>
                                    <div class="border p-2" style="max-height: 100px; overflow-y: auto;">
                                        {!! nl2br(e($campaign->reminder_1_body ?: 'N/A')) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                        @if($campaign->reminder_2_days)
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-header">
                                    <strong>Second Reminder</strong>
                                </div>
                                <div class="card-body">
                                    <p><strong>Days After:</strong> {{ $campaign->reminder_2_days }}</p>
                                    <p><strong>Subject:</strong> {{ $campaign->reminder_2_subject ?: 'N/A' }}</p>
                                    <p><strong>Body:</strong></p>
                                    <div class="border p-2" style="max-height: 100px; overflow-y: auto;">
                                        {!! nl2br(e($campaign->reminder_2_body ?: 'N/A')) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                    @endif

                    @if($campaign->notes)
                    <hr>
                    <h6 class="text-primary mb-3">Notes</h6>
                    <div class="border p-3">
                        {!! nl2br(e($campaign->notes)) !!}
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Statistics -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Statistics</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Total Leads:</span>
                            <strong>{{ $stats['total_leads'] ?? $campaign->total_leads ?? 0 }}</strong>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Emails Sent:</span>
                            <strong class="text-info">{{ $stats['sent_count'] ?? $campaign->sent_count ?? 0 }}</strong>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Reminder 1 Sent:</span>
                            <strong class="text-warning">{{ $stats['reminder_1_sent'] ?? $campaign->reminder_1_sent ?? 0 }}</strong>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Reminder 2 Sent:</span>
                            <strong class="text-warning">{{ $stats['reminder_2_sent'] ?? $campaign->reminder_2_sent ?? 0 }}</strong>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Reminder 3 Sent:</span>
                            <strong class="text-warning">0</strong>
                        </div>
                    </div>
                    <hr>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Reminders:</span>
                            <form method="POST" action="{{ route('admin.marketing.campaigns.toggle-reminders', $campaign) }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-{{ ($campaign->enable_reminders || $campaign->reminders_enabled) ? 'danger' : 'success' }}">
                                    {{ ($campaign->enable_reminders || $campaign->reminders_enabled) ? 'Disable' : 'Enable' }}
                                </button>
                            </form>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Responses:</span>
                            <strong class="text-success">{{ $stats['response_count'] ?? $campaign->response_count ?? 0 }}</strong>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Response Rate:</span>
                            <strong class="text-primary">{{ number_format($stats['response_rate'] ?? $campaign->response_rate ?? 0, 2) }}%</strong>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Failed Emails:</span>
                            <strong class="text-danger">{{ $stats['emails_failed'] ?? $campaign->emails_failed ?? 0 }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Leads by Status -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Leads by Status</h6>
                </div>
                <div class="card-body">
                    <ul class="nav nav-tabs" id="leadsTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="fresh-tab" data-toggle="tab" href="#fresh" role="tab">
                                Fresh ({{ count($leadsByStatus['fresh'] ?? []) }})
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="sent-tab" data-toggle="tab" href="#sent" role="tab">
                                Sent ({{ count($leadsByStatus['sent'] ?? []) }})
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="reminder1-tab" data-toggle="tab" href="#reminder1" role="tab">
                                Reminder 1 ({{ count($leadsByStatus['reminder_1'] ?? []) }})
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="reminder2-tab" data-toggle="tab" href="#reminder2" role="tab">
                                Reminder 2 ({{ count($leadsByStatus['reminder_2'] ?? []) }})
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="reminder3-tab" data-toggle="tab" href="#reminder3" role="tab">
                                Reminder 3 ({{ count($leadsByStatus['reminder_3'] ?? []) }})
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="responded-tab" data-toggle="tab" href="#responded" role="tab">
                                Responded ({{ count($leadsByStatus['responded'] ?? []) }})
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content" id="leadsTabContent">
                        <div class="tab-pane fade show active" id="fresh" role="tabpanel">
                            <div class="table-responsive mt-3">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Phone</th>
                                            <th>Company</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($leadsByStatus['fresh'] ?? [] as $lead)
                                        <tr>
                                            <td>
                                                <a href="{{ route('admin.marketing.leads.show', $lead) }}">
                                                    {{ $lead->name }}
                                                </a>
                                            </td>
                                            <td>{{ $lead->email ?: 'N/A' }}</td>
                                            <td>{{ $lead->phone ?: 'N/A' }}</td>
                                            <td>{{ $lead->company ?: 'N/A' }}</td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="4" class="text-center">No fresh leads</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="sent" role="tabpanel">
                            <div class="table-responsive mt-3">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Phone</th>
                                            <th>Sent At</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($leadsByStatus['sent'] ?? [] as $lead)
                                        <tr>
                                            <td>
                                                <a href="{{ route('admin.marketing.leads.show', $lead) }}">
                                                    {{ $lead->name }}
                                                </a>
                                            </td>
                                            <td>{{ $lead->email ?: 'N/A' }}</td>
                                            <td>{{ $lead->phone ?: 'N/A' }}</td>
                                            <td>{{ $lead->pivot->sent_at ? \Carbon\Carbon::parse($lead->pivot->sent_at)->format('M d, Y h:i A') : 'N/A' }}</td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="4" class="text-center">No sent leads</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="reminder1" role="tabpanel">
                            <div class="table-responsive mt-3">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Phone</th>
                                            <th>Reminder Sent At</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($leadsByStatus['reminder_1'] ?? [] as $lead)
                                        <tr>
                                            <td>
                                                <a href="{{ route('admin.marketing.leads.show', $lead) }}">
                                                    {{ $lead->name }}
                                                </a>
                                            </td>
                                            <td>{{ $lead->email ?: 'N/A' }}</td>
                                            <td>{{ $lead->phone ?: 'N/A' }}</td>
                                            <td>{{ $lead->pivot->reminder_1_sent_at ? \Carbon\Carbon::parse($lead->pivot->reminder_1_sent_at)->format('M d, Y h:i A') : 'N/A' }}</td>
                                            <td>
                                                <form method="POST" action="{{ route('admin.marketing.campaigns.remove-from-reminders', [$campaign, $lead]) }}" class="d-inline" onsubmit="return confirm('Remove this lead from reminders?')">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-warning" title="Remove from Reminders">
                                                        <i data-feather="x-circle"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="5" class="text-center">No reminder 1 leads</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="reminder2" role="tabpanel">
                            <div class="table-responsive mt-3">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Phone</th>
                                            <th>Reminder Sent At</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($leadsByStatus['reminder_2'] ?? [] as $lead)
                                        <tr>
                                            <td>
                                                <a href="{{ route('admin.marketing.leads.show', $lead) }}">
                                                    {{ $lead->name }}
                                                </a>
                                            </td>
                                            <td>{{ $lead->email ?: 'N/A' }}</td>
                                            <td>{{ $lead->phone ?: 'N/A' }}</td>
                                            <td>{{ $lead->pivot->reminder_2_sent_at ? \Carbon\Carbon::parse($lead->pivot->reminder_2_sent_at)->format('M d, Y h:i A') : 'N/A' }}</td>
                                            <td>
                                                <form method="POST" action="{{ route('admin.marketing.campaigns.remove-from-reminders', [$campaign, $lead]) }}" class="d-inline" onsubmit="return confirm('Remove this lead from reminders?')">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-warning" title="Remove from Reminders">
                                                        <i data-feather="x-circle"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="5" class="text-center">No reminder 2 leads</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="reminder3" role="tabpanel">
                            <div class="table-responsive mt-3">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Phone</th>
                                            <th>Reminder Sent At</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($leadsByStatus['reminder_3'] ?? [] as $lead)
                                        <tr>
                                            <td>
                                                <a href="{{ route('admin.marketing.leads.show', $lead) }}">
                                                    {{ $lead->name }}
                                                </a>
                                            </td>
                                            <td>{{ $lead->email ?: 'N/A' }}</td>
                                            <td>{{ $lead->phone ?: 'N/A' }}</td>
                                            <td>{{ isset($lead->pivot->reminder_3_sent_at) && $lead->pivot->reminder_3_sent_at ? \Carbon\Carbon::parse($lead->pivot->reminder_3_sent_at)->format('M d, Y h:i A') : 'N/A' }}</td>
                                            <td>
                                                <form method="POST" action="{{ route('admin.marketing.campaigns.remove-from-reminders', [$campaign, $lead]) }}" class="d-inline" onsubmit="return confirm('Remove this lead from reminders?')">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-warning" title="Remove from Reminders">
                                                        <i data-feather="x-circle"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="5" class="text-center">No reminder 3 leads</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="responded" role="tabpanel">
                            <div class="table-responsive mt-3">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Phone</th>
                                            <th>Responded At</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($leadsByStatus['responded'] ?? [] as $lead)
                                        <tr>
                                            <td>
                                                <a href="{{ route('admin.marketing.leads.show', $lead) }}">
                                                    {{ $lead->name }}
                                                </a>
                                            </td>
                                            <td>{{ $lead->email ?: 'N/A' }}</td>
                                            <td>{{ $lead->phone ?: 'N/A' }}</td>
                                            <td>{{ $lead->pivot->responded_at ? \Carbon\Carbon::parse($lead->pivot->responded_at)->format('M d, Y h:i A') : 'N/A' }}</td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="4" class="text-center">No responded leads</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Responses -->
    @if(isset($recentResponses) && $recentResponses->count() > 0)
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Responses</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Lead</th>
                                    <th>Response Date</th>
                                    <th>Type</th>
                                    <th>Message</th>
                                    <th>Sentiment</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentResponses as $response)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.marketing.leads.show', $response->lead) }}">
                                            {{ $response->lead->name }}
                                        </a>
                                    </td>
                                    <td>{{ $response->response_date->format('M d, Y h:i A') }}</td>
                                    <td><span class="badge badge-info">{{ ucfirst($response->response_type) }}</span></td>
                                    <td>{{ Str::limit($response->response_message, 50) ?: 'N/A' }}</td>
                                    <td>
                                        <span class="badge badge-{{ $response->sentiment === 'positive' ? 'success' : ($response->sentiment === 'negative' ? 'danger' : 'secondary') }}">
                                            {{ ucfirst($response->sentiment) }}
                                        </span>
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
    @endif
</div>
@endsection

