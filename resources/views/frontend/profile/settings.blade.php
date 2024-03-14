@extends('frontend.layouts.app')
@section('title','Account Settings - Opined')
@section('description','Account Settings - Opined . Easily manage your account settings and preferences on Opined.')
@section('keywords','Account Settings - Opined,Settings')

@push('meta')
<link rel="canonical" href="http://www.weopined.com/me/settings" />
<link href="http://www.weopined.com/me/settings" rel="alternate" reflang="en" />

<!-- Twitter Card data -->
<meta name="twitter:card" content="summary">
<meta name="twitter:site" content="@weopined">
<meta name="twitter:title" content="Account Settings - Opined">
<meta name="twitter:description" content="Account Settings - Opined . Easily manage your account settings and preferences on Opined.">
<meta name="twitter:creator" content="@weopined">
<meta name="twitter:image" content="http://www.weopined.com/favicon.png">

<!-- Open Graph data -->
<meta property="og:title" content="Account Settings - Opined"/>
<meta property="og:type" content="website" />
<meta property="og:url" content="http://www.weopined.com/me/settings" />
<meta property="og:image" content="http://www.weopined.com/favicon.png" />
<meta property="og:description" content="Account Settings - Opined . Easily manage your account settings and preferences on Opined." />
<meta property="og:site_name" content="Opined" />
<meta property="og:locale" content="en_US" />
<meta property="fb:app_id" content="1766000746745688" />
@endpush
@push('styles')
    <!--<style type="text/css">
            .detail {display: none;}
    </style>-->
@endpush
@push('scripts')
@if($company_ui_settings->show_google_ad=='1')
    {!! $company_ui_settings->google_adcode !!}
