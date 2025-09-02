@extends('master.master')



@section('content')
    <div class="page-content">

        <nav class="page-breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.contacts.index') }}">Contact Management</a></li>
                <li class="breadcrumb-item active" aria-current="page">Contact Details</li>
            </ol>
        </nav>

        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h6 class="card-title">Contact Details</h6>
                            <div class="back-button">
                                <a href="{{ route('admin.contacts.index') }}" class="btn btn-secondary btn-icon">
                                    <i data-feather="arrow-left"></i>
                                </a>
                            </div>
                        </div>
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="row">
                        <!-- Contact Information -->
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Contact Information</h4>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label><strong>Name:</strong></label>
                                                <p class="form-control-static">{{ $contact->name }}</p>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label><strong>Email:</strong></label>
                                                <p class="form-control-static">
                                                    <a href="mailto:{{ $contact->email }}">{{ $contact->email }}</a>
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    @if($contact->company)
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label><strong>Company:</strong></label>
                                                <p class="form-control-static">{{ $contact->company }}</p>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label><strong>Project Type:</strong></label>
                                                <p class="form-control-static">
                                                    <span class="badge bg-info">
                                                        {{ ucwords(str_replace('-', ' ', $contact->project_type)) }}
                                                    </span>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    @else
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label><strong>Project Type:</strong></label>
                                                <p class="form-control-static">
                                                    <span class="badge bg-info">
                                                        {{ ucwords(str_replace('-', ' ', $contact->project_type)) }}
                                                    </span>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    @endif

                                    <div class="form-group">
                                        <label><strong>Subject:</strong></label>
                                        <p class="form-control-static">{{ $contact->subject }}</p>
                                    </div>

                                    <div class="form-group">
                                        <label><strong>Message:</strong></label>
                                        <div class="form-control-static" style="white-space: pre-wrap; background: #f8f9fa; padding: 15px; border-radius: 5px;">
                                            {{ $contact->message }}
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label><strong>Submitted:</strong></label>
                                                <p class="form-control-static">{{ $contact->created_at->format('F j, Y \a\t g:i A') }}</p>
                                            </div>
                                        </div>
                                        @if($contact->replied_at)
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label><strong>Replied:</strong></label>
                                                <p class="form-control-static">{{ $contact->replied_at->format('F j, Y \a\t g:i A') }}</p>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Status Management -->
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Status Management</h4>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('admin.contacts.update', $contact) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        
                                        <div class="form-group">
                                            <label for="status"><strong>Status:</strong></label>
                                            <select name="status" id="status" class="form-control">
                                                <option value="new" {{ $contact->status == 'new' ? 'selected' : '' }}>New</option>
                                                <option value="read" {{ $contact->status == 'read' ? 'selected' : '' }}>Read</option>
                                                <option value="replied" {{ $contact->status == 'replied' ? 'selected' : '' }}>Replied</option>
                                                <option value="closed" {{ $contact->status == 'closed' ? 'selected' : '' }}>Closed</option>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="admin_notes"><strong>Admin Notes:</strong></label>
                                            <textarea name="admin_notes" id="admin_notes" rows="4" class="form-control" placeholder="Add internal notes about this contact...">{{ $contact->admin_notes }}</textarea>
                                        </div>

                                        <button type="submit" class="btn btn-primary btn-block">
                                            <i data-feather="save"></i> Update Status
                                        </button>
                                    </form>

                                    <hr>

                                    <div class="text-center">
                                        <a href="mailto:{{ $contact->email }}?subject=Re: {{ $contact->subject }}" class="btn btn-success btn-block">
                                            <i data-feather="mail"></i> Reply via Email
                                        </a>
                                        
                                        <form action="{{ route('admin.contacts.destroy', $contact) }}" method="POST" class="mt-2">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-block" onclick="return confirm('Are you sure you want to delete this contact?')">
                                                <i data-feather="trash"></i> Delete Contact
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Quick Actions -->
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h4 class="card-title">Quick Actions</h4>
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        <button type="button" class="btn btn-outline-info btn-sm" onclick="copyToClipboard('{{ $contact->email }}')">
                                            <i data-feather="copy"></i> Copy Email
                                        </button>
                                        <button type="button" class="btn btn-outline-info btn-sm" onclick="copyToClipboard('{{ $contact->name }}')">
                                            <i data-feather="copy"></i> Copy Name
                                        </button>
                                        <button type="button" class="btn btn-outline-info btn-sm" onclick="copyToClipboard('{{ $contact->subject }}')">
                                            <i data-feather="copy"></i> Copy Subject
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        // Show success message
        const button = event.target;
        const originalText = button.innerHTML;
        button.innerHTML = '<i data-feather="check"></i> Copied!';
        button.classList.remove('btn-outline-info');
        button.classList.add('btn-success');
        
        setTimeout(function() {
            button.innerHTML = originalText;
            button.classList.remove('btn-success');
            button.classList.add('btn-outline-info');
        }, 2000);
    }).catch(function(err) {
        console.error('Could not copy text: ', err);
        alert('Failed to copy text to clipboard');
    });
}
</script>
@endsection
