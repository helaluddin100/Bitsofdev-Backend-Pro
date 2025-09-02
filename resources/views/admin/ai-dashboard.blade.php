@extends('master.master')

@section('title', 'AI Chatbot Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">AI Chatbot Dashboard</h4>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card widget-flat">
                <div class="card-body">
                    <div class="float-end">
                        <i class="mdi mdi-help-circle widget-icon bg-success-lighten text-success"></i>
                    </div>
                    <h5 class="text-muted fw-normal mt-0" title="Total Q&A Pairs">Total Q&A Pairs</h5>
                    <h3 class="mt-3 mb-3">{{ $stats['total_qa_pairs'] }}</h3>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card widget-flat">
                <div class="card-body">
                    <div class="float-end">
                        <i class="mdi mdi-check-circle widget-icon bg-primary-lighten text-primary"></i>
                    </div>
                    <h5 class="text-muted fw-normal mt-0" title="Active Q&A Pairs">Active Q&A Pairs</h5>
                    <h3 class="mt-3 mb-3">{{ $stats['active_qa_pairs'] }}</h3>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card widget-flat">
                <div class="card-body">
                    <div class="float-end">
                        <i class="mdi mdi-chart-line widget-icon bg-info-lighten text-info"></i>
                    </div>
                    <h5 class="text-muted fw-normal mt-0" title="Total Usage">Total Usage</h5>
                    <h3 class="mt-3 mb-3">{{ $stats['total_usage'] }}</h3>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card widget-flat">
                <div class="card-body">
                    <div class="float-end">
                        <i class="mdi mdi-star widget-icon bg-warning-lighten text-warning"></i>
                    </div>
                    <h5 class="text-muted fw-normal mt-0" title="Most Used">Most Used</h5>
                    <h3 class="mt-3 mb-3">{{ $stats['most_used'] ? $stats['most_used']->usage_count : 0 }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Visitor Questions Statistics -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Visitor Questions Statistics</h4>
                    <a href="{{ route('admin.visitor-questions') }}" class="btn btn-primary">
                        <i class="mdi mdi-eye"></i> View All Questions
                    </a>
                </div>
                <div class="card-body">
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
                </div>
            </div>
        </div>
    </div>

    <!-- AI Test Section -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Test AI Response</h4>
                </div>
                <div class="card-body">
                    <form id="testAIForm">
                        @csrf
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="test_question" class="form-label">Test Question</label>
                                    <input type="text" class="form-control" id="test_question" name="test_question"
                                           placeholder="Enter a question to test AI response..." required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">&nbsp;</label>
                                    <button type="submit" class="btn btn-primary d-block w-100">
                                        <i class="mdi mdi-robot"></i> Test AI Response
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>

                    <div id="aiResponse" class="mt-3" style="display: none;">
                        <div class="alert alert-info">
                            <h6>AI Response:</h6>
                            <div id="responseText"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Q&A Pairs List -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Q&A Pairs (Most Used)</h4>
                    <a href="{{ route('admin.qa-management') }}" class="btn btn-primary">
                        <i class="mdi mdi-plus"></i> Manage Q&A Pairs
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
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
                                @forelse($qaPairs as $qa)
                                <tr>
                                    <td>
                                        <strong>{{ Str::limit($qa->question, 50) }}</strong>
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
                                    <td>{{ $qa->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-primary"
                                                    onclick="testQuestion('{{ $qa->question }}')">
                                                <i class="mdi mdi-test-tube"></i>
                                            </button>
                                            <a href="{{ route('admin.qa-management') }}?edit={{ $qa->id }}"
                                               class="btn btn-sm btn-outline-warning">
                                                <i class="mdi mdi-pencil"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">No Q&A pairs found</td>
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

<script>
document.getElementById('testAIForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const question = formData.get('test_question');

    if (!question.trim()) {
        alert('Please enter a question');
        return;
    }

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
        } else {
            alert('Error: ' + data.message);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred while testing AI response');
    }
});

function testQuestion(question) {
    document.getElementById('test_question').value = question;
    document.getElementById('testAIForm').dispatchEvent(new Event('submit'));
}
</script>
@endsection
