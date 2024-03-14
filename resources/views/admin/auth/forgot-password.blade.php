@extends('admin.layouts.auth')
@section('title', 'Opined Admin | Forgot Password')
@push('scripts')
<script>
$(document).ready(function(){
    
    $(".msg").fadeIn(2000);
    $(".msg").fadeOut(5000);
  
});
    
</script>
@endpush
@section('content')
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-12 col-md-5 col-xl-4 my-5">
            
            <h1 class="display-4 text-center mb-3">
              Forgot Password ?
            </h1>
            
            <p class="text-muted text-center mb-5">
              Enter your email to get a password reset link.
            </p>
            
            <form id="forgot_password" method="POST" action="{{route('admin.send-reset-email')}}" enctype="multipart/form-data">
                {{ csrf_field()}}
                <div class= "msg">@include('admin.partials.message')</div>
               <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" class="form-control" placeholder="name@address.com" id="email" name="email" required autofocus>
              </div>
            
              <button class="btn btn-lg btn-block btn-primary mb-3" type="submit">Reset Password</button>
  
              <div class="text-center">
                <small class="text-muted text-center">
                  Remember your password? <a href="{{route('admin.login')}}">Log in</a>.
                </small>
              </div>
            </form>
  
          </div>
        </div> 
      </div>
@endsection
