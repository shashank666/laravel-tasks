@php($colors=['#37474f','#ff9800','#0097a7','#5c007a','#00796b','#d32f2f','#5d4037','#827717','#689f38','#e91e63','#424242','#1565c0','#880e4f','#f57f17'])
<div class="card shadow-sm my-4" style="position: relative;display: -ms-flexbox;display: flex;-ms-flex-direction: column;flex-direction: column;min-width: 0;word-wrap: break-word;background-color: #fff;background-clip: border-box;border: 1px solid rgba(0,0,0,.125);border-radius: .25rem;">
        <div class="card-header opinion-header" style="padding: .75rem 1.25rem;margin-bottom: 0;background-color: #ff980036;border-bottom: 1px solid rgba(0,0,0,.125);">
                <div class="media align-items-center" style="align-items: center!important;display: flex;">
                     <div class="media-body" style="flex: 1;">
                        <div class="d-flex justify-content-between align-items-bottom w-100" style="width: 100%!important;justify-content: space-between!important;display: flex!important;">
                             <span class="ml-2" style="margin-left: .5rem!important;"><a href="{{ route('user_profile', ['username' => $opinion->user['username']]) }}" style="color:{{ $colors[array_rand($colors,1)] }}">{{$opinion->user['name']}}</a></span>
                             <!--<span class="text-secondary">
                             <small style="cursor:default;" data-toggle="tooltip" data-placement="top" title="{{Carbon\Carbon::parse($opinion->created_at)->toDayDateTimeString()}}">
                                {{Carbon\Carbon::parse($opinion->created_at)->toFormattedDateString()}}
                              </small>
                             </span>-->
                        </div>
                     </div>
            </div>
        </div>
        <div class="card-body opinion-body" style="overflow-x:hidden;overflow-y:hidden; text-align: left;flex: 1 1 auto;padding: 1.25rem;">
            <p style="margin-top: 0;
    margin-bottom: 1rem;">{{$opinion->plain_body}}</p>
              

            @if($opinion->cover_type!="none")
            <div class="row">
                <div class="col-12">
                    @if($opinion->cover_type=='YOUTUBE')
                    <div class="embed-responsive embed-responsive-4by3">
                    <iframe class="embed-responsive-item" src="https://www.youtube.com/embed/{{$opinion->cover}}"></iframe>
                    </div>

                    @elseif($opinion->cover_type=='VIDEO')

                   <img class="img-fluid rounded" src="{{$opinion->thumbnail}}" height="auto" width="auto"/>
                    <!--<video  class='video-js  vjs-default-skin  vjs-16-9' controls preload='auto' data-setup='{"fluid": true}'  {{ $opinion->thumbnail!=null?'poster='.$opinion->thumbnail.'':''}}>
                        <source src="{{route('stream_video',['video_name'=>basename($opinion->cover)])}}" type='video/mp4'>
                    </video>-->

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
                                        <img class="d-block w-100 rounded" src="{{$image}}" height="350"/>
                                    </div>
                                @endforeach
                                </div>

                                <a class="carousel-control-prev" href="#carouselIndicators-{{$opinion->id}}" role="button" data-slide="prev"><span class="carousel-control-prev-icon" aria-hidden="true"></span><span class="sr-only">Previous</span></a>
                                <a class="carousel-control-next" href="#carouselIndicators-{{$opinion->id}}" role="button" data-slide="next"><span class="carousel-control-next-icon" aria-hidden="true"></span><span class="sr-only">Next</span></a>
                            </div>
                        @else
                            <img class="img-fluid rounded" src="{{$opinion->cover}}" height="auto" width="auto"/>
                        @endif

                    @endif
                </div>
            </div>
            @endif