@extends('master.master')

@section('title', 'AI Chatbot Dashboard')

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
            <li class="breadcrumb-item active" aria-current="page">AI Chatbot Dashboard</li>
        </ol>
    </nav>

    <!-- Welcome Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="card-title mb-2">
                                <i data-feather="cpu" class="me-2"></i>
                                AI Chatbot Dashboard
                            </h4>
                            <p class="text-muted">Monitor AI performance, Q&A pairs, and visitor interactions.</p>
                        </div>
                        <div class="text-end">
                            <h6 class="text-muted">{{ now()->format('l, F j, Y') }}</h6>
                            <p class="text-muted">{{ now()->format('g:i A') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h3 class="mb-0">{{ $stats['total_qa_pairs'] }}</h3>
                            <p class="mb-0">Total Q&A Pairs</p>
                            <small class="text-white-50">Knowledge Base</small>
                        </div>
                        <div class="align-self-center">
                            <i data-feather="help-circle" class="text-white-50" style="width: 40px; height: 40px;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h3 class="mb-0">{{ $stats['active_qa_pairs'] }}</h3>
                            <p class="mb-0">Active Q&A Pairs</p>
                            <small class="text-white-50">Ready to Use</small>
                        </div>
                        <div class="align-self-center">
                            <i data-feather="check-circle" class="text-white-50" style="width: 40px; height: 40px;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h3 class="mb-0">{{ $stats['total_usage'] }}</h3>
                            <p class="mb-0">Total Usage</p>
                            <small class="text-white-50">Interactions</small>
                        </div>
                        <div class="align-self-center">
                            <i data-feather="trending-up" class="text-white-50" style="width: 40px; height: 40px;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h3 class="mb-0">{{ $stats['most_used'] ? $stats['most_used']->usage_count : 0 }}</h3>
                            <p class="mb-0">Most Used</p>
                            <small class="text-white-50">Top Answer</small>
                        </div>
                        <div class="align-self-center">
                            <i data-feather="star" class="text-white-50" style="width: 40px; height: 40px;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Visitor Questions Statistics -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h6 class="card-title mb-0">Visitor Questions Statistics</h6>
                        <a href="{{ route('admin.visitor-questions') }}" class="btn btn-primary btn-sm">
                            <i data-feather="eye" style="width: 16px; height: 16px;"></i> View All Questions
                        </a>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="d-flex align-items-center mb-3">
                                <div class="me-3">
                                    <div class="avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center">
                                        <i data-feather="help-circle" class="text-white" style="width: 20px; height: 20px;"></i>
                                    </div>
                                </div>
                                <div>
                                    <h6 class="mb-0">{{ $stats['total_questions'] }}</h6>
                                    <small class="text-muted">Total Questions</small>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="d-flex align-items-center mb-3">
                                <div class="me-3">
                                    <div class="avatar-sm bg-warning rounded-circle d-flex align-items-center justify-content-center">
                                        <i data-feather="clock" class="text-white" style="width: 20px; height: 20px;"></i>
                                    </div>
                                </div>
                                <div>
                                    <h6 class="mb-0">{{ $stats['pending_questions'] }}</h6>
                                    <small class="text-muted">Pending</small>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="d-flex align-items-center mb-3">
                                <div class="me-3">
                                    <div class="avatar-sm bg-success rounded-circle d-flex align-items-center justify-content-center">
                                        <i data-feather="check-circle" class="text-white" style="width: 20px; height: 20px;"></i>
                                    </div>
                                </div>
                                <div>
                                    <h6 class="mb-0">{{ $stats['answered_questions'] }}</h6>
                                    <small class="text-muted">Answered</small>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="d-flex align-items-center mb-3">
                                <div class="me-3">
                                    <div class="avatar-sm bg-info rounded-circle d-flex align-items-center justify-content-center">
                                        <i data-feather="award" class="text-white" style="width: 20px; height: 20px;"></i>
                                    </div>
                                </div>
                                <div>
                                    <h6 class="mb-0">{{ $stats['converted_questions'] }}</h6>
                                    <small class="text-muted">Converted</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Contact Suggestions Info -->
                    <div class="alert alert-info d-flex align-items-start mt-3" role="alert">
                        <i data-feather="info" class="me-2 flex-shrink-0" style="width: 20px; height: 20px;"></i>
                        <div>
                            <h6 class="mb-1">Smart Contact Suggestions</h6>
                            <p class="mb-0">When AI can't find an answer, it automatically provides intelligent contact suggestions based on the question type (pricing, project, support, etc.) and includes a direct link to your contact page.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- AI Test Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title mb-3">
                        <i data-feather="message-square" class="me-2"></i>
                        Test AI Response
                    </h6>
                    <form id="testAIForm">
                        @csrf
                        <div class="row">
                            <div class="col-md-9">
                                <div class="mb-3">
                                    <label for="test_question" class="form-label">Test Question</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-primary">
                                            <i data-feather="help-circle" class="text-white" style="width: 18px; height: 18px;"></i>
                                        </span>
                                        <input type="text" class="form-control" id="test_question" name="test_question"
                                               placeholder="Enter a question to test AI response..." required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">&nbsp;</label>
                                    <button type="submit" class="btn btn-primary d-block w-100">
                                        <i data-feather="send" style="width: 16px; height: 16px;"></i> Test AI
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>

                    <div id="aiResponse" class="mt-3" style="display: none;">
                        <div class="alert alert-success d-flex align-items-start">
                            <i data-feather="message-circle" class="me-2 flex-shrink-0" style="width: 20px; height: 20px;"></i>
                            <div class="flex-grow-1">
                                <h6 class="mb-2">AI Response:</h6>
                                <div id="responseText"></div>
                            </div>
                        </div>
                    </div>
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
                            <i data-feather="list" class="me-2"></i>
                            Q&A Pairs (Most Used)
                        </h6>
                        <div>
                            <a href="{{ route('admin.qa-management') }}" class="btn btn-primary btn-sm me-2">
                                <i data-feather="settings" style="width: 16px; height: 16px;"></i> Manage Q&A
                            </a>
                            <a href="{{ route('admin.ai-control') }}" class="btn btn-outline-primary btn-sm">
                                <i data-feather="cpu" style="width: 16px; height: 16px;"></i> AI Control
                            </a>
                        </div>
                    </div>

                    @if($qaPairs->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Question</th>
                                        <th>Category</th>
                                        <th>Usage Count</th>
                                        <th>Status</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($qaPairs as $qa)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i data-feather="message-circle" class="text-primary me-2" style="width: 18px; height: 18px;"></i>
                                                <strong>{{ Str::limit($qa->question, 50) }}</strong>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $qa->category ?? 'General' }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $qa->usage_count }}</span>
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
                                            <button type="button" class="btn btn-sm btn-primary me-1"
                                                    onclick="testQuestion('{{ addslashes($qa->question) }}')" title="Test Question">
                                                <i data-feather="send" style="width: 14px; height: 14px;"></i>
                                            </button>
                                            <a href="{{ route('admin.qa-management') }}?edit={{ $qa->id }}"
                                               class="btn btn-sm btn-warning" title="Edit">
                                                <i data-feather="edit-2" style="width: 14px; height: 14px;"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i data-feather="inbox" class="text-muted mb-3" style="width: 64px; height: 64px;"></i>
                            <h6 class="text-muted">No Q&A pairs found</h6>
                            <p class="text-muted">Start by adding your first Q&A pair.</p>
                            <a href="{{ route('admin.qa-management') }}" class="btn btn-primary">
                                <i data-feather="plus"></i> Add Q&A Pair
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('js')
<script>
// Initialize Feather icons
feather.replace();

