<div class="modal fade" id="forgotPasswordModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
      <div class="modal-content" style="border:0px;">
        <div class="modal-header" style="background-color:#ffffff;color:rgb(27, 27, 27);">
        <h5 class="modal-title  mx-auto" id="loginModalLabel"><!--<span style="vertical-align: bottom;margin-right:8px;">Login To</span>--><img src="/img/logo.png" width="90" height="30" alt="Opined"/></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"  style="margin-left:0;">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      {{--  <a href="https://play.google.com/store/apps/details?id=com.app.weopined" target="_blank"><img style="width: 100%;" src="/img/app-promotion-opined.png" alt="Opined Android App"/></a>  --}}
      <div class="modal-body">
            <form  method="POST"  action="{{ route('login') }}">
                    @csrf
                    <div class="login_response" style=" border-radius:5px;text-align:center;visibility:hidden;margin-bottom:8px;"></div>

                    <input id="login-email" name="email" type="text" class="form-control validate" value="{{ old('email') }}" style="
                    margin-bottom: 1rem;" autofocus="true" placeholder="Email / Mobile" required >
					<input id="login-password" type="password" name="password" class="form-control validate" placeholder="Password" style="
                    margin-bottom: 1rem;" required>


                    <div class="form-group">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox"  name="remember" id="remember" {{old('remember')?'checked=true':'checked=false'}} />
                        <label class="form-check-label text-secondary"  for="remember">Remember Me</label>
                    </div>
                    </div>

                    <button type="submit" class="btn btn-sm btn-block btn-success">Login</button>
                    <div class="d-flex flex-md-row flex-column justify-content-between">
                    <a class="mt-2 btn btn-sm btn-light"  data-dismiss="modal" data-toggle="modal" data-target="#newLoginModel">Forgot Password ?</a>
                    <a class="mt-2 btn btn-sm btn-light" data-dismiss="modal"  data-toggle="modal" data-target="#registerModal">No Account ? Register</a>
                    </div>
            </form>
      </div>
     <div class="modal-footer">
              @include('frontend.auth.social_login')
      </div>
    </div>
  </div>
</div>
