@extends('master.master')

@section('title', 'Visitor Questions Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Visitor Questions Management</h4>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card widget-flat">
                <div class="card-body">
                    <div class="float-end">
                        <i class="mdi mdi-help-circle widget-icon bg-primary-lighten text-primary"></i>
                    </div>
                    <h5 class="text-muted fw-normal mt-0" title="Total Questions">Total Questions</h5>
                    <h3 class="mt-3 mb-3">{{ $stats['total_questions'] }}</h3>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card widget-flat">
                <div class="card-body">
                    <div class="float-end">
                        <i class="mdi mdi-clock widget-icon bg-warning-lighten text-warning"></i>
                    </div>
                    <h5 class="text-muted fw-normal mt-0" title="Pending Questions">Pending Questions</h5>
                    <h3 class="mt-3 mb-3">{{ $stats['pending_questions'] }}</h3>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card widget-flat">
                <div class="card-body">
                    <div class="float-end">
                        <i class="mdi mdi-check-circle widget-icon bg-success-lighten text-success"></i>
                    </div>
                    <h5 class="text-muted fw-normal mt-0" title="Answered Questions">Answered Questions</h5>
                    <h3 class="mt-3 mb-3">{{ $stats['answered_questions'] }}</h3>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card widget-flat">
                <div class="card-body">
                    <div class="float-end">
                        <i class="mdi mdi-trophy widget-icon bg-info-lighten text-info"></i>
                    </div>
                    <h5 class="text-muted fw-normal mt-0" title="Converted Questions">Converted Questions</h5>
                    <h3 class="mt-3 mb-3">{{ $stats['converted_questions'] }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Visitor Questions List -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Visitor Questions</h4>
                    <div class="btn-group">
                        <a href="{{ route('admin.ai-dashboard') }}" class="btn btn-info">
                            <i class="mdi mdi-robot"></i> AI Dashboard
                        </a>
                        <a href="{{ route('admin.qa-management') }}" class="btn btn-primary">
                            <i class="mdi mdi-plus"></i> Manage Q&A Pairs
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
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
                                    <td>{{ $question->created_at->format('M d, Y H:i') }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-info"
                                                    onclick="viewQuestion({{ $question->id }})" title="View Details">
                                                <i class="mdi mdi-eye"></i>
                                            </button>
                                            @if($question->status === 'pending' || $question->status === 'no_match')
                                                <button type="button" class="btn btn-sm btn-outline-success"
                                                        onclick="answerQuestion({{ $question->id }})" title="Answer Question">
                                                    <i class="mdi mdi-reply"></i>
                                                </button>
                                            @endif
                                            @if($question->status === 'answered' && !$question->is_converted)
                                                <form action="{{ route('admin.mark-converted', $question->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-info" title="Mark as Converted">
                                                        <i class="mdi mdi-trophy"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">No visitor questions found</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $visitorQuestions->links() }}
                    </div>
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

<script>
function viewQuestion(id) {
    // This would typically fetch data via AJAX
    document.getElementById('questionDetails').innerHTML = `
        <p>Question ID: ${id}</p>
        <p>This would show detailed information about the visitor question.</p>
        <p>You can implement AJAX loading here to fetch and display the full details.</p>
    `;

    new bootstrap.Modal(document.getElementById('viewQuestionModal')).show();
}

function answerQuestion(id) {
    // Set form action
    document.getElementById('answerQuestionForm').action = `/admin/answer-visitor-question/${id}`;

    // This would typically fetch question data via AJAX
    document.getElementById('questionText').textContent = `Question ID: ${id} - This would show the actual question text.`;

    new bootstrap.Modal(document.getElementById('answerQuestionModal')).show();
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