@endif

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.17.0/jquery.validate.min.js"></script>
<script>
function deleteParmanent() {
  var ask = confirm("Are You Sure? All data will be lost!");
  if (ask == true) {
    event.preventDefault();document.getElementById('delete-account-form').submit();
  } 
}
function deactivateAccount() {
  var ask = confirm("Are You Sure?");
  if (ask == true) {
    event.preventDefault();document.getElementById('deactivate-account-form').submit();
  } 
}
</script>
<script>
    $(document).ready(function(){
    /*    $('.btntest').on('click',function(e){
                e.preventDefault();
        openAuthTestModal();
        $('#checkedpass').removeClass('detail');
        $('#checkpass').addClass('detail');
    }); */
        /*document.onkeydown = function(e) {
              if(event.keyCode == 123) {
                 return false;
              }
              if(e.ctrlKey && e.shiftKey && e.keyCode == 'I'.charCodeAt(0)) {
                 return false;
              }
              if(e.ctrlKey && e.shiftKey && e.keyCode == 'C'.charCodeAt(0)) {
                 return false;
              }
              if(e.ctrlKey && e.shiftKey && e.keyCode == 'J'.charCodeAt(0)) {
                 return false;
              }
              if(e.ctrlKey && e.keyCode == 'U'.charCodeAt(0)) {
                 return false;
              }
            } */
        $('#change-username-form').validate({
            rules: {
                'username': {
                    required: true,
                },
            },
            messages: {
                username:{
                    required:"Username is required !",
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
              if($('#username').val().match(/\s/g)){
                $('#username').val($('#username').val().replace(/\s/g,''));
              }

              var pattern= new RegExp(/([A-Za-z0-9_](?:(?:[A-Za-z0-9_]|(?:\.(?!\.))){0,28}(?:[A-Za-z0-9_]))?)/);
              if(!$('#username').val().trim().match(pattern)){
                  $(".response").css("background-color", '#f2dede');
                  $(".response").css("color", '#a94442');
                  $(".response").css("visibility", 'visible');
                  $(".response").show();
                  $(".response").html('Please enter an valid username');
                  $(".response").fadeOut(2500);
              }else{

                $.ajax({
                    url:"{{route('update_username')}}",
                    type:'POST',
                    headers:{ 'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content') },
                    data:$(form).serialize(),
                    dataType: 'json',
                    success: function(data) {
                        if (data.status == "error") {
                            errorsHtml = '';
                            $.each(data.errors, function(key, value) {
                                errorsHtml = errorsHtml + value[0] + '<br/>';
                            });
                            $(".response").css("background-color", '#f2dede');
                            $(".response").css("color", '#a94442');
                            $(".response").css("visibility", 'visible');
                            $(".response").show();
                            $(".response").html(errorsHtml);
                            $(".response").fadeOut(2500);
                        }else{
                            $(".response").css("background-color", '#dff0d8');
                            $(".response").css("color", '#3c763d');
                            $(".response").css("visibility", 'visible');
                            $(".response").show();
                            $(".response").html(data.message);
                            $(".response").fadeOut(1500, function() {
                                window.location.reload();
                            });
                        }
                    },
                    error: function(data) {
                    $(".response").css("background-color", '#d9edf7');
                    $(".response").css("color", '#31708f');
                    $(".response").css("visibility", 'visible');
                    $(".response").show();
                    $(".response").html('Oops !! Something Went Wrong , Please Try Again Later.');
                    $(".response").fadeOut(2500);
                    }
                });
              }
            }
        });

        $('#change-email-form').validate({
            rules: {
                'email': {
                    required: true
                },
            },
            messages: {
                email:{
                    required:"Email is required !",
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
                $.ajax({
                    url:"{{route('update_email')}}",
                    type:'POST',
                    headers:{ 'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content') },
                    data:$(form).serialize(),
                    dataType: 'json',
                    success: function(data) {
                        if (data.status == "error") {
                            errorsHtml = '';
                            $.each(data.errors, function(key, value) {
                                errorsHtml = errorsHtml + value[0] + '<br/>';
                            });
                            $(".response").css("background-color", '#f2dede');
                            $(".response").css("color", '#a94442');
                            $(".response").css("visibility", 'visible');
                            $(".response").show();
                            $(".response").html(errorsHtml);
                            $(".response").fadeOut(2500);
                        }else{
                            $(".response").css("background-color", '#dff0d8');
                            $(".response").css("color", '#3c763d');
                            $(".response").css("visibility", 'visible');
                            $(".response").show();
                            $(".response").html(data.message);
                            $(".response").fadeOut(1500, function() {
                                window.location.reload();
                            });
                        }
                    },
                    error: function(data) {
                    $(".response").css("background-color", '#d9edf7');
                    $(".response").css("color", '#31708f');
                    $(".response").css("visibility", 'visible');
                    $(".response").show();
                    $(".response").html('Oops !! Something Went Wrong , Please Try Again Later.');
                    $(".response").fadeOut(2500);
                    }
                });
            }
        });

        $('#change-mobile-form').validate({
            rules: {
                'mobile': {
                    required: true
                },
            },
            messages: {
                mobile:{
                    required:"Mobile is required !",
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
                $.ajax({
                    url:"{{route('add_mobile')}}",
                    type:'POST',
                    headers:{ 'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content') },
                    data:$(form).serialize(),
                    dataType: 'json',
                    success: function(data) {
                        if (data.status == "error") {
                            errorsHtml = '';
                            $.each(data.errors, function(key, value) {
                                errorsHtml = errorsHtml + value[0] + '<br/>';
                            });
                            $(".response").css("background-color", '#f2dede');
                            $(".response").css("color", '#a94442');
                            $(".response").css("visibility", 'visible');
                            $(".response").show();
                            $(".response").html(errorsHtml);
                            $(".response").fadeOut(2500);
                        }else{
                            $(".response").css("background-color", '#dff0d8');
                            $(".response").css("color", '#3c763d');
                            $(".response").css("visibility", 'visible');
                            $(".response").show();
                            $(".response").html(data.message);
                            $(".response").fadeOut(2500,function(){
                                $('#verify-otp-form').attr('action','/me/verify/mobile');
                                $('#resend-otp-form').attr('action','/me/resend/otp');
                                $('#changeMobileModal').modal('hide');
                                openVerifyOTPModal();
                            });
                        }
                    },
                    error: function(data) {
                    $(".response").css("background-color", '#d9edf7');
                    $(".response").css("color", '#31708f');
                    $(".response").css("visibility", 'visible');
                    $(".response").show();
                    $(".response").html('Oops !! Something Went Wrong , Please Try Again Later.');
                    $(".response").fadeOut(2500);
                    }
                });
            }
        });

        $('#change-password-form').validate({
            rules: {
                'current-password': {
                    required: true
                },
                'new-password': {
                    required: true
                },
                'confirm-password': {
                    required: true
                },
            },
            messages: {
                'current-password':{
                    required:"Current Password is required !",
                },
                'new-password':{
                    required:"New Password is required !",
                },
                'confirm-password':{
                    required:"Password Verification is required !",
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
                $.ajax({
                    url:"{{route('update_password')}}",
                    type:'POST',
                    headers:{ 'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content') },
                    data:$(form).serialize(),
                    dataType: 'json',
                    success: function(data) {
                        if (data.status == "error") {
                            errorsHtml = '';
                            $.each(data.errors, function(key, value) {
                                errorsHtml = errorsHtml + value[0] + '<br/>';
                            });
                            $(".response").css("background-color", '#f2dede');
                            $(".response").css("color", '#a94442');
                            $(".response").css("visibility", 'visible');
                            $(".response").show();
                            $(".response").html(errorsHtml);
                            $(".response").fadeOut(3500);
                        }else{
                            $(".response").css("background-color", '#dff0d8');
                            $(".response").css("color", '#3c763d');
                            $(".response").css("visibility", 'visible');
                            $(".response").show();
                            $(".response").html(data.message);
                            $(form).trigger('reset');
                            $(".response").fadeOut(2500,function(){
                                $('#changePasswordModal').modal('hide');
                            });
                            $.ajax({
                                    type: 'POST',
                                    url: "{{route('logout')}}",
                                    data: {
                                       _token: '{!! csrf_token() !!}',
                                     },
                                    success: function()
                                    {
                                        window.location = '/';
                                    }
                                });
                        }
                    },
                    error: function(data) {
                    $(".response").css("background-color", '#d9edf7');
                    $(".response").css("color", '#31708f');
                    $(".response").css("visibility", 'visible');
                    $(".response").show();
                    $(".response").html('Oops !! Something Went Wrong , Please Try Again Later.');
                    $(".response").fadeOut(2500);
                    }
                });
            }
        });
    });
