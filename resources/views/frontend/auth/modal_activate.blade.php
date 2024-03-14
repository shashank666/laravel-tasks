<div class="modal fade" id="activateModal" tabindex="-1" role="dialog" aria-labelledby="activateModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <!-- <div class="modal-dialog modal-lg" role="document"> -->
    <div class="modal-content" style="border:0px;">

        <div class="modal-header" style="background-color:#244363;color:#fff;">
            <h5 class="modal-title mx-auto" id="registerModalLabel"><span style="vertical-align: bottom;margin-right:8px;">Activate Account</span><img src="/img/logo-white.png" width="90" height="30" alt="Opined"/></h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-left:0;">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>

        <div class="modal-body">
            <div class="row">
                    <div class="col-md-12 col-12 create-account-div">
                        <!-- <div class="col-md-6 col-12 create-account-div border-right"> -->
                        <form class="mx-3" method="POST" action="{{ route('activate_account_check') }}">
                            {{ csrf_field() }}

                            <div class="input-group mb-2">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <i class="fas fa-envelope"></i>
                                        </div>
                                    </div>
                                <input id="email" name="email" type="email" class="form-control" value=""  placeholder="Email" required/>
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

                            <button id="btnOTP" type="submit" class="btn btn-sm btn-block btn-success" style="margin-top:10px;">Send OTP</button>
                            <a  class="mt-2 btn btn-sm btn-light btn-block" href="{{ route('login_form') }}">Already have an account ? Login</a>
                        </form>
                    </div>
            </div>
        </div>


    </div>
  </div>
</div>
