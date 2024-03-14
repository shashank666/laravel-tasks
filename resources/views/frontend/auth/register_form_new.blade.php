
<!DOCTYPE html>
<html>
<head>
<title>Register Form</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
{{-- <script  src="/js/web/home.min.js" type="text/javascript"></script> --}}
<script type="text/javascript" src="/js/web/core.min.js"></script>
    <!--<script type="text/javascript" src="/js/custom/main.js?<?php echo time();?>"></script>-->
	<script type="text/javascript" src="/js/custom/main.min.js?<?php echo time();?>"></script>
    <script type="text/javascript" src="/js/custom/share.js"></script>
    <script defer type="text/javascript" src='/vendor/videojs/video.min.js'></script>
    <script src="/js/yall.min.js"></script>
    <script>
      document.addEventListener("DOMContentLoaded", yall);
    </script>

<script type="application/x-javascript"> addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false); function hideURLbar(){ window.scrollTo(0,1); } </script>
<!-- Custom Theme files -->
<link href="/css/landing/loginnew.css" rel="stylesheet" type="text/css" media="all" />
<!-- //Custom Theme files -->
<!-- web font -->
<link href="//fonts.googleapis.com/css?family=Roboto:300,300i,400,400i,700,700i" rel="stylesheet">
<!-- //web font -->
</head>
<body>
	<!-- main -->
	<div class="main-w3layouts wrapper">
		<h1>Create Account</h1>
		<div class="main-agileinfo">
			<div class="agileits-top">
                <div class="logo mx-auto" style="
                display: flex;
                justify-content: center;
                margin-bottom: 1rem;
            ">
                    <img src="/img/logo-white.png" width="90" height="30" alt="Opined">
                    </div>
				<form method="POST" id="registerForm">
                    
                    {{ csrf_field() }}
                    <div class="login_response" style=" border-radius:5px;text-align:center;visibility:hidden;margin-bottom:8px;"></div>
					<input id="name" name="name" type="text" class="form-control" value="{{ old('name') }}" autofocus="true" placeholder="Name" style="
                    margin-bottom: 1rem;" required>
					<input  id="email" name="email" type="text" class="form-control validate" value="{{ old('email') }}" style="
                    margin-bottom: 1rem;" autofocus="true" placeholder="Email / Mobile" required >
					<input  id="password" type="password" name="password" class="form-control" placeholder="Password" style="
                    margin-bottom: 1rem;" required>

                    {{-- @if($company_ui_settings->check_mobile_verified)
                    <div class="input-group mb-2 input-group-md">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                <i class="fas fa-mobile-alt" style="width:16px;"></i>
                            </div>
                            <div class="input-group-text bg-white border-right-0 phonecode_label" data-toggle="modal" data-target="#phonecodeModal" style="cursor:pointer"></div>
                        </div>
                        <input type="hidden" name="phone_code"  />
                        <input id="mobile" name="mobile" type="number" class="border-left-0 form-control"   placeholder="Mobile Number"  min="100000" max="999999999999999" required/>
                    </div>
                    @endif --}}


					<input  id="password-confirm" name="password_confirmation" type="password" class="form-control" placeholder="Confirm Password"style="
                    margin-bottom: 2rem;" required>
					<div class="wthree-text">
						<label class="anim">
							<input type="checkbox" class="checkbox" required="">
                            <small>By signing up you indicate that you have read and agree to <br/> Opined&apos;s  <a href="{{ route('terms_of_service') }}" title="Terms of Service">Terms of Service</a> and <a href="{{ route('privacy_policy') }}" title="Privacy Policy">Privacy Policy</a> .</small>
						</label>
						<div class="clear"> </div>
					</div>
					{{--  <input id="btnRegister" type="submit" style="background:#ff9800;" class="btn btn-md btn-block btn-success" onclick="submitRegisterForm();">  --}}
                    <button id="btnRegister" type="button" class="btn btn-sm btn-block btn-success" style="margin-top:10px;" onclick="submitRegisterForm();" >Create Account</button>
					{{-- <button id="btnRegister" type="button" class="btn btn-md btn-block btn-success" style="margin-top:10px;" onclick="submitRegisterForm();">Submit</button> --}}
				</form>
				<p>Already have an account? <a href="{{ route('login_form') }}"> Log In</a></p>
			</div>
		</div>
		<!-- copyright -->
		<div class="colorlibcopy-agile">
			<p>Copyright &copy; {{ Carbon\Carbon::now()->format('Y') }} www.weopined.com , All Rights Reserved</p>
		</div>
		<!-- //copyright -->
		<ul class="colorlib-bubbles">
			<li></li>
			<li></li>
			<li></li>
			<li></li>
			<li></li>
			<li></li>
			<li></li>
			<li></li>
			<li></li>
			<li></li>
		</ul>
	</div>
	<!-- //main -->
</body>
</html>

