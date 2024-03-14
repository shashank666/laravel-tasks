<div class="modal fade" id="changeKeywordModal" tabindex="-1" role="dialog" aria-hidden="true" >
        <div class="modal-dialog" role="document">
            <div class="modal-content" style="border:0px;">
               <!-- <div class="modal-header" style="background-color:#244363;color:#fff;">
              <h5 class="modal-title mx-auto" id="changeKeywordModalLabel">Change The Words</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"  style="margin-left:0;">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>-->
            <div class="modal-body">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"  style="margin-left:0;">
                <span aria-hidden="true">&times;</span>
              </button>
                 <form id="change-keywords-form" method="POST"  action="{{ route('update_keywords') }}">     
                        {{ csrf_field() }} 
                        <div class="response" style="border-radius:5px;line-height:30px;height:30px;color:#FFFFFF;text-align:center;visibility:hidden;margin-bottom:8px"></div>
                        <div class="input-group mb-2"> 
                            <textarea id="keywords" name="keywords" type="text" class="form-control" value="{{ Auth::user()->keywords!=null?(Auth::user()->keywords):null }}" autofocus="true" placeholder="Three Words That Describe You" onfocus="this.placeholder = ''"onblur="this.placeholder = 'Three Words That Describe You'"></textarea>
                            <!--<input id="keywords" name="keywords" type="text" class="form-control" value="{{ Auth::user()->keywords!=null?(Auth::user()->keywords):null }}" autofocus="true" placeholder="Three Words That Describe You" onfocus="this.placeholder = ''"onblur="this.placeholder = 'Three Words That Describe You'"/> -->  
                            <div class="invalid-feedback"></div>   
                        </div>
                        <button id="btnChangeThreeword" type="submit" class="btn btn-sm btn-primary btn-block mt-2">SUBMIT</button>                                                     
                  </form>
            </div>
          </div>
        </div>
      </div>
