@extends('welcome')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-12">
            <h3 class="text-center mb-4" style="font-family: serif; color: #535353; text-transform: uppercase">Advances by Name</h3>
            @foreach($advances as $name => $months)
                @if(auth()->user()->isAdmin() || $name == auth()->user()->name)
                    <div class="card mb-4 shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h2 class="card-title mb-0 text-white">{{ $name }}</h2>
                        </div>
                        <div class="card-body">
                            @foreach($months as $month => $advs)
                                <h4 class="text-secondary mt-2 mb-3">{{ $month }}</h4>
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover table-bordered">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th>Date</th>
                                                <th>Purpose</th>
                                                <th>Payment Method</th>
                                                <th>Payment Number</th>
                                                <th>Amount</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($advs as $adv)
                                                <tr>
                                                    <td>{{ $adv->date }}</td>
                                                    <td>{{ $adv->purpose }}</td>
                                                    <td>{{ $adv->payment_method }}</td>
                                                    <td>{{ $adv->payment_number }}</td>
                                                    <td>{{ $adv->amount }}</td>
                                                    <td>
                                                        @if (auth()->user()->isAdmin())
                                                            <select class="form-control status-select" data-id="{{ $adv->id }}">
                                                                <option value="pending" {{ $adv->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                                                <option value="approved" {{ $adv->status == 'approved' ? 'selected' : '' }}>Approved</option>
                                                                <option value="paid" {{ $adv->status == 'paid' ? 'selected' : '' }}>Paid</option>
                                                            </select>
                                                        @else
                                                            @php
                                                                $badgeClass = match($adv->status) {
                                                                    'pending' => 'badge-warning',
                                                                    'approved' => 'badge-success',
                                                                    'paid' => 'badge-info',
                                                                    default => 'badge-secondary'
                                                                };
                                                            @endphp
                                                            <span class="badge {{ $badgeClass }}">{{ ucfirst($adv->status) }}</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.status-select').forEach(function(select) {
        select.addEventListener('change', function() {
            const id = this.dataset.id;
            const status = this.value;
            fetch('/advances/' + id + '/update-status', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    status: status
                })
            })
            .then(response => response.json())
            .then(data => {
                alert('Status updated successfully.');
            })
            .catch(error => {
                alert('Error updating status.');
            });
        });
    });
});
</script>
@endsection