// Test AI Form Handler
document.getElementById('testAIForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const question = formData.get('test_question');
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalBtnText = submitBtn.innerHTML;

    if (!question.trim()) {
        alert('Please enter a question');
        return;
    }

    // Show loading state
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Testing...';
    submitBtn.disabled = true;

    try {
        const response = await fetch('{{ route("admin.test-ai-response") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ test_question: question })
        });

        const data = await response.json();

        if (data.success) {
            document.getElementById('responseText').innerHTML = data.data.response.replace(/\n/g, '<br>');
            document.getElementById('aiResponse').style.display = 'block';
            
            // Scroll to response
            document.getElementById('aiResponse').scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            
            // Re-initialize feather icons
            feather.replace();
        } else {
            alert('Error: ' + data.message);
            document.getElementById('aiResponse').style.display = 'none';
        }
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred while testing AI response');
        document.getElementById('aiResponse').style.display = 'none';
    } finally {
        // Reset button
        submitBtn.innerHTML = originalBtnText;
        submitBtn.disabled = false;
        feather.replace();
    }
});

// Test Question function
function testQuestion(question) {
    document.getElementById('test_question').value = question;
    document.getElementById('test_question').focus();
    
    // Scroll to form
    document.getElementById('testAIForm').scrollIntoView({ behavior: 'smooth', block: 'center' });
}
</script>
@endsection
