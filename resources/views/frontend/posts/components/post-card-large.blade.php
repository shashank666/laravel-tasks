
        <div class="card flex-md-row h-md-200 shadow-sm mb-4" id="post-{{$post->id}}">
            <a href="/opinion/{{$post->slug}}">
                <div class="flex-auto d-md-block h-100"><img class="responsive-img"  src="{{$post->coverimage}}" alt="{{$post->title}}"  onerror="this.onerror=null;this.src='/img/noimg.png';"></div>
            </a>
            <div class="card-body d-flex flex-column align-items-start justify-content-between p-0">
            <div class="px-3 py-1">
                <h3>
                    <a class="post_title" href="/opinion/{{$post->slug}}">{{  str::limit($post->title,$limit=70,$end='...')}}</a>
                </h3>

                <small class="d-flex  flex-sm-row flex-column justify-content-start text-secondary my-2">
                        <span data-toggle="tooltip" data-placement="top" title="published on {{$post->created_at}}" class="mr-3"><i class="far fa-calendar-alt mr-2"></i>{{$post->created_at}}</span>
                        <span data-toggle="tooltip" data-placement="top"  title="{{$post->readtime}} minute read"  class="mr-3"><i class="far fa-clock mr-2"></i>{{$post->readtime}} min </span>
                        <span data-toggle="tooltip" data-placement="top" title="{{$post->ViewsCount}} Views"><i  class="fas fa-eye mr-2"></i>{{$post->ViewsCount}}</span>
                </small>

              <div class="mt-2">
                  <a class="post-body" href="/opinion/{{$post->slug}}"><p class="mb-0">{!!str::limit($post->plainbody,$limit = 150, $end = '...')!!}</p></a>
                </div>

                <div class="media align-items-center mt-2 mb-1" style="width:100%;">
                    <a class="mr-2" href="{{ route('user_profile',['username' =>$post->user->username])}}"><img class="rounded-circle" src="{{$post->user->image}}" height="40" width="40" alt="Go to the profile of {{ucfirst($post->user->name)}}"  onerror="this.onerror=null;this.src='/img/avatar.png';"></a>
                    <div class="media-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('user_profile',['username' =>$post->user->username])}}" style="color:#212121;">{{ucfirst($post->user->name)}}</a>
                        </div>
                    </div>
                </div>
            </div>


                <div class="d-flex flex-row align-items-center justify-content-between bg-white" style="height:48px;width:100%;border-top: 1px solid rgba(0,0,0,.125);">
                    <div class="pl-3">
                            @if(Auth::guest())
                            <span data-toggle="tooltip" data-placement="top" title="Please login to like this article"  onclick="$('#forgotPasswordModal').modal('show');" style="color:#ff9800;font-size:22px;" class="mr-3">
                                <i  class="far fa-thumbs-up mr-1"></i>
                                <span  style="font-size:18px;" class="likes_count_{{$post->id}}">{{$post->likesCount}}</span>
                            </span>
                            @else
                            <span style="color:#ff9800;font-size:22px;" class="like_post mr-3" id="likepost_{{$post->id}}">
                                    <i class="fas fa-thumbs-up likepost_{{$post->id}}_on" data-toggle="tooltip" data-placement="top" title="Liked" style="display:{{in_array($post->id,$liked_posts)?'inline':'none'}}"></i>
                                    <i class="far fa-thumbs-up likepost_{{$post->id}}_off" data-toggle="tooltip" data-placement="top" title="Like this Article" style="display:{{!in_array($post->id,$liked_posts)?'inline':'none'}}"></i>
                                    <span style="font-size:18px;" class="likes_count_{{$post->id}}" data-count={{$post->likesCount}}>{{$post->likesCount}}</span>
                            </span>
                            @endif

                            <span data-toggle="tooltip" data-placement="top" title="{{$post->commentsCount}} Comments"  style="color:#00acc1;font-size:22px;">
                                <i  class="far fa-comment mr-1"></i>
                                <span style="font-size:18px;" class="comments_count">{{$post->commentsCount}}</span>
                            </span>
                    </div>
                    <div class="pr-3">
                            @if(Auth::guest())
                            <span data-toggle="tooltip" data-placement="top" title="Please login bookmark this article"  onclick="$('#forgotPasswordModal').modal('show');" style="font-size:20px;color:#007bff;margin-right:8px;"><i class="far fa-bookmark"></i></span>
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
    </div>
