@php
$ext = preg_match('/\./', $post->coverimage) ? preg_replace('/^.*\./', '', $post->coverimage) : '';
@endphp
<div class="card h-100 box-shadow">
        <a href="/opinion/{{$post->slug}}"><img class="card-img-top"  src="{{preg_replace('/.[^.]*$/', '',$post->coverimage).'_350x250'.'.'.$ext}}" alt="{{$post->title}}" height="250" width="700"  onerror="this.onerror=null;this.src='/img/noimg.png';"></a>
        <div class="card-body">
            <h4 class="card-title">
                <a class="post_title" href="/opinion/{{$post->slug}}">{!!str::limit($post->title,$limit = 80 , $end = '...')!!}</a>
            </h4>

            <small class="d-flex  flex-sm-row flex-column justify-content-start text-secondary pb-2">
                    <span data-toggle="tooltip" data-placement="top" title="published on {{$post->created_at}}" class="mr-3"><i class="far fa-calendar-alt mr-2"></i>{{$post->created_at}}</span>
                    <span data-toggle="tooltip" data-placement="top"  title="{{$post->readtime}} minute read"  class="mr-3"><i class="far fa-clock mr-2"></i>{{$post->readtime}} min </span>
                    <span data-toggle="tooltip" data-placement="top" title="{{$post->ViewsCount}} Views"><i  class="fas fa-eye mr-2"></i>{{$post->ViewsCount}}</span>
            </small>

            <a  href="/opinion/{{$post->slug}}" class="post-body">
            <p class="card-text mt-2">
                  {!!str::limit($post->plainbody,$limit = 100 , $end = '...')!!}
            </p>
            </a>
            @php
            $extn = preg_match('/\./', $post->user->image) ? preg_replace('/^.*\./', '', $post->user->image) : '';
            $path=$post->user->image;
            $string="/profile";
            $substring="profile-default-opined"
            @endphp
            <div class="media align-items-center mt-2">
                    <a class="mr-2" href="{{ route('user_profile',['username' =>$post->user->username])}}"><img class="rounded-circle" src="@php
    if(strpos($path, $string) !== false || strpos($path, $substring) !== false){
    @endphp
        {{preg_replace('/.[^.]*$/', '',$post->user->image).'_40x40'.'.'.$extn}}
    @php
    }
    else{
    @endphp
    {{$post->user->image}}
    @php
}
    @endphp" height="40" width="40" alt="Go to the profile of {{ucfirst($post->user->name)}}"  onerror="this.onerror=null;this.src='/img/avatar.png';"></a>
                    <div class="media-body">
                        <div class="d-flex justify-content-between align-items-center w-100">
                                <a href="{{ route('user_profile',['username' =>$post->user->username])}}" style="color:#212121;">{{ucfirst($post->user->name)}}</a>
                        </div>
                    </div>
            </div>

        </div>
         <div class="card-footer bg-white">
                <div class="float-left">
                        @if(Auth::guest())
                        <span data-toggle="tooltip" data-placement="top" title="Please login to like this article"  onclick="openLoginModal();" style="color:rgba(36,67,99,255);font-size:22px;" class="mr-3">
                            <i  class="far fa-thumbs-up mr-1"></i>
                            <span  style="font-size:18px;" class="likes_count_{{$post->id}}">{{$post->likesCount}}</span>
                        </span>
                        @else
                        <span style="color:rgba(36,67,99,255);font-size:22px;" class="like_post mr-3" id="likepost_{{$post->id}}">
                                <i class="mr-1 fas fa-thumbs-up likepost_{{$post->id}}_on" data-toggle="tooltip" data-placement="top" title="Liked" style="display:{{in_array($post->id,$liked_posts)?'inline':'none'}}"></i>
                                <i class="mr-1 far fa-thumbs-up likepost_{{$post->id}}_off" data-toggle="tooltip" data-placement="top" title="Like this Article" style="display:{{!in_array($post->id,$liked_posts)?'inline':'none'}}"></i>
                                <span style="font-size:18px;" class="likes_count_{{$post->id}}" data-count={{$post->likesCount}}>{{$post->likesCount}}</span>
                        </span>
                        @endif

                        <span data-toggle="tooltip" data-placement="top" title="{{$post->commentsCount}} Comments"  style="color:rgba(36,67,99,255);font-size:22px;">
                            <i  class="far fa-comment mr-1"></i>
                            <span style="font-size:18px;" class="comments_count">{{$post->commentsCount}}</span>
                        </span>
                             
                </div>
                <div class="float-right">
                    @if(Auth::user())
                        @if( $post->user['id'] == Auth::user()->id)

                        <span  data-toggle="tooltip" data-placement="top" title="Edit Your Opinion" onclick="window.location.href='/opinion/edit/{{$post->slug}}'" style="cursor: pointer; font-size:20px;color:#ff9800;margin-right:8px;"><i class="fas fa-pencil-alt"></i></span>
                        
                        <button type="button" class="btn btn-sm btn-outline-none btn_delete_post" id="delete_{{$post->slug}}" name="delete_{{$post->id}}" style="cursor: pointer; font-size:20px;color:#ff0000;margin-right:8px; vertical-align: baseline;"><i class="far fa-trash-alt mr-2"></i></button>
                       
                        @endif
                    @endif
                        @if(Auth::guest())
                        <span data-toggle="tooltip" data-placement="top" title="Please login bookmark this article"  onclick="openLoginModal();" style="font-size:20px;color:#007bff;margin-right:8px;"><i class="far fa-bookmark"></i></span>
                        @else
                        <span style="color:#007bff;margin-right:8px;" class="bookmark" id="bookmark_{{$post->id}}">
                                <i class="fas fa-bookmark bookmark_{{$post->id}}_on" data-toggle="tooltip" data-placement="top" title="Bookmarked" style="font-size:20px;display:{{in_array($post->id,$bookmarked_posts)?'inline':'none'}}"></i>
                                <i class="far fa-bookmark bookmark_{{$post->id}}_off" data-toggle="tooltip" data-placement="top" title="Bookmark this Article" style="font-size:20px;display:{{!in_array($post->id,$bookmarked_posts)?'inline':'none'}}"></i>
                        </span>
                        @endif
                        <span class="dropdown-toggle share-opinion" id="share-{{$post->id}}" data-toggle="dropdown"  style="font-size:20px;color:#28A755;" aria-haspopup="true" aria-expanded="false"><i class="fas fa-share mr-1" data-toggle="tooltip" data-placement="top" title="Share this Article"></i>{{$post->sharesCount}}</span>
                <div class="dropdown-menu share-menu dropdown-menu-right" id="share-menu-{{$post->id}}" data-post="{{$post->id}}">
                @if(Auth::user())
                        <a class="dropdown-item sharethis" target="_blank" href="https://facebook.com/sharer/sharer.php?u=https://www.weopined.com/opinion/{{$post->slug}}&t={{$post->title}}" style="color:#3b5998" data-post="{{$post->id}}" data-user="{{Auth::user()->id}}" data-plateform="FACEBOOK"><i class="fab fa-facebook mr-2"></i>Share On Facebook</a>
                        <a class="dropdown-item sharethis" target="_blank" href="https://twitter.com/share?url=https://www.weopined.com/opinion/{{$post->slug}}&text={{$post->title}}&via=weopined" style="color:#1da1f2" data-post="{{$post->id}}" data-user="{{Auth::user()->id}}" data-plateform="TWITTER"><i class="fab fa-twitter mr-2"></i>Share On Twitter</a>
                        <a class="dropdown-item sharethis" target="_blank" href="https://www.linkedin.com/shareArticle?mini=true&url=https://www.weopined.com/opinion/{{$post->slug}}&title={{$post->title}}" style="color:#0077b5" data-post="{{$post->id}}" data-user="{{Auth::user()->id}}" data-plateform="LINKEDIN"><i class="fab fa-linkedin mr-2"></i>Share On Linkedin</a>
                        <a class="dropdown-item sharethis" target="_blank" href="https://api.whatsapp.com/send?&text={{$post->title}}.....Read more at Opined https://www.weopined.com/opinion/{{$post->slug}}" style="color:#128c7e" data-post="{{$post->id}}" data-user="{{Auth::user()->id}}" data-plateform="WHATSAPP"><i class="fab fa-whatsapp mr-2"></i>Share On Whatsapp</a>
                @else
                        <a class="dropdown-item sharethis" target="_blank" href="https://facebook.com/sharer/sharer.php?u=https://www.weopined.com/opinion/{{$post->slug}}&t={{$post->title}}" style="color:#3b5998" data-post="{{$post->id}}" data-plateform="FACEBOOK"><i class="fab fa-facebook mr-2"></i>Share On Facebook</a>
                        <a class="dropdown-item sharethis" target="_blank" href="https://twitter.com/share?url=https://www.weopined.com/opinion/{{$post->slug}}&text={{$post->title}}&via=weopined" style="color:#1da1f2" data-post="{{$post->id}}" data-plateform="TWITTER"><i class="fab fa-twitter mr-2"></i>Share On Twitter</a>
                        <a class="dropdown-item sharethis" target="_blank" href="https://www.linkedin.com/shareArticle?mini=true&url=https://www.weopined.com/opinion/{{$post->slug}}&title={{$post->title}}" style="color:#0077b5" data-post="{{$post->id}}" data-plateform="LINKEDIN"><i class="fab fa-linkedin mr-2"></i>Share On Linkedin</a>
                        <a class="dropdown-item sharethis" target="_blank" href="https://api.whatsapp.com/send?&text={{$post->title}}.....Read more at Opined https://www.weopined.com/opinion/{{$post->slug}}" style="color:#128c7e" data-post="{{$post->id}}" data-plateform="WHATSAPP"><i class="fab fa-whatsapp mr-2"></i>Share On Whatsapp</a>
                @endif
                        </div>
                </div>
         </div>
    </div>
