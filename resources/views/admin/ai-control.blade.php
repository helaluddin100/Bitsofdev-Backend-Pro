@extends('master.master')

@section('title', 'AI Control Dashboard')

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
                <li class="breadcrumb-item active" aria-current="page">AI Control</li>
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
                                    <i data-feather="cpu" class="me-2"></i>
                                    AI Control Dashboard
                                </h4>
                                <p class="text-muted">Manage your AI system settings and monitor learning progress.</p>
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

        <!-- Success/Error Messages -->
        @if (session('success'))
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

        @if (session('error'))
            <div class="row mb-3">
                <div class="col-12">
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i data-feather="alert-circle" class="me-2"></i>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
            </div>
        @endif

        <!-- AI Settings Card -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i data-feather="settings" class="me-2"></i>
                            AI System Settings
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.ai.update-settings') }}">
                            @csrf

                            <div class="row">
                                <!-- AI Provider Selection -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">AI Provider</label>
                                    <select name="ai_provider" class="form-select">
                                        <option value="gemini" {{ $settings->ai_provider == 'gemini' ? 'selected' : '' }}>
                                            Google Gemini
                                        </option>
                                        <option value="own_ai" {{ $settings->ai_provider == 'own_ai' ? 'selected' : '' }}>
                                            Own AI (Training Mode)
                                        </option>
                                        <option value="none" {{ $settings->ai_provider == 'none' ? 'selected' : '' }}>
                                            Disabled
                                        </option>
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Learning Threshold</label>
                                    <input type="number" name="learning_threshold"
                                        value="{{ $settings->learning_threshold }}" min="1" max="100"
                                        class="form-control">
                                    <small class="form-text text-muted">Minimum responses needed to activate own AI</small>
                                </div>
                            </div>

                            <!-- Toggle Switches -->
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="training_mode" value="1"
                                            {{ $settings->training_mode ? 'checked' : '' }} id="trainingMode">
                                        <label class="form-check-label" for="trainingMode">
                                            <strong>Training Mode</strong>
                                            <br><small class="text-muted">Use own AI instead of external AI</small>
                                        </label>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="use_static_responses"
                                            value="1" {{ $settings->use_static_responses ? 'checked' : '' }}
                                            id="staticResponses">
                                        <label class="form-check-label" for="staticResponses">
                                            <strong>Static Responses</strong>
                                            <br><small class="text-muted">Enable pre-defined answers</small>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Save Button -->
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">
                                    <i data-feather="save" class="me-2"></i>
                                    Save Settings
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i data-feather="zap" class="me-2"></i>
                            Quick Actions
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Switch to Gemini -->
                            <div class="col-md-4 mb-3">
                                <form method="POST" action="{{ route('admin.ai.switch-provider') }}">
                                    @csrf
                                    <input type="hidden" name="provider" value="gemini">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i data-feather="cpu" class="me-2"></i>
                                        Switch to Gemini
                                    </button>
                                </form>
                            </div>

                            <!-- Switch to Own AI -->
                            <div class="col-md-4 mb-3">
                                <form method="POST" action="{{ route('admin.ai.switch-provider') }}">
                                    @csrf
                                    <input type="hidden" name="provider" value="own_ai">
                                    <button type="submit" class="btn btn-success w-100">
                                        <i data-feather="brain" class="me-2"></i>
                                        Switch to Own AI
                                    </button>
                                </form>
                            </div>

                            <!-- Activate Learned Responses -->
                            <div class="col-md-4 mb-3">
                                <form method="POST" action="{{ route('admin.ai.activate-learned') }}">
                                    @csrf
                                    <button type="submit" class="btn btn-warning w-100">
                                        <i data-feather="play" class="me-2"></i>
                                        Activate Learned ({{ $learningStats['pending_review'] }})
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Learning Statistics -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i data-feather="trending-up" class="me-2"></i>
                            AI Learning Statistics
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
                                <div class="card bg-primary text-white">
                                    <div class="card-body text-center">
                                        <h3 class="mb-0">{{ $learningStats['total_learned'] }}</h3>
                                        <p class="mb-0">Total Learned</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
                                <div class="card bg-success text-white">
                                    <div class="card-body text-center">
                                        <h3 class="mb-0">{{ $learningStats['active_learned'] }}</h3>
                                        <p class="mb-0">Active Responses</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
                                <div class="card bg-warning text-white">
                                    <div class="card-body text-center">
                                        <h3 class="mb-0">{{ $learningStats['pending_review'] }}</h3>
                                        <p class="mb-0">Pending Review</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
                                <div class="card bg-info text-white">
                                    <div class="card-body text-center">
                                        <h3 class="mb-0">{{ $learningStats['learning_progress'] }}%</h3>
                                        <p class="mb-0">Learning Progress</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Progress Bar -->
                        <div class="mt-4">
                            <div class="d-flex justify-content-between mb-2">
                                <span>AI Learning Progress</span>
                                <span>{{ $learningStats['learning_progress'] }}%</span>
                            </div>
                            <div class="progress">
                                <div class="progress-bar bg-info" role="progressbar"
                                    style="width: {{ $learningStats['learning_progress'] }}%"
                                    aria-valuenow="{{ $learningStats['learning_progress'] }}" aria-valuemin="0"
                                    aria-valuemax="100">
                                </div>
                            </div>
                            @if ($learningStats['can_activate_own_ai'])
                                <div class="mt-2 text-success">
                                    <i data-feather="check-circle" class="me-1"></i>
                                    Ready to activate own AI!
                                </div>
                            @else
                                <div class="mt-2 text-muted">
                                    Need {{ $settings->learning_threshold - $learningStats['active_learned'] }} more
                                    responses to activate own AI
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Learned Questions -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i data-feather="clock" class="me-2"></i>
                            Recent Learned Questions
                        </h5>
                    </div>
                    <div class="card-body">
                        @if ($recentQuestions->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Question</th>
                                            <th>Answer Preview</th>
                                            <th>Status</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($recentQuestions as $qa)
                                            <tr>
                                                <td>{{ Str::limit($qa->question, 50) }}</td>
                                                <td>{{ Str::limit($qa->answer_1, 80) }}</td>
                                                <td>
                                                    @if ($qa->is_active)
                                                        <span class="badge bg-success">Active</span>
                                                    @else
                                                        <span class="badge bg-warning">Pending</span>
                                                    @endif
                                                </td>
                                                <td>{{ $qa->created_at->format('M j, Y') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4 text-muted">
                                <i data-feather="inbox" class="mb-3" style="width: 48px; height: 48px;"></i>
                                <p>No learned questions yet. Start using the chatbot to build your AI knowledge base!</p>
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

    // Auto-refresh page every 30 seconds to show latest data (optional)
    // setTimeout(function() {
    //     location.reload();
    // }, 30000);
</script>
@endsection
