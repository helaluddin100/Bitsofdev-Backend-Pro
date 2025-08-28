@extends('master.master')

@section('content')
    <div class="page-content">

        <nav class="page-breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Forms</a></li>
                <li class="breadcrumb-item active" aria-current="page">Create Pricing Plan</li>
            </ol>
        </nav>

        <div class="row">
            <div class="col-lg-8 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Create Pricing Plan</h4>

                        <form action="{{ route('admin.pricing.store') }}" method="Post" id="pricing-form">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Plan Name</label>
                                        <input id="name" class="form-control" name="name" type="text" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="sort_order" class="form-label">Display Order</label>
                                        <input id="sort_order" class="form-control" name="sort_order" type="number" min="0" value="0">
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" name="description" id="description" rows="3" required></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="price" class="form-label">Price</label>
                                        <input id="price" class="form-control" name="price" type="number" step="0.01" min="0" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="currency" class="form-label">Currency</label>
                                        <select class="form-control" name="currency" id="currency" required>
                                            <option value="USD">USD ($)</option>
                                            <option value="EUR">EUR (€)</option>
                                            <option value="GBP">GBP (£)</option>
                                            <option value="BDT">BDT (৳)</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="billing_cycle" class="form-label">Billing Cycle</label>
                                <select class="form-control" name="billing_cycle" id="billing_cycle" required>
                                    <option value="monthly">Monthly</option>
                                    <option value="yearly">Yearly</option>
                                    <option value="one-time">One Time</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <div class="form-check">
                                    <label class="form-check-label" for="is_popular">
                                        Popular Plan
                                    </label>
                                    <input type="checkbox" class="form-check-input" name="is_popular" id="is_popular" value="1">
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="form-check">
                                    <label class="form-check-label" for="is_active">
                                        Active
                                    </label>
                                    <input type="checkbox" class="form-check-input" checked name="is_active" id="is_active" value="1">
                                </div>
                            </div>

                            <!-- Features Section -->
                            <div class="card mt-4">
                                <div class="card-header">
                                    <h6 class="mb-0">Plan Features</h6>
                                </div>
                                <div class="card-body">
                                    <div id="features-container">
                                        <div class="feature-item row mb-3">
                                            <div class="col-md-5">
                                                <input type="text" class="form-control" name="features[0][name]" placeholder="Feature name" required>
                                            </div>
                                            <div class="col-md-5">
                                                <input type="text" class="form-control" name="features[0][description]" placeholder="Feature description (optional)">
                                            </div>
                                            <div class="col-md-2">
                                                <button type="button" class="btn btn-danger btn-sm" onclick="removeFeature(this)">
                                                    <i data-feather="trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="addFeature()">
                                        <i data-feather="plus"></i> Add Feature
                                    </button>
                                </div>
                            </div>

                            <input class="btn btn-primary" type="submit" value="Submit">
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@section('js')
<script>
let featureCount = 1;

function addFeature() {
    const container = document.getElementById('features-container');
    const newFeature = document.createElement('div');
    newFeature.className = 'feature-item row mb-3';
    newFeature.innerHTML = `
        <div class="col-md-5">
            <input type="text" class="form-control" name="features[${featureCount}][name]" placeholder="Feature name" required>
        </div>
        <div class="col-md-5">
            <input type="text" class="form-control" name="features[${featureCount}][description]" placeholder="Feature description (optional)">
        </div>
        <div class="col-md-2">
            <button type="button" class="btn btn-danger btn-sm" onclick="removeFeature(this)">
                <i data-feather="trash"></i>
            </button>
        </div>
    `;
    container.appendChild(newFeature);
    featureCount++;

    // Reinitialize Feather icons
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
}

function removeFeature(button) {
    button.closest('.feature-item').remove();
}

// Initialize when page loads
document.addEventListener('DOMContentLoaded', function() {
    // Reinitialize Feather icons
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
});
</script>
@endsection
