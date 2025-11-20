@extends('master.master')

@section('title', 'Visitor Questions Management')

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
            <li class="breadcrumb-item active" aria-current="page">Visitor Questions</li>
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
                                <i data-feather="message-circle" class="me-2"></i>
                                Visitor Questions Management
                            </h4>
                            <p class="text-muted">Monitor and respond to visitor questions from your AI chatbot.</p>
                        </div>
                        <div>
                            <a href="{{ route('admin.ai-dashboard') }}" class="btn btn-outline-primary me-2">
                                <i data-feather="cpu" style="width: 16px; height: 16px;"></i> AI Dashboard
                            </a>
                            <a href="{{ route('admin.qa-management') }}" class="btn btn-primary">
                                <i data-feather="plus" style="width: 16px; height: 16px;"></i> Manage Q&A
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

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h3 class="mb-0">{{ $stats['total_questions'] }}</h3>
                            <p class="mb-0">Total Questions</p>
                            <small class="text-white-50">All Time</small>
                        </div>
                        <div class="align-self-center">
                            <i data-feather="help-circle" class="text-white-50" style="width: 40px; height: 40px;"></i>
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
                            <h3 class="mb-0">{{ $stats['pending_questions'] }}</h3>
                            <p class="mb-0">Pending Questions</p>
                            <small class="text-white-50">Needs Review</small>
                        </div>
                        <div class="align-self-center">
                            <i data-feather="clock" class="text-white-50" style="width: 40px; height: 40px;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h3 class="mb-0">{{ $stats['answered_questions'] }}</h3>
                            <p class="mb-0">Answered Questions</p>
                            <small class="text-white-50">Resolved</small>
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
                            <h3 class="mb-0">{{ $stats['converted_questions'] }}</h3>
                            <p class="mb-0">Converted Questions</p>
                            <small class="text-white-50">To Q&A Pairs</small>
                        </div>
                        <div class="align-self-center">
                            <i data-feather="award" class="text-white-50" style="width: 40px; height: 40px;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Visitor Questions List -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h6 class="card-title mb-0">
                            <i data-feather="users" class="me-2"></i>
                            Visitor Questions
                        </h6>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Question</th>
                                    <th>Status</th>
                                    <th>Answer</th>
                                    <th>Visitor Info</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($visitorQuestions as $question)
                                <tr>
                                    <td>
                                        <strong>{{ Str::limit($question->question, 60) }}</strong>
                                        @if($question->qaPair)
                                            <br><small class="text-muted">Matched with Q&A #{{ $question->qaPair->id }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @switch($question->status)
                                            @case('pending')
                                                <span class="badge bg-warning">Pending</span>
                                                @break
                                            @case('answered')
                                                <span class="badge bg-success">Answered</span>
                                                @break
                                            @case('converted')
                                                <span class="badge bg-info">Converted</span>
                                                @break
                                            @case('no_match')
                                                <span class="badge bg-danger">No Match</span>
                                                @break
                                            @default
                                                <span class="badge bg-secondary">{{ $question->status }}</span>
                                        @endswitch
                                    </td>
                                    <td>
                                        @if($question->answer)
                                            <span class="text-success">{{ Str::limit($question->answer, 50) }}</span>
                                        @else
                                            <span class="text-muted">No answer yet</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small>
                                            IP: {{ $question->visitor_ip }}<br>
                                            Session: {{ Str::limit($question->visitor_session, 10) }}
                                        </small>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $question->created_at->format('M d, Y H:i') }}</small>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info me-1"
                                                onclick="viewQuestion({{ $question->id }})" title="View Details">
                                            <i data-feather="eye" style="width: 14px; height: 14px;"></i>
                                        </button>
                                        @if($question->status === 'pending' || $question->status === 'no_match')
                                            <button type="button" class="btn btn-sm btn-success me-1"
                                                    onclick="answerQuestion({{ $question->id }})" title="Answer Question">
                                                <i data-feather="message-square" style="width: 14px; height: 14px;"></i>
                                            </button>
                                        @endif
                                        @if($question->status === 'answered' && !$question->is_converted)
                                            <form action="{{ route('admin.mark-converted', $question->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-warning" title="Mark as Converted">
                                                    <i data-feather="award" style="width: 14px; height: 14px;"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <i data-feather="inbox" class="text-muted mb-3" style="width: 48px; height: 48px;"></i>
                                        <h6 class="text-muted">No visitor questions found</h6>
                                        <p class="text-muted">Questions from visitors will appear here.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($visitorQuestions->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $visitorQuestions->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- View Question Modal -->
<div class="modal fade" id="viewQuestionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Question Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="questionDetails">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>

<!-- Answer Question Modal -->
<div class="modal fade" id="answerQuestionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Answer Question</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="answerQuestionForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Question:</label>
                        <div id="questionText" class="form-control-plaintext bg-light p-2 rounded"></div>
                    </div>

                    <div class="mb-3">
                        <label for="answer" class="form-label">Your Answer <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="answer" name="answer" rows="4" required></textarea>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="create_qa_pair" name="create_qa_pair" value="1">
                            <label class="form-check-label" for="create_qa_pair">
                                Create Q&A pair from this question
                            </label>
                        </div>
                    </div>

                    <div id="qaPairFields" style="display: none;">
                        <div class="mb-3">
                            <label for="question_for_qa" class="form-label">Question for Q&A Pair <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="question_for_qa" name="question">
                        </div>
                        <div class="mb-3">
                            <label for="category" class="form-label">Category</label>
                            <input type="text" class="form-control" id="category" name="category" placeholder="e.g., Services, Pricing, Process">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Submit Answer</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('js')
<script>
// Initialize Feather icons
feather.replace();

function viewQuestion(id) {
    // This would typically fetch data via AJAX
    document.getElementById('questionDetails').innerHTML = `
        <div class="text-center py-4">
            <i data-feather="message-circle" class="text-primary mb-3" style="width: 48px; height: 48px;"></i>
            <h6>Question ID: ${id}</h6>
            <p class="text-muted">This would show detailed information about the visitor question.</p>
            <p class="text-muted">You can implement AJAX loading here to fetch and display the full details.</p>
        </div>
    `;

    const modal = new bootstrap.Modal(document.getElementById('viewQuestionModal'));
    modal.show();
    
    // Re-initialize feather icons in modal
    setTimeout(() => feather.replace(), 100);
}

function answerQuestion(id) {
    // Set form action
    document.getElementById('answerQuestionForm').action = `/admin/answer-visitor-question/${id}`;

    // This would typically fetch question data via AJAX
    document.getElementById('questionText').textContent = `Question ID: ${id} - This would show the actual question text.`;

    const modal = new bootstrap.Modal(document.getElementById('answerQuestionModal'));
    modal.show();
}

// Show/hide Q&A pair fields
document.getElementById('create_qa_pair').addEventListener('change', function() {
    const qaPairFields = document.getElementById('qaPairFields');
    const questionField = document.getElementById('question_for_qa');

    if (this.checked) {
        qaPairFields.style.display = 'block';
        questionField.required = true;
    } else {
        qaPairFields.style.display = 'none';
        questionField.required = false;
    }
});
</script>
@endsection
