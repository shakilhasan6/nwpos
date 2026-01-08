@extends('welcome')

@section('content')
<div class="container-fluid mt-4">
    <h2 class="mb-3" style="font-family: serif; color: #535353;">SERVICE REPORT DETAILS</h2>

    {{-- Success Message --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif

    {{-- Service Report Details --}}
    <div class="card shadow-lg">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Service Report - {{ $serviceReport->tid }}</h5>
        </div>
        <div class="card-body p-4">

            <!-- Service Information Section -->
            <div class="card mb-3">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><strong>📋 Service Information</strong></h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Zone Name:</strong> {{ $serviceReport->zone_name }}</p>
                            <p><strong>Engineer Name:</strong> {{ $serviceReport->engineer_name }}</p>
                            <p><strong>TID:</strong> {{ $serviceReport->tid }}</p>
                            <p><strong>POS Serial:</strong> {{ $serviceReport->pos_serial }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Merchant Address:</strong> {{ $serviceReport->merchant_address }}</p>
                            <p><strong>Service Type:</strong> {{ $serviceReport->service_type }}</p>
                            <p><strong>Created At:</strong> {{ $serviceReport->created_at->timezone('Asia/Dhaka')->format('d-m-Y h:i A') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Remarks Section -->
            <div class="card mb-3">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><strong>📝 Remarks</strong></h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-light border">
                        {{ $serviceReport->remarks ?? 'No remarks added' }}
                    </div>
                </div>
            </div>

            <!-- Image Section -->
            @if (!empty($serviceReport->service_report_image_path))
                <div class="card">
                    <div class="card-header bg-light">
                        <h6 class="mb-0"><strong>📷 Service Report Image</strong></h6>
                    </div>
                    <div class="card-body text-center">
                        <img src="{{ asset('storage/' . $serviceReport->service_report_image_path) }}"
                             alt="Service Report Image" class="img-fluid img-thumbnail"
                             style="max-height: 450px;">
                    </div>
                </div>
            @else
                <div class="card">
                    <div class="card-header bg-light">
                        <h6 class="mb-0"><strong>📷 Service Report Image</strong></h6>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info mb-0">
                            <i class="fa fa-info-circle"></i> No image uploaded
                        </div>
                    </div>
                </div>
            @endif

        </div>
        <div class="card-footer bg-light">
            <a href="{{ route('service-reports.index') }}" class="btn btn-primary">
                <i class="fa fa-arrow-left"></i> Back to Form
            </a>
        </div>
    </div>
</div>
@endsection