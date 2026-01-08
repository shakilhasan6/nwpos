@extends('welcome')

@section('content')
    <style>
        .conv-row {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            margin-bottom: 10px;
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            .conv-row {
                grid-template-columns: repeat(2, 1fr);
            }

            label {
                display: block !important;
                margin-bottom: 3px;
                font-weight: 600;
            }
        }

        input,
        select {
            width: 100%;
            border: 1px solid #cccccc;
            padding: 8px;
            border-radius: 6px;
        }
    </style>

    <div class="container mt-3">

        <h3 class="mb-3" style="font-family: serif; color: #535353; text-transform: uppercase">Engineer Convince</h3>


        <form action="{{ route('engineer_logs.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">

                <label>Engineer Name</label>
                <input type="text" value="{{ Auth::User()->name }}" readonly name="engineer_name"
                    placeholder="Enter your Real Name..Ex-> Shakil Hasan(11553).." required>
            </div>

            <div id="date-blocks">

                <!-- First Date Block -->
                <div class="date-block border p-3 mb-3">
                    <div class="d-flex justify-content-between">
                        <h5>Date Log</h5>

                    </div>

                    <input type="date" name="date[]" class="form-control mb-2" required>

                    <div class="rows-container border-bottom  ">

                        <div class="conv-row border-bottom border-info pt-4 pb-4">

                            <div>
                                <label>From</label>
                                <input type="text" name="from[0][]" required>
                            </div>

                            <div>
                                <label>To</label>
                                <input type="text" name="to[0][]" required>
                            </div>

                            <div>
                                <label>Transport</label>
                                <input type="text" name="transport[0][]" required>
                            </div>

                            <div>
                                <label>Amount</label>
                                <input type="number" step="0.01" class="amount" name="amount[0][]"
                                    onkeyup="calculateTotals()" required>
                            </div>

                            <div>
                                <label>Purpose</label>
                                <input type="text" name="purpose[0][]" required>
                            </div>

                            <div>
                                <label>Food</label>
                                <input type="number" step="0.01" class="food" name="food[0][]"
                                    onkeyup="calculateTotals()">
                            </div>

                            <div>
                                <label>Hotel</label>
                                <input type="number" step="0.01" class="hotel" name="hotel[0][]"
                                    onkeyup="toggleHotelImage(this); calculateTotals()">
                            </div>
                            <div>
                                <label>Remarck</label>
                                <input type="text" name="remarks[0][]" required>
                            </div>

                            <div class="hotel-image-box" style="">
                                <label>Document</label>
                                <input type="file" name="hotel_image[0][]">
                            </div>
                            <div class="mt-3">
                                <label>&nbsp;</label>
                                <button type="button" class="btn btn-danger btn-sm remove-row mt-4"
                                    onclick="removeRow(this)">Remove</button>

                            </div>

                        </div>

                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <h6 class="mt-2 text-end">Total: <span class="date-total">0</span> BDT</h6>
                        <button type="button" class="btn btn-sm btn-primary" onclick="addRow(this)">+ Add Row</button>

                    </div>

                </div>

            </div>

            <button type="button" onclick="addDateBlock()" class="btn btn-success mb-3">+ Add New Date</button>

            <h4 class="text-end">Grand Total: <span id="grand-total">0</span> BDT</h4>

            <button class="btn btn-primary mt-3">Save</button>
        </form>
    </div>


    <script>
        let dateIndex = 1;

        function addRow(btn) {
            let block = btn.closest('.date-block');
            let container = block.querySelector('.rows-container');

            let firstRow = container.querySelector('.conv-row').cloneNode(true);

            firstRow.querySelectorAll("input").forEach(i => {
                i.value = "";
            });

            container.appendChild(firstRow);
        }
        //removeRow
        function removeRow(btn) {
            let block = btn.closest('.date-block');
            let rows = block.querySelectorAll('.conv-row');

            if (rows.length > 1) {
                btn.closest('.conv-row').remove();
                calculateTotals(); // total re-calc
            } else {
                alert("Last row cannot be removed! Add more rows first.");
            }
        }


        function addDateBlock() {

            let block = document.querySelector(".date-block").cloneNode(true);

            block.querySelectorAll("input").forEach(i => {
                i.value = "";
            });

            block.querySelector(".rows-container").innerHTML =
                block.querySelector(".conv-row").outerHTML;

            // name index update
            block.querySelectorAll("input").forEach(i => {
                i.name = i.name.replace(/\[\d+\]/, "[" + dateIndex + "]");
            });

            // Add remove button to the header
            let header = block.querySelector('.d-flex.justify-content-between');
            header.innerHTML += '<button type="button" class="btn btn-danger btn-sm ms-2 mb-2" onclick="removeDateBlock(this)"><i class="fas fa-trash"></i></button>';

            document.getElementById("date-blocks").appendChild(block);
            dateIndex++;

            calculateTotals();
        }

        function removeDateBlock(btn) {
            let blocks = document.querySelectorAll('.date-block');
            if (blocks.length > 1) {
                btn.closest('.date-block').remove();
                calculateTotals();
            } else {
                alert("Last date block cannot be removed! Add more dates first.");
            }
        }

       

        function calculateTotals() {
            let dateBlocks = document.querySelectorAll(".date-block");
            let grandTotal = 0;

            dateBlocks.forEach(block => {
                let total = 0;

                block.querySelectorAll(".amount").forEach(i => total += Number(i.value));
                block.querySelectorAll(".food").forEach(i => total += Number(i.value));
                block.querySelectorAll(".hotel").forEach(i => total += Number(i.value));

                block.querySelector('.date-total').innerText = total.toFixed(2);
                grandTotal += total;
            });

            document.getElementById("grand-total").innerText = grandTotal.toFixed(2);
        }
    </script>
@endsection
