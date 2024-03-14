<div class="modal fade" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="PaymentModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
      <div class="modal-content" style="border:0px;">
        <div class="modal-header" style="background-color:#244363;color:#fff;">
          Fill Ammount
        <button type="button" class="close " data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      
      <div class="modal-body">
            <form  method="POST"  action="{{ route('admin.payment.pay') }}">
                    @csrf
                    <input type="hidden" name="user_pay" id="user_pay" value=""/>
                      <div class="input-group mb-2"> 
                      <div class="input-group-prepend">
                          <div class="input-group-text">
                              <i class="fas fa-dollar-sign"></i>
                          </div>
                      </div> 
                      <input id="payment_refrence_number" name="payment_refrence_number" type="text" class="form-control" value="" placeholder="Payment Refrence Number" required/>   
                   </div>
                    <div class="input-group mb-2"> 
                      <div class="input-group-prepend">
                          <div class="input-group-text">
                              <i class="fas fa-dollar-sign"></i>
                          </div>
                      </div> 
                      <input id="paid_ammount" name="paid_ammount" type="number" class="form-control" value="" autofocus="true" placeholder="Ammount" required/>   
                   </div>
                    <button type="submit" class="btn btn-sm btn-block btn-success">Paid</button>
                    <div class="d-flex flex-md-row flex-column justify-content-between">
                    
                    </div>
            </form>
      </div>
      
    </div>
  </div>
</div>
