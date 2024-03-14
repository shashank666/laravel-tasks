<div class="modal fade" id="changePasswordModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content" style="border:0px;">
                <div class="modal-header" style="background-color:#244363;color:#fff;">
              <h5 class="modal-title mx-auto" id="changePasswordModalLabel">Change Account Password</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"  style="margin-left:0;">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
                 <form  id="change-password-form" method="POST"  action="{{ route('update_password') }}">     
                        {{ csrf_field() }} 
                        <div class="response" style="border-radius:5px;line-height:30px;height:30px;color:#FFFFFF;text-align:center;visibility:hidden;margin-bottom:8px"></div>
                        <div class="input-group mb-2"> 
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <!--<i class="fas fa-key"></i>-->
                                    Current Password
                                </div>
                            </div> 
                            <input id="current-password" name="current-password" type="password" class="form-control" value="" autofocus="true" placeholder=" " required/>   
                            <div class="invalid-feedback"></div>       
                        </div>
                        <div class="input-group mb-2"> 
                                <div class="input-group-prepend">
                                    <div class="input-group-text" style="padding-right: 33px;">
                                        <!--<i class="fas fa-key"></i>-->
                                        New Password
                                    </div>
                                </div> 
                                <input id="new-password" name="new-password" type="password" class="form-control" value=""  placeholder=" " required/>   
                                <div class="invalid-feedback"></div>       
                        </div>
                        <div class="input-group mb-2"> 
                                <div class="input-group-prepend">
                                    <div class="input-group-text" style="padding-right: 25px;">
                                        <!--<i class="fas fa-key"></i>-->
                                        Verify Password
                                    </div>
                                </div> 
                                <input id="confirm-password" name="confirm-password" type="password" class="form-control" value="" placeholder=" " required/>  

                                <div class="invalid-feedback"></div>   <span id='message'></span>    
                        </div>
                        <button id="btnChangePassword" type="submit" class="btn btn-sm btn-primary btn-block mt-2">SUBMIT</button>                                                     
                  </form>
            </div>
          </div>
        </div>
    </div>

