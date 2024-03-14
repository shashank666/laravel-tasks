@extends('frontend.layouts.app')

@section('title','Invitation To Opined')
@section('description','Welcome to Opined, a platform to enable every person to read, write and discuss individual opinions
on top trending topics and stories. Every day, thousands of people share, read, discuss and write
their opinion on Opined.')
@section('keywords','Opined')

@push('meta')
<link rel="canonical" href="https://www.weopined.com/invitation" />
<link href="https://www.weopined.com/invitation" rel="alternate" reflang="en" />

<!-- Twitter Card data -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:site" content="@weopined">
<meta name="twitter:title" content="Invitation To Opined">
<meta name="twitter:description" content="Welcome to Opined, a platform to enable every person to read, write and discuss individual opinions
on top trending topics and stories. Every day, thousands of people share, read, discuss and write
their opinion on Opined.">
<meta name="twitter:creator" content="@weopined">
<meta name="twitter:image:src" content="https://www.weopined.com/img/opined-cover.jpg">


<!-- Open Graph data -->
<meta property="og:title" content="Invitation To Opined" />
<meta property="og:type" content="article" />
<meta property="og:url" content="https://www.weopined.com/invitation" />
<meta property="og:image" content="https://www.weopined.com/img/opined-cover.jpg" />
<meta property="og:description" content="Welcome to Opined, a platform to enable every person to read, write and discuss individual opinions
on top trending topics and stories. Every day, thousands of people share, read, discuss and write
their opinion on Opined."/>
<meta property="og:site_name" content="Opined" />
<meta property="og:locale" content="en_US" />
<meta property="fb:app_id" content="1766000746745688" />
@endpush
@push('styles')
<style>
    body{
        background-image: url('/img/bg-pattern.png'),linear-gradient(to right, #051937, #532a60, #a83267, #ea534c, #ff9800);
        background-size: auto;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.17.0/jquery.validate.min.js"></script>
<script>
        $(document).ready(function(){
                $('#invitation-form').validate({
                        rules: {
                            'name': {
                                required: true
                            },
                            'email': {
                                required: true,
                                email:true
                            },
                            'password':{
                                required: true,
                                minlength:6
                            },
                            'password_confirmation':{
                                required: true,
                                equalTo: '[name="password"]'
                            },
                            'terms': {
                                required: true
                            },
                        },
                        messages: {
                            name:{
                                required:"Name is required !"
                            },
                            email:{
                                required:"Email is required !",
                            },
                            password: {
                              required:"Password is required !",
                              minlength: "Password must be 6 characters long !"
                            },
                            password_confirmation:{
                                required:"Confirm Password is required !",
                                equalTo:"Confirm password should be same as password !"
                            },
                            terms:{
                                required:"You must be agree with Opined's Terms of Service and Privacy Policy !"
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
                                url:form.action,
                                type:form.method,
                                headers:{ 'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content') },
                                data:$(form).serialize(),
                                dataType: 'json',
                                success: function(data) {
                                        if (data.status == "error") {
                                            errorsHtml = '';
                                            $.each(data.errors, function(key, value) {
                                                errorsHtml = errorsHtml + value[0] + '<br/>';
                                            });
                                            $(".login_response").css("background-color", '#f2dede');
                                            $(".login_response").css("color", '#a94442');
                                            $(".login_response").css("visibility", 'visible');
                                            $(".login_response").show();
                                            $(".login_response").html(errorsHtml);
                                            $(".login_response").fadeOut(2500);
                                        } else {
                                            $(".login_response").css("background-color", '#dff0d8');
                                            $(".login_response").css("color", '#3c763d');
                                            $(".login_response").css("visibility", 'visible');
                                            $(".login_response").show();
                                            $(".login_response").html(data.message);
                                            if(cmv==1){
                                                $('#userid').val(data.user_id);
                                                $(".login_response").fadeOut(1500, function() {
                                                    $('#verify-otp-form').attr('action','/register');
                                                    $('#resend-otp-form').attr('action','/resendOTP');
                                                    closeRegisterModal();
                                                    openVerifyOTPModal();
                                                });
                                            }else{
                                                $(".login_response").fadeOut(1500, function() {
                                                    window.location.href = "/";
                                                });
                                            }
                                        }
                                },error: function(data) {
                                    $(".login_response").css("background-color", '#d9edf7');
                                    $(".login_response").css("color", '#31708f');
                                    $(".login_response").css("visibility", 'visible');
                                    $(".login_response").show();
                                    $(".login_response").html('Oops !! Something Went Wrong , Please Try Again Later.');
                                    $(".login_response").fadeOut(2500);
                                }
                            });
                        }
                });
        });
</script>
@endpush

@section('content')
<div class="row my-3">
    <div class="offset-md-3 col-md-6 col-12">
            <div class="card shadow">
                <div class="card-header bg-white">
                        <h2 class="text-center font-weight-light">Welcome To Opined</h2>
                        <p class="px-5 text-secondary text-justify font-weight-light">Opined is a platform that enables you to discuss and express your opinions on the topic of your choice.
                          Every day, thousands of people share, read, discuss and write their opinion on Opined.
                        </p>
                </div>
                <div class="card-body">
                    @php($action=$company_ui_settings->check_mobile_verified==1?'/create_account':'/register')
                    <form  method="POST" id="invitation-form" action="{{$action}}">
                                {{ csrf_field() }}
                                <div class="login_response" style=" border-radius:5px;text-align:center;visibility:hidden;margin-bottom:8px;"></div>


                                <div class="input-group mb-2">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <i class="fas fa-user"></i>
                                            </div>
                                        </div>
                                    <input id="register-name" name="name" type="text" class="form-control" value="{{ old('name') }}" autofocus="true" placeholder="Name"  autocomplete='name' required/>
                                    <div class="invalid-feedback"></div>
                                </div>


                                <div class="input-group mb-2">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <i class="fas fa-envelope"></i>
                                            </div>
                                        </div>
                                    <input id="register-email" name="email" type="email" class="form-control" value="{{ old('email') }}"  placeholder="Email" autocomplete='email' required/>
                                    <div class="invalid-feedback"></div>
                                </div>

                                @if($company_ui_settings->check_mobile_verified==1)
                                <div class="input-group mb-2">
                                        <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    <i class="fas fa-mobile-alt" style="width:16px;"></i>
                                                </div>
                                                <div class="input-group-text bg-white border-right-0 phonecode_label" data-toggle="modal" data-target="#phonecodeModal" style="cursor:pointer"></div>
                                        </div>
                                        <input type="hidden" name="phone_code"  />
                                        <input id="register-mobile" name="mobile" type="number" class="border-left-0 form-control"   placeholder="Mobile Number" min="100000" max="999999999999999"  required/>
                                        <div class="invalid-feedback"></div>
                                </div>
                                @endif

                                <div class="input-group mb-2">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <i class="fas fa-key"></i>
                                            </div>
                                        </div>
                                    <input id="register-password" type="password" name="password" class="form-control" placeholder="Password" required/>
                                    <div class="invalid-feedback"></div>
                                </div>

                                <div class="input-group mb-2">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <i class="fas fa-key"></i>
                                            </div>
                                        </div>
                                    <input id="register-password-confirm" name="password_confirmation" type="password" class="form-control" placeholder="Confirm Password" required/>
                                    <div class="invalid-feedback"></div>
                                </div>

                                <div class="form-group">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="terms" name="terms" required/>
                                            <div class="invalid-feedback"></div>
                                            <label class="form-check-label text-secondary text-justify font-weight-light" for="terms">
                                                By signing up you indicate that you have read and agree to Opined&apos;s  <a href="{{ route('terms_of_service') }}" title="Terms of Service">Terms of Service</a> and <a href="{{ route('privacy_policy') }}" title="Privacy Policy">Privacy Policy</a> .
                                            </label>
                                        </div>
                                </div>

                                <button id="btnRegisterUser" type="submit" class="btn btn-block btn-success" style="margin-top:10px;">Create Account</button>

                                <div class="text-center mt-2">
                                        <a  class="btn btn-light btn-block" data-dismiss="modal" data-toggle="modal" href="#loginModal">Already have an account ? Login</a>
                                </div>

                    </form>
                </div>

                <div class="card-footer bg-white">
                            @include('frontend.auth.social_login')
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
