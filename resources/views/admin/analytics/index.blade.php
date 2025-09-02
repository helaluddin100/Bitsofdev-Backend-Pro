@extends('master.master')

@section('content')
    <div class="page-content">
        <nav class="page-breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Analytics</li>
            </ol>
        </nav>

        <!-- Date Range Filter -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="card-title">Date Range</h6>
                            <div class="d-flex gap-2">
                                <select id="dateRange" class="form-select" style="width: auto;">
                                    <option value="7">Last 7 days</option>
                                    <option value="30" selected>Last 30 days</option>
                                    <option value="90">Last 90 days</option>
                                    <option value="365">Last year</option>
                                </select>
                                <button id="exportBtn" class="btn btn-success">
                                    <i data-feather="download"></i> Export CSV
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="row">
            <div class="col-xl-3 col-sm-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avatar avatar-lg">
                                    <div class="avatar-content bg-primary">
                                        <i data-feather="users" class="text-white"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1">Total Visitors</h6>
                                <h4 class="mb-0">{{ number_format($data['total_visitors']) }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avatar avatar-lg">
                                    <div class="avatar-content bg-success">
                                        <i data-feather="trending-up" class="text-white"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1">Daily Visitors</h6>
                                <h4 class="mb-0">{{ number_format($data['daily_visitors']) }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avatar avatar-lg">
                                    <div class="avatar-content bg-info">
                                        <i data-feather="repeat" class="text-white"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1">Returning</h6>
                                <h4 class="mb-0">{{ number_format($data['returning_visitors']) }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avatar avatar-lg">
                                    <div class="avatar-content bg-warning">
                                        <i data-feather="clock" class="text-white"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1">Avg. Session</h6>
                                <h4 class="mb-0">{{ $data['avg_session_duration'] }} min</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row">
            <!-- Visitor Trend Chart -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Visitor Trend (Last {{ $days }} days)</h6>
                        <div class="chart-container">
                            <canvas id="visitorTrendChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Visitors by Country -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Visitors by Country</h6>
                        <div class="chart-container">
                            <canvas id="countryChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Charts Row -->
        <div class="row">
            <!-- Top Pages -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Top Visited Pages</h6>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Page</th>
                                        <th>Visits</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data['top_pages'] as $page)
                                        <tr>
                                            <td>
                                                <div class="text-truncate" style="max-width: 200px;"
                                                    title="{{ $page->page_url }}">
                                                    {{ $page->page_url }}
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary">{{ $page->visits }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Most Clicked Actions -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Most Clicked Actions</h6>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Action</th>
                                        <th>Clicks</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data['most_clicked'] as $action => $count)
                                        <tr>
                                            <td>{{ $action }}</td>
                                            <td>
                                                <span class="badge bg-success">{{ $count }}</span>
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

        <!-- Recent Visitors Table -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Recent Visitors</h6>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Visitor ID</th>
                                        <th>Location</th>
                                        <th>Device</th>
                                        <th>Page</th>
                                        <th>Time Spent</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data['recent_visitors'] as $visitor)
                                        <tr>
                                            <td>
                                                <code>{{ Str::limit($visitor->visitor_id, 12) }}</code>
                                            </td>
                                            <td>
                                                @if ($visitor->location && isset($visitor->location['country']))
                                                    <span class="badge bg-light text-dark">
                                                        {{ $visitor->location['country'] }}
                                                        @if (isset($visitor->location['city']))
                                                            , {{ $visitor->location['city'] }}
                                                        @endif
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary">Unknown</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-info">{{ $visitor->device ?? 'Unknown' }}</span>
                                            </td>
                                            <td>
                                                <div class="text-truncate" style="max-width: 200px;"
                                                    title="{{ $visitor->page_url }}">
                                                    {{ $visitor->page_url }}
                                                </div>
                                            </td>
                                            <td>
                                                @if ($visitor->time_spent > 0)
                                                    <span
                                                        class="badge bg-success">{{ gmdate('i:s', $visitor->time_spent) }}</span>
                                                @else
                                                    <span class="badge bg-warning">Active</span>
                                                @endif
                                            </td>
                                            <td>{{ $visitor->created_at->format('M d, Y H:i') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $data['recent_visitors']->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <style>
        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
        }

        .chart-container canvas {
            max-height: 300px !important;
        }

        .no-data-message {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 300px;
            color: #6c757d;
            font-style: italic;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize charts
            initializeCharts();

            // Date range change handler
            document.getElementById('dateRange').addEventListener('change', function() {
                const days = this.value;
                updateAnalytics(days);
            });

            // Export button handler
            document.getElementById('exportBtn').addEventListener('click', function() {
                const days = document.getElementById('dateRange').value;
                window.location.href = `{{ route('admin.analytics.export') }}?days=${days}&format=csv`;
            });
        });

        function initializeCharts() {
            // Visitor Trend Chart
            const trendCtx = document.getElementById('visitorTrendChart').getContext('2d');
            const trendData = @json($data['visitor_trend']);

            // Handle empty data
            if (!trendData || trendData.length === 0) {
                document.getElementById('visitorTrendChart').parentElement.innerHTML =
                    '<div class="no-data-message"><p>No visitor data available yet</p></div>';
            } else {
                new Chart(trendCtx, {
                    type: 'line',
                    data: {
                        labels: trendData.map(item => item.date),
                        datasets: [{
                            label: 'Unique Visitors',
                            data: trendData.map(item => item.unique_visitors),
                            borderColor: 'rgb(75, 192, 192)',
                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                            tension: 0.1
                        }, {
                            label: 'Total Visits',
                            data: trendData.map(item => item.total_visits),
                            borderColor: 'rgb(255, 99, 132)',
                            backgroundColor: 'rgba(255, 99, 132, 0.2)',
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
            }

            // Country Chart
            const countryCtx = document.getElementById('countryChart').getContext('2d');
            const countryData = @json($data['visitors_by_country']);

            // Handle empty data
            if (!countryData || countryData.length === 0) {
                document.getElementById('countryChart').parentElement.innerHTML =
                    '<div class="no-data-message"><p>No country data available yet</p></div>';
            } else {
                new Chart(countryCtx, {
                    type: 'doughnut',
                    data: {
                        labels: countryData.map(item => item.country || 'Unknown'),
                        datasets: [{
                            data: countryData.map(item => item.count),
                            backgroundColor: [
                                '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF',
                                '#FF9F40', '#FF6384', '#C9CBCF', '#4BC0C0', '#FF6384'
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
            }
        }

        function updateAnalytics(days) {
            // Update visitor trend chart
            fetch(`{{ route('admin.analytics.data') }}?days=${days}&type=trend`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update the trend chart data
                        const chart = Chart.getChart('visitorTrendChart');
                        if (chart) {
                            chart.data.labels = data.data.map(item => item.date);
                            chart.data.datasets[0].data = data.data.map(item => item.unique_visitors);
                            chart.data.datasets[1].data = data.data.map(item => item.total_visits);
                            chart.update();
                        }
                    }
                });

            // Update country chart
            fetch(`{{ route('admin.analytics.data') }}?days=${days}&type=country`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update the country chart data
                        const chart = Chart.getChart('countryChart');
                        if (chart) {
                            chart.data.labels = data.data.map(item => item.country || 'Unknown');
                            chart.data.datasets[0].data = data.data.map(item => item.count);
                            chart.update();
                        }
                    }
                });

            // Reload page to update summary cards and tables
            window.location.href = `{{ route('admin.analytics.index') }}?days=${days}`;
        }
    </script>
@endsection
