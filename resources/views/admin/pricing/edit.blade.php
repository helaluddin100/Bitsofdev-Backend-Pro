@extends('master.master')

@section('content')
    <div class="page-content">
        <nav class="page-breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.pricing.index') }}">Pricing</a></li>
                <li class="breadcrumb-item active">Edit Plan</li>
            </ol>
        </nav>

        <div class="row">
            <div class="col-lg-8 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Edit Pricing Plan: {{ $pricing->name }}</h4>

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('admin.pricing.update', $pricing) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label for="name" class="form-label">Plan Name <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    name="name" id="name" value="{{ old('name', $pricing->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Description <span
                                        class="text-danger">*</span></label>
                                <textarea class="form-control @error('description') is-invalid @enderror" name="description" id="description"
                                    rows="3" required>{{ old('description', $pricing->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="price" class="form-label">Price <span
                                                class="text-danger">*</span></label>
                                        <input type="number" class="form-control @error('price') is-invalid @enderror"
                                            name="price" id="price" value="{{ old('price', $pricing->price) }}"
                                            step="0.01" min="0" required>
                                        @error('price')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="currency" class="form-label">Currency <span
                                                class="text-danger">*</span></label>
                                        <select class="form-select @error('currency') is-invalid @enderror" name="currency"
                                            id="currency" required>
                                            <option value="">Select Currency</option>
                                            <option value="USD"
                                                {{ old('currency', $pricing->currency) == 'USD' ? 'selected' : '' }}>
                                                USD ($)</option>
                                            <option value="EUR"
                                                {{ old('currency', $pricing->currency) == 'EUR' ? 'selected' : '' }}>
                                                EUR (€)</option>
                                            <option value="GBP"
                                                {{ old('currency', $pricing->currency) == 'GBP' ? 'selected' : '' }}>
                                                GBP (£)</option>
                                        </select>
                                        @error('currency')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="billing_cycle" class="form-label">Billing Cycle <span
                                                class="text-danger">*</span></label>
                                        <select class="form-select @error('billing_cycle') is-invalid @enderror"
                                            name="billing_cycle" id="billing_cycle" required>
                                            <option value="">Select Cycle</option>
                                            <option value="monthly"
                                                {{ old('billing_cycle', $pricing->billing_cycle) == 'monthly' ? 'selected' : '' }}>
                                                Monthly</option>
                                            <option value="quarterly"
                                                {{ old('billing_cycle', $pricing->billing_cycle) == 'quarterly' ? 'selected' : '' }}>
                                                Quarterly</option>
                                            <option value="yearly"
                                                {{ old('billing_cycle', $pricing->billing_cycle) == 'yearly' ? 'selected' : '' }}>
                                                Yearly</option>
                                            <option value="one-time"
                                                {{ old('billing_cycle', $pricing->billing_cycle) == 'one-time' ? 'selected' : '' }}>
                                                One Time</option>
                                        </select>
                                        @error('billing_cycle')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="features" class="form-label">Features</label>
                                <div id="features-container">
                                    @foreach ($pricing->features as $index => $feature)
                                        <div class="feature-item mb-2">
                                            <div class="input-group">
                                                <input type="text" class="form-control"
                                                    name="features[{{ $index }}][name]"
                                                    value="{{ $feature->name }}" placeholder="Feature name" required>
                                                <input type="text" class="form-control"
                                                    name="features[{{ $index }}][description]"
                                                    value="{{ $feature->description }}"
                                                    placeholder="Feature description (optional)">
                                                <button type="button" class="btn btn-outline-danger"
                                                    onclick="removeFeature(this)">
                                                    <i data-feather="trash-2"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="addFeature()">
                                    <i data-feather="plus"></i> Add Feature
                                </button>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="sort_order" class="form-label">Display Order</label>
                                        <input type="number" class="form-control" name="sort_order" id="sort_order"
                                            value="{{ old('sort_order', $pricing->sort_order ?? 0) }}" min="0"
                                            placeholder="Lower = First">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="slug" class="form-label">Slug (Auto-generated)</label>
                                        <input type="text" class="form-control" name="slug" id="slug"
                                            value="{{ old('slug', $pricing->slug) }}" readonly>
                                        <small class="form-text text-muted">This will be automatically generated from the
                                            plan name</small>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" name="is_popular"
                                                id="is_popular" value="1"
                                                {{ old('is_popular', $pricing->is_popular) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_popular">Popular Plan</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" name="is_active"
                                                id="is_active" value="1"
                                                {{ old('is_active', $pricing->is_active) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_active">Active</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" name="is_featured"
                                                id="is_featured" value="1"
                                                {{ old('is_featured', $pricing->is_featured ?? false) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_featured">Featured Plan</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('admin.pricing.index') }}" class="btn btn-secondary">Cancel</a>
                                <div>
                                    <button type="button" class="btn btn-danger me-2" onclick="deletePlan()">
                                        <i data-feather="trash-2"></i> Delete Plan
                                    </button>
                                    <button type="submit" class="btn btn-primary" onclick="return validateForm()">Update
                                        Plan</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        let featureCount = {{ count($pricing->features) }};

        function addFeature() {
            const container = document.getElementById('features-container');
            const featureDiv = document.createElement('div');
            featureDiv.className = 'feature-item mb-2';
            featureDiv.innerHTML = `
                <div class="input-group">
                    <input type="text" class="form-control"
                           name="features[${featureCount}][name]"
                           placeholder="Feature name" required>
                    <input type="text" class="form-control"
                           name="features[${featureCount}][description]"
                           placeholder="Feature description (optional)">
                    <button type="button" class="btn btn-outline-danger" onclick="removeFeature(this)">
                        <i data-feather="trash-2"></i>
                    </button>
                </div>
            `;
            container.appendChild(featureDiv);
            featureCount++;

            if (typeof feather !== 'undefined') {
                feather.replace();
            }
        }

        function removeFeature(button) {
            button.closest('.feature-item').remove();
        }

        function deletePlan() {
            if (confirm('Are you sure you want to delete this pricing plan? This action cannot be undone.')) {
                // Create a form to submit DELETE request
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route('admin.pricing.destroy', $pricing) }}';

                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';

                const methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'DELETE';

                form.appendChild(csrfToken);
                form.appendChild(methodField);
                document.body.appendChild(form);
                form.submit();
            }
        }

        // Auto-generate slug from name
        document.getElementById('name').addEventListener('input', function() {
            const name = this.value;
            const slug = name.toLowerCase()
                .replace(/[^a-z0-9 -]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-')
                .trim('-');
            document.getElementById('slug').value = slug;
        });

        // Form validation
        function validateForm() {
            const requiredFields = ['name', 'description', 'price', 'currency', 'billing_cycle'];
            let isValid = true;

            requiredFields.forEach(fieldId => {
                const field = document.getElementById(fieldId);
                const value = field.value.trim();

                if (!value) {
                    field.classList.add('is-invalid');
                    isValid = false;
                } else {
                    field.classList.remove('is-invalid');
                }
            });

            return isValid;
        }

        // Add form submit validation
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            form.addEventListener('submit', function(e) {
                if (!validateForm()) {
                    e.preventDefault();
                    alert('Please fill in all required fields.');
                }
            });
        });
    </script>
@endsection
