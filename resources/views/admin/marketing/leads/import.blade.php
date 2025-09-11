@extends('master.master')

@section('title', 'Import Leads')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Import Leads</h1>
        <div class="btn-group">
            <a href="{{ route('admin.marketing.leads.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Leads
            </a>
            <a href="{{ route('admin.marketing.leads.template.download') }}" class="btn btn-info">
                <i class="fas fa-download"></i> Download Template
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Import Form -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Import Leads from Excel/CSV</h6>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <h6><i class="fas fa-exclamation-triangle"></i> Errors Found:</h6>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle"></i> {{ session('error') }}
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i> {{ session('success') }}
                        </div>
                    @endif

                    <!-- Progress Indicator -->
                    <div id="importProgress" class="alert alert-info" style="display: none;">
                        <div class="d-flex align-items-center">
                            <div class="spinner-border spinner-border-sm mr-3" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                            <div>
                                <strong>Processing your file...</strong>
                                <div class="progress mt-2" style="height: 20px;">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated"
                                         role="progressbar" style="width: 0%">
                                        <span id="progressText">0%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('admin.marketing.leads.import') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="form-group mb-4">
                            <label for="category" class="form-label">Category *</label>
                            <select class="form-control @error('category') is-invalid @enderror"
                                    id="category" name="category" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->name }}"
                                            {{ old('category') == $category->name ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">All imported leads will be assigned to this category.</small>
                        </div>

                        <div class="form-group mb-4">
                            <label for="file" class="form-label">File *</label>
                            <input type="file" class="form-control @error('file') is-invalid @enderror"
                                   id="file" name="file" accept=".xlsx,.xls,.csv" required>
                            @error('file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Supported formats: Excel (.xlsx, .xls) or CSV (.csv). Maximum file size: 10MB.
                                <br><strong>Note:</strong> Excel files will be converted automatically.
                            </small>
                        </div>

                        <div class="alert alert-info">
                            <h6><i class="fas fa-info-circle"></i> Import Instructions</h6>
                            <ul class="mb-0">
                                <li>Download the template file to see the required format</li>
                                <li><strong>Required fields: Name, Email, Category</strong></li>
                                <li>Phone number is optional - only email is required</li>
                                <li>Duplicate leads (same email) will be skipped</li>
                                <li>Make sure your file has the correct column headers</li>
                            </ul>
                        </div>

                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-primary" id="importBtn">
                                <i class="fas fa-upload"></i> Import Leads
                            </button>
                            <a href="{{ route('admin.marketing.leads.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                            <a href="{{ asset('test_leads_with_email.csv') }}" class="btn btn-info" download>
                                <i class="fas fa-download"></i> Download Test CSV
                            </a>
                            <a href="{{ route('admin.marketing.leads.test-import') }}" class="btn btn-warning" target="_blank">
                                <i class="fas fa-flask"></i> Test Import
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Instructions -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Template Format</h6>
                </div>
                <div class="card-body">
                    <h6>Required Fields:</h6>
                    <ul class="list-unstyled">
                        <li><code>name</code> - Lead name (required)</li>
                    </ul>

                    <h6>Optional Fields:</h6>
                    <ul class="list-unstyled">
                        <li><code>email</code> - Email address</li>
                        <li><code>phone</code> - Primary phone number</li>
                        <li><code>phones</code> - Multiple phone numbers (JSON array)</li>
                        <li><code>company</code> - Company name</li>
                        <li><code>full_address</code> - Complete address</li>
                        <li><code>street</code> - Street name</li>
                        <li><code>municipality</code> - City/Municipality</li>
                        <li><code>website</code> - Website URL</li>
                        <li><code>domain</code> - Domain name</li>
                        <li><code>facebook</code> - Facebook URL</li>
                        <li><code>instagram</code> - Instagram URL</li>
                        <li><code>twitter</code> - Twitter URL</li>
                        <li><code>yelp</code> - Yelp URL</li>
                        <li><code>latitude</code> - Latitude coordinate</li>
                        <li><code>longitude</code> - Longitude coordinate</li>
                        <li><code>rating</code> - Business rating (0-5)</li>
                        <li><code>review_count</code> - Number of reviews</li>
                        <li><code>claimed</code> - Is business claimed (true/false)</li>
                        <li><code>notes</code> - Additional notes</li>
                    </ul>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Sample Data</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead>
                                <tr>
                                    <th>name</th>
                                    <th>email</th>
                                    <th>phone</th>
                                    <th>category</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Sample Business</td>
                                    <td>sample@example.com</td>
                                    <td>+1234567890</td>
                                    <td>restaurant</td>
                                </tr>
                                <tr>
                                    <td>Another Business</td>
                                    <td>another@example.com</td>
                                    <td>+0987654321</td>
                                    <td>car wash</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Form submission with progress indicator
    $('form').on('submit', function(e) {
        const fileInput = $('#file');
        const importBtn = $('#importBtn');
        const progressDiv = $('#importProgress');
        const progressBar = $('.progress-bar');
        const progressText = $('#progressText');

        if (fileInput[0].files.length === 0) {
            e.preventDefault();
            alert('Please select a file to import.');
            return;
        }

        // Show progress indicator
        progressDiv.show();
        importBtn.prop('disabled', true);
        importBtn.html('<i class="fas fa-spinner fa-spin"></i> Processing...');

        // Simulate progress (since we can't track real progress easily)
        let progress = 0;
        const progressInterval = setInterval(function() {
            progress += Math.random() * 15;
            if (progress > 90) progress = 90;

            progressBar.css('width', progress + '%');
            progressText.text(Math.round(progress) + '%');
        }, 500);

        // Clear interval after form submission
        setTimeout(function() {
            clearInterval(progressInterval);
        }, 30000); // 30 seconds max
    });
});
</script>
@endpush
