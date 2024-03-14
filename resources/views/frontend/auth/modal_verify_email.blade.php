<div class="modal fade" id="verifyEmailModal" tabindex="-1" role="dialog" aria-labelledby="verifyEmailModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content" style="border:0px;">
      <div class="modal-header"  style="background-color:#244363;color:#fff;">
        <h5 class="modal-title" id="verifyEmailModalLabel">Verify Your Email <i class="ml-2 fas fa-envelope"></i></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
            <p>Hey {{  Auth::user()->name }} , you haven&apos;t verified your email address yet. please verify your email address.</p>
            <div class="text-center">
            <button class="my-1 btn btn-warning"  onclick="event.preventDefault();document.getElementById('send-verification-form').submit();">Verify Email Address</button>
            </div>
        </div>
    </div>
  </div>
</div>