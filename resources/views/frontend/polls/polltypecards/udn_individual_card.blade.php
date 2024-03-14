<div class="row">
    <div class="vote">
       <div class="upvote" style="text-align: center;">0</div>
      <div class="incrementup mr-2">
      <button type="button" class="btn btn-primary btn-sm btn3d  increment up"><i class="fa fa-chevron-up mr-2" aria-hidden="true"></i>Upvote
      </button>
    </div>
       
      </div>
      <div class="vote">
          <div class="neutral" style="text-align: center;">0</div>
      <div class="incrementneutral  mr-2">
        <button type="button" class="btn btn-warning btn-sm btn3d increment nl"><i class="fa fa-adjust mr-2" aria-hidden="true"></i>Neutral</button>
        </div>
      
</div>
<div class="vote">
          <div class="downvote" style="text-align: center;">0</div>
      <div class="incrementdown  mr-2">
        <button type="button" class="btn btn-danger btn-sm btn3d increment down"><i class="fa fa-chevron-down mr-2" aria-hidden="true"></i>Downvote</button></div>

    </div>

    <form id="poll_voting" role="form"  action="{{route('poll-vote')}}" method="POST" enctype="multipart/form-data">
        {{csrf_field()}}
      <input type="hidden" name="voting_type" id="voting_type" value="" />
      <input type="hidden" name="voting_type_head" id="voting_type_head" value="UDN" />
      <input type="hidden" name="voting" id="voting" value="" />
      <input type="hidden" name="poll_id" id="poll_id" value="{{$poll->id}}">
      <input type="hidden" name="url" id="url" value="{{$poll->slug}}">
      <button type="button" id="btn_poll_before" class="btn btn-success float-right disabled" style="margin-top: 30%;">Vote <i class="fas fa-paper-plane"></i></button>
      <button type="submit" id="btn_poll_after" class="btn btn-success float-right" style="margin-top: 30%; display: none">Vote <i class="fas fa-paper-plane"></i></button>
    </form>
    </div>