@extends('master.master')

@section('title', 'Campaigns Management')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Campaigns Management</h1>
        <div class="btn-group">
            <a href="{{ route('admin.marketing.campaigns.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Create Campaign
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filters</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.marketing.campaigns.index') }}">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="search">Search</label>
                            <input type="text" class="form-control" id="search" name="search"
                                   value="{{ request('search') }}" placeholder="Campaign name or subject...">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="category">Category</label>
                            <select class="form-control" id="category" name="category">
                                <option value="">All Categories</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->name }}" {{ request('category') == $category->name ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select class="form-control" id="status" name="status">
                                <option value="">All Status</option>
                                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                                <option value="sending" {{ request('status') == 'sending' ? 'selected' : '' }}>Sending</option>
                                <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>Sent</option>
                                <option value="paused" {{ request('status') == 'paused' ? 'selected' : '' }}>Paused</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <div class="d-flex">
                                <button type="submit" class="btn btn-primary mr-2">
                                    <i class="fas fa-search"></i> Filter
                                </button>
                                <a href="{{ route('admin.marketing.campaigns.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Clear
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Campaigns Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Campaigns ({{ $campaigns->total() }} total)</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="campaignsTable">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Subject</th>
                            <th>Status</th>
                            <th>Leads</th>
                            <th>Sent</th>
                            <th>Response Rate</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($campaigns as $campaign)
                        <tr>
                            <td>
                                <a href="{{ route('admin.marketing.campaigns.show', $campaign) }}">
                                    {{ Str::limit($campaign->name, 30) }}
                                </a>
                            </td>
                            <td>
                                <span class="badge badge-info">{{ $campaign->category }}</span>
                            </td>
                            <td>{{ Str::limit($campaign->email_subject, 40) }}</td>
                            <td>
                                <span class="badge badge-{{ $this->getStatusBadgeColor($campaign->status) }}">
                                    {{ ucfirst($campaign->status) }}
                                </span>
                            </td>
                            <td>{{ $campaign->leads_count }}</td>
                            <td>{{ $campaign->sent_count ?? 0 }}</td>
                            <td>{{ $campaign->response_rate ?? 0 }}%</td>
                            <td>{{ $campaign->created_at->format('M d, Y') }}</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.marketing.campaigns.show', $campaign) }}"
                                       class="btn btn-info" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.marketing.campaigns.edit', $campaign) }}"
                                       class="btn btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    @if($campaign->status === 'draft' || $campaign->status === 'scheduled')
                                        <form method="POST" action="{{ route('admin.marketing.campaigns.send', $campaign) }}"
                                              class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-success" title="Send">
                                                <i class="fas fa-paper-plane"></i>
                                            </button>
                                        </form>
                                    @endif

                                    @if($campaign->status === 'sending' || $campaign->status === 'scheduled')
                                        <form method="POST" action="{{ route('admin.marketing.campaigns.pause', $campaign) }}"
                                              class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-warning" title="Pause">
                                                <i class="fas fa-pause"></i>
                                            </button>
                                        </form>
                                    @endif

                                    @if($campaign->status === 'paused')
                                        <form method="POST" action="{{ route('admin.marketing.campaigns.resume', $campaign) }}"
                                              class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-success" title="Resume">
                                                <i class="fas fa-play"></i>
                                            </button>
                                        </form>
                                    @endif

                                    <form method="POST" action="{{ route('admin.marketing.campaigns.duplicate', $campaign) }}"
                                          class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-info" title="Duplicate">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    </form>

                                    <form method="POST" action="{{ route('admin.marketing.campaigns.destroy', $campaign) }}"
                                          class="d-inline" onsubmit="return confirm('Are you sure?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center">No campaigns found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center">
                {{ $campaigns->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Add any JavaScript functionality here
</script>
@endpush

@php
function getStatusBadgeColor($status) {
    return match($status) {
        'draft' => 'secondary',
        'scheduled' => 'info',
        'sending' => 'warning',
        'sent' => 'success',
        'paused' => 'danger',
        'completed' => 'primary',
        default => 'secondary'
    };
}
@endphp
