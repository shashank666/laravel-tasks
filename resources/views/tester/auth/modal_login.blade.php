<div class="modal fade" id="loginModalTest" tabindex="-1" role="dialog" aria-labelledby="loginModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
      <div class="modal-content" style="border:0px;">
        <div class="modal-header" style="background-color:#244363;color:#fff;">
        <h5 class="modal-title  mx-auto" id="loginModalLabel"><span style="vertical-align: bottom;margin-right:8px;">Login To</span><img src="/img/logo-white.png" width="90" height="30" /></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"  style="margin-left:0;">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
            <form  method="POST"  action="{{ route('login') }}">
                    @csrf
                    <div class="login_response" style=" border-radius:5px;text-align:center;visibility:hidden;margin-bottom:8px;"></div>

                  <div class="input-group mb-2">
                      <div class="input-group-prepend">
                          <div class="input-group-text">
                              <i class="fas fa-envelope"></i>
                          </div>
                      </div>
                      <input id="login-email" name="email" type="email" class="form-control validate" value="{{ old('email') }}" autofocus="true" placeholder="Email" required/>
                  </div>

                  <div class="input-group mb-2">
                      <div class="input-group-prepend">
                          <div class="input-group-text">
                              <i class="fas fa-key"></i>
                          </div>
                      </div>
                      <input id="login-password" type="password" name="password" class="form-control validate" placeholder="Password" required/>
                  </div>


                    <div class="form-group">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox"  name="remember" id="remember" {{old('remember')?'checked=true':'checked=false'}} />
                        <label class="form-check-label text-secondary"  for="remember">Remember Me</label>
                    </div>
                    </div>

                    <button type="submit" class="btn btn-sm btn-block btn-success">Login</button>
                    <div class="d-flex flex-md-row flex-column justify-content-between">
                    <a class="mt-2 btn btn-sm btn-light"  data-dismiss="modal" data-toggle="modal" data-target="#forgotPasswordModal">Forgot Password ?</a>
                    <a class="mt-2 btn btn-sm btn-light" data-dismiss="modal"  data-toggle="modal" data-target="#registerModal">No Account ? Register</a>
                    </div>
            </form>
      </div>
      <div class="modal-footer">
              @include('tester.auth.social_login')
      </div>
    </div>
  </div>
</div>
