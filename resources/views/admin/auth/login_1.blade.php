@extends('admin.layouts.auth')
@section('title', 'Opined Admin | Login')

@section('content')
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-12 col-md-5 col-xl-4 my-5">
        
     
        <h1 class="display-4 text-center mb-3">Sign in</h1>
        
      
        <p class="text-muted text-center mb-5">Opined - Admin Panel</p>
        
        <form id="sign_in" method="POST" action="{{ route('admin.login_1') }}">

        {{ csrf_field() }}
        @include('admin.partials.message')

          <div class="form-group">
            <label>Email Address</label>
            <input type="email" name="email" class="form-control" placeholder="name@address.com" required>
          </div>

        
          <div class="form-group">
            <div class="row">
              <div class="col">
                <label>Password</label>
              </div>
              <div class="col-auto">
                <a  href="{{route('admin.forgot-password') }}" class="form-text small text-muted">
                  Forgot password?
                </a>
              </div>
            </div>

            <div class="input-group input-group-merge">
              <input type="password" name="password" class="form-control form-control-appended" placeholder="Enter your password" required>
              <div class="input-group-append">
                <span class="input-group-text password_toggle" data-toggle="password">
                  <i class="fe fe-eye"></i>
                </span>
              </div>
            </div>
          </div>

         
          <button class="btn btn-lg btn-block btn-primary mb-3" type="submit">Sign in</button>
          <!--<div class="text-center">
            <small class="text-muted text-center">
              Dont have an account yet? <a href="{{route('admin.register')}}">Sign up</a>.
            </small>
          </div>-->
          
        </form>

      </div>
    </div> 
  </div> 
@endsection
