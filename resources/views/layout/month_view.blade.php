@extends('welcome')

@section('content')
<style>
.table-excel { width:100%; border-collapse: collapse; font-size:14px; }
.table-excel th, .table-excel td { border:1px solid #ddd; padding:6px 10px; text-align:left; }
.table-excel thead th { position: sticky; top:0; background:#007bff; color:#fff; z-index:5; }
.table-excel tbody tr:hover { background:#f1f9ff; }
.table-excel th.sticky, .table-excel td.sticky { position: sticky; left:0; background:#f8f9fb; z-index:6; }

.controls { margin-bottom:15px; display:flex; gap:8px; align-items:center; flex-wrap:wrap; }
.searchbox { width:250px; }
#grandTotal { font-weight:600; color:#007bff; margin-left:auto; }

.number { text-align:right; }
</style>

<div class="container-fluid">
    <h2>Engineer Logs for {{ $engineer }} - {{ $month }}</h2>
    <div class="controls">
        <input type="text" id="searchBox" class="form-control searchbox" placeholder="Search...">
        <button class="btn btn-primary btn-sm" id="searchBtn">Search</button>
        <div id="grandTotal">Grand Total: {{ number_format($grandTotal, 2) }}</div>
    </div>

    <div style="overflow:auto; max-height:70vh; border:1px solid #ddd; border-radius:6px;">
        <table class="table-excel" id="excelTable">
            <thead>
                <tr>
                    <th class="sticky">#</th>
                    <th>Date</th>
                    <th>From</th>
                    <th>To</th>
                    <th>Transport</th>
                    <th>Purpose</th>
                    <th>Amount</th>
                    <th>Food</th>
                    <th>Hotel</th>
                    <th>Total</th>
                    <th>Remarks</th>
                    <th>Img</th>
                </tr>
            </thead>
            <tbody id="tableBody">
                @foreach($mergedEntries as $index => $entry)
                <tr data-log-id="{{ $entry['log_id'] }}" data-entry-index="{{ $entry['entry_index'] }}" data-row-index="{{ $entry['row_index'] }}">
                    <td class="sticky">{{ $index + 1 }}</td>
                    <td>{{ $entry['date'] ? \Carbon\Carbon::parse($entry['date'])->format('d M Y') : '' }}</td>
                    <td @if(auth()->user()->isAdmin() || auth()->user()->isChecker() || auth()->user()->isVerify()) contenteditable="true" data-field="from" @endif>{{ $entry['from'] }}</td>
                    <td @if(auth()->user()->isAdmin() || auth()->user()->isChecker() || auth()->user()->isVerify()) contenteditable="true" data-field="to" @endif>{{ $entry['to'] }}</td>
                    <td @if(auth()->user()->isAdmin() || auth()->user()->isChecker() || auth()->user()->isVerify()) contenteditable="true" data-field="transport" @endif>{{ $entry['transport'] }}</td>
                    <td @if(auth()->user()->isAdmin() || auth()->user()->isChecker() || auth()->user()->isVerify()) contenteditable="true" data-field="purpose" @endif>{{ $entry['purpose'] }}</td>
                    <td class="number" @if(auth()->user()->isAdmin() || auth()->user()->isChecker() || auth()->user()->isVerify()) contenteditable="true" data-field="amount" @endif>{{ number_format($entry['amount'], 2) }}</td>
                    <td class="number" @if(auth()->user()->isAdmin() || auth()->user()->isChecker() || auth()->user()->isVerify()) contenteditable="true" data-field="food" @endif>{{ number_format($entry['food'], 2) }}</td>
                    <td class="number" @if(auth()->user()->isAdmin() || auth()->user()->isChecker() || auth()->user()->isVerify()) contenteditable="true" data-field="hotel" @endif>{{ number_format($entry['hotel'], 2) }}</td>
                    <td class="number">{{ number_format($entry['total'], 2) }}</td>
                    <td @if(auth()->user()->isAdmin() || auth()->user()->isChecker() || auth()->user()->isVerify()) contenteditable="true" data-field="remarks" @endif>{{ $entry['remarks'] }}</td>
                @if($entry['hotel_image'])
                    <td><button class="btn btn-sm btn-primary" onclick="showImage('/storage/{{ $entry['hotel_image'] }}')">View Image</button></td>
                @else
                    <td></td>
                @endif
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Image Modal -->
<div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalLabel">Hotel Image</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="" alt="Hotel Image" class="img-fluid">
            </div>
        </div>
    </div>
</div>

<script>
// Function to show image in modal
function showImage(src) {
    document.getElementById('modalImage').src = src;
    $('#imageModal').modal('show');
}

document.addEventListener('DOMContentLoaded', function() {
    // Search functionality
    document.getElementById('searchBtn').addEventListener('click', function() {
        const q = document.getElementById('searchBox').value.toLowerCase();
        const rows = document.querySelectorAll('#tableBody tr');
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(q) ? '' : 'none';
        });
    });

    // Inline editing
    document.querySelectorAll('[contenteditable="true"]').forEach(td => {
        td.addEventListener('blur', function() {
            const tr = this.closest('tr');
            const logId = tr.dataset.logId;
            const entryIndex = tr.dataset.entryIndex;
            const rowIndex = tr.dataset.rowIndex;
            const field = this.dataset.field;
            const value = this.textContent.trim();

            // Send AJAX request
            fetch('{{ route("engineer_logs.update_entry") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    log_id: logId,
                    entry_index: entryIndex,
                    row_index: rowIndex,
                    field: field,
                    value: value
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Optionally update the total column if needed
                    if (['amount', 'food', 'hotel'].includes(field)) {
                        // Recalculate total for the row
                        const amount = parseFloat(tr.querySelector('[data-field="amount"]').textContent) || 0;
                        const food = parseFloat(tr.querySelector('[data-field="food"]').textContent) || 0;
                        const hotel = parseFloat(tr.querySelector('[data-field="hotel"]').textContent) || 0;
                        const total = amount + food + hotel;
                        tr.querySelector('td:nth-child(10)').textContent = total.toFixed(2);

                        // Update grand total
                        updateGrandTotal();
                    }
                } else {
                    alert('Update failed');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Update failed');
            });
        });
    });

    function updateGrandTotal() {
        let grandTotal = 0;
        document.querySelectorAll('#tableBody tr').forEach(tr => {
            const total = parseFloat(tr.querySelector('td:nth-child(10)').textContent) || 0;
            grandTotal += total;
        });
        document.getElementById('grandTotal').textContent = 'Grand Total: ' + grandTotal.toFixed(2);
    }
});
</script>
@endsection