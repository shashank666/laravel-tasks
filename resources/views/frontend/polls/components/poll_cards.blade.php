<div class="card">
  <div class="card-header" style="background: white;color: #495057;padding: 2%;font-size: 70%;">
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
    <h5 class="card-title" onclick="window.location.href='{{ '/polls/'.$poll->slug}}'" style="cursor: pointer;">
      <a href="{{ '/polls/'.$poll->slug}}" style="color:#000">{!! $poll->title !!}</a></h5>
    <p class="card-text" onclick="window.location.href='{{ '/polls/'.$poll->slug}}'" style="cursor: pointer;">
      <a href="{{ '/polls/'.$poll->slug}}"  style="color:#000"> {!! $poll->description !!}</a>
    </p>
    <a href="{{ '/polls/'.$poll->slug}}" class="btn" style="background-color: #ff9800; color: #fff">Vote <i class="fas fa-paper-plane ml-2" style="color: #fdfdfd"></i></a>
    @if(auth()->user() && $poll->user_id==auth()->user()->id )
      <form action="{{ route('deletePoll',$poll) }}" method="post" style="display: inline-block;">
        @csrf
        @method('DELETE')
        <button class="btn btn-danger">Delete <i class="fa fa-trash" aria-hidden="true" style="color: #fdfdfd;"></i></button>
      </form>
    @endif
    <div class="float-right">
      <a class="btn btn-circle btn-twitter waves-effect waves-circle waves-float mr-2" target="_blank" role="button" href="https://twitter.com/share?text={{$poll->title}} LIVE POLL : Vote and Share your opinion on Opined&url=https://www.weopined.com/polls/{{$poll->slug}}" data-post="{{$poll->id}}" data-plateform="TWITTER"><span><i class="fab fa-twitter"></i></span></a>
      <a class="btn btn-circle btn-facebook waves-effect waves-circle waves-float mr-2"  target="_blank" role="button" href="https://facebook.com/sharer/sharer.php?u=https://www.weopined.com/polls/{{$poll->slug}}" data-post="{{$poll->id}}" data-plateform="FACEBOOK"><span><i class="fab fa-facebook-f"></i></span></a>
      <a class="btn btn-circle btn-whatsapp  waves-effect waves-circle waves-float"  target="_blank" role="button" href="https://api.whatsapp.com/send?&text={{$poll->title}} : https://www.weopined.com/polls/{{$poll->slug}} LIVE POLL : Vote and Share your opinion on Opined" data-post="{{$poll->id}}" data-plateform="WHATSAPP"><span><i class="fab fa-whatsapp"></i></span></a>
    </div>
  </div>
</div>
