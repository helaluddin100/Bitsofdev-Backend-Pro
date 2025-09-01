@extends('master.master')

@section('content')
    <div class="page-content">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Analytics Test Page</h4>
                        <p>If you can see this page, the analytics route is working!</p>

                        <div class="alert alert-success">
                            <strong>Success!</strong> Analytics route is accessible.
                        </div>

                        <div class="mt-3">
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-primary">Back to Dashboard</a>
                            <a href="{{ route('admin.analytics.index') }}" class="btn btn-success">Go to Full Analytics</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
