@php
$ext = preg_match('/\./', $user['image']) ? preg_replace('/^.*\./', '', $user['image']) : '';
$path=$user['image'];
$string="/storage/profile";
$substring="profile-default-opined"
@endphp
<div class="card shadow-sm my-2" id="opinion_{{$opinion->id}}">

    <!-- Image location to be changed -->
    <div class="card-header opinion-header">
            <div class="media align-items-center">
                <a href="{{ route('user_profile', ['username' => $user['username']]) }}" data-toggle="tooltip" data-placement="right" title="Go to the profile of {{ucfirst($user['name'])}}"><img class="rounded-circle" src="
    @php
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
    @endphp" height="40" width="40" alt="{{ucfirst($user['name'])}}" onerror="this.onerror=null;this.src='/img/avatar_thumb.png';"/></a>
                <div class="media-body">
                    <div class="d-flex justify-content-between align-items-center w-100">
                        <span class="ml-2"><a href="{{ route('user_profile', ['username' => $user['username']]) }}" style="color:#212121;">{{ucfirst($user['name'])}}</a><br/>
                        <span style="font-size:10px">{{'@'.$user['username']}}</span></span>
                        <span class="text-secondary" data-toggle="tooltip" data-placement="top" title="{{Carbon\Carbon::parse($opinion->created_at)->toDayDateTimeString()}}" style="cursor:default;font-size:12px">
                            {{Carbon\Carbon::parse($opinion->created_at)->toFormattedDateString()}}
                        </span>
                    </div>
                </div>
        </div>
    </div>
    @php
    $ext_opinion = preg_match('/\./', $opinion->cover) ? preg_replace('/^.*\./', '', $opinion->cover) : '';
    @endphp
    <div class="card-body opinion-body" style="overflow-x:hidden;overflow-y:hidden; cursor:pointer">
        <p onclick="window.location.href='{{ '/@'.$opinion->user['username'].'/opinion/'.$opinion->uuid}}'">{!!nl2br($opinion->body)!!}</p>
        @include('frontend.opinions.components.thread_link')
        @if($opinion->cover_type!="none")
        <div class="row">
            <div class="col-12">
                @if($opinion->cover_type=='YOUTUBE')
                <div class="embed-responsive embed-responsive-4by3">
                <iframe class="embed-responsive-item" src="https://www.youtube.com/embed/{{$opinion->cover}}"></iframe>
                </div>

                @elseif($opinion->cover_type=='VIDEO')
                <video  class='video-js  vjs-default-skin  vjs-16-9' controls preload='auto' data-setup='{"fluid": true}'>
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
                        <img class="img-fluid rounded" src="{{preg_replace('/.[^.]*$/', '',$opinion->cover).'_314x240'.'.'.$ext_opinion}}" height="auto" width="auto" alt="{{str_replace('#',' ',$opinion->hash_tags)}}"/>
                    @endif

                @endif
            </div>
        </div>
        @endif

    </div>
    {{-- <div class="card-footer opinion-footer">
        <span class="dropdown-toggle share-opinion float-right" id="share-{{$opinion->id}}" data-toggle="dropdown"  style="font-size:18px;color:#28A755;" aria-haspopup="true" aria-expanded="false" name = "test">
            <i class="fas fa-share mr-1" data-toggle="tooltip" data-placement="top" title="Share this Opinion"></i>{{$opinion->sharesCount}}
        </span>
    
    </div> --}}
</div>
