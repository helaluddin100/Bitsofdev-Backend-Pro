@extends('master.master')

@section('title', 'Leads Management')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Leads Management</h1>
        <div class="btn-group">
            <a href="{{ route('admin.marketing.leads.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add Lead
            </a>
            <a href="{{ route('admin.marketing.leads.import.form') }}" class="btn btn-success">
                <i class="fas fa-upload"></i> Import Leads
            </a>
            <a href="{{ route('admin.marketing.leads.template.download') }}" class="btn btn-info">
                <i class="fas fa-download"></i> Download Template
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filters</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.marketing.leads.index') }}">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="search">Search</label>
                            <input type="text" class="form-control" id="search" name="search"
                                   value="{{ request('search') }}" placeholder="Name, email, phone...">
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
                            <label for="municipality">Municipality</label>
                            <select class="form-control" id="municipality" name="municipality">
                                <option value="">All Municipalities</option>
                                @foreach($municipalities as $municipality)
                                    <option value="{{ $municipality }}" {{ request('municipality') == $municipality ? 'selected' : '' }}>
                                        {{ $municipality }}
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
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
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
                                <a href="{{ route('admin.marketing.leads.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Clear
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Leads Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Leads ({{ $leads->total() }} total)</h6>
            <div>
                <button type="button" class="btn btn-sm btn-danger" onclick="bulkDelete()">
                    <i class="fas fa-trash"></i> Delete Selected
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="leadsTable">
                    <thead class="thead-dark">
                        <tr>
                            <th width="5%">
                                <input type="checkbox" id="selectAll">
                            </th>
                            <th width="20%">Name</th>
                            <th width="20%">Email</th>
                            <th width="15%">Phone</th>
                            <th width="15%">Category</th>
                            <th width="15%">Municipality</th>
                            <th width="10%">Status</th>
                            <th width="10%">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($leads as $lead)
                        <tr>
                            <td>
                                <input type="checkbox" class="lead-checkbox" value="{{ $lead->id }}">
                            </td>
                            <td>
                                <a href="{{ route('admin.marketing.leads.show', $lead) }}" class="text-primary font-weight-bold">
                                    {{ Str::limit($lead->name, 30) }}
                                </a>
                            </td>
                            <td>
                                @if($lead->email)
                                    <span class="text-success">{{ Str::limit($lead->email, 25) }}</span>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>
                                @if($lead->phone)
                                    <span class="text-info">{{ $lead->phone }}</span>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-info">{{ $lead->category }}</span>
                            </td>
                            <td>
                                @if($lead->municipality)
                                    <span class="text-dark">{{ Str::limit($lead->municipality, 20) }}</span>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-{{ $lead->is_active ? 'success' : 'danger' }}">
                                    {{ $lead->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.marketing.leads.show', $lead) }}"
                                       class="btn btn-info btn-sm" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.marketing.leads.edit', $lead) }}"
                                       class="btn btn-warning btn-sm" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.marketing.leads.toggle-status', $lead) }}"
                                          class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-{{ $lead->is_active ? 'secondary' : 'success' }} btn-sm"
                                                title="{{ $lead->is_active ? 'Deactivate' : 'Activate' }}">
                                            <i class="fas fa-{{ $lead->is_active ? 'pause' : 'play' }}"></i>
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.marketing.leads.destroy', $lead) }}"
                                          class="d-inline" onsubmit="return confirm('Are you sure?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-inbox fa-3x mb-3"></i>
                                    <p>No leads found.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center">
                {{ $leads->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Bulk Delete Modal -->
<div class="modal fade" id="bulkDeleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Bulk Delete</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the selected leads? This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <form method="POST" action="{{ route('admin.marketing.leads.bulk-delete') }}" id="bulkDeleteForm">
                    @csrf
                    <button type="submit" class="btn btn-danger">Delete Selected</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<style>
    .table th, .table td {
        vertical-align: middle;
    }
    .btn-group-sm .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }
    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter,
    .dataTables_wrapper .dataTables_info,
    .dataTables_wrapper .dataTables_processing,
    .dataTables_wrapper .dataTables_paginate {
        color: #333;
    }
</style>
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
$(document).ready(function() {
    try {
        // Check if jQuery is loaded
        if (typeof $ === 'undefined') {
            console.error('jQuery is not loaded!');
            return;
        }

        // Check if DataTable is available
        if (typeof $.fn.DataTable === 'undefined') {
            console.error('DataTables is not loaded!');
            return;
        }

        // Initialize DataTable
        $('#leadsTable').DataTable({
            "pageLength": 25,
            "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            "order": [[1, "asc"]],
            "columnDefs": [
                { "orderable": false, "targets": [0, 7] }
            ],
            "language": {
                "search": "Search leads:",
                "lengthMenu": "Show _MENU_ leads per page",
                "info": "Showing _START_ to _END_ of _TOTAL_ leads",
                "infoEmpty": "No leads available",
                "infoFiltered": "(filtered from _MAX_ total leads)"
            },
            "dom": '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                   '<"row"<"col-sm-12"tr>>' +
                   '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
            "responsive": true
        });

        console.log('DataTable initialized successfully');
    } catch (error) {
        console.error('Error initializing DataTable:', error);
        // Fallback: Show message if DataTable fails
        $('#leadsTable').before('<div class="alert alert-warning">DataTable failed to load. Using basic table view.</div>');
    }

    // Select All functionality
    $('#selectAll').on('change', function() {
        const checkboxes = $('.lead-checkbox');
        checkboxes.prop('checked', this.checked);
    });

    // Individual checkbox change
    $('.lead-checkbox').on('change', function() {
        const totalCheckboxes = $('.lead-checkbox').length;
        const checkedCheckboxes = $('.lead-checkbox:checked').length;
        $('#selectAll').prop('checked', totalCheckboxes === checkedCheckboxes);
    });
});

// Bulk delete functionality
function bulkDelete() {
    const selectedLeads = $('.lead-checkbox:checked').map(function() {
        return this.value;
    }).get();

    if (selectedLeads.length === 0) {
        alert('Please select at least one lead to delete.');
        return;
    }

    // Clear previous inputs
    $('#bulkDeleteForm').find('input[name="lead_ids[]"]').remove();

    // Add selected lead IDs to form
    selectedLeads.forEach(leadId => {
        const input = $('<input>').attr({
            type: 'hidden',
            name: 'lead_ids[]',
            value: leadId
        });
        $('#bulkDeleteForm').append(input);
    });

    // Show modal
    $('#bulkDeleteModal').modal('show');
}
</script>
@endpush
