<div class="modal fade" tabindex="-1" role="dialog" id="reply-modal">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="reply-modal-label"></h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <form method="POST" action="{{ route('admin.send_reply') }}" id="reply-form">
                {{ csrf_field() }}
                <input type="hidden" id="reply-name" name="name" value="" />
                <input type="hidden" id="reply-message-id" name="message_id" value="" />
                 <div class="form-group">
                     <label>To :</label>
                     <input class="form-control" name="email" type="email" value="" id="reply-email" required/>
                 </div>
                 <div class="form-group">
                        <label>Subject :</label>
                        <input class="form-control" name="subject" type="text" value="" id="reply-subject" required/>
                </div>
                <div class="form-group">
                        <label>Message :</label>
                        <textarea rows="5" name="message" class="form-control auto-growth" id="reply-message" required></textarea>
                </div>
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-block btn-success">SUBMIT REPLY AND MARK AS READ</button>
        </div>
      </div>
    </div>
  </div>
