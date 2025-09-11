@extends('master.master')

@section('title', 'Marketing Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Marketing Dashboard</h1>
        <div class="btn-group">
            <a href="{{ route('admin.marketing.leads.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add Lead
            </a>
            <a href="{{ route('admin.marketing.campaigns.create') }}" class="btn btn-success">
                <i class="fas fa-plus"></i> Create Campaign
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Leads</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['total_leads']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Active Campaigns</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['active_campaigns']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-bullhorn fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Emails Sent</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['sent_emails']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-envelope fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Response Rate</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['response_rate'] }}%</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Stats Row -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-secondary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">Total Responses</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['total_responses']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-reply fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Pending Reminders</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['pending_reminders']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-dark shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">This Month Leads</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['this_month_leads']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Qualified Leads</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['qualified_leads']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-star fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row">
        <!-- Monthly Performance Chart -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Monthly Performance</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="monthlyPerformanceChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Category Performance -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Category Performance</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="categoryPerformanceChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity Row -->
    <div class="row">
        <!-- Recent Campaigns -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Campaigns</h6>
                    <a href="{{ route('admin.marketing.campaigns.index') }}" class="btn btn-sm btn-primary">View All</a>
                </div>
                <div class="card-body">
                    @if($recentCampaigns->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Status</th>
                                        <th>Leads</th>
                                        <th>Response Rate</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentCampaigns as $campaign)
                                    <tr>
                                        <td>
                                            <a href="{{ route('admin.marketing.campaigns.show', $campaign) }}">
                                                {{ Str::limit($campaign->name, 30) }}
                                            </a>
                                        </td>
                                        <td>
                                            <span class="badge badge-{{ $campaign->status === 'sent' ? 'success' : ($campaign->status === 'sending' ? 'warning' : 'secondary') }}">
                                                {{ ucfirst($campaign->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $campaign->leads_count }}</td>
                                        <td>{{ $campaign->response_rate }}%</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">No campaigns found.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Recent Responses -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Responses</h6>
                    <a href="{{ route('admin.marketing.responses.index') }}" class="btn btn-sm btn-primary">View All</a>
                </div>
                <div class="card-body">
                    @if($recentResponses->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Lead</th>
                                        <th>Campaign</th>
                                        <th>Type</th>
                                        <th>Date</th>
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
                                        <td>
                                            <a href="{{ route('admin.marketing.campaigns.show', $response->campaign) }}">
                                                {{ Str::limit($response->campaign->name, 20) }}
                                            </a>
                                        </td>
                                        <td>
                                            <span class="badge badge-info">{{ ucfirst($response->response_type) }}</span>
                                        </td>
                                        <td>{{ $response->response_date->format('M d, Y') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">No responses found.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Monthly Performance Chart
const monthlyData = @json($monthlyPerformance);
const monthlyCtx = document.getElementById('monthlyPerformanceChart').getContext('2d');
new Chart(monthlyCtx, {
    type: 'line',
    data: {
        labels: monthlyData.map(item => item.month),
        datasets: [{
            label: 'Leads',
            data: monthlyData.map(item => item.leads),
            borderColor: 'rgb(75, 192, 192)',
            tension: 0.1
        }, {
            label: 'Campaigns',
            data: monthlyData.map(item => item.campaigns),
            borderColor: 'rgb(255, 99, 132)',
            tension: 0.1
        }, {
            label: 'Responses',
            data: monthlyData.map(item => item.responses),
            borderColor: 'rgb(54, 162, 235)',
            tension: 0.1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// Category Performance Chart
const categoryData = @json($categoryPerformance);
const categoryCtx = document.getElementById('categoryPerformanceChart').getContext('2d');
new Chart(categoryCtx, {
    type: 'doughnut',
    data: {
        labels: categoryData.map(item => item.name),
        datasets: [{
            data: categoryData.map(item => item.response_rate),
            backgroundColor: [
                '#FF6384',
                '#36A2EB',
                '#FFCE56',
                '#4BC0C0',
                '#9966FF',
                '#FF9F40'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});
</script>
@endpush
