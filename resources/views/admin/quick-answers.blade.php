@extends('master.master')

@section('title', 'Quick Answers Management')

@push('styles')
<style>
    .avatar-sm {
        width: 40px;
        height: 40px;
    }
    .quick-answer-card {
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .quick-answer-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
</style>
@endpush

@section('content')
<div class="page-content">
    <nav class="page-breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.ai-dashboard') }}">AI Chatbot</a></li>
            <li class="breadcrumb-item active" aria-current="page">Quick Answers</li>
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
                                <i data-feather="zap" class="me-2"></i>
                                Quick Answers Management
                            </h4>
                            <p class="text-muted">Manage pre-defined quick answers and test website data integration.</p>
                        </div>
                        <div>
                            <a href="{{ route('admin.ai-dashboard') }}" class="btn btn-outline-primary me-2">
                                <i data-feather="cpu" style="width: 16px; height: 16px;"></i> AI Dashboard
                            </a>
                            <a href="{{ route('admin.visitor-questions') }}" class="btn btn-primary">
                                <i data-feather="eye" style="width: 16px; height: 16px;"></i> Visitor Questions
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Website Data Integration Test -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="card-title mb-0">
                            <i data-feather="database" class="me-2"></i>
                            Website Data Integration Test
                        </h6>
                        <button type="button" class="btn btn-primary btn-sm" onclick="testWebsiteData()">
                            <i data-feather="activity" style="width: 16px; height: 16px;"></i> Test Website Data
                        </button>
                    </div>
                    
                    <p class="text-muted">This will test if AI can fetch and use data from your website's blog, projects, team, about, and pricing sections.</p>
                    
                    <div id="websiteDataResult" class="mt-3" style="display: none;">
                        <!-- Results will be shown here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Contact Suggestions Info -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title mb-3">
                        <i data-feather="message-square" class="me-2"></i>
                        Smart Contact Suggestions
                    </h6>
                    
                    <p class="text-muted mb-4">When AI can't find an answer, it automatically provides intelligent contact suggestions based on the question type:</p>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="mb-3">
                                        <i data-feather="help-circle" class="text-primary me-2"></i>
                                        Question Types & Responses
                                    </h6>
                                    <ul class="list-unstyled">
                                        <li class="mb-2">
                                            <i data-feather="chevron-right" class="text-success me-1" style="width: 16px; height: 16px;"></i>
                                            <strong>Pricing Questions:</strong> "For detailed quote, contact our team..."
                                        </li>
                                        <li class="mb-2">
                                            <i data-feather="chevron-right" class="text-success me-1" style="width: 16px; height: 16px;"></i>
                                            <strong>Project Questions:</strong> "Our development team can discuss your requirements..."
                                        </li>
                                        <li class="mb-2">
                                            <i data-feather="chevron-right" class="text-success me-1" style="width: 16px; height: 16px;"></i>
                                            <strong>Support Questions:</strong> "Our support team is here to help..."
                                        </li>
                                        <li>
                                            <i data-feather="chevron-right" class="text-success me-1" style="width: 16px; height: 16px;"></i>
                                            <strong>Service Questions:</strong> "We'd recommend speaking directly with our team..."
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="mb-3">
                                        <i data-feather="check-circle" class="text-success me-2"></i>
                                        Features
                                    </h6>
                                    <ul class="list-unstyled">
                                        <li class="mb-2">
                                            <i data-feather="check" class="text-success me-2" style="width: 16px; height: 16px;"></i>
                                            Automatic contact page link
                                        </li>
                                        <li class="mb-2">
                                            <i data-feather="check" class="text-success me-2" style="width: 16px; height: 16px;"></i>
                                            Context-aware suggestions
                                        </li>
                                        <li class="mb-2">
                                            <i data-feather="check" class="text-success me-2" style="width: 16px; height: 16px;"></i>
                                            Professional responses
                                        </li>
                                        <li>
                                            <i data-feather="check" class="text-success me-2" style="width: 16px; height: 16px;"></i>
                                            Clickable contact links
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Answers List -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title mb-4">
                        <i data-feather="zap" class="me-2"></i>
                        Quick Answers Library
                    </h6>

                    <div class="row">
                        @foreach($quickAnswers as $keyword => $answer)
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card border quick-answer-card">
                                <div class="card-body">
                                    <div class="d-flex align-items-start mb-3">
                                        <div class="avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center me-3 flex-shrink-0">
                                            <i data-feather="message-circle" class="text-white" style="width: 20px; height: 20px;"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="card-title text-primary mb-1">{{ $keyword }}</h6>
                                            <span class="badge bg-secondary">Quick Answer</span>
                                        </div>
                                    </div>
                                    <p class="card-text text-muted mb-3">{{ Str::limit($answer, 100) }}</p>
                                    <button type="button" class="btn btn-sm btn-primary w-100" 
                                            onclick="testQuickAnswer('{{ addslashes($keyword) }}')">
                                        <i data-feather="send" style="width: 14px; height: 14px;"></i> Test Answer
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    
                    @if(count($quickAnswers) === 0)
                        <div class="text-center py-5">
                            <i data-feather="inbox" class="text-muted mb-3" style="width: 64px; height: 64px;"></i>
                            <h6 class="text-muted">No quick answers available</h6>
                            <p class="text-muted">Quick answers will appear here when configured.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Test Results Modal -->
    <div class="modal fade" id="testResultModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Test Result</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="testResultContent">
                    <!-- Content will be loaded here -->
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

function testWebsiteData() {
    const resultDiv = document.getElementById('websiteDataResult');
    resultDiv.innerHTML = `
        <div class="text-center py-3">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2 text-muted">Testing website data integration...</p>
        </div>
    `;
    resultDiv.style.display = 'block';

    fetch('{{ route("admin.test-website-data") }}')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                let html = '<div class="alert alert-success d-flex align-items-start"><i data-feather="check-circle" class="me-2 flex-shrink-0"></i><div><h6 class="mb-2">Website Data Integration Test Results:</h6>';
                
                Object.keys(data.data).forEach(key => {
                    const count = data.data[key].length;
                    html += `<p class="mb-1"><strong>${key.charAt(0).toUpperCase() + key.slice(1)}:</strong> ${count} items found</p>`;
                });
                
                html += '</div></div>';
                resultDiv.innerHTML = html;
                feather.replace();
            } else {
                resultDiv.innerHTML = `<div class="alert alert-danger d-flex align-items-start"><i data-feather="alert-circle" class="me-2"></i><div>Error: ${data.message}</div></div>`;
                feather.replace();
            }
        })
        .catch(error => {
            resultDiv.innerHTML = `<div class="alert alert-danger d-flex align-items-start"><i data-feather="alert-circle" class="me-2"></i><div>Error: ${error.message}</div></div>`;
            feather.replace();
        });
}

