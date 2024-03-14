
<div class="modal fade modal-dialog" id="verifyMobileModal" tabindex="-1" role="dialog" aria-labelledby="verifyMobileModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content" style="border:0px;">
        <div class="modal-header"  style="background-color:#244363;color:#fff;">
          <h5 class="modal-title" id="verifyMobileModalLabel">Verify Your Mobile <i class="ml-2 fas fa-mobile-alt"></i></h5>
          <!--<button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>-->
        </div>
        <div class="modal-body">
              <div class="otp_response" style=" border-radius:5px;text-align:center;visibility:hidden;margin-bottom:8px;"></div>
              <p>Hey {{  Auth::user()->name }} , To secure your account, please verify your mobile number.</p>
              <form method="POST" action="{{route('add_mobile')}}" onsubmit=sendOTP(event);>
                {{ csrf_field() }}
                <div class="input-group mb-2">
                      <div class="input-group-prepend">
                          <div class="input-group-text">
                              <i class="fas fa-mobile-alt" style="width:16px;"></i>
                          </div>
                          <div class="input-group-text bg-white border-right-0 phonecode_label" data-toggle="modal" data-target="#phonecodeModal" style="cursor:pointer"></div>
                      </div>
                      <input id="add_phone_code" type="hidden" name="phone_code" class="phone_code" />
                      <input id="add_mobile" name="mobile"  type="number"  value="{{ Auth::user()->mobile!=null?(Auth::user()->mobile):'' }}" class="border-left-0 form-control"   placeholder="Mobile Number" min="100000" max="999999999999999" required/>
                  </div>
                <button type="submit" class="btn btn-sm btn-block btn-success"  id="sendMobileOTP">Verify Mobile Nubmer</button>
              </form>
          </div>
      </div>
    </div>
  </div>
