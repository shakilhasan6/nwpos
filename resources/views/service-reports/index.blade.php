@extends('welcome')

@section('content')
    <div class="container-fluid mt-2">
        <h2 class="mb-3" style="font-family: serif; color: #535353;">SERVICE REPORTS</h2>

        {{-- Success/Error Messages --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        @endif

        <div class="mb-3">
            <a href="{{ route('service-reports.create') }}" class="btn btn-primary">
                <i class="fa fa-plus"></i> Add New Service Report
            </a>
        </div>

        <div class="card shadow-sm">
            <div class="card-body table-responsive">
                <table id="serviceReportsTable" class="table table-striped table-hover align-middle">
                    <thead class="table-success">
                        <tr>
                            <th>SN</th>
                            <th>TID</th>
                            <th>Bank</th>
                            <th>Engineer</th>
                            <th>Service Type</th>
                            <th>Date/Time</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($serviceReports as $key => $report)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $report->tid }}</td>
                                <td>{{ $report->bank_name }}</td>
                                <td>{{ $report->engineer_name }}</td>
                                <td>{{ $report->service_type }}</td>
                                <td>{{ $report->created_at->timezone('Asia/Dhaka')->format('d-m-Y h:i A') }}</td>
                                <td class="text-center">
                                    @php
                                        $user = auth()->user();
                                        $canEdit = $user->isAdmin() || $user->isChecker() || $user->isVerify();
                                    @endphp
                                    @if ($canEdit)
                                        <button type="button" class="btn btn-sm btn-info" data-toggle="modal"
                                            data-target="#editModal{{ $report->id }}">
                                            <i class="fa fa-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-primary" data-toggle="modal"
                                            data-target="#detailsModal{{ $report->id }}">
                                            <i class="fa fa-eye"></i>
                                        </button>

                                        <form action="{{ route('service-reports.destroy', $report->id) }}" method="POST"
                                            class="d-inline" onsubmit="return confirm('Are you sure?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        @else
                                            <button type="button" class="btn btn-sm btn-primary" data-toggle="modal"
                                            data-target="#detailsModal{{ $report->id }}">
                                            <i class="fa fa-eye"></i>
                                        </button>
                                    @endif
                                    </form>
                                </td>
                            </tr>

                            <div class="modal fade" id="editModal{{ $report->id }}" tabindex="-1" role="dialog"
                                aria-hidden="true" style="z-index: 1060;">
                                <div class="modal-dialog modal-lg" role="document">
                                    <div class="modal-content">
                                        <form action="{{ route('service-reports.update', $report->id) }}" method="POST"
                                            enctype="multipart/form-data">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-header bg-info text-white">
                                                <h5 class="modal-title">Edit Service Report - {{ $report->tid }}</h5>
                                                <button type="button" class="close text-white" data-dismiss="modal"
                                                    aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body p-4">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label><strong>Zone Name *</strong></label>
                                                            <select name="zone_name" class="form-control" required>
                                                                @foreach (['Barisal', 'Bogor', 'Chattogram Central', 'Chattogram North', 'Chattogram South', 'Cumilla', 'Dhaka Central', 'Dhaka North', 'Dhaka South', 'Faridpur', 'Gazipur', 'Khulna', 'Mymensingh', 'Narayanganj', 'Noakhali', 'Rajshahi', 'Rangpur', 'Sylhet', 'Tangail'] as $zone)
                                                                    <option value="{{ $zone }}"
                                                                        {{ $report->zone_name == $zone ? 'selected' : '' }}>
                                                                        {{ $zone }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="form-group">
                                                            <label><strong>Bank Name *</strong></label>
                                                            <select name="bank_name" class="form-control" required>
                                                                @foreach (['PBL', 'MTB', 'IBBL', 'EBL', 'CITY'] as $bank)
                                                                    <option value="{{ $bank }}"
                                                                        {{ $report->bank_name == $bank ? 'selected' : '' }}>
                                                                        {{ $bank }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="form-group">
                                                            <label><strong>Engineer Name *</strong></label>
                                                            <input type="text" name="engineer_name" class="form-control"
                                                                readonly value="{{ $report->engineer_name }}" required>
                                                        </div>
                                                        <div class="form-group">
                                                            <label><strong>TID *</strong></label>
                                                            <input type="text" name="tid" class="form-control"
                                                                value="{{ $report->tid }}" required>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label><strong>Merchant Address *</strong></label>
                                                            <textarea name="merchant_address" class="form-control" rows="2" required>{{ $report->merchant_address }}</textarea>
                                                        </div>
                                                        <div class="form-group">
                                                            <label><strong>Service Type *</strong></label>
                                                            <select name="service_type" class="form-control" required>
                                                                @foreach (['Merchant Deploy', 'Branch Deploy', 'Support', 'Replace', 'Roll Out', 'Roll Out Not Done'] as $type)
                                                                    <option value="{{ $type }}"
                                                                        {{ $report->service_type == $type ? 'selected' : '' }}>
                                                                        {{ $type }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="form-group">
                                                            <label><strong>POS Serial *</strong></label>
                                                            <input type="text" name="pos_serial" class="form-control"
                                                                value="{{ $report->pos_serial }}" required>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label><strong>Remarks</strong></label>
                                                    <textarea name="remarks" class="form-control" rows="3">{{ $report->remarks }}</textarea>
                                                </div>
                                                <div class="form-group">
                                                    <label><strong>Update Image</strong></label>
                                                    <input type="file" name="service_report_image" class="form-control"
                                                        accept="image/*">
                                                    @if ($report->service_report_image_path)
                                                        <img src="{{ asset('storage/' . $report->service_report_image_path) }}"
                                                            class="mt-2 img-thumbnail" style="max-height: 150px;">
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-success">Update Report</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <div class="modal fade" id="detailsModal{{ $report->id }}" tabindex="-1" role="dialog"
                                aria-labelledby="detailsModalLabel{{ $report->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-xl">
                                    <div class="modal-content shadow-lg">
                                        <div class="modal-header bg-primary text-white">
                                            <h5 class="modal-title" id="detailsModalLabel{{ $report->id }}">Service
                                                Report Details - {{ $report->tid }}</h5>
                                            <button type="button" class="close text-white"
                                                data-dismiss="modal">&times;</button>
                                        </div>
                                        <div class="modal-body p-4">

                                            <div class="card mb-3">
                                                <div class="card-header bg-light">
                                                    <h6 class="mb-0"><strong>📋 Service Information</strong></h6>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <p><strong>Zone Name:</strong> {{ $report->zone_name }}</p>
                                                            <p><strong>Bank Name:</strong> {{ $report->bank_name }}</p>
                                                            <p><strong>Engineer Name:</strong> {{ $report->engineer_name }}
                                                            </p>
                                                            <p><strong>TID:</strong> {{ $report->tid }}</p>
                                                            <p><strong>POS Serial:</strong> {{ $report->pos_serial }}</p>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <p><strong>Merchant Address:</strong>
                                                                {{ $report->merchant_address }}</p>
                                                            <p><strong>Service Type:</strong> {{ $report->service_type }}
                                                            </p>
                                                            <p><strong>Created At:</strong>
                                                                {{ $report->created_at->timezone('Asia/Dhaka')->format('d-m-Y h:i A') }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="card mb-3">
                                                <div class="card-header bg-light">
                                                    <h6 class="mb-0"><strong>📝 Remarks</strong></h6>
                                                </div>
                                                <div class="card-body">
                                                    <div class="alert alert-light border">
                                                        {{ $report->remarks ?? 'No remarks added' }}
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="card">
                                                <div class="card-header bg-light">
                                                    <h6 class="mb-0"><strong>📷 Service Report Image</strong></h6>
                                                </div>
                                                <div class="card-body text-center">
                                                    @if (!empty($report->service_report_image_path))
                                                        <a href="{{ asset('storage/' . $report->service_report_image_path) }}"
                                                            target="_blank">
                                                            <img src="{{ asset('storage/' . $report->service_report_image_path) }}"
                                                                alt="Service Report Image" class="img-fluid img-thumbnail"
                                                                style="max-height: 450px; cursor: pointer;">
                                                        </a>
                                                    @else
                                                        <div class="alert alert-info mb-0">
                                                            <i class="fa fa-info-circle"></i> No image uploaded
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>

                                        </div>
                                        <div class="modal-footer bg-light">
                                            <button type="button" class="btn btn-secondary"
                                                data-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        @empty
                            <tr>
                                <td colspan="7" class="text-center">No reports found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
