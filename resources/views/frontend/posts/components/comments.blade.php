
@foreach($comments as $comment)
  @include('frontend.posts.components.comment', ['comment' => $comment])

@if($comment->replies->count() > 0)
    
      @foreach($comment->replies as $reply)
    <div class="card mb-3 ml-5" id="comment-{{$reply->id}}">
 <div class="card-body">
    <div class="media">
        <img class="d-flex mr-3 rounded-circle" src="{{$reply->user['image']}}" height="50" width="50"/>
        <div class="media-body">
            <h5 class="mt-0">{{$reply->user['name']}}</h5>
        {{$reply->comment}}
        </div>
    </div>
 </div>
  <div class="card-footer bg-white">
        @if(Auth::user() && $reply->user->id!=Auth::user()->id)
        <button type="button" class="btn btn-sm btn-outline-success" id="-comment-{{$reply->id}}"><i class="fas fa-reply mr-2"></i>Reply</button>   
        @endif

        @if(Auth::user() && $reply->user->id==Auth::user()->id)
        <button type="button" class="btn btn-sm btn-outline-primary editComment" id="edit-comment-{{$reply->id}}"><i class="fas fa-pencil-alt mr-2"></i>Edit</button>
        <button type="button" class="btn btn-sm btn-outline-danger deleteComment" id="delete-comment-{{$reply->id}}"><i class="far fa-trash-alt mr-2"></i>Delete</button>
        @endif       
  </div>
</div>
    @endforeach 
@endif

@endforeach