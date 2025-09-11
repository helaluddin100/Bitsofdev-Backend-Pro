@extends('master.master')

@section('title', 'Lead Details')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Lead Details</h1>
        <div class="btn-group">
            <a href="{{ route('admin.marketing.leads.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Leads
            </a>
            <a href="{{ route('admin.marketing.leads.edit', $lead) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Edit Lead
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Lead Information -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Lead Information</h6>
                    <span class="badge badge-{{ $lead->is_active ? 'success' : 'danger' }}">
                        {{ $lead->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-primary mb-3">Basic Information</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Name:</strong></td>
                                    <td>{{ $lead->name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Email:</strong></td>
                                    <td>{{ $lead->email ?: 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Phone:</strong></td>
                                    <td>{{ $lead->phone ?: 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Company:</strong></td>
                                    <td>{{ $lead->company ?: 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Category:</strong></td>
                                    <td><span class="badge badge-info">{{ $lead->category }}</span></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-primary mb-3">Address Information</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Address:</strong></td>
                                    <td>{{ $lead->address ?: 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Municipality:</strong></td>
                                    <td>{{ $lead->municipality ?: 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Coordinates:</strong></td>
                                    <td>
                                        @if($lead->latitude && $lead->longitude)
                                            {{ $lead->latitude }}, {{ $lead->longitude }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-6">
                            <h6 class="text-primary mb-3">Website & Social Media</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Website:</strong></td>
                                    <td>
                                        @if($lead->website)
                                            <a href="{{ $lead->website }}" target="_blank">{{ $lead->website }}</a>
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Facebook:</strong></td>
                                    <td>
                                        @if($lead->facebook)
                                            <a href="{{ $lead->facebook }}" target="_blank">{{ $lead->facebook }}</a>
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Instagram:</strong></td>
                                    <td>
                                        @if($lead->instagram)
                                            <a href="{{ $lead->instagram }}" target="_blank">{{ $lead->instagram }}</a>
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Twitter:</strong></td>
                                    <td>
                                        @if($lead->twitter)
                                            <a href="{{ $lead->twitter }}" target="_blank">{{ $lead->twitter }}</a>
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Yelp:</strong></td>
                                    <td>
                                        @if($lead->yelp)
                                            <a href="{{ $lead->yelp }}" target="_blank">{{ $lead->yelp }}</a>
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-primary mb-3">Business Information</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Rating:</strong></td>
                                    <td>
                                        @if($lead->rating)
                                            {{ $lead->rating }}/5
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Review Count:</strong></td>
                                    <td>{{ $lead->review_count ?: 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Claimed:</strong></td>
                                    <td>
                                        <span class="badge badge-{{ $lead->claimed ? 'success' : 'secondary' }}">
                                            {{ $lead->claimed ? 'Yes' : 'No' }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Created:</strong></td>
                                    <td>{{ $lead->created_at->format('M d, Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Last Contacted:</strong></td>
                                    <td>
                                        @if($lead->last_contacted_at)
                                            {{ $lead->last_contacted_at->format('M d, Y H:i') }}
                                        @else
                                            Never
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($lead->notes)
                    <div class="row mt-4">
                        <div class="col-12">
                            <h6 class="text-primary mb-3">Notes</h6>
                            <div class="bg-light p-3 rounded">
                                {{ $lead->notes }}
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Campaign Statistics -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Campaign Statistics</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border-end">
                                <h4 class="text-primary">{{ $campaignStats['total_campaigns'] }}</h4>
                                <p class="text-muted mb-0">Total Campaigns</p>
                            </div>
                        </div>
                        <div class="col-6">
                            <h4 class="text-success">{{ $campaignStats['responded_campaigns'] }}</h4>
                            <p class="text-muted mb-0">Responded</p>
                        </div>
                    </div>
                    <hr>
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border-end">
                                <h4 class="text-info">{{ $campaignStats['response_rate'] }}%</h4>
                                <p class="text-muted mb-0">Response Rate</p>
                            </div>
                        </div>
                        <div class="col-6">
                            <h4 class="text-warning">{{ $campaignStats['contact_count'] }}</h4>
                            <p class="text-muted mb-0">Contact Count</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Campaigns -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Campaigns</h6>
                </div>
                <div class="card-body">
                    @if($lead->campaigns->count() > 0)
                        @foreach($lead->campaigns->take(5) as $campaign)
                        <div class="d-flex align-items-center mb-3">
                            <div class="flex-grow-1">
                                <h6 class="mb-1">{{ $campaign->name }}</h6>
                                <small class="text-muted">{{ $campaign->pivot->status }}</small>
                            </div>
                            <span class="badge badge-{{ $this->getStatusBadgeColor($campaign->pivot->status) }}">
                                {{ ucfirst($campaign->pivot->status) }}
                            </span>
                        </div>
                        @endforeach
                    @else
                        <p class="text-muted">No campaigns yet.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@php
function getStatusBadgeColor($status) {
    return match($status) {
        'fresh' => 'secondary',
        'sent' => 'primary',
        'reminder_1' => 'warning',
        'reminder_2' => 'info',
        'responded' => 'success',
        'bounced' => 'danger',
        'unsubscribed' => 'dark',
        default => 'secondary'
    };
}
@endphp
