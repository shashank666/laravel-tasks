@extends('admin.layouts.auth')
@section('title', 'Opined Admin | Register')

  @section('content')
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-12 col-md-5 col-xl-4 my-5">
        
        <h1 class="display-4 text-center mb-3">Sign up</h1>
        <p class="text-muted text-center mb-5">Opined - Admin Panel</p>

        <form id="sign_up" method="POST" action="{{route('admin.register')}}">
            {{ csrf_field() }}
            @include('admin.partials.message')

            <div class="form-group">
            <label>Name</label>
            <input type="text" class="form-control" name="name" placeholder="Name" required autofocus>
            </div>

            <div class="form-group">
            <label> Email Address</label>
            <input type="email" class="form-control" name="email" placeholder="name@address.com" required>
            </div>

            <div class="form-group">
              <label>Password</label>
              <div class="input-group input-group-merge">
                <input type="password" class="form-control form-control-appended" name="password" placeholder="Enter your password"  minlength="6"  required>
                <div class="input-group-append">
                  <span class="input-group-text password_toggle" data-toggle="password">
                    <i class="fe fe-eye"></i>
                  </span>
                </div>
              </div>
            </div>

        
            <div class="form-group">
              <label>Confirm Password</label>
              <div class="input-group input-group-merge">
                  <input type="password" class="form-control  form-control-appended" name="password_confirmation" minlength="6" placeholder="Confirm Password" required>
                  <div class="input-group-append">
                  <span class="input-group-text password_toggle" data-toggle="password_confirmation">
                    <i class="fe fe-eye"></i>
                  </span>
                </div>
              </div>
            </div>

            <div class="form-group">
            <label> Secure Key</label>
            <input type="text" class="form-control" name="securekey" placeholder="XXXXXXXX" required>
            </div>
            
            <button class="btn btn-lg btn-block btn-primary mb-3" type="submit">Sign up</button>

            <div class="text-center">
              <small class="text-muted text-center">
                Already have an account? <a href="{{ route('admin.login') }}">Log in</a>.
              </small>
            </div>

        </form>

      </div>
    </div> 
  </div>
@endsection

