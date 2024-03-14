@extends('frontend.layouts.app')
@section('title',"Payment Details - Opined")
@section('description',"Payment Details")
@section('keywords',"Payment Details")

@push('meta')
<link rel="canonical" href="http://www.weopined.com/me/payment" />
<link href="http://www.weopined.com/me/payment" rel="alternate" reflang="en" />


<!-- Twitter Card data -->
<meta name="twitter:card" content="summary">
<meta name="twitter:site" content="@weopined">
<meta name="twitter:title" content="Payment Details - Opined">
<meta name="twitter:description" content="Opined Payment Details">
<meta name="twitter:creator" content="@weopined">
<meta name="twitter:image" content="http://www.weopined.com/favicon.png">

<!-- Open Graph data -->
<meta property="og:title" content="Payment Details - Opined"/>
<meta property="og:type" content="website" />
<meta property="og:url" content="http://www.weopined.com/me/payment" />
<meta property="og:image" content="http://www.weopined.com/favicon.png" />
<meta property="og:description" content="Opined Payment Details" />
<meta property="og:site_name" content="Opined" />
<meta property="og:locale" content="en_US" />
<meta property="fb:app_id" content="1766000746745688" />
@endpush

@push('styles')
 <link href="/vendor/select2/select2.min.css" rel="stylesheet" />
 <style>
 body{
        background-image: url('/img/bg-pattern.png'),linear-gradient(to right, #051937, #532a60, #a83267, #ea534c, #ff9800);
        background-size: auto;
 }
 </style>
 @endpush

@push('scripts')
<script src='https://www.google.com/recaptcha/api.js'></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.17.0/jquery.validate.min.js"></script>
<script src="/vendor/select2/select2.min.js"></script>

<script>
        $(document).ready(function(){
                $("#state").select2();

                $("#bank_name").select2({
                        minimumInputLength: 1,
                        tags: false,
                        ajax: {
                            url: "{{ route('search_banks') }}",
                            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                            dataType: 'json',
                            type: "GET",
                            data: function (params) {
                                var query = {
                                  q: params.term,
                                 // city:$('#city').val()
                                }
                                return query;
                              },
                              processResults: function (data) {
                                return {
                                    results: $.map(data.banks, function (item) {
                                       return {
                                            text: item,
                                            id: item,
                                        }
                                    })
                                };
                            }

                        }
                });

                $('#payment_submit').attr('disabled','disabled');
                $('#payment-form').validate({
                        rules: {
                            'mobile': {
                                required: true
                            },
                            'address': {
                                required: true
                            },
                            'zip_code':{
                                required: true
                            },
                            'city':{
                                required: true
                            },
                            'state':{
                                required: true
                            },
                           'bank_name':{
                                required: true
                           },
                           'account_holdername':{
                                required: true
                           },
                           'account_no':{
                                required: true
                           },
                           'account_type':{
                                required: true
                           },
                           'bank_ifsc_code':{
                                required: true
                           }
                        },
                        messages: {
                                mobile:{
                                        required:'Mobile number is required !'
                                },
                                address: {
                                        required: 'Address is required !'
                                },
                                zip_code:{
                                        required: 'ZIP Code is required !'
                                },
                                city:{
                                        required: 'City is required !'
                                    },
                                state:{
                                        required: 'State is required !'
                                    },
                                bank_name:{
                                        required: 'Bankname is required !'
                                },
                                account_holdername:{
                                        required: 'Account Holder Name is required !'
                                   },
                                account_no:{
                                        required: 'Account Number is required !'
                                   },
                                account_type:{
                                        required: 'Account type is required !'
                                   },
                                bank_ifsc_code:{
                                        required: 'IFSC Code is required !',
                                  }
                        },
                        highlight: function (input) {
                            $(input).addClass('is-invalid');
                        },
                        unhighlight: function (input) {
                            $(input).removeClass('is-invalid');
                        },
                        errorPlacement: function (error, element) {
                            $(element).next().append(error);
                        },
                        submitHandler: function(form) {
                        $("#payment_submit").attr('disabled','disabled');
                        form.submit();
                        }
                });
        });

        function enableBtn(){
                $("#payment_submit").removeAttr('disabled');
        }
</script>
@endpush

@section('content')
      <div class="row">
        <div class="offset-xl-3 col-xl-6  offset-lg-3 col-lg-6 offset-md-3 col-md-6  col-sm-12 col-12">

            @include('frontend.partials.message')
                <form id="payment-form" method="POST" action="{{ route('payment',['action'=>'create']) }}">
                <div class="card mt-5 shadow">
                        <div class="card-header bg-white">
                                <h5 class="text-center  mb-0 font-weight-light">Writer's Details</h5>
                        </div>
                        <div class="card-body">
                                {{csrf_field()}}
                                <div class="form-row">
                                        <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                        <label for="email">Email</label>
                                                        <input type="text" class="form-control" id="email"  value="{{ Auth::user()->email }}" autocomplete="nope" readonly disabled>
                                                </div>
                                        </div>
                                        <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                        <label for="mobile">Mobile No.</label>
                                                        <div class="input-group">
                                                        <div class="input-group-prepend">
                                                                <div class="input-group-text">{{ Auth::user()->phone_code }}
                                                                </div>
                                                        </div>
                                                        <input type="text" class="form-control" id="mobile" name="mobile"  value="{{ Auth::user()->mobile }}" autocomplete="nope" readonly disabled>
                                                    </div>
                                                </div>
                                        </div>
                                </div>

                                <div class="form-group">
                                        <label for="address">Resident Address</label>
                                        <textarea rows="2" class="form-control" id="address" name="address"  autocomplete='street-address' required></textarea>
                                        <div class="invalid-feedback"></div>
                                </div>

                                <div class="form-row">
                                        <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                        <label for="zip_code">Zip/Pin Code</label>
                                                        <input class="form-control" type="text" name="zip_code" id="zip_code" pattern="[1-9][0-9]{5}" autocomplete='postal-code' required/>
                                                        <div class="invalid-feedback"></div>
                                                </div>
                                        </div>
                                        <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                        <label for="city">City</label>
                                                        <input class="form-control" type="text"  id="city" name="city" autocomplete='address-level2' required/>
                                                        {{--  <select class="form-control" id="city" name="city"  autocomplete='address-level2' required>
                                                        </select>  --}}
                                                        <div class="invalid-feedback"></div>
                                                </div>
                                        </div>
                                </div>
                                @if(Auth::user()->phone_code == "+91")
                                <div class="form-group">
                                        <label for="country">State</label>
                                        <select class="form-control" id="state" name="state"  autocomplete='address-level1' required>
                                                <option selected disabled>Select State</option>
                                                @foreach($states as $state)
                                                <option value="{{ $state }}">{{ $state }}</option>
                                                @endforeach
                                        </select>
                                        <div class="invalid-feedback"></div>
                                </div>
                                @else
                                 <div class="form-group">
                                        <label for="country">Country</label>
                                        <input class="form-control" type="text"  id="country" name="country" value="" autocomplete='address-level2' required/>
                                        <div class="invalid-feedback"></div>
                                </div>

                                
                                @endif
<!--
                                <div class="form-group">
                                        <label for="bank_name">Bank Name</label>
                                        <select class="form-control" id="bank_name"  name="bank_name" required></select>
                                        <div class="invalid-feedback"></div>
                                </div>

                                <div class="form-row">
                                        <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                        <label for="account_holdername">Account Holder Name</label>
                                                        <input type="text" class="form-control" id="account_holdername"  name="account_holdername" autocomplete="nope" required>
                                                        <div class="invalid-feedback"></div>
                                                </div>
                                        </div>
                                        <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                        <label for="account_no">Account Number</label>
                                                        <input type="text" class="form-control" id="account_no" name="account_no" autocomplete="nope" required>
                                                        <div class="invalid-feedback"></div>
                                                </div>
                                        </div>
                                </div>

                                <div class="form-row">
                                        <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                <label for="account_type">Account Type</label>
                                                <select class="form-control" id="account_type" name="account_type" required>
                                                        <option selected disabled>Select Account Type</option>
                                                        <option value="saving">Saving Account</option>
                                                        <option value="current">Current Account</option>
                                                </select>
                                                <div class="invalid-feedback"></div>
                                                </div>
                                        </div>
                                        <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                        <label for="bank_ifsc_code">IFSC Code</label>
                                                        <input type="text" class="form-control" id="bank_ifsc_code" name="bank_ifsc_code" autocomplete="nope"  required>
                                                        <div class="invalid-feedback"></div>
                                                </div>
                                        </div>
                                </div>
-->
                                <div class="form-group">
                                        <div class="g-recaptcha" data-sitekey="6Ld3WmwUAAAAAKqBCXMWe_tk4jyBDKgoTSA0mmBH" data-callback="enableBtn"></div>
                                </div>

                                <button id="payment_submit"  type="submit"  class="btn btn-success btn-block">SUBMIT DETAILS</button>
                        </div>
                </div>
                </form>
        </div>
    </div>
@endsection
