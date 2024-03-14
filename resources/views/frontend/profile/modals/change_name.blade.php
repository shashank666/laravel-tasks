<div class="modal fade" id="changeNameModal" tabindex="-1" role="dialog" aria-hidden="true" >
        <div class="modal-dialog" role="document">
            <div class="modal-content" style="border:0px;">
                <div class="modal-header" style="background-color:#244363;color:#fff;">
              <h5 class="modal-title mx-auto" id="changeNameModalLabel">Change Your Name</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"  style="margin-left:0;">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
                 <form id="change-name-form" method="POST"  action="{{ route('update_name') }}">     
                        {{ csrf_field() }} 
                        <div class="response" style="border-radius:5px;line-height:30px;height:30px;color:#FFFFFF;text-align:center;visibility:hidden;margin-bottom:8px"></div>
                        <div class="input-group mb-2"> 
                            <input id="name" name="name" type="text" class="form-control" value="{{ Auth::user()->name!=null?(Auth::user()->name):null }}" autofocus="true" placeholder="Full Name" onfocus="this.placeholder = ''"onblur="this.placeholder = 'Full Name'"/>   
                            <div class="invalid-feedback"></div>   
                        </div>
                        <button id="btnChangeName" type="submit" class="btn btn-sm btn-primary btn-block mt-2">SUBMIT</button>                                                     
                  </form>
            </div>
          </div>
        </div>
      </div>
