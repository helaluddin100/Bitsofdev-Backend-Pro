@extends('master.master')

@section('title', 'Edit Campaign')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Campaign</h1>
        <div class="btn-group">
            <a href="{{ route('admin.marketing.campaigns.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Campaigns
            </a>
            <a href="{{ route('admin.marketing.campaigns.show', $campaign) }}" class="btn btn-info">
                <i class="fas fa-eye"></i> View Campaign
            </a>
        </div>
    </div>

    <!-- Edit Campaign Form -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Campaign Information</h6>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.marketing.campaigns.update', $campaign) }}">
                @csrf
                @method('PUT')

                <div class="row">
                    <!-- Basic Information -->
                    <div class="col-md-6">
                        <h6 class="text-primary mb-3">Basic Information</h6>

                        <div class="form-group mb-3">
                            <label for="name" class="form-label">Campaign Name *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                   id="name" name="name" value="{{ old('name', $campaign->name) }}" required>
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
                                    <option value="{{ $category->name }}" {{ old('category', $campaign->category) == $category->name ? 'selected' : '' }}>
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
                                      id="description" name="description" rows="3">{{ old('description', $campaign->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_active" id="is_active"
                                       value="1" {{ old('is_active', $campaign->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Active Campaign
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Email Content -->
                    <div class="col-md-6">
                        <h6 class="text-primary mb-3">Email Content</h6>

                        <div class="form-group mb-3">
                            <label for="email_subject" class="form-label">Email Subject *</label>
                            <input type="text" class="form-control @error('email_subject') is-invalid @enderror"
                                   id="email_subject" name="email_subject" value="{{ old('email_subject', $campaign->email_subject) }}" required>
                            @error('email_subject')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="email_body" class="form-label">Email Body *</label>
                            <textarea class="form-control @error('email_body') is-invalid @enderror"
                                      id="email_body" name="email_body" rows="8" required>{{ old('email_body', $campaign->email_body) }}</textarea>
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
                                       value="immediate" {{ old('schedule_type', $campaign->schedule_type) == 'immediate' ? 'checked' : '' }} required>
                                <label class="form-check-label" for="schedule_immediate">
                                    Send Immediately
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="schedule_type" id="schedule_scheduled"
                                       value="scheduled" {{ old('schedule_type', $campaign->schedule_type) == 'scheduled' ? 'checked' : '' }}>
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
                                   id="scheduled_at" name="scheduled_at" 
                                   value="{{ old('scheduled_at', $campaign->scheduled_at ? $campaign->scheduled_at->format('Y-m-d\TH:i') : '') }}">
                            @error('scheduled_at')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <hr>

                <!-- Reminders -->
                <div class="row">
                    <div class="col-md-12">
                        <h6 class="text-primary mb-3">Reminders</h6>

                        <div class="form-group mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="enable_reminders" id="enable_reminders"
                                       value="1" {{ old('enable_reminders', $campaign->enable_reminders || $campaign->reminders_enabled) ? 'checked' : '' }}>
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
                                                       id="reminder_1_days" name="reminder_1_days" 
                                                       value="{{ old('reminder_1_days', $campaign->reminder_1_days) }}"
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
                                                       id="reminder_1_subject" name="reminder_1_subject" 
                                                       value="{{ old('reminder_1_subject', $campaign->reminder_1_subject) }}">
                                                @error('reminder_1_subject')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="reminder_1_body" class="form-label">Body</label>
                                        <textarea class="form-control @error('reminder_1_body') is-invalid @enderror"
                                                  id="reminder_1_body" name="reminder_1_body" rows="4">{{ old('reminder_1_body', $campaign->reminder_1_body) }}</textarea>
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
                                                       id="reminder_2_days" name="reminder_2_days" 
                                                       value="{{ old('reminder_2_days', $campaign->reminder_2_days) }}"
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
                                                       id="reminder_2_subject" name="reminder_2_subject" 
                                                       value="{{ old('reminder_2_subject', $campaign->reminder_2_subject) }}">
                                                @error('reminder_2_subject')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="reminder_2_body" class="form-label">Body</label>
                                        <textarea class="form-control @error('reminder_2_body') is-invalid @enderror"
                                                  id="reminder_2_body" name="reminder_2_body" rows="4">{{ old('reminder_2_body', $campaign->reminder_2_body) }}</textarea>
                                        @error('reminder_2_body')
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
                                      id="notes" name="notes" rows="3">{{ old('notes', $campaign->notes) }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="form-group mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Campaign
                    </button>
                    <a href="{{ route('admin.marketing.campaigns.show', $campaign) }}" class="btn btn-secondary">
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
    });
</script>
@endpush

