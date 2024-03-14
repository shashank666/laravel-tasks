<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta http-equiv = "Content-Type" content = "text/html;charset=UTF-8" />
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <title>@yield('title')</title>
    <meta name="description" content="@yield('description')"/>
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <meta name="keywords" content="@yield('keywords')"/>
    <meta name="copyright" content="Copyright &copy; {{ Carbon\Carbon::now()->format('Y') }} www.weopined.com , All Rights Reserved"/>
    <meta name = "revised" content = "Opined, {{ Carbon\Carbon::now('Asia/Kolkata') }}" />
    <meta name = "author" content = "Opined" />
    <meta name="robots" content="index,follow"/>
    <link href="/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <script type="text/javascript" src="/vendor/jquery/jquery.min.js"></script>
    <script type="text/javascript" src="/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript" src="/vendor/fontawesome/fontawesome.js"></script>
    <style>
    :root {
    --input-padding-x: .75rem;
    --input-padding-y: .75rem;
   }

html,
body {
  height: 100%;
}

body {
  display: -ms-flexbox;
  display: -webkit-box;
  display: flex;
  -ms-flex-align: center;
  -ms-flex-pack: center;
  -webkit-box-align: center;
  align-items: center;
  -webkit-box-pack: center;
  justify-content: center;
  padding-top: 40px;
  padding-bottom: 40px;
  background-color: #f5f5f5;
}

.form-resetpassword {
  width: 100%;
  max-width: 420px;
  padding: 15px;
  margin: 0 auto;
}

.form-label-group {
  position: relative;
  margin-bottom: 1rem;
}

.form-label-group > input,
.form-label-group > label {
  padding: var(--input-padding-y) var(--input-padding-x);
}

.form-label-group > label {
  position: absolute;
  top: 0;
  left: 0;
  display: block;
  width: 100%;
  margin-bottom: 0; /* Override default `<label>` margin */
  line-height: 1.5;
  color: #495057;
  border: 1px solid transparent;
  border-radius: .25rem;
  transition: all .1s ease-in-out;
}

.form-label-group input::-webkit-input-placeholder {
  color: transparent;
}

.form-label-group input:-ms-input-placeholder {
  color: transparent;
}

.form-label-group input::-ms-input-placeholder {
  color: transparent;
}

.form-label-group input::-moz-placeholder {
  color: transparent;
}

.form-label-group input::placeholder {
  color: transparent;
}

.form-label-group input:not(:placeholder-shown) {
  padding-top: calc(var(--input-padding-y) + var(--input-padding-y) * (2 / 3));
  padding-bottom: calc(var(--input-padding-y) / 3);
}

.form-label-group input:not(:placeholder-shown) ~ label {
  padding-top: calc(var(--input-padding-y) / 3);
  padding-bottom: calc(var(--input-padding-y) / 3);
  font-size: 12px;
  color: #777;
}

</style>
</head>

  <body>
    <form class="form-resetpassword needs-validation"  method="POST" action="{{ route('password.reset.api') }}" novalidate> 
      <div class="text-center mb-4">
        <a class="text-dark" href="/" title="Opined - Where Every Opinion Matters !">
             <img src="/img/logo.png" height="40px;" width="120px;" class="mb-3"  alt="logo">
        </a>
        <h1 class="h3 mb-3 font-weight-normal">Reset Password</h1>
      </div>

      
    @if(strpos("$token","_"))
     <input type="hidden" name="token" value="{{substr($token, 0, strpos($token, '_'))}}" />
     @else
     <input type="hidden" name="token" value="{{$token}}" /> 
    @endif
      
      {{csrf_field()}}
      @include('frontend.partials.message')

        <div class="form-label-group">
        <input id="email" name="email" type="email" placeholder="Emaill Address" class="form-control validate" value="" autofocus required>   
        <div class="invalid-feedback">
            @if ($errors->has('email')){{ $errors->first('email') }} @endif
        </div>
        <label for="email">Email address</label>
      </div>  

      <div class="form-label-group">
         <input id="password" min="6" name="password" placeholder="Password" type="password" class="form-control validate" required>
          <div class="invalid-feedback">
            @if ($errors->has('password')){{ $errors->first('password') }} @endif
         </div>
        <label for="password">Password</label>
      </div>

       <div class="form-label-group">
           <input id="passwordConfirm" name="password_confirmation" placeholder="Confirm Password" type="password" class="form-control validate" required>
           <label for="passwordConfirm">Confirm Password</label>
      </div>

      
     <button type="submit" class="btn btn-lg btn-primary btn-block">Reset Password</button>

    </form>
  </body>
</html>

