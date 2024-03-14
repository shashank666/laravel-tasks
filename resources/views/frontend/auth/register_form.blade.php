
@extends('frontend.layouts.app')
<div class="container mt-5" id="login">
    <div style="margin-left: 15%; margin-right: 15%;">
    <h3 class="modal-title mx-auto text-center" id="registerModalLabel"><span style="vertical-align: bottom;margin-right:8px;">Create Account</span><img src="/img/logo.png" width="90" height="30" alt="Opined"/></h3>
    <form class="mx-3" method="POST" id="registerForm">
        {{ csrf_field() }}
        <div class="login_response" style=" border-radius:5px;text-align:center;visibility:hidden;margin-bottom:8px;"></div>

        <div class="input-group mb-2">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="fas fa-user"></i>
                    </div>
                </div>
            <input id="name" name="name" type="text" class="form-control" value="{{ old('name') }}" autofocus="true" placeholder="Name" required/>
        </div>


        <div class="input-group mb-2">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="fas fa-envelope"></i>
                    </div>
                </div>
            <input id="email" name="email" type="email" class="form-control" value="{{ old('email') }}"  placeholder="Email" required/>
        </div>

        @if($company_ui_settings->check_mobile_verified)
        <div class="input-group mb-2">
            <div class="input-group-prepend">
                <div class="input-group-text">
                    <i class="fas fa-mobile-alt" style="width:16px;"></i>
                </div>
                <div class="input-group-text bg-white border-right-0 phonecode_label" data-toggle="modal" data-target="#phonecodeModal" style="cursor:pointer"></div>
            </div>
            <input type="hidden" name="phone_code"  />
            <input id="mobile" name="mobile" type="number" class="border-left-0 form-control"   placeholder="Mobile Number"  min="100000" max="999999999999999" required/>
        </div>
        @endif


        <div class="input-group mb-2">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="fas fa-key"></i>
                    </div>
                </div>
            <input id="password" type="password" name="password" class="form-control" placeholder="Password" required/>
        </div>

        <div class="input-group mb-2">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="fas fa-key"></i>
                    </div>
                </div>
            <input id="password-confirm" name="password_confirmation" type="password" class="form-control" placeholder="Confirm Password" required/>
        </div>


        <div class="input-group mb-2">
            <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="agree" required>
                        <label class="form-check-label text-secondary" for="agree">
                        <small>By signing up you indicate that you have read and agree to <br/> Opined&apos;s  <a href="{{ route('terms_of_service') }}" title="Terms of Service">Terms of Service</a> and <a href="{{ route('privacy_policy') }}" title="Privacy Policy">Privacy Policy</a> .</small>
            </label>
            </div>
        </div>


        <button id="btnRegister" type="button" class="btn btn-sm btn-block btn-success" style="margin-top:10px;" onclick="submitRegisterForm();" >Create Account</button>
        <a  class="mt-2 btn btn-sm btn-light btn-block" data-dismiss="modal" data-toggle="modal" href="#loginModal">Already have an account ? Login</a>
    </form>
    </div>
</div>
