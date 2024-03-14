<div class="modal fade" id="changeMobileModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content" style="border:0px;">
                <div class="modal-header" style="background-color:#244363;color:#fff;">
              <h5 class="modal-title mx-auto" id="changeMobileModalLabel">Change Mobile Number</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"  style="margin-left:0;">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
                 <form id="change-mobile-form"  method="POST"  action="{{ route('add_mobile') }}">
                        {{ csrf_field() }}
                        <div class="response" style="border-radius:5px;line-height:30px;height:30px;color:#FFFFFF;text-align:center;visibility:hidden;margin-bottom:8px"></div>
                        <div class="input-group mb-2">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <i class="fas fa-mobile-alt" style="width:16px;"></i>
                                </div>
                                <div class="input-group-text bg-white border-right-0 phonecode_label" data-toggle="modal" data-target="#phonecodeModal" style="cursor:pointer"></div>
                            </div>
                            <input type="hidden" name="phone_code"  />
                            <input id="add_mobile" name="mobile" type="number" class="border-left-0 form-control"   placeholder="Mobile Number" min="100000" max="999999999999999" required/>
                            <div class="invalid-feedback"></div>
                          </div>
                        <button id="btnChangeMobile" type="submit" class="btn btn-sm btn-primary btn-block mt-2">SUBMIT</button>
                  </form>
            </div>
          </div>
        </div>
      </div>
