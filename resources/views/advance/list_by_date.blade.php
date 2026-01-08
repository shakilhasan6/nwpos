@extends('welcome')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-header">
                <h3 class="page-title" style="font-family: serif; color: #535353; text-transform: uppercase">Advances by Date</h3>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('report.index') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Advances by Date</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    @foreach($advances as $month => $dates)
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">{{ $month }}</h4>
                    </div>
                    <div class="card-body">
                        @foreach($dates as $date => $advs)
                            <div class="mb-4">
                                <h5 class="text-primary">{{ $date }}</h5>
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th>Name</th>
                                                <th>Purpose</th>
                                                <th>Payment Method</th>
                                                <th>Amount</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($advs as $advance)
                                                <tr>
                                                    <td>{{ $advance->user ? $advance->user->name : 'Unknown User' }}</td>
                                                    <td>{{ $advance->purpose }}</td>
                                                    <td>{{ $advance->payment_method}}</td>
                                                    <td><strong>{{ number_format($advance->amount, 2) }} BDT</strong></td>
                                                    <td>
                                                        @php
                                                            $user = auth()->user();
                                                            $canEdit = $user->isAdmin() || $user->isChecker() || $user->isVerify();
                                                        @endphp
                                                        @if ($canEdit)
                                                            <select class="form-control form-control-sm status-select" data-id="{{ $advance->id }}">
                                                                <option value="pending" {{ $advance->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                                                <option value="approved" {{ $advance->status == 'approved' ? 'selected' : '' }}>Approved</option>
                                                                <option value="paid" {{ $advance->status == 'paid' ? 'selected' : '' }}>Paid</option>
                                                            </select>
                                                        @else
                                                            <span class="badge badge-{{ $advance->status == 'paid' ? 'success' : ($advance->status == 'approved' ? 'info' : 'warning') }}">{{ ucfirst($advance->status) }}</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<script>
$(document).ready(function() {
    $('.status-select').change(function() {
        var id = $(this).data('id');
        var status = $(this).val();
        $.ajax({
            url: '/advances/' + id + '/update-status',
            type: 'POST',
            data: {
                status: status,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                alert('Status updated successfully.');
            },
            error: function() {
                alert('Error updating status.');
            }
        });
    });
});
</script>
@endsection