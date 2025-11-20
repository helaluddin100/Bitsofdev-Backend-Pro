@extends('master.master')

@section('title', 'Q&A Management')

@push('styles')
<style>
    .avatar-sm {
        width: 40px;
        height: 40px;
    }
</style>
@endpush

@section('content')
<div class="page-content">
    <nav class="page-breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.ai-dashboard') }}">AI Chatbot</a></li>
            <li class="breadcrumb-item active" aria-current="page">Q&A Management</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="card-title mb-2">
                                <i data-feather="list" class="me-2"></i>
                                Q&A Management
                            </h4>
                            <p class="text-muted">Manage your AI chatbot's question and answer pairs.</p>
                        </div>
                        <div>
                            <a href="{{ route('admin.ai-dashboard') }}" class="btn btn-outline-primary">
                                <i data-feather="cpu" style="width: 16px; height: 16px;"></i> AI Dashboard
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="row mb-3">
            <div class="col-12">
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i data-feather="check-circle" class="me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="row mb-3">
            <div class="col-12">
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i data-feather="alert-circle" class="me-2"></i>
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        </div>
    @endif

    <!-- Add/Edit Q&A Form -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title mb-4">
                        <i data-feather="@if(request('edit'))edit-2@else plus-circle @endif" class="me-2"></i>
                        @if(request('edit'))
                            Edit Q&A Pair
                        @else
                            Add New Q&A Pair
                        @endif
                    </h6>
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
                                    <i data-feather="save" style="width: 16px; height: 16px;"></i>
                                    {{ request('edit') ? 'Update' : 'Create' }} Q&A Pair
                                </button>
                                @if(request('edit'))
                                    <a href="{{ route('admin.qa-management') }}" class="btn btn-secondary">
                                        <i data-feather="x" style="width: 16px; height: 16px;"></i> Cancel
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
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h6 class="card-title mb-0">
                            <i data-feather="database" class="me-2"></i>
                            All Q&A Pairs
                        </h6>
                        <a href="{{ route('admin.ai-dashboard') }}" class="btn btn-info btn-sm">
                            <i data-feather="cpu" style="width: 16px; height: 16px;"></i> AI Dashboard
                        </a>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover">
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
                                    <td>
                                        <small class="text-muted">{{ $qa->created_at->format('M d, Y') }}</small>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info me-1"
                                                onclick="viewQA({{ $qa->id }})" title="View Details">
                                            <i data-feather="eye" style="width: 14px; height: 14px;"></i>
                                        </button>
                                        <a href="{{ route('admin.qa-management', ['edit' => $qa->id]) }}"
                                           class="btn btn-sm btn-warning me-1" title="Edit">
                                            <i data-feather="edit-2" style="width: 14px; height: 14px;"></i>
                                        </a>
                                        <form action="{{ route('admin.qa-toggle', $qa->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-{{ $qa->is_active ? 'danger' : 'success' }} me-1"
                                                    title="{{ $qa->is_active ? 'Deactivate' : 'Activate' }}">
                                                <i data-feather="{{ $qa->is_active ? 'pause' : 'play' }}" style="width: 14px; height: 14px;"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.qa-delete', $qa->id) }}" method="POST" class="d-inline"
                                              onsubmit="return confirm('Are you sure you want to delete this Q&A pair?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                <i data-feather="trash-2" style="width: 14px; height: 14px;"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <i data-feather="inbox" class="text-muted mb-3" style="width: 48px; height: 48px;"></i>
                                        <h6 class="text-muted">No Q&A pairs found</h6>
                                        <p class="text-muted">Start by adding your first Q&A pair above.</p>
                                    </td>
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

@endsection

@section('js')
<script>
// Initialize Feather icons
feather.replace();

function viewQA(id) {
    // This would typically fetch data via AJAX
    // For now, we'll show a simple message
    document.getElementById('qaDetails').innerHTML = `
        <div class="text-center py-4">
            <i data-feather="message-circle" class="text-primary mb-3" style="width: 48px; height: 48px;"></i>
            <h6>Q&A ID: ${id}</h6>
            <p class="text-muted">This would show detailed information about the Q&A pair.</p>
            <p class="text-muted">You can implement AJAX loading here to fetch and display the full details.</p>
        </div>
    `;

    const modal = new bootstrap.Modal(document.getElementById('viewQAModal'));
    modal.show();
    
    // Re-initialize feather icons in modal
    setTimeout(() => feather.replace(), 100);
}
</script>
@endsection
