<div class="card" style="margin-top: 1%;">
  <div class="card-header" style="background: #ffffff;color: rgb(14, 14, 14);padding: 2%;font-size: 70%;">
    Poll Created By &nbsp;
    @if($poll->user_id!="Opined Team")
    @if($poll->user!=null)
    <span style="color: #ff9800">{{$poll->user->name}}</span>
    @else
      <span style="color: #ff9800">{{$poll->user_id}}</span>
    @endif
    @else
    <span style="color: #ff9800">Opined</span>
    @endif
  </div>
  <div class="card-body">
    <h5 class="card-title">{!! $poll->title !!}</h5>
    <p class="card-text">{!! $poll->description !!}</p>
    @if($poll->poll_type == "UDN")
    @include('frontend.polls.polltypecards.udn_result_card',['user'=>$poll->user])
    @elseif($poll->poll_type == "MCPS")
    @include('frontend.polls.polltypecards.mcps_result_card',['user'=>$poll->user])
    @endif
  
    
   
</div>