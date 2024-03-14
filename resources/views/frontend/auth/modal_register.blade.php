<div class="modal fade" id="registerModal" tabindex="-1" role="dialog" aria-labelledby="registerModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <!-- <div class="modal-dialog modal-lg" role="document"> -->
    <div class="modal-content" style="border:0px;">

        <div class="modal-header" style="background-color:#ffffff;color:rgb(27, 27, 27);">
            <h5 class="modal-title mx-auto" id="registerModalLabel"><span style="vertical-align: bottom;margin-right:8px;">Create Account</span><img src="/img/logo.png" width="90" height="30" alt="Opined"/></h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-left:0;">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>

        <div class="modal-body">
            <div class="row">
                    <div class="col-md-12 col-12 create-account-div">
                        <!-- <div class="col-md-6 col-12 create-account-div border-right"> -->
                        <form class="mx-3" method="POST" id="registerForm">
                            {{ csrf_field() }}
                            <div class="login_response" style=" border-radius:5px;text-align:center;visibility:hidden;margin-bottom:8px;"></div>

                            <div class="input-group mb-2">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <i class="fas fa-user"></i>
                                        </div>
                                    </div>
                                <input id="name" name="name" type="text" class="form-control" value="{{ old('name') }}" autofocus="true" placeholder="Name" required/>
                            </div>


                            <div class="input-group mb-2">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <i class="fas fa-envelope"></i>
                                        </div>
                                    </div>
                                <input id="email" name="email" type="email" class="form-control" value="{{ old('email') }}"  placeholder="Email" required/>
                            </div>

                            @if($company_ui_settings->check_mobile_verified)
                            <div class="input-group mb-2">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        <i class="fas fa-mobile-alt" style="width:16px;"></i>
                                    </div>
                                    <div class="input-group-text bg-white border-right-0 phonecode_label" data-toggle="modal" data-target="#phonecodeModal" style="cursor:pointer"></div>
                                </div>
                                <input type="hidden" name="phone_code"  />
                                <input id="mobile" name="mobile" type="number" class="border-left-0 form-control"   placeholder="Mobile Number"  min="100000" max="999999999999999" required/>
                            </div>
                            @endif


                            <div class="input-group mb-2">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <i class="fas fa-key"></i>
                                        </div>
                                    </div>
                                <input id="password" type="password" name="password" class="form-control" placeholder="Password" required/>
                            </div>

                            <div class="input-group mb-2">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <i class="fas fa-key"></i>
                                        </div>
                                    </div>
                                <input id="password-confirm" name="password_confirmation" type="password" class="form-control" placeholder="Confirm Password" required/>
                            </div>


                            <div class="input-group mb-2">
                                <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="agree" required>
                                            <label class="form-check-label text-secondary" for="agree">
                                            <small>By signing up you indicate that you have read and agree to <br/> Opined&apos;s  <a href="{{ route('terms_of_service') }}" title="Terms of Service">Terms of Service</a> and <a href="{{ route('privacy_policy') }}" title="Privacy Policy">Privacy Policy</a> .</small>
                                </label>
                                </div>
                            </div>


                            <button id="btnRegister" type="button" class="btn btn-sm btn-block btn-success" style="margin-top:10px;" onclick="submitRegisterForm();" disabled>Create Account</button>
                            <a  class="mt-2 btn btn-sm btn-light btn-block" data-dismiss="modal" data-toggle="modal" href="#loginModal">Already have an account ? Login</a>
                        </form>
                    </div>

                   {{--  <div class="col-md-6 col-12 social-login-div text-center py-5">
                            <p class="text-justify font-weight-light">Already have an account with Facebook, Google+ or Twitter or Linkedin ? Signup using your social media account.</p>
                            <div class="btn-group mb-2" role="group" aria-label="Continue With Facebook" style="background:#3b5998;border-radius:4px;">
                                    <a href="/auth/facebook" style="text-decoration:none;color:#fff;" class="btn btn-sm"><i class="fab fa-facebook-square"></i></a>
                                    <a href="/auth/facebook"  style="text-decoration:none;color:#fff;width:200px;text-align: left;"  class="btn btn-sm">Continue With Facebook</a>
                            </div>

                            <div class="btn-group mb-2" role="group" aria-label="Continue With Google +" style="background:#dd4b39;border-radius:4px;">
                                    <a href="/auth/google" style="text-decoration:none;color:#fff;" class="btn btn-sm"><i class="fab fa-google"></i></a>
                                    <a href="/auth/google"  style="text-decoration:none;color:#fff;width:200px;text-align: left;"  class="btn btn-sm">Continue With Google</a>
                            </div>

                            <div class="btn-group mb-2" role="group" aria-label="Continue With Twitter" style="background:#1da1f2;border-radius:4px;">
                                    <a href="/auth/twitter" style="text-decoration:none;color:#fff;" class="btn btn-sm"><i class="fab fa-twitter-square"></i></a>
                                    <a href="/auth/twitter"  style="text-decoration:none;color:#fff;width:200px;text-align: left;"  class="btn btn-sm">Continue With Twitter</a>
                            </div>

                            <div class="btn-group mb-2" role="group" aria-label="Continue With Linkedin" style="background:#0077b5;border-radius:4px;">
                                    <a href="/auth/linkedin" style="text-decoration:none;color:#fff;" class="btn btn-sm"><i class="fab fa-linkedin"></i></a>
                                    <a href="/auth/linkedin"  style="text-decoration:none;color:#fff;width:200px;text-align: left;"  class="btn btn-sm">Continue With Linkedin</a>
                            </div>
                    </div>   --}}
            </div>
        </div>


    </div>
  </div>
</div>
