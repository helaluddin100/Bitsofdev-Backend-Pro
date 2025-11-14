@extends('master.master')

@section('title', 'Create Campaign')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Create New Campaign</h1>
        <div class="btn-group">
            <a href="{{ route('admin.marketing.campaigns.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Campaigns
            </a>
        </div>
    </div>

    <!-- Create Campaign Form -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Campaign Information</h6>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.marketing.campaigns.store') }}">
                @csrf

                <div class="row">
                    <!-- Basic Information -->
                    <div class="col-md-6">
                        <h6 class="text-primary mb-3">Basic Information</h6>

                        <div class="form-group mb-3">
                            <label for="name" class="form-label">Campaign Name *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                   id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="category" class="form-label">Category *</label>
                            <select class="form-control @error('category') is-invalid @enderror"
                                    id="category" name="category" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->name }}" {{ old('category') == $category->name ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Email Content -->
                    <div class="col-md-6">
                        <h6 class="text-primary mb-3">Email Content</h6>

                        <div class="form-group mb-3">
                            <label for="email_subject" class="form-label">Email Subject *</label>
                            <input type="text" class="form-control @error('email_subject') is-invalid @enderror"
                                   id="email_subject" name="email_subject" value="{{ old('email_subject') }}" required>
                            @error('email_subject')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="email_body" class="form-label">Email Body *</label>
                            <textarea class="form-control @error('email_body') is-invalid @enderror"
                                      id="email_body" name="email_body" rows="8" required>{{ old('email_body') }}</textarea>
                            @error('email_body')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">You can use HTML formatting in the email body.</small>
                        </div>
                    </div>
                </div>

                <hr>

                <!-- Scheduling -->
                <div class="row">
                    <div class="col-md-12">
                        <h6 class="text-primary mb-3">Scheduling</h6>

                        <div class="form-group mb-3">
                            <label class="form-label">Schedule Type *</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="schedule_type" id="schedule_immediate"
                                       value="immediate" {{ old('schedule_type', 'immediate') == 'immediate' ? 'checked' : '' }} required>
                                <label class="form-check-label" for="schedule_immediate">
                                    Send Immediately
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="schedule_type" id="schedule_scheduled"
                                       value="scheduled" {{ old('schedule_type') == 'scheduled' ? 'checked' : '' }}>
                                <label class="form-check-label" for="schedule_scheduled">
                                    Schedule for Later
                                </label>
                            </div>
                            @error('schedule_type')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3" id="scheduled_at_group" style="display: none;">
                            <label for="scheduled_at" class="form-label">Scheduled Date & Time</label>
                            <input type="datetime-local" class="form-control @error('scheduled_at') is-invalid @enderror"
                                   id="scheduled_at" name="scheduled_at" value="{{ old('scheduled_at') }}">
                            @error('scheduled_at')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <hr>

                <!-- Lead Selection -->
                <div class="row">
                    <div class="col-md-12">
                        <h6 class="text-primary mb-3">Select Leads</h6>
                        
                        <div class="form-group mb-3">
                            <label class="form-label">Search Leads</label>
                            <input type="text" class="form-control" id="leadSearch" placeholder="Search by name, email, phone, or company...">
                        </div>

                        <div class="form-group mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label mb-0">Select Leads ({{ count($leads) }} available)</label>
                                <div>
                                    <button type="button" class="btn btn-sm btn-info" onclick="selectAllLeads()">Select All</button>
                                    <button type="button" class="btn btn-sm btn-secondary" onclick="deselectAllLeads()">Deselect All</button>
                                </div>
                            </div>
                            <div class="border p-3" style="max-height: 300px; overflow-y: auto;" id="leadsContainer">
                                @foreach($leads as $lead)
                                <div class="form-check lead-item" data-lead-name="{{ strtolower($lead->name) }}" 
                                     data-lead-email="{{ strtolower($lead->email ?? '') }}" 
                                     data-lead-phone="{{ strtolower($lead->phone ?? '') }}" 
                                     data-lead-company="{{ strtolower($lead->company ?? '') }}">
                                    <input class="form-check-input lead-checkbox" type="checkbox" 
                                           name="lead_ids[]" value="{{ $lead->id }}" id="lead_{{ $lead->id }}">
                                    <label class="form-check-label" for="lead_{{ $lead->id }}">
                                        <strong>{{ $lead->name }}</strong>
                                        @if($lead->email)
                                            <span class="text-muted"> - {{ $lead->email }}</span>
                                        @endif
                                        @if($lead->phone)
                                            <span class="text-muted"> - {{ $lead->phone }}</span>
                                        @endif
                                        @if($lead->company)
                                            <span class="text-muted"> - {{ $lead->company }}</span>
                                        @endif
                                    </label>
                                </div>
                                @endforeach
                            </div>
                            <small class="form-text text-muted">
                                Selected: <span id="selectedCount">0</span> leads
                            </small>
                        </div>
                    </div>
                </div>

                <hr>

                <!-- Reminders -->
                <div class="row">
                    <div class="col-md-12">
                        <h6 class="text-primary mb-3">Reminders (Auto: 5, 7, 10 days)</h6>

                        <div class="form-group mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="enable_reminders" id="enable_reminders"
                                       value="1" {{ old('enable_reminders') ? 'checked' : '' }}>
                                <label class="form-check-label" for="enable_reminders">
                                    Enable Reminders
                                </label>
                            </div>
                        </div>

                        <div id="reminders_section" style="display: none;">
                            <!-- Reminder 1 -->
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h6 class="mb-0">First Reminder</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group mb-3">
                                                <label for="reminder_1_days" class="form-label">Days After</label>
                                                <input type="number" class="form-control @error('reminder_1_days') is-invalid @enderror"
                                                       id="reminder_1_days" name="reminder_1_days" value="{{ old('reminder_1_days', 5) }}"
                                                       min="1" max="30">
                                                @error('reminder_1_days')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-9">
                                            <div class="form-group mb-3">
                                                <label for="reminder_1_subject" class="form-label">Subject</label>
                                                <input type="text" class="form-control @error('reminder_1_subject') is-invalid @enderror"
                                                       id="reminder_1_subject" name="reminder_1_subject" value="{{ old('reminder_1_subject') }}">
                                                @error('reminder_1_subject')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="reminder_1_body" class="form-label">Body</label>
                                        <textarea class="form-control @error('reminder_1_body') is-invalid @enderror"
                                                  id="reminder_1_body" name="reminder_1_body" rows="4">{{ old('reminder_1_body') }}</textarea>
                                        @error('reminder_1_body')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Reminder 2 -->
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h6 class="mb-0">Second Reminder</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group mb-3">
                                                <label for="reminder_2_days" class="form-label">Days After</label>
                                                <input type="number" class="form-control @error('reminder_2_days') is-invalid @enderror"
                                                       id="reminder_2_days" name="reminder_2_days" value="{{ old('reminder_2_days', 7) }}"
                                                       min="1" max="30">
                                                @error('reminder_2_days')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-9">
                                            <div class="form-group mb-3">
                                                <label for="reminder_2_subject" class="form-label">Subject</label>
                                                <input type="text" class="form-control @error('reminder_2_subject') is-invalid @enderror"
                                                       id="reminder_2_subject" name="reminder_2_subject" value="{{ old('reminder_2_subject') }}">
                                                @error('reminder_2_subject')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="reminder_2_body" class="form-label">Body</label>
                                        <textarea class="form-control @error('reminder_2_body') is-invalid @enderror"
                                                  id="reminder_2_body" name="reminder_2_body" rows="4">{{ old('reminder_2_body') }}</textarea>
                                        @error('reminder_2_body')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Reminder 3 -->
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h6 class="mb-0">Third Reminder</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group mb-3">
                                                <label for="reminder_3_days" class="form-label">Days After</label>
                                                <input type="number" class="form-control @error('reminder_3_days') is-invalid @enderror"
                                                       id="reminder_3_days" name="reminder_3_days" value="{{ old('reminder_3_days', 10) }}"
                                                       min="1" max="30">
                                                @error('reminder_3_days')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-9">
                                            <div class="form-group mb-3">
                                                <label for="reminder_3_subject" class="form-label">Subject</label>
                                                <input type="text" class="form-control @error('reminder_3_subject') is-invalid @enderror"
                                                       id="reminder_3_subject" name="reminder_3_subject" value="{{ old('reminder_3_subject') }}">
                                                @error('reminder_3_subject')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="reminder_3_body" class="form-label">Body</label>
                                        <textarea class="form-control @error('reminder_3_body') is-invalid @enderror"
                                                  id="reminder_3_body" name="reminder_3_body" rows="4">{{ old('reminder_3_body') }}</textarea>
                                        @error('reminder_3_body')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <hr>

                <!-- Notes -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror"
                                      id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="form-group mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Create Campaign
                    </button>
                    <a href="{{ route('admin.marketing.campaigns.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Show/hide scheduled_at field based on schedule_type
        $('input[name="schedule_type"]').on('change', function() {
            if ($(this).val() === 'scheduled') {
                $('#scheduled_at_group').show();
                $('#scheduled_at').prop('required', true);
            } else {
                $('#scheduled_at_group').hide();
                $('#scheduled_at').prop('required', false);
            }
        });

        // Trigger on page load
        if ($('input[name="schedule_type"]:checked').val() === 'scheduled') {
            $('#scheduled_at_group').show();
            $('#scheduled_at').prop('required', true);
        }

        // Show/hide reminders section based on enable_reminders checkbox
        $('#enable_reminders').on('change', function() {
            if ($(this).is(':checked')) {
                $('#reminders_section').show();
            } else {
                $('#reminders_section').hide();
            }
        });

        // Trigger on page load
        if ($('#enable_reminders').is(':checked')) {
            $('#reminders_section').show();
        }

        // Lead search functionality
        $('#leadSearch').on('keyup', function() {
            var searchTerm = $(this).val().toLowerCase();
            $('.lead-item').each(function() {
                var leadName = $(this).data('lead-name') || '';
                var leadEmail = $(this).data('lead-email') || '';
                var leadPhone = $(this).data('lead-phone') || '';
                var leadCompany = $(this).data('lead-company') || '';
                
                if (leadName.includes(searchTerm) || leadEmail.includes(searchTerm) || 
                    leadPhone.includes(searchTerm) || leadCompany.includes(searchTerm)) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });

        // Update selected count
        function updateSelectedCount() {
            var count = $('.lead-checkbox:checked').length;
            $('#selectedCount').text(count);
        }

        $('.lead-checkbox').on('change', updateSelectedCount);
        updateSelectedCount();
    });

    function selectAllLeads() {
        $('.lead-item:visible .lead-checkbox').prop('checked', true);
        $('.lead-checkbox').trigger('change');
    }

    function deselectAllLeads() {
        $('.lead-checkbox').prop('checked', false);
        $('.lead-checkbox').trigger('change');
    }
</script>
@endpush

