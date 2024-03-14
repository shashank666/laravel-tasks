
@extends('frontend.layouts.app')

@section('content')
<div class="container mt-5" id="login">
    <div style="margin-left: 15%; margin-right: 15%;">
        <a href="https://play.google.com/store/apps/details?id=com.app.weopined" target="_blank"><img style="width: 100%; border-radius:5px;" src="/img/app-promotion-opined.png" alt="Opined Android App"/></a>
        <form  method="POST"  action="{{ route('login') }}" class='mt-4'>
            @csrf
            <div class="login_response" style=" border-radius:5px;text-align:center;visibility:hidden;margin-bottom:8px;"></div>

            <div class="input-group mb-2 input-group-md">
                <div class="input-group-prepend">
                    <div class="input-group-text" style="background-position:center;background-color:white; border:1px solid black; background-repeat:no-repeat;background-size:cover;width: 41px">     
                    <i class="fas fa-envelope"></i>
                    </div>
                </div>
                <input id="login-email" name="email" type="text" class="form-control validate" value="{{ old('email') }}" style="border:1px solid black; outline:none;" autofocus="true" placeholder="Email / Mobile" required/>
            </div>

            <div class="input-group mb-2 input-group-md">
                <div class="input-group-prepend">
                    <div class="input-group-text" style="background-color: white;border:1px solid black;">
                    <i class="fas fa-lock"></i>
                    </div>
                </div>
                <input id="login-password" type="password" name="password" class="form-control validate" placeholder="Password" style="border:1px solid black;" required/>
            </div>


            <div class="form-group">
            <div class="form-check">
            
                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{old('remember')?'checked=true':'checked=false'}} />
                <label class="form-check-label text-secondary"  for="remember">Remember Me</label>
            </div>
            </div>

            <button type="submit" style="background:#244362;" class="btn btn-md btn-block btn-success">Login</button>
            <div class="d-flex flex-md-row flex-column justify-content-between">
            <a class="mt-2 btn btn-md btn-light" style="background:#244362; color:white;"  data-dismiss="modal" data-toggle="modal" data-target="#newLoginModal">Forgot Password ?</a>
            <a class="mt-2 btn btn-md btn-light" style="background:#244362; color:white;" href="{{ route('register_form') }}">No Account ? Register</a>
            </div>
        </form>
    </div>
</div>
@endsection