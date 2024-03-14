

   @if(Auth::guest())
   <table>
      
        <td>Neutral:</td>
        <td>
          <img src="/img/neutral.jpg"
          width='200'
          height='20'>
          {{$poll_result_neutral}}
        </td>
      </tr>
      <tr>
        <td>Upvote:<br>Downvote:
        </td>
        <td>
        <img src="/img/voteblur.png"
          width='200'
          height='40' style="width: 90%; cursor: pointer;" data-toggle="modal" href="#loginModal">
        </td>
      </tr>
      </table>
    {{--@if($voting_type=="upvote")
    <table>
      <tr>
        <td>Upvote:</td>
        <td>
          <img src="/img/upvote.png"
          width='200'
          height='20'>
          {{$poll_result_up}}
        </td>
      </tr>
      <tr>
        <td>Neutral:<br>Downvote:
        </td>
        <td>
        <img src="/img/voteblur.png"
          width='200'
          height='40' style="width: 90%; cursor: pointer;" data-toggle="modal" href="#loginModal">
        </td>
      </tr>
      
      </table>
      @elseif($voting_type=="neutral")
    <table>
      
        <td>Neutral:</td>
        <td>
          <img src="/img/neutral.jpg"
          width='200'
          height='20'>
          {{$poll_result_neutral}}
        </td>
      </tr>
      <tr>
        <td>Upvote:<br>Downvote:
        </td>
        <td>
        <img src="/img/voteblur.png"
          width='200'
          height='40' style="width: 90%; cursor: pointer;" data-toggle="modal" href="#loginModal">
        </td>
      </tr>
      </table>
      @elseif($voting_type=="downvote")
    <table>
      
        <td>Downvote:</td>
        <td>
          <img src="/img/down.jpg"
          width='200'
          height='20'>
          {{$poll_result_down}}
        </td>
      </tr>
      <tr>
        <td>Upvote:<br>Neutral:
        </td>
        <td>
        <img src="/img/voteblur.png"
          width='200'
          height='40' style="width: 90%; cursor: pointer;" data-toggle="modal" href="#loginModal">
        </td>
      </tr>
      <tr>
      </table>
      @endif--}}
    @else
    <table>
      <tr>
        <td>Upvote:</td>
        <td>
          <img src="/img/upvote.png"
          width='{{100*round($poll_result_up/($poll_result_down+$poll_result_up+$poll_result_neutral),2)}}'
          height='20' style="width:{{90*round($poll_result_up/($poll_result_down+$poll_result_up+$poll_result_neutral),2)}}%">
          {{$poll_result_up}}
        </td>
      </tr>
      <tr>
        <td>Neutral:</td>
        <td>
          <img src="/img/neutral.jpg"
          width='{{100*round($poll_result_neutral/($poll_result_down+$poll_result_up+$poll_result_neutral),2)}}'
          height='20' style="width:{{90*round($poll_result_neutral/($poll_result_down+$poll_result_up+$poll_result_neutral),2)}}%">
          {{$poll_result_neutral}}
        </td>
      </tr>
      <tr>
        <td>Downvote:</td>
        <td>
          <img src="/img/down.jpg"
          width='{{100*round($poll_result_down/($poll_result_down+$poll_result_up+$poll_result_neutral),2)}}'
          height='20' style="width:{{90*round($poll_result_down/($poll_result_down+$poll_result_up+$poll_result_neutral),2)}}%">
          {{$poll_result_down}}
        </td>
      </tr>
      </table>
      @endif
      </div> 
   
      <div class="card-footer" style="background: #fff8dc;">
      <a class="btn btn-circle btn-twitter waves-effect waves-circle waves-float mr-2" target="_blank" role="button" href="https://twitter.com/share?text={{$poll->title}} LIVE POLL : Vote and Share your opinion on Opined&url=https://www.weopined.com/polls/{{$poll->slug}}" data-post="{{$poll->id}}" data-plateform="TWITTER"><span><i class="fab fa-twitter"></i></span></a>
        <a class="btn btn-circle btn-facebook waves-effect waves-circle waves-float mr-2"  target="_blank" role="button" href="https://facebook.com/sharer/sharer.php?u=https://www.weopined.com/polls/{{$poll->slug}}" data-post="{{$poll->id}}" data-plateform="FACEBOOK"><span><i class="fab fa-facebook-f"></i></span></a>
        <a class="btn btn-circle btn-whatsapp  waves-effect waves-circle waves-float"  target="_blank" role="button" href="https://api.whatsapp.com/send?&text={{$poll->title}} : https://www.weopined.com/polls/{{$poll->slug}} LIVE POLL : Vote and Share your opinion on Opined" data-post="{{$poll->id}}" data-plateform="WHATSAPP"><span><i class="fab fa-whatsapp"></i></span></a>
        {{--<div class="float-right" style="margin-top: 1%; font-size: 1rem">
          <span style="color: #244363">Total Votes: </span>
          @if(Auth::guest())
          <a data-toggle="modal" href="#loginModal"> Login To View Result </a>
          @else
          <span style="color: #ff9800">{{$total_votes}}</span>
          @endif
        </div>--}}
      </div>
      <div class="row">
        <div class="col-md-12" ><span style="color: red">*</span><span> Only logged-in user's vote will be counted in final results</span>
        </div>
      </div>
 </div>