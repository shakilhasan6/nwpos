@extends('welcome')

@section('content')
    <div class="container-fluid mt-4">
        <h2 class="mb-3" style="font-family: serif; color: #535353;">ASSIGNE REPORTS</h2>

        {{-- Success Message --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        @endif

        {{-- Error Message --}}
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        @endif

        {{-- Data Table --}}
        <div class="card shadow-sm">
            <div class="card-body table-responsive">
                <table id="reportsTable" class="table table-striped table-hover align-middle">
                    <thead class="table-success">
                        <tr>
                            <th>SN</th>
                            <th>TID</th>
                            <th>Merchant</th>
                            <th>Address</th>
                            <th>Status</th>
                            <th>Engineer</th>
                            <th>Bank Name</th>
                            <th>Date/Time</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($data ?? [] as $key => $report)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $report->tid }}</td>
                                <td>{{ $report->merchent }}</td>
                                <td>{{ $report->address }}</td>
                                <td>
                                    <span
                                        class="badge {{ ($report->status ?? '') === 'Done' ? 'badge-success' : 'badge-warning' }}">
                                        {{ $report->status ?? 'Pending' }}
                                    </span>
                                </td>
                                <td>{{ $report->engineer_name }}</td>
                                <td>{{ $report->bank }}</td>
                                <td>{{ $report->assignment_date }}</td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-info" data-toggle="modal"
                                        data-target="#editModal{{ $report->id }}">
                                        <i class="fa fa-edit"></i>
                                    </button>

                                    <button type="button" class="btn btn-sm btn-primary" data-toggle="modal"
                                        data-target="#detailsModal{{ $report->id }}">
                                        <i class="fa fa-eye"></i> 
                                    </button>

                                    @if (auth()->user()->isAdmin())
                                        <form action="{{ route('reports.destroy', $report->id) }}" method="POST"
                                            class="d-inline" onsubmit="return confirm('Are you sure?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fa fa-trash"></i> 
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>

                            <!-- Edit Modal -->
                            <div class="modal fade" id="editModal{{ $report->id }}" tabindex="-1" role="dialog"
                                aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <form action="{{ route('report.update', $report->id) }}" method="POST"
                                            enctype="multipart/form-data">
                                            @csrf
                                            @method('POST')
                                            <div class="modal-header bg-info text-white">
                                                <h5 class="modal-title">Edit Report - {{ $report->tid }}</h5>
                                                <button type="button" class="close text-white"
                                                    data-dismiss="modal">&times;</button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label><strong>TID (Read Only)</strong></label>
                                                            <input type="text" class="form-control"
                                                                value="{{ $report->tid }}" disabled>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label><strong>MID (Read Only)</strong></label>
                                                            <input type="text" class="form-control"
                                                                value="{{ $report->mid }}" disabled>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label><strong>Status *</strong></label>
                                                    <select name="status" class="form-control" required>
                                                        <option value="pending"
                                                            {{ ($report->status ?? '') === 'pending' ? 'selected' : '' }}>
                                                            Pending</option>
                                                        <option value="assigned"
                                                            {{ ($report->status ?? '') === 'assigned' ? 'selected' : '' }}>
                                                            Assigned</option>
                                                        <option value="completed"
                                                            {{ ($report->status ?? '') === 'completed' ? 'selected' : '' }}>
                                                            Completed</option>

                                                    </select>
                                                </div>

                                                <div class="form-group">
                                                    <label><strong>Remarks</strong></label>
                                                    <textarea name="remarks" class="form-control" rows="4" placeholder="Enter any remarks here...">{{ $report->remarks ?? '' }}</textarea>
                                                </div>

                                                <div class="form-group">
                                                    <label><strong>Upload Image</strong></label>
                                                    <input type="file" name="image" class="form-control"
                                                        accept="image/*">
                                                    <small class="text-muted d-block mt-2">Allowed: JPG, PNG, GIF (Max
                                                        2MB)</small>

                                                    @if (!empty($report->image_path))
                                                        <div class="mt-3">
                                                            <strong>Current Image:</strong>
                                                            <div class="mt-2">
                                                                <img src="{{ asset('storage/' . $report->image_path) }}"
                                                                    alt="Report Image" class="img-thumbnail"
                                                                    style="max-width: 200px; max-height: 150px;">
                                                            </div>
                                                            <small class="text-muted">Current image will be replaced if new
                                                                file is uploaded</small>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-success">
                                                    <i class="fa fa-save"></i> Update Report
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Details Modal -->
                            <div class="modal fade" id="detailsModal{{ $report->id }}" tabindex="-1" role="dialog"
                                aria-labelledby="detailsModalLabel{{ $report->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-xl">
                                    <div class="modal-content shadow-lg">
                                        <div class="modal-header bg-primary text-white">
                                            <h5 class="modal-title" id="detailsModalLabel{{ $report->id }}">Report
                                                Details - {{ $report->tid }}</h5>
                                            <button type="button" class="close text-white"
                                                data-dismiss="modal">&times;</button>
                                        </div>
                                        <div class="modal-body p-4">

                                            <!-- Terminal Information Section -->
                                            <div class="card mb-3">
                                                <div class="card-header bg-light">
                                                    <h6 class="mb-0"><strong>📱 Terminal Information</strong></h6>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <p><strong>TID:</strong> {{ $report->tid }}</p>
                                                            <p><strong>MID:</strong> {{ $report->mid }}</p>
                                                            <p><strong>Merchant:</strong> {{ $report->merchent }}</p>
                                                            <p><strong>POS Serial:</strong> {{ $report->pos_s }}</p>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <p><strong>Address:</strong> {{ $report->address }}</p>
                                                            <p><strong>Officer:</strong> {{ $report->officer }}</p>
                                                            <p><strong>Contact Number:</strong> {{ $report->number }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Assignment Information Section -->
                                            <div class="card mb-3">
                                                <div class="card-header bg-light">
                                                    <h6 class="mb-0"><strong>👤 Assignment Information</strong></h6>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <p><strong>Engineer Name:</strong> {{ $report->engineer_name }}
                                                            </p>
                                                            <p><strong>Engineer Contact:</strong>
                                                                {{ $report->engineer_contact ?? 'N/A' }}</p>
                                                            <p><strong>Bank Name:</strong> {{ $report->bank ?? 'N/A' }}</p>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <p><strong>Assign Date:</strong> <span
                                                                    class="badge badge-info">{{ $report->assignment_date ?? 'N/A' }}</span>
                                                            </p>
                                                            <p><strong>Status:</strong> <span
                                                                    class="badge {{ ($report->status ?? '') === 'Done' ? 'badge-success' : 'badge-warning' }}">{{ $report->status ?? 'Pending' }}</span>
                                                            </p>
                                                            <p><strong>Update
                                                                    Date:</strong>{{ $report->updated_at->timezone('Asia/Dhaka')->format('d-m-Y h:i A') }}
                                                            </p>
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
                                                        {{ $report->remarks ?? 'No remarks added' }}
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Image Section -->
                                            @if (!empty($report->image_path))
                                                <div class="card">
                                                    <div class="card-header bg-light">
                                                        <h6 class="mb-0"><strong>📷 Uploaded Image</strong></h6>
                                                    </div>
                                                    <div class="card-body text-center">
                                                        <img src="{{ asset('storage/' . $report->image_path) }}"
                                                            alt="Report Image" class="img-fluid img-thumbnail"
                                                            style="max-height: 450px;">
                                                    </div>
                                                </div>
                                            @else
                                                <div class="card">
                                                    <div class="card-header bg-light">
                                                        <h6 class="mb-0"><strong>📷 Uploaded Image</strong></h6>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="alert alert-info mb-0">
                                                            <i class="fa fa-info-circle"></i> No image uploaded
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif

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
                                <td colspan="9" class="text-center text-muted p-5">
                                    <strong>No reports found.</strong>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Search Functionality --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tableCard = document.querySelector('.card-body');
            if (!tableCard) return;

            const searchInput = document.createElement('input');
            searchInput.type = 'text';
            searchInput.placeholder = 'Search by TID, MID, Merchant, Engineer...';
            searchInput.classList.add('form-control', 'mb-3');
            tableCard.prepend(searchInput);

            const table = document.getElementById('reportsTable');
            if (!table) return;
            const rows = table.querySelectorAll('tbody tr');

            searchInput.addEventListener('keyup', function() {
                const query = this.value.toLowerCase();
                rows.forEach(row => {
                    if (!row.querySelector('td')) return;
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(query) || query === '' ? '' : 'none';
                });
            });

            if (window.feather) feather.replace();
        });
    </script>
@endsection
