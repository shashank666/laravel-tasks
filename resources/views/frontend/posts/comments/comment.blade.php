<div class="card comment mb-3" id="{{ 'comment_'.$comment->id }}">
        <div class="card-body">
        <div class="row">
            <div class="col-auto">
            <a  href="{{ route('user_profile',['username'=>$comment->user['username']]) }}">
                <img src="{{ $comment->user['image'] }}" alt="..." class="rounded-circle" height="48" width="48">
            </a>
            </div>
            <div class="col ml-n2">
                <div class="comment-body">
                    <div class="row">
                    <div class="col">
                    <a style="text-decoration:none;color:#212121;" href="{{ route('user_profile',['username'=>$comment->user['username']]) }}">
                        <h5 class="comment-title">
                        {{ $comment->user['name'] }}
                        </h5>
                    </a>
                    </div>
                    <div class="col-auto">
                        <time class="comment-time text-secondary" style="font-size:14px">
                        {{ $comment->created_at }}
                        </time>
                    </div>
                    </div>
                    <p class="comment-text">
                        @if($comment->comment!=null)
                        {{ $comment->comment }}
                        @endif
                        @if($comment->media!=null)
                        <br/><br/><img src="{{ $comment->media }}" class="img-fluid rounded" alt="..." style="max-width:300px;max-height:300px"/>
                        @endif
                    </p>
                </div>
                <div class="comment-footer">
                        <div class="row">
                            <div class="col-12 col-md-6">
                                    @if(Auth::guest())
                                    <span onclick="openLoginModal();" class="like-icon-opined mr-3">
                                        <i  class="far fa-thumbs-up mr-1"></i>
                                        <span  style="font-size:16px;" class="comment_likes_count_{{$comment->id}}">{{$comment->likes_count}} Likes</span>
                                    </span>
                                    @else
                                     <span class="like-icon-opined likeComment mr-3" id="likeComment_{{ $comment->id }}" data-commentid="{{ $comment->id }}">
                                            <i class="fas fa-thumbs-up likeComment_{{ $comment->id }}_on"  style="display:{{in_array($comment->id,$liked_comments)?'inline':'none'}}"></i>
                                            <i class="far fa-thumbs-up likeComment_{{$comment->id}}_off" style="display:{{!in_array($comment->id,$liked_comments)?'inline':'none'}}"></i>
                                            <span style="font-size:16px;" class="comment_likes_count_{{$comment->id}}">{{$comment->likes_count}} Likes</span>
                                    </span>
                                    @endif

                                    <span class="comment-icon-opined loadReplies" id="{{ 'loadReplies_'.$comment->id  }}" data-count="{{ $comment->replies_count }}" data-commentid="{{ $comment->id }}" data-loaded="0">
                                        <i  class="far fa-comments mr-1"></i>
                                        <span style="font-size:16px" id="{{ 'commentReplyCount_'.$comment->id }}" data-count="{{ $comment->replies_count }}">{{$comment->replies_count}} Replies</span>
                                    </span>
                            </div>
                            <div class="col-12 col-md-6 text-right mt-md-0 mt-2">
                                @if(Auth::guest())
                                <button type="button" class="float-right btn btn-sm btn-outline-success" onclick="openLoginModal();"><i class="fas fa-reply mr-2"></i>Reply</button>
                                @else
                                    @if(Auth::user()->id==$comment->user->id)
                                    <button type="button" class="btn btn-sm btn-outline-primary editComment" data-post="{{ $post_id }}" data-commentid="{{ $comment->id  }}"  data-parentid="{{ $comment->parent_id }}" data-media="{{ $comment->media==null?'NONE':$comment->media}}" data-comment="{{ $comment->comment==null?'NONE':$comment->comment}}" data-title="Edit Comment"><i class="fas fa-pencil-alt"></i></button>
                                    <button type="button" class="btn btn-sm btn-outline-danger deleteComment"  data-post="{{ $post_id }}" data-commentid="{{ $comment->id  }}" data-parentid="{{ $comment->parent_id }}"><i class="far fa-trash-alt"></i></button>
                                    @else
                                    <button type="button" class="float-right btn btn-sm btn-outline-success replyComment" data-post="{{ $post_id }}" data-parentid="{{ $comment->id }}"  data-title="{{ 'Reply To '.$comment->user['name'] }}"><i class="fas fa-reply mr-2"></i>Reply</button>
                                    @endif
                                @endif
                            </div>
                        </div>
                </div>
            </div>
        </div>
        </div>
    </div>
<div class="ml-md-5 ml-0"  id="{{ 'replies_'.$comment->id }}" style="display:none">
</div>
