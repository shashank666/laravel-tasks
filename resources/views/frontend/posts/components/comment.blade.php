<div class="card mb-3" id="comment-{{$comment->id}}">
 <div class="card-body">
    <div class="media">
        <img class="d-flex mr-3 rounded-circle" src="{{$comment->user['image']}}" height="50" width="50"/>
        <div class="media-body">
            <h5 class="mt-0">{{$comment->user['name']}}
            <span class="float-right text-muted" style="font-size:14px;">
            
            @if(Carbon\Carbon::parse($comment->updated_at)->diffInHours(Carbon\Carbon::now(), false) >= 24)
                {{Carbon\Carbon::parse($comment->updated_at)->toFormattedDateString()}}
            @else
                {{Carbon\Carbon::parse($comment->updated_at)->diffForHumans()}}
            @endif
            
            </span>
            </h5>
        <p>{{$comment->comment}}</p>
         @if(Auth::user() && $comment->user->id!=Auth::user()->id)
        <button type="button" class="btn btn-sm btn-outline-success" id="-comment-{{$comment->id}}"><i class="fas fa-reply mr-2"></i>Reply</button>   
        @endif

        @if(Auth::user() && $comment->user->id==Auth::user()->id)
        <button type="button" class="btn btn-sm btn-outline-primary editComment" id="edit-comment-{{$comment->id}}"><i class="fas fa-pencil-alt mr-2"></i>Edit</button>
        <button type="button" class="btn btn-sm btn-outline-danger deleteComment" id="delete-comment-{{$comment->id}}"><i class="far fa-trash-alt mr-2"></i>Delete</button>
        @endif 

        </div>
    </div>
 </div>
</div>