</script>
@endpush

@section('content')
@include('frontend.profile.modals.change_username')
@include('frontend.profile.modals.change_email')
@include('frontend.profile.modals.change_mobile')
@include('frontend.profile.modals.change_password')
@include('frontend.profile.modals.auth_test')
@include('frontend.profile.modals.delete_account')
<!--<div class="row" oncontextmenu="return false;">-->
<div class="row">
    <div class="offset-xl-1 col-xl-10 offset-lg-1 col-lg-10 offset-md-1 col-md-10 col-sm-12 col-12">
        @include('frontend.partials.message')

        <h3 class="mb-5">Settings</h3>

            <div class="card mb-4 shadow-sm">
            <div class="card-header  bg-opined-blue">
                <h5 class="font-weight-normal text-white mb-0">Account</h5>
            </div>
            <div class="card-body">
<!--                  <div class="border-bottom mb-3">
                    <h6>Your username
                      <button class="btn btn-sm btn-light float-right waves-effect waves-float" type="button" data-toggle="modal" data-target="#changeUsernameModal">
                            <i class="fas fa-pencil-alt mr-2"></i>
                      Change</button></h6>
                    <p class="text-secondary">{{env('APP_URL').'/@'.Auth::user()->username}}</p>
                  </div>
-->

                  <div class="border-bottom mb-3">
                      <h6>Your Email
                        <button class="btn btn-sm btn-light float-right waves-effect waves-float" type="button" data-toggle="modal" data-target="#changeEmailModal">
                        <i class="fas fa-pencil-alt mr-2"></i>
                        Change
                      </button></h6>
                      <p class="text-secondary">
                        {{ Auth::user()->email }}
                        @if(Auth::user()->email_verified==1)
                        <span class="ml-2 badge badge-success p-1">Verified</span>
                        @else
                        <span class="ml-2 badge badge-warning p-1">Not Verified</span>
                        @endif
                      </p>
                  </div>

                  <div class="border-bottom mb-3">
                      <h6>Your Mobile
                        <button class="btn btn-sm btn-light float-right waves-effect waves-float" type="button" data-toggle="modal" data-target="#changeMobileModal">
                            <i class="fas fa-pencil-alt mr-2"></i>
                          Change</button></h6>
                      <p class="text-secondary">
                        {{ Auth::user()->mobile!=null?(Auth::user()->mobile):'You have not added your mobile number yet.' }}
                        @if(Auth::user()->mobile!=null)
                            @if(Auth::user()->mobile_verified==1)
                            <span class="ml-2 badge badge-success p-1">Verified</span>
                            @else
                            <span class="ml-2 badge badge-warning p-1">Not Verified</span>
                            @endif
                        @endif

                      </p>
                  </div>

                  <div class="mb-3">
                      <h6>Newsletter Subscription
                            @if(Auth::user()->is_subscribed==1)
                            <button class="btn btn-sm btn-warning float-right waves-effect waves-float" type="button" onclick="window.location.href='{{ route('subscription',['status'=>0]) }}'">
                            <i class="fas fa-bell mr-2"></i>
                          Unsubscribe</button>
                            @else
                            <button class="btn btn-sm btn-success float-right waves-effect waves-float" type="button" onclick="window.location.href='{{ route('subscription',['status'=>1]) }}'">
                            <i class="fas fa-bell mr-2"></i>
                          Subscribe</button>
                            @endif
                        
                        </h6>
                      <p class="text-secondary">
                        
                        
                            @if(Auth::user()->is_subscribed==1)
                            <span class="ml-2 badge badge-success p-1">Subscribed</span>
                            @else
                            <span class="ml-2 badge badge-warning p-1">Not Subscribed</span>
                            @endif
                        

                      </p>
                  </div>

            </div>
            </div>

           <!-- @if($company_ui_settings->show_google_ad=='1' && $google_ad)
            <div class="my-5">
                {!! $google_ad->ad_code !!}
            </div>
            @endif
