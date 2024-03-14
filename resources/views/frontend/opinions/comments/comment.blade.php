<div class="comment mb-3" id="{{ 'comment_'.$comment->id }}">
            <div class="row mx-0">
                <div class="col-auto mx-0 px-0">
                <a class="text-left" href="{{ route('user_profile',['username'=>$comment->user['username']]) }}">
                    <img src="{{ $comment->user['image'] }}" alt="..." class="rounded-circle" height="36" width="36">
                </a>
                </div>
                <div class="col ml-2" style="background-color: #f9fbfd;border-radius: .5rem;padding:0.75rem 1rem;">
                    <div class="comment-body">
                        <div class="row">
                        <div class="col">
                        <a style="text-decoration:none;color:#212121;" href="{{ route('user_profile',['username'=>$comment->user['username']]) }}">
                            <h6 class="comment-title">
                            {{ $comment->user['name'] }}
                            </h6>
                        </a>
                        </div>
                        <div class="col-auto">
                            <time class="comment-time text-secondary" style="font-size:12px;color: #95aac9;">
                            {{ date('M d, Y', strtotime($comment->created_at)) }}
                            </time>
                        </div>
                        </div>
                        <p class="comment-text">
                            @if($comment->comment!=null)
                            {{ $comment->comment }}
                            @endif
                            @if($comment->media!=null)
                            <br/><br/><img src="{{ $comment->media }}" class="img-fluid rounded" alt="..." style="max-width:250px;max-height:250px"/>
                            @endif
                        </p>
                    </div>
                    <div class="comment-footer">
                            <div class="row">
                                <div class="col-12 col-md-8">
                                        @if(Auth::guest())
                                        <span onclick="openLoginModal();" class="like-icon-opined mr-3">
                                            <i  class="far fa-thumbs-up mr-1"  style="font-size:14px;"></i>

                                            <span  style="font-size:14px;" class="comment_likes_count_{{$comment->id}}">{{$comment->likes_count}} Agrees</span>

                                            <i  class="far fa-thumbs-down mr-1"  style="font-size:14px;"></i>

                                            <span  style="font-size:14px;" class="comment_disagree_count_{{$comment->id}}">{{$comment->disagree_count}} Disagrees</span>
                                        </span>
                                        @else
                                        <span class="like-icon-opined likeComment mr-3" id="likeComment_{{ $comment->id }}" data-commentid="{{ $comment->id }}">
                                                <i class="fas fa-thumbs-up likeComment_{{ $comment->id }}_on"  style="font-size:14px;display:{{in_array($comment->id,$liked_comments)?'inline':'none'}}"></i>
                                                <i class="far fa-thumbs-up likeComment_{{$comment->id}}_off" style="font-size:14px;display:{{!in_array($comment->id,$liked_comments)?'inline':'none'}}"></i>
                                                <span style="font-size:14px;" class="comment_likes_count_{{$comment->id}}">{{$comment->likes_count}} Agrees</span>
                                        </span>

                                        <span class="like-icon-opined disagreeComment mr-3" id="disagreeComment_{{ $comment->id }}" data-commentid="{{ $comment->id }}">
                                                <i class="fas fa-thumbs-down disagreeComment_{{ $comment->id }}_on"  style="font-size:14px;display:{{in_array($comment->id,$disagreed_comments)?'inline':'none'}}"></i>

                                                <i class="far fa-thumbs-down disagreeComment_{{$comment->id}}_off" style="font-size:14px;display:{{!in_array($comment->id,$disagreed_comments)?'inline':'none'}}"></i>
                                                <span style="font-size:14px;" class="comment_disagree_count_{{$comment->id}}">{{$comment->disagree_count}} Disagrees</span>
                                        </span>
                                        @endif

                                        <span class="comment-icon-opined loadReplies" id="{{ 'loadReplies_'.$comment->id  }}" data-count="{{ $comment->replies_count }}" data-commentid="{{ $comment->id }}" data-loaded="0" data-opinion="{{ $opinion_id }}">
                                            <i  class="far fa-comments mr-1" style="font-size:14px;"></i>
                                            <span style="font-size:14px" id="{{ 'commentReplyCount_'.$comment->id }}" data-count="{{ $comment->replies_count }}">{{$comment->replies_count}} Replies</span>
                                        </span>
                                </div>
                                <div class="col-12 col-md-4 text-right mt-md-0 mt-2">
                                    @if(Auth::guest())
                                    <button type="button" class="float-right btn btn-sm btn-link text-success" onclick="openLoginModal();"><i class="fas fa-reply mr-2"></i>Reply</button>
                                    @else
                                        @if(Auth::user()->id==$comment->user->id)
                                        <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn  text-primary btn-link editComment" data-opinion="{{ $opinion_id }}" data-commentid="{{ $comment->id  }}"  data-parentid="{{ $comment->parent_id }}" data-media="{{ $comment->media==null?'NONE':$comment->media}}" data-comment="{{ $comment->comment==null?'NONE':$comment->comment}}" data-title="Edit Comment"><i class="fas fa-pencil-alt"></i></button>
                                        <button type="button" class="btn  text-danger btn-link deleteComment"  data-opinion="{{ $opinion_id }}" data-commentid="{{ $comment->id  }}" data-parentid="{{ $comment->parent_id }}"><i class="far fa-trash-alt"></i></button>
                                        </div>
                                        @else
                                        <button type="button" class="text-success float-right btn btn-sm btn-link replyComment" data-opinion="{{ $opinion_id }}" data-parentid="{{ $comment->id }}"  data-title="{{ 'Reply To '.$comment->user['name'] }}"><i class="fas fa-reply mr-2"></i>Reply</button>
                                        @endif
                                    @endif
                                </div>
                            </div>
                    </div>
                </div>
            </div>
</div>
<div class="ml-md-5 ml-0"  id="{{ 'replies_'.$comment->id }}" style="display:none"></div>
