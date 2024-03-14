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
  <div class="card-body ">
    <h5 class="card-title">{!! $poll->title !!}</h5>
    <p class="card-text">{!! $poll->description !!}</p>
  
    @if($poll->poll_type == "UDN")
    @include('frontend.polls.polltypecards.udn_individual_card',['user'=>$poll->user])
    @elseif($poll->poll_type == "MCPS")
    @include('frontend.polls.polltypecards.mcps_individual_card',['user'=>$poll->user])
    @endif

   </div> 
      <div class="card-footer float-right">
      <a class="btn btn-circle btn-twitter waves-effect waves-circle waves-float mr-2" target="_blank" role="button" href="https://twitter.com/share?text={{$poll->title}} LIVE POLL : Vote and Share your opinion on Opined&url=https://www.weopined.com/polls/{{$poll->slug}}" data-post="{{$poll->id}}" data-plateform="TWITTER"><span><i class="fab fa-twitter"></i></span></a>
        <a class="btn btn-circle btn-facebook waves-effect waves-circle waves-float mr-2"  target="_blank" role="button" href="https://facebook.com/sharer/sharer.php?u=https://www.weopined.com/polls/{{$poll->slug}}" data-post="{{$poll->id}}" data-plateform="FACEBOOK"><span><i class="fab fa-facebook-f"></i></span></a>
        <a class="btn btn-circle btn-whatsapp  waves-effect waves-circle waves-float"  target="_blank" role="button" href="https://api.whatsapp.com/send?&text={{$poll->title}}: https://www.weopined.com/polls/{{$poll->slug}} LIVE POLL : Vote and Share your opinion on Opined" data-post="{{$poll->id}}" data-plateform="WHATSAPP"><span><i class="fab fa-whatsapp"></i></span></a>
        </div>
 </div>
</div>