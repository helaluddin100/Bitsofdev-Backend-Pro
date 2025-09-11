@extends('master.master')

@section('title', 'Edit Lead')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Lead</h1>
        <div class="btn-group">
            <a href="{{ route('admin.marketing.leads.show', $lead) }}" class="btn btn-info">
                <i class="fas fa-eye"></i> View Lead
            </a>
            <a href="{{ route('admin.marketing.leads.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Leads
            </a>
        </div>
    </div>

    <!-- Edit Lead Form -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Lead Information</h6>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.marketing.leads.update', $lead) }}">
                @csrf
                @method('PUT')

                <div class="row">
                    <!-- Basic Information -->
                    <div class="col-md-6">
                        <h6 class="text-primary mb-3">Basic Information</h6>

                        <div class="form-group mb-3">
                            <label for="name" class="form-label">Name *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                   id="name" name="name" value="{{ old('name', $lead->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                   id="email" name="email" value="{{ old('email', $lead->email) }}">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="phone" class="form-label">Phone</label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                   id="phone" name="phone" value="{{ old('phone', $lead->phone) }}">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="company" class="form-label">Company</label>
                            <input type="text" class="form-control @error('company') is-invalid @enderror"
                                   id="company" name="company" value="{{ old('company', $lead->company) }}">
                            @error('company')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="category" class="form-label">Category *</label>
                            <select class="form-control @error('category') is-invalid @enderror"
                                    id="category" name="category" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->name }}"
                                            {{ old('category', $lead->category) == $category->name ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1"
                                       {{ old('is_active', $lead->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Active Lead
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Address Information -->
                    <div class="col-md-6">
                        <h6 class="text-primary mb-3">Address Information</h6>

                        <div class="form-group mb-3">
                            <label for="address" class="form-label">Address</label>
                            <textarea class="form-control @error('address') is-invalid @enderror"
                                      id="address" name="address" rows="3">{{ old('address', $lead->address) }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="municipality" class="form-label">Municipality</label>
                            <input type="text" class="form-control @error('municipality') is-invalid @enderror"
                                   id="municipality" name="municipality" value="{{ old('municipality', $lead->municipality) }}">
                            @error('municipality')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="latitude" class="form-label">Latitude</label>
                                    <input type="number" step="any" class="form-control @error('latitude') is-invalid @enderror"
                                           id="latitude" name="latitude" value="{{ old('latitude', $lead->latitude) }}">
                                    @error('latitude')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="longitude" class="form-label">Longitude</label>
                                    <input type="number" step="any" class="form-control @error('longitude') is-invalid @enderror"
                                           id="longitude" name="longitude" value="{{ old('longitude', $lead->longitude) }}">
                                    @error('longitude')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Website & Social Media -->
                    <div class="col-md-6">
                        <h6 class="text-primary mb-3">Website & Social Media</h6>

                        <div class="form-group mb-3">
                            <label for="website" class="form-label">Website</label>
                            <input type="url" class="form-control @error('website') is-invalid @enderror"
                                   id="website" name="website" value="{{ old('website', $lead->website) }}">
                            @error('website')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="facebook" class="form-label">Facebook</label>
                            <input type="url" class="form-control @error('facebook') is-invalid @enderror"
                                   id="facebook" name="facebook" value="{{ old('facebook', $lead->facebook) }}">
                            @error('facebook')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="instagram" class="form-label">Instagram</label>
                            <input type="url" class="form-control @error('instagram') is-invalid @enderror"
                                   id="instagram" name="instagram" value="{{ old('instagram', $lead->instagram) }}">
                            @error('instagram')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="twitter" class="form-label">Twitter</label>
                            <input type="url" class="form-control @error('twitter') is-invalid @enderror"
                                   id="twitter" name="twitter" value="{{ old('twitter', $lead->twitter) }}">
                            @error('twitter')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="yelp" class="form-label">Yelp</label>
                            <input type="url" class="form-control @error('yelp') is-invalid @enderror"
                                   id="yelp" name="yelp" value="{{ old('yelp', $lead->yelp) }}">
                            @error('yelp')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Business Information -->
                    <div class="col-md-6">
                        <h6 class="text-primary mb-3">Business Information</h6>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="rating" class="form-label">Rating</label>
                                    <input type="number" step="0.1" min="0" max="5"
                                           class="form-control @error('rating') is-invalid @enderror"
                                           id="rating" name="rating" value="{{ old('rating', $lead->rating) }}">
                                    @error('rating')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="review_count" class="form-label">Review Count</label>
                                    <input type="number" min="0"
                                           class="form-control @error('review_count') is-invalid @enderror"
                                           id="review_count" name="review_count" value="{{ old('review_count', $lead->review_count) }}">
                                    @error('review_count')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="claimed" name="claimed" value="1"
                                       {{ old('claimed', $lead->claimed) ? 'checked' : '' }}>
                                <label class="form-check-label" for="claimed">
                                    Claimed Business
                                </label>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror"
                                      id="notes" name="notes" rows="4">{{ old('notes', $lead->notes) }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Lead
                    </button>
                    <a href="{{ route('admin.marketing.leads.show', $lead) }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
