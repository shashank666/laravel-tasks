
<!DOCTYPE html>
<html>
<head>
<title>Login Form</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
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
		<h1>Log in</h1>
		<div class="main-agileinfo">
			<div class="agileits-top">
                <div class="logo mx-auto" style="
                display: flex;
                justify-content: center;
                margin-bottom: 1rem;
            ">
                    <img src="/img/logo-white.png" width="90" height="30" alt="Opined">
                    </div>
				<form method="POST"  action="{{ route('login') }}">
                    
                    @csrf 
					{{-- <input class="text" type="text" name="Username" placeholder="Username" required=""> --}}
					<input id="login-email" name="email" type="text" class="form-control validate" value="{{ old('email') }}" style="
                    margin-bottom: 1rem;" autofocus="true" placeholder="Email / Mobile" required >
					<input id="login-password" type="password" name="password" class="form-control validate" placeholder="Password" style="
                    margin-bottom: 1rem;" required>
					{{-- <input class="text w3lpass" type="password" name="password" placeholder="Confirm Password" required=""> --}}
					{{-- <div class="wthree-text">
						<label class="anim">
							<input type="checkbox" class="checkbox" required="">
							<span>I Agree To The Terms & Conditions</span>
						</label>
						<div class="clear"> </div>
					</div> --}}
					<input type="submit" style="background:#ff9800;" class="btn btn-md btn-block btn-success">
				</form>
				<p>Don't have an Account? <a href="{{ route('register_form') }}"> Create New Account!</a></p>
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

