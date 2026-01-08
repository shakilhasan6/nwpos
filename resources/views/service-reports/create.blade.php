@extends('welcome')

@section('content')
    <div class="container">
        <h3 style="font-family: serif; color: #535353; text-transform: uppercase">Service Report Form</h3>
        <form action="{{ route('service-reports.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label for="zone_name">Zone Name</label>
                <select name="zone_name" id="zone_name" class="form-control" required>
                    <option value="">Select Zone</option>
                    <option value="Barisal">Barisal</option>
                    <option value="Bogor">Bogora</option>
                    <option value="Chattogram Central">Chattogram Central</option>
                    <option value="Chattogram North">Chattogram North</option>
                    <option value="Chattogram South">Chattogram South</option>
                    <option value="Cumilla">Cumilla</option>
                    <option value="Dhaka Central">Dhaka Central</option>
                    <option value="Dhaka North">Dhaka North</option>
                    <option value="Dhaka South">Dhaka South</option>
                    <option value="Faridpur">Faridpur</option>
                    <option value="Gazipur">Gazipur</option>
                    <option value="Khulna">Khulna</option>
                    <option value="Mymensingh">Mymensingh</option>
                    <option value="Narayanganj">Narayanganj</option>
                    <option value="Noakhali">Noakhali</option>
                    <option value="Rajshahi">Rajshahi</option>
                    <option value="Rangpur">Rangpur</option>
                    <option value="Sylhet">Sylhet</option>
                    <option value="Tangail">Tangail</option>
                </select>
            </div>
            <div class="form-group">
                <label for="Bank_name">Bank Name</label>
                <select name="bank_name" id="bank_name" class="form-control" required>
                    <option value="">Select Bank</option>
                    <option value="PBL">PBL</option>
                    <option value="MTB">MTB</option>
                    <option value="IBBL">IBBL</option>
                    <option value="EBL">EBL</option>
                    <option value="CITY">CITY</option>
                </select>
            </div>

            <div class="form-group">
                <label for="engineer_name">Engineer Name</label>
                <input type="text" name="engineer_name" id="engineer_name" class="form-control"
                    value="{{ Auth::user()->name }}" readonly required>
            </div>

            <div class="form-group">
                <label for="tid">TID</label>
                <input type="text" name="tid" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="pos_serial">POS Serial</label>
                <input type="text" name="pos_serial" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="merchant_address">Merchant Address</label>
                <textarea name="merchant_address" class="form-control" required></textarea>
            </div>

            <div class="form-group">
                <label>Service Type</label>
                <div>
                    <input type="radio" name="service_type" value="Merchant Deploy" required> Merchant Deploy<br>
                    <input type="radio" name="service_type" value="Branch Deploy"> Branch Deploy<br>
                    <input type="radio" name="service_type" value="Support"> Support<br>
                    <input type="radio" name="service_type" value="Replace"> Replace<br>
                    <input type="radio" name="service_type" value="Roll Out"> Roll Out<br>
                    <input type="radio" name="service_type" value="Roll Out Not Done"> Roll Out Not Done<br>
                </div>
            </div>

            <div class="form-group">
                <label for="remarks">Remarks</label>
                <textarea name="remarks" id="remarks" class="form-control"></textarea>
            </div>

            <div class="form-group">
                <label for="service_report_image">Service Report Image</label>
                <input type="file" name="service_report_image" accept="image/*" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary">Save</button>
        </form>
    </div>
@endsection