-->
      <!--      <div class="card mb-4 shadow-sm">
                <div class="card-header bg-opined-blue">
                    <h5 class="font-weight-normal text-white mb-0">Payment</h5>
                </div>
                <div class="card-body">
                    <h6>Your Payment Details
                        @if(Auth::user() && Auth::user()->registered_as_writer==1)-->
                        <!--<button class="btn btn-sm btn-light float-right  waves-effect waves-float" onclick="window.location.href='{{ route('show_payment_page') }}'">
                          <i class="fas fa-pencil-alt mr-2"></i>
                          Change
                        </button>-->
                       <!-- <button class="btn btn-sm btn-light float-right  waves-effect waves-float btntest mr-2" data-toggle="modal" data-target="#authTestModal">
                          <i class="fas fa-pencil-alt mr-2"></i>
                          Show Details
                        </button>
                        @else
                        <button class="btn btn-link float-right" onclick="window.location.href='{{ route('writer_terms') }}'">Register as Writer</button>
                        @endif
                    </h6>
                    @if(Auth::user() && Auth::user()->registered_as_writer==1)
                        @if($user_account!==null)-->
                            <!--<div class="row bg-light mt-5">
                                <div class="col-md-6 col-12">
                                <table class="table table-borderless detail" id= checkedpass>
                                    <tbody>
                                        <tr><th>Mobile No</th><th>:</th><td>{{ $user_account->mobile!=null?$user_account->mobile:' - '  }}</td></tr>
                                        <tr><th>Account Holder Name</th><th>:</th><td>{{ $user_account->account_holdername!=null?$user_account->account_holdername:' - ' }}</td></tr>
                                        <tr><th>Account Number</th><th>:</th><td>{{ $user_account->account_no!=null?$user_account->account_no:' - '  }}</td></tr>
                                        <tr><th>Account Type</th><th>:</th><td>{{ $user_account->account_type!=null?ucfirst($user_account->account_type):' - '  }}</td></tr>
                                        <tr><th>Bank Name</th><th>:</th><td>{{ $user_account->bank_name!=null?$user_account->bank_name:' - '  }}</td></tr>
                                        <tr><th>IFSC Code</th><th>:</th><td>{{  $user_account->bank_ifsc_code!=null?$user_account->bank_ifsc_code:' - '  }}</td></tr>

                                    </tbody>
                                </table>
                                
                                </div>
                                <div class="col-md-6 col-12">
                                    <table class="table table-borderless">
                                        <tbody>
                                            <tr><th>Resident Address</th><th>:</th><td>{{ $user_account->address!=null?$user_account->address:' - '  }}</td></tr>
                                            <tr><th>Zip Code</th><th>:</th><td>{{ $user_account->zip_code!=null?$user_account->zip_code:' - '  }}</td></tr>
                                            <tr><th>City</th><th>:</th><td>{{ $user_account->city!=null?$user_account->city:' - '  }}</td></tr>
                                            <tr><th>State</th><th>:</th><td>{{ $user_account->state!=null?$user_account->state:' - '  }}</td></tr>

                                        </tbody>
                                    </table>
                                </div>
                            </div>-->
         <!--               @else
                        <p class="text-secondary">No payment details found .</p>
                        @endif
                    @else
                    <p class="text-secondary">You have not registered as writer yet.</p>
                    @endif
                </div>
            </div>
