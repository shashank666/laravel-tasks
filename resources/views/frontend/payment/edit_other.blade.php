@extends('frontend.layouts.app')
@section('title',"Update Payment Details - Opined")
@section('description',"Update Payment Details")
@section('keywords',"Update Payment Details")

@push('meta')
<link rel="canonical" href="http://www.weopined.com/me/payment" />
<link href="http://www.weopined.com/me/payment" rel="alternate" reflang="en" />


<!-- Twitter Card data -->
<meta name="twitter:card" content="summary">
<meta name="twitter:site" content="@weopined">
<meta name="twitter:title" content="Update Payment Details - Opined">
<meta name="twitter:description" content="Opined Payment Details">
<meta name="twitter:creator" content="@weopined">
<meta name="twitter:image" content="http://www.weopined.com/favicon.png">

<!-- Open Graph data -->
<meta property="og:title" content="Update Payment Details - Opined"/>
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
 @endpush

@push('scripts')
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
                        }
                });
        });
</script>
@endpush

@section('content')
      <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="card">
                
                <div class="card-header">
                    <div class="row mt-3" style="text-align: center;">
                            <div class="col-lg-3">
                                    <div class="form-group">
                                            <a href="article_performance" class="btn btn-primary">Article Performance</a>
                                    </div>
                            </div>
                            <div class="col-lg-3">
                                    <div class="form-group">
                                            
                                            <a href="invoices" class="btn btn-success">View Transaction</a>
                                            
                                    </div>
                            </div>
                            @if($user_earning)
                            <div class="col-lg-3">
                                    <div class="form-group">
                                       <span  class=" badge badge-primary" style="padding: 10px; font-size: 1.0rem;font-weight: 400;">Total Earning: $ {{number_format($user_earning->total_earning, 2)}}</span>
                    
                                    </div>
                            </div>
                            <div class="col-lg-3">
                                    <div class="form-group">
                                      <span  class="badge badge-success" style="padding: 10px; font-size: 1.0rem;font-weight: 400;">Total Dues: $ {{number_format($user_earning->total_earning - $user_earning->total_paid, 2)}}</span>
                    
                                    </div>
                            </div>
                            @else
                            <div class="col-lg-3">
                                    <div class="form-group">
                                       <span  class=" badge badge-primary" style="padding: 10px; font-size: 1.0rem;font-weight: 400;">Total Earning: $ {{number_format(0,2)}}</span>
                    
                                    </div>
                            </div>
                            <div class="col-lg-3">
                                    <div class="form-group">
                                      <span  class="badge badge-success" style="padding: 10px; font-size: 1.0rem;font-weight: 400;">Total Dues: $ {{number_format(0,2)}}</span>
                    
                                    </div>
                            </div>
                            @endif
                        </div>
                    </div>
        </div>
    </div>
        <div class="offset-xl-3 col-xl-6  offset-lg-3 col-lg-6 offset-md-3 col-md-6  col-sm-12 col-12">

            @include('frontend.partials.message')
                <form id="payment-form" method="POST" action="{{ route('payment',['action'=>'edit']) }}">
                <div class="card mt-5 shadow">
                        <div class="card-header bg-light">
                                <h5 class="text-center  mb-0 font-weight-normal">Edit Writer's Details</h5>
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
                                                        <input type="text" class="form-control" id="mobile"  value="{{ Auth::user()->mobile }}" autocomplete="nope" readonly disabled>
                                                    </div>
                                                </div>
                                        </div>
                                </div>

                                <div class="form-group">
                                        <label for="address">Resident Address</label>
                                        <textarea rows="2" class="form-control" id="address" name="address"   autocomplete='street-address' required>{{ $account->address }}</textarea>
                                        <div class="invalid-feedback"></div>
                                </div>

                                <div class="form-row">
                                        <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                        <label for="zip_code">Zip/Pin Code</label>
                                                        <input class="form-control" type="text" name="zip_code" id="zip_code"  value="{{ $account->zip_code }}" pattern="[1-9][0-9]{5}" autocomplete='postal-code' required/>
                                                        <div class="invalid-feedback"></div>
                                                </div>
                                        </div>
                                        <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                        <label for="city">City</label>
                                                        <input class="form-control" type="text"  id="city" name="city" value="{{ $account->city }}" autocomplete='address-level2' required/>
                                                        <div class="invalid-feedback"></div>
                                                </div>
                                        </div>
                                </div>

                                <div class="form-group">
                                        <label for="country">Country</label>
                                        <input class="form-control" type="text"  id="country" name="country" value="{{ $account->country }}" autocomplete='address-level2' required/>
                                        <div class="invalid-feedback"></div>
                                </div>

                                

                                <button type="submit"  class="btn btn-success btn-block">UPDATE DETAILS</button>
                        </div>
                </div>
                </form>
        </div>
    </div>
@endsection
