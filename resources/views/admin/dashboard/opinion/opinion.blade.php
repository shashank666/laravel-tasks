@foreach($opinions as $opinion)
<div class="card" id="opinion_{{$opinion->id}}">
    <div class="card-header">
        Opinion ID : {{$opinion->id}}
        <span class="text-right">
        @if($opinion->platform=='website')
        <span class="badge badge-primary"><i class="fas fa-globe"></i></span>
        @else
        <span class="badge badge-success"><i class="fab fa-android"></i></span>
        @endif
       </span>
    </div>
    <div class="card-body">
          <div class="mb-3">
            <div class="row align-items-center">
              <div class="col-auto">
                @if($opinion->user)
                <a href="{{ route('admin.user_details',['id'=>$opinion->user['id']]) }}">
                    <img class="rounded-circle" src="{{$opinion->user['image']}}"  alt="{{ucfirst($opinion->user['name'])}}" height="50" width="50" onerror="this.onerror=null;this.src='/img/avatar.png';"/>
                </a>
                @else
                    <img class="rounded-circle" src="/img/avatar.png"  alt="USER NOT FOUND" height="50" width="50" />
                @endif
              </div>
              <div class="col ml-n2">
                    <h4 class="card-title mb-1">
                        @if($opinion->user)
                        <a href="{{ route('admin.user_details',['id'=>$opinion->user['id']]) }}">{{ucfirst($opinion->user['name'])}}</a>
                        @else
                        <a href="javascript:void(0);">USER NOT FOUND</a>
                        @endif
                    </h4>

                    <p class="card-text small text-muted">
                    <span class="fe fe-clock"></span>
                    {{Carbon\Carbon::parse($opinion->created_at)->toDayDateTimeString()}}
                    </p>
              </div>
              <div class="col-auto">
                  @if($opinion->is_active==1)
                    <span class="is_active is_active_1 badge badge-success  p-2" style="display:{{ $opinion->is_active==1 ? 'block':'none' }}" id = "activity_{{ $opinion->id }}">Active</span>
                  @else
                    <span class="is_active is_active_0 badge badge-danger p-2" style="display:{{ $opinion->is_active==0 ? 'block':'none' }}" id = "activity_{{ $opinion->id }}">Disabled</span>
                  @endif
              </div>
            </div>
          </div>

          <div>
                <p>{!!$opinion->body!!}</p>
                @if($opinion->links!==null)
                    @php($links=json_decode($opinion->links))

                    @foreach($links as $link)
                        @if($link->status=='OK')
                                @if($link->type=='link')
                                    <div>
                                        <a href="{{$link->url}}" target="_blank" title="{{$link->title!==null?$link->title:''}}">
                                            @if($link->image!=null)
                                                <img class="img-fluid rounded mb-2" src="{{$link->image}}" alt="{{$link->title!==null?$link->title:'image'}}" style="max-width:600px;max-height:600px" onerror="this.onerror=null;this.src='/img/noimg.png';">
                                                <div>
                                                    @if($link->title!==null)
                                                    <h4 class="card-title my-2">{{$link->title}}</h4>
                                                    @endif
                                                    <p class="card-text">
                                                    @if($link->providerIcon!==null)
                                                    <img src="{{$link->providerIcon}}" width="30" height="30" class="mr-2"/>
                                                    @endif
                                                    @if($link->providerUrl!==null)<small>{{$link->providerUrl}}</small>@endif
                                                    </p>
                                                </div>
                                            @else
                                                <div>
                                                    @if($link->title!==null)
                                                    <h4 class="card-title my-2">{{$link->title}}</h4>
                                                    @endif
                                                    <p class="card-text">
                                                    @if($link->providerIcon!==null)
                                                    <img src="{{$link->providerIcon}}" width="30" height="30"/>
                                                    @endif
                                                    @if($link->providerUrl!==null)<small>{{$link->providerUrl}}</small>@endif
                                                </div>
                                            @endif
                                        </a>
                                    </div>
                                @elseif($link->type=='photo' || $link->type=='image')
                                    <a href="{{$link->url}}" target="_blank" title="{{$link->title!==null?$link->title:''}}" class="my-2">
                                        <img class="img-fluid rounded" src="{{$link->image}}" style="max-with:600px;max-height:600px"/>
                                    </a>
                                @elseif($link->type=='video')
                                        <a href="{{$link->url}}" target="_blank" class="text-dark">
                                            <p>{{$link->title!==null?$link->title:$link->url}}</p>
                                        </a>

                                        @if($link->code!==null)
                                        <div class="embed-responsive embed-responsive-4by3 mb-2">
                                        {!!$link->code!!}
                                        </div>
                                        @endif

                                @elseif($link->type=='rich')
                                    <div>
                                        <a href="{{$link->url}}" target="_blank" title="{{$link->title!==null?$link->title:''}}">
                                        <div class="card-body bg-white">
                                            @if($link->title!==null)
                                            <h4 class="card-title">{{$link->title}}</h4>
                                            @endif
                                            @if($link->description!==null)<p class="card-text" style="font-size:14px;">{{str::limit($link->description,60,'...')}}</p>@endif
                                            <p class="card-text mt-1">
                                            @if($link->providerIcon!==null)
                                            <img src="{{$link->providerIcon}}" width="30" height="30" class="mr-2"/>
                                            @endif
                                            @if($link->providerUrl!==null)<small>{{$link->providerUrl}}</small>@endif
                                        </div>
                                        </a>
                                    </div>
                                @endif
                        @else
                                <a href="{{$link->url}}" target="_blank" class="mb-2">{{$link->url}}</a>
                        @endif
                    @endforeach
                @endif
          </div>

          @if($opinion->cover_type!="none")
          <div class="row">
              <div class="col-sm-12">

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
                                      <img class="d-block w-100 rounded" src="{{$image}}" height="350"/>
                                  </div>
                              @endforeach
                              </div>

                              <a class="carousel-control-prev" href="#carouselIndicators-{{$opinion->id}}" role="button" data-slide="prev"><span class="carousel-control-prev-icon" aria-hidden="true"></span><span class="sr-only">Previous</span></a>
                              <a class="carousel-control-next" href="#carouselIndicators-{{$opinion->id}}" role="button" data-slide="next"><span class="carousel-control-next-icon" aria-hidden="true"></span><span class="sr-only">Next</span></a>
                          </div>
                      @else
                          {{--<img class="img-fluid rounded" src="{{$opinion->cover}}"  style="max-height:600px;max-width:600px"/>--}}
                          <img class="img-fluid rounded" src="{{$opinion->cover}}"/>
                      @endif

                  @endif
              </div>
          </div>
          @endif

    </div>
    <!--<div class="card-footer">
            <div class="row">
                    <div class="col">
                      <a href="" class="btn btn-white">
                          {{$opinion->likesCount}} Likes
                      </a>
                      <a href="" class="btn btn-white">
                          {{$opinion->commentsCount}} Comments
                      </a>
                    </div>
                    <div class="col-auto">
                            @if($opinion->is_active==1)
                            <button data-id="{{ $opinion->id }}" data-event="enable" data-action="{{  route('admin.update_opinion_visibility',['id'=>$opinion->id]) }}"   class="btn-opinion btn btn-warning btn-sm">Disable Opinion</button>
                            @else
                            <button data-id="{{ $opinion->id }}" data-event="disable"  data-action="{{ route('admin.update_opinion_visibility',['id'=>$opinion->id])  }}" class="btn-opinion btn btn-success btn-sm">Enable Opinion</button>
                            @endif
                            <button data-id="{{ $opinion->id }}" data-event="delete"  data-action="{{ route('admin.delete_opinion',['id'=>$opinion->id]) }}" class="btn-opinion btn btn-danger btn-sm">Delete Opinion</button>
                    </div>
            </div>
    </div>-->
    <div class="card-footer">
            <div class="row">
                    <div class="col">
                      <!--<a href="" class="btn btn-white">
                          {{$opinion->likesCount}} Likes
                      </a>
                      <a href="" class="btn btn-white">
                          {{$opinion->commentsCount}} Comments
                      </a>
                    -->
                      <span class="btn btn-white btn-opinion-likes" style="margin-bottom: 1rem" data-opinion="{{$opinion->id}}" data-toggle="tooltip" data-placement="top" title="Total Likes"><i class="far fa-thumbs-up mr-1 like-icon-opined"></i>
                        <span class="align-top like-icon-text">
                        {{$opinion->likesCount}} Likes
                        </span>
                </span>
                <span class="btn btn-white btn-opinion-shares" style="margin-bottom: 1rem" data-opinion="{{$opinion->id}}" data-toggle="tooltip" data-placement="top" title="Total Shares"><i class="fa fa-share-alt mr-1" aria-hidden="true"></i>
                        <span class="align-top like-icon-text">
                        {{$opinion->shares_Count}} Shares
                        </span>
                </span>
                      <span class="comment  showComment btn btn-white" data-opinion="{{$opinion->id}}" data-toggle="tooltip" data-placement="top" title="Comment">
                            <i class="far fa-comments mr-1 comment-icon-opined"></i>
                            <span class="align-top comment-icon-text" id="comment_count_{{$opinion->id}}">{{$opinion->commentsCount}} Comments</span>
                    </span>
                      
                    </div>

                    @if($opinion->user)
                      @if($opinion->user['id']>4405 && $opinion->user['id']<4706)
                     <span>Dummy</span>
                      @endif
                    @endif
                      <span id="like_{{$opinion->id}}" class="like">
                        <span id="like_{{$opinion->id}}_off"  data-placement="top" title="Like">
                        <i class="far fa-thumbs-up mr-1 like-icon-opined"></i>
                        </span>
                        <span id="like_{{$opinion->id}}_on"  style="display:none"  data-placement="top" title="Liked">
                        <i class="fas fa-thumbs-up mr-1 like-icon-opined"></i>
                        </span>
                      </span>
                    <div class="col-auto">
                            @if($opinion->is_active==1)
                            <button data-id="{{ $opinion->id }}" class="btn-opinion btn btn-warning btn-sm disable" data-toggle="tooltip" data-placement="top" title="Disable Opinion" id = "disabled_{{ $opinion->id }}">Disable Opinion</button>
                            @else
                            <button data-id="{{ $opinion->id }}" class="btn-opinion btn btn-success btn-sm disable" data-toggle="tooltip" data-placement="top" title="Enable Opinion" id = "disabled_{{ $opinion->id }}">Enable Opinion</button>
                            @endif
                            
                            <button  id="delete_{{$opinion->id}}" class="btn-opinion btn btn-danger btn-sm  delete" data-toggle="tooltip" data-placement="top" title="Delete Opinion">Delete Opinion</button>
                            
          <!--{{--
                            @if($opinion->is_active==1)
                            <button data-id="{{ $opinion->id }}" data-event="enable" data-action="{{  route('admin.update_opinion_visibility',['id'=>$opinion->id]) }}"   class="btn-opinion btn btn-warning btn-sm" id = "disabled_{{ $opinion->id }}">Disable Opinion</button>
                            @else
                            <button data-id="{{ $opinion->id }}" data-event="disable"  data-action="{{ route('admin.update_opinion_visibility',['id'=>$opinion->id])  }}" class="btn-opinion btn btn-success btn-sm" id = "disabled_{{ $opinion->id }}">Enable Opinion</button>
                            @endif
                            <button  data-id="{{ $opinion->id }}" data-event="delete"  data-action="{{ route('admin.delete_opinion',['id'=>$opinion->id]) }}" class="btn-opinion btn btn-danger btn-sm confirmation" id="delete_opinion">Delete Opinion</button>
                            <span id="delete_{{$opinion->id}}" class="float-right  ml-3 delete text-danger" data-toggle="tooltip" data-placement="top" title="Delete Opinion">
                              <i class="far fa-trash-alt" style="font-size:20px;cursor:pointer"></i>
                          </span>
                          <span data-id="{{ $opinion->id }}" class="float-right  ml-3 disable text-danger" data-toggle="tooltip" data-placement="top" title="Disable Opinion">
                              <i class="far fa-trash-alt" style="font-size:20px;cursor:pointer"></i>
                          </span>--}}-->
                    </div>
            </div>
            @include('admin.dashboard.opinion.comments.add_comment')
                
            <div class="comments_div_admin{{ $opinion->id }}"></div>
            
    </div>
</div>
@endforeach
@include('frontend.opinions.crud.delete')