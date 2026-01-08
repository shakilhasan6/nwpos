@extends('welcome')

@section('content')
<div class="container-fluid mt-2">

    <h3 class="mb-3" style="font-family: serif; color: #312f2f; text-transform: uppercase">EBL BANK DATA</h3>

    <div class="d-flex mb-3">
        {{-- Add New Button --}}
        <button type="button" class="btn btn-success" data-toggle="modal" data-target="#createServiceModal">
            <i data-feather="plus"></i> Add New Data
        </button>

        {{-- Import Excel Button --}}
        <button type="button" class="btn btn-primary ml-2" data-toggle="modal" data-target="#importCSVModal">
            <i data-feather="upload"></i> Import Excel
        </button>

        {{-- Search Box --}}
        <div class="ml-3">
            <input type="text" id="searchInput" class="form-control" placeholder="Search by TID, MID, Merchant..." style="width: 300px;">
        </div>
    </div>

    {{-- Success Message --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    @endif

    {{-- Error Message --}}
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    @endif

    {{-- Data Table --}}
    <div class="card shadow-sm">
        <div class="card-body table-responsive">
            @if($data && $data->count() > 0)
                <div class="table-responsive">
                    <table id="eblTable" class="table table-striped table-hover">
                        <thead class="bg-success text-white">
                            <tr>
                                <th>SN</th>
                                <th>TID</th>
                                <th>MID</th>
                                <th>MERCHANT</th>
                                <th>ADDRESS</th>
                                <th>SERIAL</th>
                                <th>OFFICER</th>
                                <th>NUMBER</th>
                                <th style="width: 220px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data as $key => $item)
                                <tr>
                                    <td>{{ $data->firstItem() + $key }}</td>
                                    <td>{{ $item->tid }}</td>
                                    <td>{{ $item->mid }}</td>
                                    <td>{{ $item->merchent }}</td>
                                    <td>{{ $item->address }}</td>
                                    <td>{{ $item->pos_s }}</td>
                                    <td>{{ $item->officer }}</td>
                                    <td>{{ $item->number }}</td>
                                    <td>
                                        {{-- Assign Engineer --}}
                                        <button class="btn btn-warning btn-sm" data-toggle="modal"
                                            data-target="#assignModal{{ $item->id }}">
                                            <i data-feather="user-plus"></i> Assign
                                        </button>

                                        {{-- Edit --}}
                                        <button class="btn btn-info btn-sm" data-toggle="modal"
                                            data-target="#editModal{{ $item->id }}">
                                            <i data-feather="edit"></i> Edit
                                        </button>

                                        {{-- Delete --}}
                                        <form action="{{ route('ebl.destroy', $item->id) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">
                                                <i data-feather="trash-2"></i> Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>

                                {{-- Edit Modal --}}
                                <div class="modal fade" id="editModal{{ $item->id }}" tabindex="-1" role="dialog"
                                    aria-labelledby="editModalLabel{{ $item->id }}" aria-hidden="true">
                                    <div class="modal-dialog modal-lg" role="document">
                                        <div class="modal-content">
                                            <form method="POST" action="{{ route('ebl.update', $item->id) }}">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-header bg-primary text-white">
                                                    <h5 class="modal-title">Edit Data</h5>
                                                    <button type="button" class="close text-white" data-dismiss="modal">
                                                        <span>&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>TID</label>
                                                                <input type="text" name="tid" class="form-control" value="{{ $item->tid }}" required>
                                                            </div>
                                                            <div class="form-group">
                                                                <label>MID</label>
                                                                <input type="text" name="mid" class="form-control" value="{{ $item->mid }}" required>
                                                            </div>
                                                            <div class="form-group">
                                                                <label>MERCHANT NAME</label>
                                                                <input type="text" name="merchent" class="form-control" value="{{ $item->merchent }}" required>
                                                            </div>
                                                            <div class="form-group">
                                                                <label>ADDRESS</label>
                                                                <input type="text" name="address" class="form-control" value="{{ $item->address }}" required>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>OFFICER</label>
                                                                <input type="text" name="officer" class="form-control" value="{{ $item->officer }}" required>
                                                            </div>
                                                            <div class="form-group">
                                                                <label>NUMBER</label>
                                                                <input type="text" name="number" class="form-control" value="{{ $item->number }}" required>
                                                            </div>
                                                            <div class="form-group">
                                                                <label>POS SERIAL</label>
                                                                <input type="text" name="pos_s" class="form-control" value="{{ $item->pos_s }}" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-primary">Update</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                {{-- Assign Modal --}}
                                <div class="modal fade" id="assignModal{{ $item->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                    <div class="modal-dialog modal-lg" role="document">
                                        <div class="modal-content">
                                            <form action="{{ route('ebl.assign', $item->id) }}" method="POST">
                                                @csrf
                                                <div class="modal-header bg-warning">
                                                    <h5 class="modal-title">Assign Engineer</h5>
                                                    <button type="button" class="close" data-dismiss="modal">
                                                        <span>&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>Engineer Name</label>
                                                                <input type="text" name="engineer_name" class="form-control" placeholder="Engineer Name" required>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>Engineer Contact</label>
                                                                <input type="text" name="engineer_contact" class="form-control" placeholder="Engineer Contact" required>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>Assignment Date</label>
                                                                <input type="date" name="assignment_date" class="form-control" required>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>Status</label>
                                                                <select name="status" class="form-control" required>
                                                                    <option value="">Select Status</option>
                                                                    <option value="pending">Pending</option>

                                                                </select>
                                                            </div>
                                                        </div>
                                                {{-- bank name list  --}}
                                                     <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>Bank Name</label>
                                                                <select name="bank" class="form-control" required>
                                                                    <option value="">Select Status</option>
                                                                    <option value="PBL">PBL</option>
                                                                    <option value="MTB">MTB</option>
                                                                    <option value="IBBL">IBBL</option>
                                                                   <option value="EBL">EBL</option>
                                                                   <option value="City">City</option>
                                                                </select>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-success">Assign Now</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-muted">
                        Showing {{ $data->firstItem() }} to {{ $data->lastItem() }} of {{ $data->total() }} entries
                    </div>
                    <div>
                        {{ $data->links('pagination::bootstrap-4') }}
                    </div>
                </div>

            @else
                <div class="alert alert-info">
                    <strong>No Data Found!</strong> Please add data or import from CSV.
                </div>
            @endif
        </div>
    </div>

</div>

{{-- Create Modal --}}
<div class="modal fade" id="createServiceModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form method="POST" action="{{ route('ebl.store') }}">
                @csrf
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Add New Data</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>TID</label>
                                <input type="text" name="tid" class="form-control" placeholder="TID" required>
                            </div>
                            <div class="form-group">
                                <label>MID</label>
                                <input type="text" name="mid" class="form-control" placeholder="MID" required>
                            </div>
                            <div class="form-group">
                                <label>MERCHANT NAME</label>
                                <input type="text" name="merchent" class="form-control" placeholder="Merchant Name" required>
                            </div>
                            <div class="form-group">
                                <label>ADDRESS</label>
                                <input type="text" name="address" class="form-control" placeholder="Address" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>OFFICER</label>
                                <input type="text" name="officer" class="form-control" placeholder="Officer" required>
                            </div>
                            <div class="form-group">
                                <label>NUMBER</label>
                                <input type="text" name="number" class="form-control" placeholder="Number" required>
                            </div>
                            <div class="form-group">
                                <label>POS SERIAL</label>
                                <input type="text" name="pos_s" class="form-control" placeholder="POS Serial" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Import CSV Modal --}}
<div class="modal fade" id="importCSVModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form method="POST" action="{{ route('ebl.import') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Import Excel Data</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label><strong>Select XLSX File</strong></label>
                        <input type="file" name="file" class="form-control" accept=".csv,.txt,.xlsx,.xls" required>
                        <small class="text-muted">Accepts  XLSX</small>
                    </div>
                    <div class="alert alert-info">
                        <strong>নোট:</strong> Excel ফাইলে নিম্নলিখিত columns থাকতে হবে:<br>
                        • TID<br>
                        • MID<br>
                        • MERCHANT<br>
                        • ADDRESS<br>
                        • OFFICER<br>
                        • NUMBER<br>
                        • POS_SERIAL
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Import Excel</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Search Script --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const eblTable = document.getElementById('eblTable');

    if (searchInput && eblTable) {
        const rows = eblTable.querySelectorAll('tbody tr');

        searchInput.addEventListener('keyup', function() {
            const query = this.value.toLowerCase();
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(query) ? '' : 'none';
            });
        });
    }

    // Reinitialize feather icons
    if (window.feather) {
        feather.replace();
    }
});
</script>

@endsection