-->
<!--
            @if($company_ui_settings->show_google_ad=='1' && $google_ad)
            <div class="my-5">
                {!! $google_ad->ad_code !!}
            </div>
            @endif
-->
            <div class="card mb-4 shadow-sm">
            <div class="card-header bg-opined-blue">
                    <h5 class="font-weight-normal text-white mb-0">Security</h5>
            </div>
            <div class="card-body">

                    <div class="border-bottom mb-3">
                            <h6>Change Account Password
                              <button class="btn btn-sm btn-primary float-right waves-effect waves-float"  data-toggle="modal" data-target="#changePasswordModal">
                                    <i class="fas fa-key mr-2"></i>
                                    Change Password
                                </button></h6>
                            <p class="text-secondary">This will change your account password </p>
                    </div>

                   <div class="border-bottom mb-3">
                    <h6>Sign out of all other sessions
                      <button class="btn btn-sm btn-warning float-right  waves-effect waves-float" type="button" onclick="event.preventDefault();document.getElementById('delete-sessions-form').submit();">
                        <i class="fas fa-sign-out-alt"></i>
                        Signout Sessions
                    </button></h6>
                    <p class="text-secondary">This will sign you out of sessions in other browsers or on other computers.</p>
                    <form id="delete-sessions-form" class="d-none" method="POST" action="{{route('delete_sessions')}}">
                    {{csrf_field()}}
                    </form>
                  </div>

                  <div class="border-bottom mb-3">
                    <h6>Deactivate account
                      <button class="btn btn-sm btn-warning float-right waves-effect waves-float " type="button" onclick="deactivateAccount()">
                        <i class="fas fa-lock mr-2"></i>
                        Deactivate Account
                      </button></h6>
                    <p class="text-secondary">Deactivate your account and all of your content.</p>
                    <form id="deactivate-account-form" class="d-none" method="POST" action="{{route('deactivate_account')}}">
                      {{csrf_field()}}
                    </form>
                  </div>

                  <div>
                    <h6>Delete account
                      <button class="btn btn-sm btn-danger float-right waves-effect waves-float " type="button" data-toggle="modal" data-target="#deleteAccountModal">
                        <i class="fas fa-trash mr-2"></i>
                        Delete Account
                      </button></h6>
                    <p class="text-secondary">Permanently delete your account and all of your content.</p>
                    <!-- <form id="delete-account-form" class="d-none" method="POST" action="{{route('delete_account')}}">
                      {{csrf_field()}}
                    </form> -->
                  </div>
                  
            </div>
            </div>

            <!--@if($company_ui_settings->show_google_ad=='1' && $google_ad)
            <div class="mt-3">
                {!! $google_ad->ad_code !!}
            </div>
            @endif-->

    </div>
</div>
@endsection
