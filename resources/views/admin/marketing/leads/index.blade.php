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
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->name }}"
                                            {{ request('category') == $category->name ? 'selected' : '' }}>
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
                                    @foreach ($municipalities as $municipality)
                                        <option value="{{ $municipality }}"
                                            {{ request('municipality') == $municipality ? 'selected' : '' }}>
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
                                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active
                                    </option>
                                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>
                                        Inactive</option>
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
                <h6 class="m-0 font-weight-bold text-primary">Leads ({{ count($leads) }} total)</h6>

            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="dataTableExample">
                        <thead class="thead-dark">
                            <tr>
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
                                        <a href="{{ route('admin.marketing.leads.show', $lead) }}"
                                            class="text-primary font-weight-bold">
                                            {{ Str::limit($lead->name, 30) }}
                                        </a>
                                    </td>
                                    <td>
                                        @if ($lead->email)
                                            <span class="text-success">{{ Str::limit($lead->email, 25) }}</span>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($lead->phone)
                                            <span class="text-info">{{ $lead->phone }}</span>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-info">{{ $lead->category }}</span>
                                    </td>
                                    <td>
                                        @if ($lead->municipality)
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
                                        <div class="action-buttons">
                                            <a href="{{ route('admin.marketing.leads.show', $lead) }}"
                                                class="btn btn-info btn-icon" title="View Details">
                                                <i data-feather="eye"></i>
                                            </a>
                                            <a href="{{ route('admin.marketing.leads.edit', $lead) }}"
                                                class="btn btn-primary btn-icon" title="Edit Lead">
                                                <i data-feather="edit"></i>
                                            </a>
                                            <form method="POST"
                                                action="{{ route('admin.marketing.leads.toggle-status', $lead) }}"
                                                class="d-inline-block m-0">
                                                @csrf
                                                <button type="submit"
                                                    class="btn btn-{{ $lead->is_active ? 'secondary' : 'success' }} btn-icon"
                                                    title="{{ $lead->is_active ? 'Deactivate' : 'Activate' }}">
                                                    <i data-feather="{{ $lead->is_active ? 'pause' : 'play' }}"></i>
                                                </button>
                                            </form>
                                            <form method="POST"
                                                action="{{ route('admin.marketing.leads.destroy', $lead) }}"
                                                class="d-inline-block m-0"
                                                onsubmit="return confirm('Are you sure you want to delete this lead?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-icon"
                                                    title="Delete Lead">
                                                    <i data-feather="trash-2"></i>
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

                <!-- Laravel Pagination removed - DataTable handles pagination client-side -->
            </div>
        </div>
    </div>


@endsection

