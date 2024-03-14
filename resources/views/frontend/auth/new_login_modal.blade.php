<div class="modal fade" id="newLoginModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content" style="border:0px;">
            <div class="modal-header" style="background-color:#244363;color:#fff;">
          <h5 class="modal-title mx-auto" id="forgotPasswordModalLabel">Forgot Password ? </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"  style="margin-left:0;">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
             <form  id="sendEmailForm" method="POST"  action="/password/email">     
                  {{ csrf_field() }} 
                    <div class="response" style="border-radius:5px;line-height:30px;height:30px;color:#FFFFFF;text-align:center;visibility:hidden;margin-bottom:8px"></div>
                    <div class="input-group mb-2"> 
                      <div class="input-group-prepend">
                          <div class="input-group-text">
                              <i class="fas fa-envelope"></i>
                          </div>
                      </div> 
                      <input id="forgot-email" name="email" type="email" class="form-control" value="{{ old('email') }}" autofocus="true" placeholder="Email" required/>   
                   </div>
                    <button id="btnSendEmail" type="button" class="btn btn-sm btn-primary btn-block mt-2" onclick="sendResetPasswordLink();">Send Password Reset Link</button>                                                     
              </form>
              <a class="mt-2 btn btn-sm btn-light btn-block" data-dismiss="modal" data-toggle="modal" href="#loginModal"> Login To Account </a>
        </div>
      </div>
    </div>
  </div>