@extends('frontend.layouts.error')
@section('title','Verify Your email and phone number  - Opined')

@section('content')
<div class="row mt-20 mb-20" style="margin: auto;width: 37rem;padding: 10px;">
    <div class="col-12">
    <form id="verify-otp-form-activate"  method="POST"  action="{{ route('activate_account') }}">
                      {{ csrf_field() }}
                      <div class="form-group">
                      	<input id="email" name="email" type="hidden" value="{{$email}}" />
                          <input id="otp_mobile" name="otp_mobile" type="number" class="form-control validate"  autofocus="true" placeholder="Enter OTP, Sent to your number" required/>
                      </div>
                      <div class="form-group">
                          <input id="otp_email" name="otp_email" type="otp_email" class="form-control validate"  autofocus="true" placeholder="Enter OTP, Sent to your email" required/>
                      </div>
                      <div class="form-group">
				         <input id="password" min="6" name="password" placeholder="Enter New Password" type="password" class="form-control validate" required>
				      </div>
                      <button id="btnVerifyOTP" type="submit" class="btn btn-sm btn-block btn-success">Verify</button>
                    </div>
    </form>
    </div>
</div>
@endsection