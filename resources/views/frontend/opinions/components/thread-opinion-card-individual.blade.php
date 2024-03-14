@php
$ext = preg_match('/\./', $user['image']) ? preg_replace('/^.*\./', '', $user['image']) : '';
$path=$user['image'];
$string="/storage/profile";
$substring="avatar_thumb"
@endphp
<div class="card shadow-sm my-4" id="opinion_{{$opinion->id}}">
        <div class="card-header opinion-header" style="border-top-left-radius: 20px;border-top-right-radius:20px">
                <div class="media align-items-center">
                    <a href="{{ route('user_profile', ['username' => $user['username']]) }}" data-toggle="tooltip" data-placement="right" title="Go to the profile of {{ucfirst($user['name'])}}"><img class="rounded-circle" src="@php
    if(strpos($path, $string) !== false || strpos($path, $substring) !== false){
    @endphp
        {{preg_replace('/.[^.]*$/', '',$user['image']).'_40x40'.'.'.$ext}}
    @php
    }
    else{
    @endphp
    {{$user['image']}}
    @php
}
    @endphp" height="40" width="40" alt="{{ucfirst($user['name'])}}" onerror="this.onerror=null;this.src='/img/avatar.png';"/></a>
                     <div class="media-body">
                        <div class="d-flex justify-content-between align-items-bottom w-100">
                             <span class="ml-2"><a href="{{ route('user_profile', ['username' => $user['username']]) }}" style="color:#212121;">{{ucfirst($user['name'])}}</a></span>
                             <span class="text-secondary">
                             <small style="cursor:default;" data-toggle="tooltip" data-placement="top" title="{{Carbon\Carbon::parse($opinion->created_at)->toDayDateTimeString()}}">
                                {{Carbon\Carbon::parse($opinion->created_at)->toFormattedDateString()}}
                              </small>
                             </span>
                        </div>
                     </div>
            </div>
        </div>
        <div class="card-body opinion-body" style="overflow-x:hidden;overflow-y:hidden">
            <p>{!!nl2br($opinion->body)!!}</p>
              @include('frontend.opinions.components.thread_link_card')

            @if($opinion->cover_type!="none")
            <div class="row">
                <div class="col-12">
                    @if($opinion->cover_type=='YOUTUBE')
                    <div class="embed-responsive embed-responsive-4by3">
                    <iframe class="embed-responsive-item" src="https://www.youtube.com/embed/{{$opinion->cover}}"></iframe>
                    </div>

                    @elseif($opinion->cover_type=='VIDEO')
                    <video  class='video-js  vjs-default-skin  vjs-16-9' controls preload='auto' data-setup='{"fluid": true}'  {{ $opinion->thumbnail!=null?'poster='.$opinion->thumbnail.'':''}}>
                        <source src="{{route('stream_video',['video_name'=>basename($opinion->cover)])}}" type='video/mp4'>
                    </video>

                     @elseif($opinion->cover_type=='EMBED')
                    <div class="embed-responsive embed-responsive-4by3">
                    <iframe class="embed-responsive-item" srcdoc="{{$opinion->cover}}"></iframe>
                    </div>

                    @elseif($opinion->cover_type=='GIF')
                    <img class="img-fluid rounded" src="{{$opinion->cover}}" height="auto" width="auto"/>
                    @else

                        @php($arr=explode(',',$opinion->cover))
                        @if(count($arr)>1)
                            <div id="carouselIndicators-{{$opinion->id}}" class="carousel slide" data-ride="carousel">
                                <ol class="carousel-indicators">
                                    @foreach(explode(',',$opinion->cover) as $index=>$image)
                                    <li data-target="#carouselIndicators-{{$opinion->id}}" data-slide-to="0" class="{{$index==0?'active':''}}"></li>
                                    @endforeach
                                </ol>

                                <div class="carousel-inner">
                                @foreach(explode(',',$opinion->cover) as $index=>$image)
                                    <div class="carousel-item {{$index==0?'active':''}}">
                                        <img class="d-block w-100 rounded" src="{{$image}}" height="350" alt="{{str_replace('#',' ',$opinion->hash_tags)}}"/>
                                    </div>
                                @endforeach
                                </div>

                                <a class="carousel-control-prev" href="#carouselIndicators-{{$opinion->id}}" role="button" data-slide="prev"><span class="carousel-control-prev-icon" aria-hidden="true"></span><span class="sr-only">Previous</span></a>
                                <a class="carousel-control-next" href="#carouselIndicators-{{$opinion->id}}" role="button" data-slide="next"><span class="carousel-control-next-icon" aria-hidden="true"></span><span class="sr-only">Next</span></a>
                            </div>
                        @else
                            <img class="img-fluid rounded" src="{{$opinion->cover}}" height="auto" width="auto" alt="{{str_replace('#',' ',$opinion->hash_tags)}}"/>
                        @endif

                    @endif
                </div>
            </div>
            @endif

        </div>
         <div class="card-footer opinion-footer" style="border-bottom-left-radius: 20px;border-bottom-right-radius:20px">

            {{-- Like & Comment --}}
            <div class="like_comment_div">
                @if(Auth::guest())

                <span class="like_guest" data-placement="top" title="Please Login To Like"><i class="far fa-arrow-alt-circle-up mr-1 like-icon-opined"></i>
                        @if($opinion->AgreeCount>1)
                        <span class="align-top like-icon-text">{{$opinion->AgreeCount}} Agrees</span>
                        @else
                        <span class="align-top like-icon-text">{{$opinion->AgreeCount}} Agree</span>
                        @endif
                </span>

                <span class="dislike_guest" data-placement="top" title="Please Login To Disagree"><i class="far fa-arrow-alt-circle-down mr-1 like-icon-opined"></i>
                        @if($opinion->DisagreeCount>1)
                        <span class="align-top like-icon-text">{{$opinion->DisagreeCount}} Disagrees</span>
                        @else
                        <span class="align-top like-icon-text">{{$opinion->DisagreeCount}} Disagree</span>
                        @endif
                </span>
                
                <span class="mr-4 showComment" data-opinion="{{$opinion->id}}"  data-placement="top" title="Please Login To Comment"><i class="far fa-comments mr-1 comment-icon-opined"></i>
                        <span class="align-top comment-icon-text" id="comment_count_{{$opinion->id}}">
                        {{$opinion->commentsCount}}
                        </span>
                </span>

                @else

                {{-- Agree Part------ --}}
                    {{-- Agree Icons--}}
                    <span id="like_{{$opinion->id}}" class="like" >
                        <span id="like_{{$opinion->id}}_off" style="display:{{in_array($opinion->id,$liked)?'none':'inline'}}"  data-placement="top" title="Agree">
                        <i class="far fa-arrow-alt-circle-up mr-1 like-icon-opined"></i>
                        </span>
                        <span id="like_{{$opinion->id}}_on"  style="display:{{in_array($opinion->id,$liked)?'inline':'none'}}"  data-placement="top" title="Agreed">
                        <i class="fas fa-arrow-alt-circle-up like-icon-opined"></i>
                        </span>
                    </span>
                    {{-- Agree Text --}}
                     @if($opinion->AgreeCount>1)
                        <span class="align-top like-icon-text btn-opinion-likes-count mr-4" style="cursor: grab;" id="agree_count_{{$opinion->id}}" data-opinion="{{$opinion->id}}"  data-placement="top" title="See Likes">{{$opinion->AgreeCount}} Agrees</span>
                        @else
                        <span class="align-top like-icon-text btn-opinion-likes-count mr-4" style="cursor: grab;" id="agree_count_{{$opinion->id}}" data-opinion="{{$opinion->id}}"  data-placement="top" title="See Likes">{{$opinion->AgreeCount}} Agree</span>
                     @endif


                {{-- Disagree Part----- --}}
                     {{-- DisAgree Icons--}}
                     <span id="dislike_{{$opinion->id}}" class="dislike">
                        <span id="dislike_{{$opinion->id}}_off" style="display:{{in_array($opinion->id,$disliked)?'none':'inline'}}"  data-placement="top" title="Disagree">
                            <i class="far fa-arrow-alt-circle-down mr-1 like-icon-opined"></i>
                            </span>

                        <span id="dislike_{{$opinion->id}}_on"  style="display:{{in_array($opinion->id,$disliked)?'inline':'none'}}"  data-placement="top" title="Disagreed">
                            <i class="fas fa-arrow-alt-circle-down mr-1 like-icon-opined"></i>
                            </span>
                    </span>
                    {{-- DisAgree Text --}}
                     @if($opinion->DisagreeCount>1)
                        <span class="align-top like-icon-text btn-opinion-likes-count mr-4" style="cursor: grab;" id="disagree_count_{{$opinion->id}}" data-opinion="{{$opinion->id}}"  data-placement="top" title="See DisLikes">{{$opinion->DisagreeCount}} Disagrees </span>
                     @else
                        <span class="align-top like-icon-text btn-opinion-likes-count mr-4" style="cursor: grab;" id="disagree_count_{{$opinion->id}}" data-opinion="{{$opinion->id}}"  data-placement="top" title="See DisLikes">{{$opinion->DisagreeCount}} Disagree </span>

                     @endif



                     {{-- Comments Icon --}}
                    <span class="comment mr-4 showComment" data-opinion="{{$opinion->id}}" data-placement="top" title="Comment">
                            <i class="far fa-comments mr-1 comment-icon-opined"></i>
                            <span class="align-top comment-icon-text" id="comment_count_{{$opinion->id}}">{{$opinion->commentsCount}}</span>
                    </span>

                    {{-- If the Post Belongs to current user --}}
                   

                @endif
                <span class="dropdown-toggle share-opinion float-right" id="share-{{$opinion->id}}" data-toggle="dropdown"  style="font-size:18px;color:#28A755;" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-share mr-1" data-toggle="tooltip" data-placement="top" title="Share this Opinion"></i>{{$opinion->sharesCount}}
                </span>

                <div class="dropdown-menu share-menu dropdown-menu-right" id="share-menu-{{$opinion->id}}">
                @if(Auth::user())
                <a class="dropdown-item sharethis" target="_blank" href="https://facebook.com/sharer/sharer.php?u=http://www.weopined.com/{{ '@'.$opinion->user['username'].'/opinion/'.$opinion->uuid}}" style="color:#3b5998"  data-opinion="{{$opinion->id}}" data-user="{{Auth::user()->id}}" data-plateform="FACEBOOK"><i class="fab fa-facebook mr-2"></i>Share On Facebook</a>
                <a class="dropdown-item sharethis" target="_blank" href="https://twitter.com/share?url=http://www.weopined.com/{{ '@'.$opinion->user['username'].'/opinion/'.$opinion->uuid}}&via=weopined" style="color:#1da1f2"  data-opinion="{{$opinion->id}}" data-user="{{Auth::user()->id}}" data-plateform="TWITTER"><i class="fab fa-twitter mr-2"></i>Share On Twitter</a>
                <a class="dropdown-item sharethis" target="_blank" href="https://www.linkedin.com/shareArticle?mini=true&url=http://www.weopined.com/{{ '@'.$opinion->user['username'].'/opinion/'.$opinion->uuid}}" style="color:#0077b5"  data-opinion="{{$opinion->id}}" data-user="{{Auth::user()->id}}" data-plateform="LINKEDIN"><i class="fab fa-linkedin mr-2"></i>Share On Linkedin</a>
                <a class="dropdown-item sharethis" target="_blank" href="https://api.whatsapp.com/send?&text=http://www.weopined.com/{{ '@'.$opinion->user['username'].'/opinion/'.$opinion->uuid}}" style="color:#128c7e"  data-opinion="{{$opinion->id}}" data-user="{{Auth::user()->id}}" data-plateform="WHATSAPP"><i class="fab fa-whatsapp mr-2"></i>Share On Whatsapp</a>
                <a class="embed-opinion dropdown-item sharethis" href="javascript:void(0);" data-url="https://www.weopined.com/{{ '@'.$opinion->user['username'].'/opinion/'.$opinion->uuid.'/share'}}" style="color:#37474f" data-opinion="{{$opinion->id}}" data-user="{{Auth::user()->id}}" data-plateform="EMBED"><i class="fas fa-code mr-2" style="font-size:14px"></i>Embed</a>
        @else
                <a class="dropdown-item sharethis" target="_blank" href="https://facebook.com/sharer/sharer.php?u=http://www.weopined.com/{{ '@'.$opinion->user['username'].'/opinion/'.$opinion->uuid}}" style="color:#3b5998"  data-opinion="{{$opinion->id}}" data-plateform="FACEBOOK"><i class="fab fa-facebook mr-2"></i>Share On Facebook</a>
                <a class="dropdown-item sharethis" target="_blank" href="https://twitter.com/share?url=http://www.weopined.com/{{ '@'.$opinion->user['username'].'/opinion/'.$opinion->uuid}}&via=weopined" style="color:#1da1f2"  data-opinion="{{$opinion->id}}" data-plateform="TWITTER"><i class="fab fa-twitter mr-2"></i>Share On Twitter</a>
                <a class="dropdown-item sharethis" target="_blank" href="https://www.linkedin.com/shareArticle?mini=true&url=http://www.weopined.com/{{ '@'.$opinion->user['username'].'/opinion/'.$opinion->uuid}}" style="color:#0077b5"  data-opinion="{{$opinion->id}}" data-plateform="LINKEDIN"><i class="fab fa-linkedin mr-2"></i>Share On Linkedin</a>
                <a class="dropdown-item sharethis" target="_blank" href="https://api.whatsapp.com/send?&text=http://www.weopined.com/{{ '@'.$opinion->user['username'].'/opinion/'.$opinion->uuid}}" style="color:#128c7e"  data-opinion="{{$opinion->id}}" data-plateform="WHATSAPP"><i class="fab fa-whatsapp mr-2"></i>Share On Whatsapp</a>
                <a class="embed-opinion dropdown-item sharethis" href="javascript:void(0);" data-url="https://www.weopined.com/{{ '@'.$opinion->user['username'].'/opinion/'.$opinion->uuid.'/share'}}" style="color:#37474f" data-opinion="{{$opinion->id}}" data-plateform="EMBED"><i class="fas fa-code mr-2" style="font-size:14px"></i>Embed</a>
        @endif
                </div>
				<!--<div style="color:red;font-size:10px;text-align:right"><a href="https://www.weopined.com" target="_blank">weopined.com </a></div>-->
            </div>
            @if(Auth::user() && Auth::user()->id==$opinion->user->id)
            <span id="delete_{{$opinion->id}}" class="float-right  ml-3 delete text-danger"  data-placement="top" title="Delete Opinion">
                <i class="far fa-trash-alt" style="font-size:20px;cursor:pointer"></i>
            </span>
            <small class="flex-sm-row flex-column  text-secondary pb-2 mt-2" alignment='end'>
           <span data-toggle="tooltip" data-placement="top" title="{{$opinion->views}} Impressions"><i  class="fas fa-eye mr-2"></i>{{$opinion->views}}</span>
           </small>
            @endif
            
            <div class="row pl-3 bg-light pt-2">
                <p class=" text-dark font-weight-bold"> What is Your Opinion on this? </p>
                <span class="col showComment comment-icon-text" id="comment_count_{{$opinion->id}}">
                    @if($opinion->commentsCount>1)
                    <p class="d-flex flex-row-reverse text-dark clickableText "> {{$opinion->commentsCount}} Opinions </p>
                    @elseif($opinion->commentsCount===1)
                    <p class="d-flex flex-row-reverse text-dark clickableText "> {{$opinion->commentsCount}} Opinion </p>
                    @else 
                    <p class="d-flex flex-row-reverse text-dark clickableText "> No Opinions Yet </p>
                    @endif
                    </span>
                
            </div>
            
            <div>
                @include('frontend.opinions.comments.add_comment')
                <div class="comments_div_{{ $opinion->id }}"></div>
            </div>
         </div>
</div>
