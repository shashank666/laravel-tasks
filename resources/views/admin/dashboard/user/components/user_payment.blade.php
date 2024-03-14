<div class="card">
        <div class="card-header">
            <h4 class="card-header-title">Payment Information</h4>
        </div>
        <div class="card-body">
        @if(!empty($data) || $data!=null)
        <table class="table table-borderless">
                <tbody>
                    <tr><th>Mobile No</th><th>:</th><td>{{ $data->mobile!=null?$data->mobile:' - '  }}</td></tr>
                    <tr><th>Account Holder Name</th><th>:</th><td>{{ $data->account_holdername!=null?$data->account_holdername:' - ' }}</td></tr>
                    <tr><th>Account Number</th><th>:</th><td>{{ $data->account_no!=null?$data->account_no:' - '  }}</td></tr>
                    <tr><th>Account Type</th><th>:</th><td>{{ $data->account_type!=null?ucfirst($data->account_type):' - '  }}</td></tr>
                    <tr><th>Bank Name</th><th>:</th><td>{{ $data->bank_name!=null?$data->bank_name:' - '  }}</td></tr>
                    <tr><th>IFSC Code</th><th>:</th><td>{{  $data->bank_ifsc_code!=null?$data->bank_ifsc_code:' - '  }}</td></tr>
                    <tr><th>Resident Address</th><th>:</th><td>{{ $data->address!=null?$data->address:' - '  }}</td></tr>
                    <tr><th>Zip Code</th><th>:</th><td>{{ $data->zip_code!=null?$data->zip_code:' - '  }}</td></tr>
                    <tr><th>City</th><th>:</th><td>{{ $data->city!=null?$data->city:' - '  }}</td></tr>
                    <tr><th>State</th><th>:</th><td>{{ $data->state!=null?$data->state:' - '  }}</td></tr>
                </tbody>
        </table>
        @else
        <div class="alert alert-warning">
             No Payment Details Found.
        </div>
        @endif
    </div>
</div>