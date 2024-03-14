
<div class="modal fade modal-dialog" id="verifyOTPModal" tabindex="-1" role="dialog" aria-labelledby="verifyOTPModalLabel" aria-hidden="true" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog" role="document">
        <div class="modal-content" style="border:0px;">
          <div class="modal-header" style="background-color:#244363;color:#fff;">
          <h5 class="modal-title  mx-auto" id="verifyOTPModalLabel"><span style="vertical-align: bottom;margin-right:8px;">Verify Mobile Number</span></h5>
          <!--<button type="button" class="close" data-dismiss="modal" aria-label="Close"  style="margin-left:0;">
            <span aria-hidden="true">&times;</span>
          </button>-->
        </div>
          <div class="container">
            <div class="row">
                <div class="col-md-12">
                OTP sent to your number: <span id="mobileno" name="mobileno"></span><button onclick="openRegisterModal()" type="button" class="ml-5 btn btn-outline-warning btn-sm">Change Number</button>
               </div>
            </div>                 
        </div>
        <div class="modal-body" style="padding: 0em 1em 0em 1em;">
              <form id="verify-otp-form"  method="POST"  action="">
                      {{ csrf_field() }}
                      <div class="otp_response" style=" border-radius:5px;text-align:center;visibility:hidden;margin-bottom:8px;"></div>
                      <input type="hidden" id="userid" name="userid" value=""/>
                      <div class="form-group">
                          <input id="otp" name="otp" type="number" class="form-control validate"  autofocus="true" placeholder="Enter OTP" required/>
                      </div>
                      <button id="btnVerifyOTP" type="submit" class="btn btn-sm btn-block btn-success">Verify</button>
                    </div>
              </form>
              
                
                  <form id="resend-otp-form" method="POST" action="">
                  <div class="text-center">
                        {{ csrf_field() }}
                      <button id="btnResendOTP" type="submit" class="btn btn-link">Resend OTP</button>
                  </div>
              </form>
                


        </div>

      </div>
    </div>
  </div>
