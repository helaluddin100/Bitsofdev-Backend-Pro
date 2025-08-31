@extends('master.master')

@section('content')
    <div class="page-content">
        <nav class="page-breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">About Page Management</li>
            </ol>
        </nav>

        <div class="row">
            <div class="col-12">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
            </div>
        </div>

        <!-- Company Information Section -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Company Information</h4>
                        <a href="{{ route('admin.about.edit') }}" class="btn btn-primary btn-sm">
                            <i data-feather="edit-3"></i> Edit Information
                        </a>
                    </div>
                    <div class="card-body">
                        @if ($about)
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Company Name</h6>
                                    <p class="text-muted">{{ $about->company_name }}</p>
                                </div>
                                <div class="col-md-6">
                                    <h6>Status</h6>
                                    <span class="badge bg-{{ $about->is_active ? 'success' : 'danger' }}">
                                        {{ $about->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Years Experience</h6>
                                    <p class="text-muted">{{ $about->years_experience }}+</p>
                                </div>
                                <div class="col-md-6">
                                    <h6>Projects Delivered</h6>
                                    <p class="text-muted">{{ $about->projects_delivered }}+</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Happy Clients</h6>
                                    <p class="text-muted">{{ $about->happy_clients }}+</p>
                                </div>
                                <div class="col-md-6">
                                    <h6>Support Availability</h6>
                                    <p class="text-muted">{{ $about->support_availability }}</p>
                                </div>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <p class="text-muted">No company information found.</p>
                                <a href="{{ route('admin.about.edit') }}" class="btn btn-primary">
                                    <i data-feather="plus"></i> Create Company Information
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Company Values Section -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Company Values</h4>
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                            data-bs-target="#addValueModal">
                            <i data-feather="plus"></i> Add Value
                        </button>
                    </div>
                    <div class="card-body">
                        @if ($values->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Order</th>
                                            <th>Title</th>
                                            <th>Description</th>
                                            <th>Icon</th>
                                            <th>Color</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($values as $value)
                                            <tr>
                                                <td>{{ $value->sort_order }}</td>
                                                <td>{{ $value->title }}</td>
                                                <td>{{ Str::limit($value->description, 50) }}</td>
                                                <td>{{ $value->icon }}</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="w-4 h-4 rounded mr-2"
                                                            style="background: linear-gradient(to right, {{ str_replace('from-', '', $value->color) }})">
                                                        </div>
                                                        {{ $value->color }}
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-{{ $value->is_active ? 'success' : 'danger' }}">
                                                        {{ $value->is_active ? 'Active' : 'Inactive' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-outline-primary"
                                                        onclick="editValue({{ $value->id }})">
                                                        <i data-feather="edit-3"></i>
                                                    </button>
                                                    <form action="{{ route('admin.about.values.destroy', $value) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                                            onclick="return confirm('Are you sure?')">
                                                            <i data-feather="trash-2"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <p class="text-muted">No company values found.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Company Processes Section -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Company Processes</h4>
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                            data-bs-target="#addProcessModal">
                            <i data-feather="plus"></i> Add Process
                        </button>
                    </div>
                    <div class="card-body">
                        @if ($processes->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Order</th>
                                            <th>Step</th>
                                            <th>Title</th>
                                            <th>Description</th>
                                            <th>Icon</th>
                                            <th>Color</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($processes as $process)
                                            <tr>
                                                <td>{{ $process->sort_order }}</td>
                                                <td>{{ $process->step_number }}</td>
                                                <td>{{ $process->title }}</td>
                                                <td>{{ Str::limit($process->description, 50) }}</td>
                                                <td>{{ $process->icon }}</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="w-4 h-4 rounded mr-2"
                                                            style="background: linear-gradient(to right, {{ str_replace('from-', '', $process->color) }})">
                                                        </div>
                                                        {{ $process->color }}
                                                    </div>
                                                </td>
                                                <td>
                                                    <span
                                                        class="badge bg-{{ $process->is_active ? 'success' : 'danger' }}">
                                                        {{ $process->is_active ? 'Active' : 'Inactive' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-outline-primary"
                                                        onclick="editProcess({{ $process->id }})">
                                                        <i data-feather="edit-3"></i>
                                                    </button>
                                                    <form action="{{ route('admin.about.processes.destroy', $process) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                                            onclick="return confirm('Are you sure?')">
                                                            <i data-feather="trash-2"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <p class="text-muted">No company processes found.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Value Modal -->
    <div class="modal fade" id="addValueModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Company Value</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('admin.about.values.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="title" class="form-label">Title</label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="icon" class="form-label">Icon</label>
                            <input type="text" class="form-control" id="icon" name="icon"
                                placeholder="e.g., Target, Users, Award">
                        </div>
                        <div class="mb-3">
                            <label for="color" class="form-label">Color</label>
                            <select class="form-select" id="color" name="color" required>
                                <option value="from-blue-500 to-blue-600">Blue</option>
                                <option value="from-purple-500 to-purple-600">Purple</option>
                                <option value="from-green-500 to-green-600">Green</option>
                                <option value="from-red-500 to-red-600">Red</option>
                                <option value="from-yellow-500 to-yellow-600">Yellow</option>
                                <option value="from-pink-500 to-pink-600">Pink</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="sort_order" class="form-label">Sort Order</label>
                            <input type="number" class="form-control" id="sort_order" name="sort_order" value="0"
                                min="0" required>
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="is_active" name="is_active" checked>
                                <label class="form-check-label" for="is_active">Active</label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Value</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Process Modal -->
    <div class="modal fade" id="addProcessModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Company Process</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('admin.about.processes.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="step_number" class="form-label">Step Number</label>
                            <input type="text" class="form-control" id="step_number" name="step_number"
                                placeholder="e.g., 01, 02, 03" required>
                        </div>
                        <div class="mb-3">
                            <label for="process_title" class="form-label">Title</label>
                            <input type="text" class="form-control" id="process_title" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label for="process_description" class="form-label">Description</label>
                            <textarea class="form-control" id="process_description" name="description" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="process_icon" class="form-label">Icon</label>
                            <input type="text" class="form-control" id="process_icon" name="icon"
                                placeholder="e.g., Lightbulb, Target, Zap">
                        </div>
                        <div class="mb-3">
                            <label for="process_color" class="form-label">Color</label>
                            <select class="form-select" id="process_color" name="color" required>
                                <option value="from-blue-500 to-blue-600">Blue</option>
                                <option value="from-purple-500 to-purple-600">Purple</option>
                                <option value="from-green-500 to-green-600">Green</option>
                                <option value="from-red-500 to-red-600">Red</option>
                                <option value="from-yellow-500 to-yellow-600">Yellow</option>
                                <option value="from-pink-500 to-pink-600">Pink</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="process_sort_order" class="form-label">Sort Order</label>
                            <input type="number" class="form-control" id="process_sort_order" name="sort_order"
                                value="0" min="0" required>
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="process_is_active" name="is_active"
                                    checked>
                                <label class="form-check-label" for="process_is_active">Active</label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Process</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        function editValue(valueId) {
            // Implement edit functionality
            alert('Edit value ' + valueId + ' - To be implemented');
        }

        function editProcess(processId) {
            // Implement edit functionality
            alert('Edit process ' + processId + ' - To be implemented');
        }
    </script>
@endsection
