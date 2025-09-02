@extends('master.master')

@section('title', 'Q&A Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Q&A Management</h4>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Add/Edit Q&A Form -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        @if(request('edit'))
                            Edit Q&A Pair
                        @else
                            Add New Q&A Pair
                        @endif
                    </h4>
                </div>
                <div class="card-body">
                    @php
                        $editingQA = request('edit') ? $qaPairs->find(request('edit')) : null;
                    @endphp

                    <form action="{{ request('edit') ? route('admin.qa-update', request('edit')) : route('admin.qa-store') }}" method="POST">
                        @csrf
                        @if(request('edit'))
                            @method('PUT')
                        @endif

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="question" class="form-label">Question <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="question" name="question"
                                           value="{{ old('question', $editingQA->question ?? '') }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="category" class="form-label">Category</label>
                                    <input type="text" class="form-control" id="category" name="category"
                                           value="{{ old('category', $editingQA->category ?? '') }}"
                                           placeholder="e.g., Services, Pricing, Process">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="answer_1" class="form-label">Answer 1 <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="answer_1" name="answer_1" rows="3" required>{{ old('answer_1', $editingQA->answer_1 ?? '') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="answer_2" class="form-label">Answer 2</label>
                                    <textarea class="form-control" id="answer_2" name="answer_2" rows="3">{{ old('answer_2', $editingQA->answer_2 ?? '') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="answer_3" class="form-label">Answer 3</label>
                                    <textarea class="form-control" id="answer_3" name="answer_3" rows="3">{{ old('answer_3', $editingQA->answer_3 ?? '') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="answer_4" class="form-label">Answer 4</label>
                                    <textarea class="form-control" id="answer_4" name="answer_4" rows="3">{{ old('answer_4', $editingQA->answer_4 ?? '') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="answer_5" class="form-label">Answer 5</label>
                                    <textarea class="form-control" id="answer_5" name="answer_5" rows="3">{{ old('answer_5', $editingQA->answer_5 ?? '') }}</textarea>
                                </div>
                            </div>
                        </div>

                        @if(request('edit'))
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1"
                                               {{ old('is_active', $editingQA->is_active ?? true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">
                                            Active
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="mdi mdi-content-save"></i>
                                    {{ request('edit') ? 'Update' : 'Create' }} Q&A Pair
                                </button>
                                @if(request('edit'))
                                    <a href="{{ route('admin.qa-management') }}" class="btn btn-secondary">
                                        <i class="mdi mdi-close"></i> Cancel
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Q&A Pairs List -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">All Q&A Pairs</h4>
                    <a href="{{ route('admin.ai-dashboard') }}" class="btn btn-info">
                        <i class="mdi mdi-robot"></i> AI Dashboard
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Question</th>
                                    <th>Category</th>
                                    <th>Answers Count</th>
                                    <th>Usage Count</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($qaPairs as $qa)
                                <tr>
                                    <td>
                                        <strong>{{ Str::limit($qa->question, 60) }}</strong>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $qa->category ?? 'General' }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ count($qa->answers) }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">{{ $qa->usage_count }}</span>
                                    </td>
                                    <td>
                                        @if($qa->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td>{{ $qa->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-info"
                                                    onclick="viewQA({{ $qa->id }})" title="View Details">
                                                <i class="mdi mdi-eye"></i>
                                            </button>
                                            <a href="{{ route('admin.qa-management', ['edit' => $qa->id]) }}"
                                               class="btn btn-sm btn-outline-warning" title="Edit">
                                                <i class="mdi mdi-pencil"></i>
                                            </a>
                                            <form action="{{ route('admin.qa-toggle', $qa->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-{{ $qa->is_active ? 'danger' : 'success' }}"
                                                        title="{{ $qa->is_active ? 'Deactivate' : 'Activate' }}">
                                                    <i class="mdi mdi-{{ $qa->is_active ? 'pause' : 'play' }}"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.qa-delete', $qa->id) }}" method="POST" class="d-inline"
                                                  onsubmit="return confirm('Are you sure you want to delete this Q&A pair?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                    <i class="mdi mdi-delete"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">No Q&A pairs found</td>
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

<!-- View Q&A Modal -->
<div class="modal fade" id="viewQAModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Q&A Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="qaDetails">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>

<script>
function viewQA(id) {
    // This would typically fetch data via AJAX
    // For now, we'll show a simple message
    document.getElementById('qaDetails').innerHTML = `
        <p>Q&A ID: ${id}</p>
        <p>This would show detailed information about the Q&A pair.</p>
        <p>You can implement AJAX loading here to fetch and display the full details.</p>
    `;

    new bootstrap.Modal(document.getElementById('viewQAModal')).show();
}
</script>
@endsection
