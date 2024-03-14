<div class="modal fade" id="changeUsernameModal" tabindex="-1" role="dialog" aria-hidden="true" >
        <div class="modal-dialog" role="document">
            <div class="modal-content" style="border:0px;">
                <div class="modal-header" style="background-color:#244363;color:#fff;">
              <h5 class="modal-title mx-auto" id="changeUsernameModalLabel">Change Username</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"  style="margin-left:0;">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
                 <form id="change-username-form" method="POST"  action="{{ route('update_username') }}">     
                        {{ csrf_field() }} 
                        <div class="response" style="border-radius:5px;line-height:30px;height:30px;color:#FFFFFF;text-align:center;visibility:hidden;margin-bottom:8px"></div>
                        <div class="input-group mb-2"> 
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                        <i class="fas fa-user"></i>
                                </div>
                            </div> 
                            <input id="username" name="username" type="text" class="form-control" value="{{ Auth::user()->username!=null?(Auth::user()->username):null }}" autofocus="true" placeholder="Username" required/>   
                            <div class="invalid-feedback"></div>       
                        </div>
                        <button id="btnChangeUsername" type="submit" class="btn btn-sm btn-primary btn-block mt-2">SUBMIT</button>                                                     
                  </form>
            </div>
          </div>
        </div>
      </div>
