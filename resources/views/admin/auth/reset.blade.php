@extends('admin.layouts.auth')
@section('title', 'Opined Admin | Reset Password')

@section('content')
    <form id="reset_password" method="POST" action="">
        {{ csrf_field() }}
        @include('admin.partials.message')

        <input type="hidden" class="form-control" name="email" >

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

        <button class="btn btn-lg btn-block btn-primary mb-3" type="submit">Submit</button>

    </form>
@endsection
