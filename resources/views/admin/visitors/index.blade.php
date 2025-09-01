@extends('master.master')

@section('title', 'Visitor Data Management')

@section('content')
    <div class="page-wrapper">
        <div class="page-content">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Visitor Data Management</h4>
                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.visitors.export', ['format' => 'csv'] + request()->query()) }}"
                                    class="btn btn-success btn-sm">
                                    <i class="fas fa-download me-1"></i> Export CSV
                                </a>
                                <button type="button" class="btn btn-danger btn-sm" id="bulkDeleteBtn"
                                    style="display: none;">
                                    <i class="fas fa-trash me-1"></i> Delete Selected
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- Summary Cards -->
                            <div class="row mb-4">
                                <div class="col-md-3">
                                    <div class="card bg-primary text-white">
                                        <div class="card-body">
                                            <h5 class="card-title">Total Visitors</h5>
                                            <h3>{{ number_format($summary['total_visitors']) }}</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-success text-white">
                                        <div class="card-body">
                                            <h5 class="card-title">Unique Visitors</h5>
                                            <h3>{{ number_format($summary['unique_visitors']) }}</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-info text-white">
                                        <div class="card-body">
                                            <h5 class="card-title">Avg Time (min)</h5>
                                            <h3>{{ $summary['avg_time_spent'] }}</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-warning text-white">
                                        <div class="card-body">
                                            <h5 class="card-title">Total Pages</h5>
                                            <h3>{{ number_format($summary['total_pages']) }}</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Advanced Filters -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0">Advanced Filters</h5>
                                </div>
                                <div class="card-body">
                                    <form method="GET" action="{{ route('admin.visitors.index') }}" id="filterForm">
                                        <div class="row">
                                            <!-- Search -->
                                            <div class="col-md-3 mb-3">
                                                <label class="form-label">Search</label>
                                                <input type="text" class="form-control" name="search"
                                                    value="{{ request('search') }}" placeholder="Search...">
                                            </div>

                                            <!-- Date Range -->
                                            <div class="col-md-2 mb-3">
                                                <label class="form-label">From Date</label>
                                                <input type="date" class="form-control" name="date_from"
                                                    value="{{ request('date_from') }}">
                                            </div>
                                            <div class="col-md-2 mb-3">
                                                <label class="form-label">To Date</label>
                                                <input type="date" class="form-control" name="date_to"
                                                    value="{{ request('date_to') }}">
                                            </div>

                                            <!-- Device -->
                                            <div class="col-md-2 mb-3">
                                                <label class="form-label">Device</label>
                                                <select class="form-select" name="device">
                                                    <option value="">All Devices</option>
                                                    @foreach ($filterOptions['devices'] as $device)
                                                        <option value="{{ $device }}"
                                                            {{ request('device') == $device ? 'selected' : '' }}>
                                                            {{ $device }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <!-- Browser -->
                                            <div class="col-md-2 mb-3">
                                                <label class="form-label">Browser</label>
                                                <select class="form-select" name="browser">
                                                    <option value="">All Browsers</option>
                                                    @foreach ($filterOptions['browsers'] as $browser)
                                                        <option value="{{ $browser }}"
                                                            {{ request('browser') == $browser ? 'selected' : '' }}>
                                                            {{ $browser }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <!-- OS -->
                                            <div class="col-md-2 mb-3">
                                                <label class="form-label">Operating System</label>
                                                <select class="form-select" name="os">
                                                    <option value="">All OS</option>
                                                    @foreach ($filterOptions['operating_systems'] as $os)
                                                        <option value="{{ $os }}"
                                                            {{ request('os') == $os ? 'selected' : '' }}>
                                                            {{ $os }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <!-- Country -->
                                            <div class="col-md-2 mb-3">
                                                <label class="form-label">Country</label>
                                                <select class="form-select" name="country">
                                                    <option value="">All Countries</option>
                                                    @foreach ($filterOptions['countries'] as $country)
                                                        <option value="{{ $country }}"
                                                            {{ request('country') == $country ? 'selected' : '' }}>
                                                            {{ $country }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <!-- City -->
                                            <div class="col-md-2 mb-3">
                                                <label class="form-label">City</label>
                                                <select class="form-select" name="city">
                                                    <option value="">All Cities</option>
                                                    @foreach ($filterOptions['cities'] as $city)
                                                        <option value="{{ $city }}"
                                                            {{ request('city') == $city ? 'selected' : '' }}>
                                                            {{ $city }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <!-- Time Spent Range -->
                                            <div class="col-md-2 mb-3">
                                                <label class="form-label">Min Time (sec)</label>
                                                <input type="number" class="form-control" name="time_spent_min"
                                                    value="{{ request('time_spent_min') }}" min="0">
                                            </div>
                                            <div class="col-md-2 mb-3">
                                                <label class="form-label">Max Time (sec)</label>
                                                <input type="number" class="form-control" name="time_spent_max"
                                                    value="{{ request('time_spent_max') }}" min="0">
                                            </div>

                                            <!-- Filter Buttons -->
                                            <div class="col-12 mb-3">
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="fas fa-filter me-1"></i> Apply Filters
                                                </button>
                                                <a href="{{ route('admin.visitors.index') }}" class="btn btn-secondary">
                                                    <i class="fas fa-times me-1"></i> Clear Filters
                                                </a>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <!-- Visitors Table -->
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>
                                                <input type="checkbox" id="selectAll" class="form-check-input">
                                            </th>
                                            <th>ID</th>
                                            <th>Visitor ID</th>
                                            <th>IP Address</th>
                                            <th>Location</th>
                                            <th>Device</th>
                                            <th>Browser</th>
                                            <th>OS</th>
                                            <th>Page URL</th>
                                            <th>Time Spent</th>
                                            <th>Created At</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($visitors as $visitor)
                                            <tr>
                                                <td>
                                                    <input type="checkbox" class="form-check-input visitor-checkbox"
                                                        value="{{ $visitor->id }}">
                                                </td>
                                                <td>{{ $visitor->id }}</td>
                                                <td>
                                                    <span
                                                        class="badge bg-info">{{ Str::limit($visitor->visitor_id, 15) }}</span>
                                                </td>
                                                <td>
                                                    <code>{{ $visitor->ip }}</code>
                                                </td>
                                                <td>
                                                    @if ($visitor->location && is_array($visitor->location))
                                                        <div class="d-flex flex-column">
                                                            <span
                                                                class="badge bg-success">{{ $visitor->location['country'] ?? 'Unknown' }}</span>
                                                            <small
                                                                class="text-muted">{{ $visitor->location['city'] ?? 'Unknown' }}</small>
                                                        </div>
                                                    @else
                                                        <span class="text-muted">No location data</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span
                                                        class="badge bg-primary">{{ $visitor->device ?? 'Unknown' }}</span>
                                                </td>
                                                <td>{{ $visitor->browser ?? 'Unknown' }}</td>
                                                <td>{{ $visitor->os ?? 'Unknown' }}</td>
                                                <td>
                                                    <div class="text-truncate" style="max-width: 200px;"
                                                        title="{{ $visitor->page_url }}">
                                                        {{ Str::limit($visitor->page_url, 50) }}
                                                    </div>
                                                </td>
                                                <td>
                                                    @if ($visitor->time_spent)
                                                        <span
                                                            class="badge bg-warning">{{ gmdate('H:i:s', $visitor->time_spent) }}</span>
                                                    @else
                                                        <span class="text-muted">N/A</span>
                                                    @endif
                                                </td>
                                                <td>{{ $visitor->created_at->format('M d, Y H:i') }}</td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="{{ route('admin.visitors.show', $visitor) }}"
                                                            class="btn btn-info btn-sm" title="View Details">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <form action="{{ route('admin.visitors.destroy', $visitor) }}"
                                                            method="POST" class="d-inline delete-form">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger btn-sm"
                                                                title="Delete" onclick="return confirm('Are you sure?')">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="12" class="text-center py-4">
                                                    <div class="text-muted">
                                                        <i class="fas fa-inbox fa-3x mb-3"></i>
                                                        <h5>No visitor data found</h5>
                                                        <p>Try adjusting your filters or visit your website to generate some
                                                            data.</p>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            @if ($visitors->hasPages())
                                <div class="d-flex justify-content-center mt-4">
                                    {{ $visitors->appends(request()->query())->links() }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bulk Delete Form -->
    <form id="bulkDeleteForm" action="{{ route('admin.visitors.bulk-delete') }}" method="POST" style="display: none;">
        @csrf
        <input type="hidden" name="visitor_ids" id="bulkDeleteIds">
    </form>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Select All functionality
            const selectAll = document.getElementById('selectAll');
            const checkboxes = document.querySelectorAll('.visitor-checkbox');
            const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');

            selectAll.addEventListener('change', function() {
                checkboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
                updateBulkDeleteButton();
            });

            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', updateBulkDeleteButton);
            });

            function updateBulkDeleteButton() {
                const checkedBoxes = document.querySelectorAll('.visitor-checkbox:checked');
                if (checkedBoxes.length > 0) {
                    bulkDeleteBtn.style.display = 'inline-block';
                } else {
                    bulkDeleteBtn.style.display = 'none';
                }
            }

            // Bulk Delete functionality
            bulkDeleteBtn.addEventListener('click', function() {
                const checkedBoxes = document.querySelectorAll('.visitor-checkbox:checked');
                const ids = Array.from(checkedBoxes).map(cb => cb.value);

                if (ids.length === 0) return;

                if (confirm(`Are you sure you want to delete ${ids.length} visitor records?`)) {
                    document.getElementById('bulkDeleteIds').value = JSON.stringify(ids);
                    document.getElementById('bulkDeleteForm').submit();
                }
            });

            // Auto-submit form on filter change
            const filterForm = document.getElementById('filterForm');
            const filterInputs = filterForm.querySelectorAll('select, input[type="date"], input[type="number"]');

            filterInputs.forEach(input => {
                input.addEventListener('change', () => {
                    filterForm.submit();
                });
            });
        });
    </script>
@endpush