@push('styles')
    <style>
        .table th,
        .table td {
            vertical-align: middle;
            padding: 0.75rem;
        }

        .table thead th {
            background-color: #4e73df;
            color: white;
            font-weight: 600;
            border: none;
        }

        .table tbody tr:hover {
            background-color: #f8f9fc;
        }

        .table-bordered {
            border: 1px solid #e3e6f0;
        }

        .table-bordered th,
        .table-bordered td {
            border: 1px solid #e3e6f0;
        }

        .btn-sm {
            padding: 0.375rem 0.5rem;
            font-size: 0.875rem;
            line-height: 1.5;
            min-width: 35px;
        }

        .btn-sm i {
            font-size: 0.875rem;
        }

        .action-buttons {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
        }

        .action-buttons>a,
        .action-buttons>form {
            margin-right: 0.25rem;
            margin-bottom: 0.25rem;
        }

        .action-buttons>a:last-child,
        .action-buttons>form:last-child {
            margin-right: 0;
        }

        .action-buttons .btn {
            margin: 0;
        }

        .action-buttons form {
            display: inline-block;
        }

        /* Button icon styling */
        .btn-icon {
            padding: 0.5rem;
            width: 38px;
            height: 38px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 0.35rem;
        }

        .btn-icon i {
            width: 18px;
            height: 18px;
        }

        .action-buttons .btn-icon {
            margin-right: 0.25rem;
        }

        .action-buttons .btn-icon:last-child,
        .action-buttons form:last-child .btn-icon {
            margin-right: 0;
        }

        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter,
        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_processing,
        .dataTables_wrapper .dataTables_paginate {
            color: #333;
        }

        .dataTables_wrapper .dataTables_filter input {
            border: 1px solid #d1d3e2;
            border-radius: 0.35rem;
            padding: 0.375rem 0.75rem;
        }

        .card {
            border: none;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }

        .card-header {
            background-color: #f8f9fc;
            border-bottom: 1px solid #e3e6f0;
        }

        .badge {
            padding: 0.35em 0.65em;
            font-weight: 600;
        }

        @media (max-width: 768px) {
            .table-responsive {
                display: block;
                width: 100%;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            .btn-sm {
                padding: 0.25rem 0.4rem;
                font-size: 0.75rem;
                min-width: 30px;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            var table;
            window.selectedLeads = []; // Store selected lead IDs globally

            // Initialize DataTable immediately to prevent auto-initialization by data-table.js
            if ($.fn.DataTable && $('#dataTableExample').length) {
                // Destroy existing DataTable if it exists (in case data-table.js already initialized it)
                if ($.fn.DataTable.isDataTable('#dataTableExample')) {
                    $('#dataTableExample').DataTable().destroy();
                }

                // Small delay to ensure DOM is ready
                setTimeout(function() {

                    // Initialize DataTable with custom settings
                    table = $('#dataTableExample').DataTable({
                        "aLengthMenu": [
                            [10, 25, 50, 100, -1],
                            [10, 25, 50, 100, "All"]
                        ],
                        "iDisplayLength": -1, // Show all rows by default
                        "language": {
                            search: ""
                        },
                        "order": [
                            [1, "asc"]
                        ], // Sort by Name column
                        "columnDefs": [{
                                "orderable": false,
                                "targets": [0, 7]
                            } // Disable sorting for checkbox and actions columns
                        ]
                    });

                    // Customize search input
                    $('#dataTableExample').each(function() {
                        var datatable = $(this);
                        // SEARCH - Add the placeholder for Search and Turn this into in-line form control
                        var search_input = datatable.closest('.dataTables_wrapper').find(
                            'div[id$=_filter] input');
                        search_input.attr('placeholder', 'Search');
                        search_input.removeClass('form-control-sm');
                        // LENGTH - Inline-Form control
                        var length_sel = datatable.closest('.dataTables_wrapper').find(
                            'div[id$=_length] select');
                        length_sel.removeClass('form-control-sm');
                    });

                    // Re-initialize checkboxes after DataTable pagination
                    table.on('draw', function() {
                        // Restore checkbox states from global array
                        $('#dataTableExample tbody .lead-checkbox').each(function() {
                            const leadId = $(this).val();
                            if (window.selectedLeads.indexOf(leadId) !== -1) {
                                $(this).prop('checked', true);
                            } else {
                                $(this).prop('checked', false);
                            }
                        });

                        updateSelectAllState();

                        // Re-initialize Feather icons after pagination
                        if (typeof feather !== 'undefined') {
                            feather.replace();
                        }
                    });
                }, 50);
            }

            // Select All functionality - Use event delegation for DataTable
            $(document).on('change', '#selectAll', function() {
                const isChecked = this.checked;

                // Check/uncheck all visible checkboxes
                $('#dataTableExample tbody .lead-checkbox').each(function() {
                    $(this).prop('checked', isChecked);
                    const leadId = $(this).val();

                    if (isChecked) {
                        // Add to selected array if not already present
                        if (window.selectedLeads.indexOf(leadId) === -1) {
                            window.selectedLeads.push(leadId);
                        }
                    } else {
                        // Remove from selected array
                        const index = window.selectedLeads.indexOf(leadId);
                        if (index > -1) {
                            window.selectedLeads.splice(index, 1);
                        }
                    }
                });
            });

            // Individual checkbox change - Use event delegation for DataTable
            $(document).on('change', '.lead-checkbox', function() {
                const leadId = $(this).val();
                const isChecked = $(this).is(':checked');

                if (isChecked) {
                    // Add to selected array if not already present
                    if (window.selectedLeads.indexOf(leadId) === -1) {
                        window.selectedLeads.push(leadId);
                    }
                } else {
                    // Remove from selected array
                    const index = window.selectedLeads.indexOf(leadId);
                    if (index > -1) {
                        window.selectedLeads.splice(index, 1);
                    }
                    // Uncheck select all if individual checkbox is unchecked
                    $('#selectAll').prop('checked', false);
                }

                updateSelectAllState();
            });

            // Function to update select all checkbox state
            function updateSelectAllState() {
                const visibleCheckboxes = $('#dataTableExample tbody .lead-checkbox');
                const visibleChecked = $('#dataTableExample tbody .lead-checkbox:checked');

                if (visibleCheckboxes.length > 0 && visibleCheckboxes.length === visibleChecked.length) {
                    $('#selectAll').prop('checked', true);
                } else {
                    $('#selectAll').prop('checked', false);
                }
            }

            // Initialize Feather icons after DataTable renders
            if (typeof feather !== 'undefined') {
                setTimeout(function() {
                    feather.replace();
                }, 200);
            }
        });

        // Bulk delete functionality
        function bulkDelete() {
            // Get all selected leads from the global array
            var selectedLeads = [];

            // First, get from the global array (stores selections across all pages)
            if (typeof window.selectedLeads !== 'undefined' && window.selectedLeads.length > 0) {
                selectedLeads = window.selectedLeads.slice(); // Copy array
            }

            // Also get currently visible checked checkboxes (in case global array is not updated)
            $('.lead-checkbox:checked').each(function() {
                const leadId = $(this).val();
                if (selectedLeads.indexOf(leadId) === -1) {
                    selectedLeads.push(leadId);
                }
            });

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