function testQuickAnswer(keyword) {
    const modal = new bootstrap.Modal(document.getElementById('testResultModal'));
    const content = document.getElementById('testResultContent');
    
    content.innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-3">Testing quick answer for: <strong>${keyword}</strong></p>
        </div>
    `;
    
    modal.show();
    
    // API call
    fetch('{{ route("admin.test-ai-response") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ test_question: keyword })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            content.innerHTML = `
                <div class="alert alert-success d-flex align-items-start">
                    <i data-feather="check-circle" class="me-2 flex-shrink-0"></i>
                    <div>
                        <h6 class="mb-3">Quick Answer Test Successful!</h6>
                        <p class="mb-2"><strong>Question:</strong> ${data.data.question}</p>
                        <p class="mb-2"><strong>Answer Type:</strong> <span class="badge bg-primary">${data.data.type}</span></p>
                        <p class="mb-2"><strong>Response:</strong></p>
                        <div class="bg-light p-3 rounded">${data.data.response}</div>
                    </div>
                </div>
            `;
        } else {
            content.innerHTML = `
                <div class="alert alert-warning d-flex align-items-start">
                    <i data-feather="alert-triangle" class="me-2 flex-shrink-0"></i>
                    <div>
                        <h6 class="mb-3">No Quick Answer Found</h6>
                        <p class="mb-2"><strong>Question:</strong> ${keyword}</p>
                        <p class="mb-0"><strong>Message:</strong> ${data.message}</p>
                    </div>
                </div>
            `;
        }
        feather.replace();
    })
    .catch(error => {
        content.innerHTML = `
            <div class="alert alert-danger d-flex align-items-start">
                <i data-feather="x-circle" class="me-2 flex-shrink-0"></i>
                <div>
                    <h6 class="mb-2">Test Failed</h6>
                    <p class="mb-0">Error: ${error.message}</p>
                </div>
            </div>
        `;
        feather.replace();
    });
}
</script>
@endsection
