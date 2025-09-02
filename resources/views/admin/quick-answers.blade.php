@extends('master.master')

@section('title', 'Quick Answers Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Quick Answers Management</h4>
            </div>
        </div>
    </div>

    <!-- Website Data Integration Test -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Website Data Integration Test</h4>
                    <button type="button" class="btn btn-primary" onclick="testWebsiteData()">
                        <i class="mdi mdi-test-tube"></i> Test Website Data
                    </button>
                </div>
                <div class="card-body">
                    <p>This will test if AI can fetch and use data from your website's blog, projects, team, about, and pricing sections.</p>
                    <div id="websiteDataResult" class="mt-3" style="display: none;">
                        <!-- Results will be shown here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Contact Suggestions Info -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Smart Contact Suggestions</h4>
                </div>
                <div class="card-body">
                    <p>When AI can't find an answer, it automatically provides intelligent contact suggestions based on the question type:</p>
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Question Types & Responses:</h6>
                            <ul>
                                <li><strong>Pricing Questions:</strong> "For detailed quote, contact our team..."</li>
                                <li><strong>Project Questions:</strong> "Our development team can discuss your requirements..."</li>
                                <li><strong>Support Questions:</strong> "Our support team is here to help..."</li>
                                <li><strong>Service Questions:</strong> "We'd recommend speaking directly with our team..."</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6>Features:</h6>
                            <ul>
                                <li>✅ Automatic contact page link</li>
                                <li>✅ Context-aware suggestions</li>
                                <li>✅ Professional responses</li>
                                <li>✅ Clickable contact links</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Answers List -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Quick Answers</h4>
                    <div class="btn-group">
                        <a href="{{ route('admin.ai-dashboard') }}" class="btn btn-info">
                            <i class="mdi mdi-robot"></i> AI Dashboard
                        </a>
                        <a href="{{ route('admin.visitor-questions') }}" class="btn btn-primary">
                            <i class="mdi mdi-eye"></i> Visitor Questions
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($quickAnswers as $keyword => $answer)
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="card border">
                                <div class="card-body">
                                    <h6 class="card-title text-primary">{{ $keyword }}</h6>
                                    <p class="card-text">{{ Str::limit($answer, 100) }}</p>
                                    <button type="button" class="btn btn-sm btn-outline-primary" 
                                            onclick="testQuickAnswer('{{ $keyword }}')">
                                        <i class="mdi mdi-test-tube"></i> Test
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
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

<script>
function testWebsiteData() {
    const resultDiv = document.getElementById('websiteDataResult');
    resultDiv.innerHTML = '<div class="text-center"><div class="spinner-border" role="status"></div><p>Testing website data integration...</p></div>';
    resultDiv.style.display = 'block';

    fetch('{{ route("admin.test-website-data") }}')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                let html = '<div class="alert alert-success"><h6>Website Data Integration Test Results:</h6>';
                
                Object.keys(data.data).forEach(key => {
                    const count = data.data[key].length;
                    html += `<p><strong>${key.charAt(0).toUpperCase() + key.slice(1)}:</strong> ${count} items found</p>`;
                });
                
                html += '</div>';
                resultDiv.innerHTML = html;
            } else {
                resultDiv.innerHTML = `<div class="alert alert-danger">Error: ${data.message}</div>`;
            }
        })
        .catch(error => {
            resultDiv.innerHTML = `<div class="alert alert-danger">Error: ${error.message}</div>`;
        });
}

function testQuickAnswer(keyword) {
    const modal = new bootstrap.Modal(document.getElementById('testResultModal'));
    const content = document.getElementById('testResultContent');
    
    content.innerHTML = `
        <div class="text-center">
            <div class="spinner-border" role="status"></div>
            <p>Testing quick answer for: <strong>${keyword}</strong></p>
        </div>
    `;
    
    modal.show();
    
    // Simulate API call
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
                <div class="alert alert-success">
                    <h6>Quick Answer Test Successful!</h6>
                    <p><strong>Question:</strong> ${data.data.question}</p>
                    <p><strong>Answer Type:</strong> ${data.data.type}</p>
                    <p><strong>Response:</strong></p>
                    <div class="bg-light p-3 rounded">${data.data.response}</div>
                </div>
            `;
        } else {
            content.innerHTML = `
                <div class="alert alert-warning">
                    <h6>No Quick Answer Found</h6>
                    <p><strong>Question:</strong> ${keyword}</p>
                    <p><strong>Message:</strong> ${data.message}</p>
                </div>
            `;
        }
    })
    .catch(error => {
        content.innerHTML = `
            <div class="alert alert-danger">
                <h6>Test Failed</h6>
                <p>Error: ${error.message}</p>
            </div>
        `;
    });
}
</script>
@endsection
