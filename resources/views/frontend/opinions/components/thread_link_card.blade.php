 
 @if($opinion->links!==null)
    @php($links=json_decode($opinion->links))
    
    @foreach($links as $link)
  
    @if($link->status=='OK')  
            @if($link->type=='link')
            <div class="card bg-white mb-2 border-0">
                <a href="{{$link->url}}" target="_blank" title="{{$link->title!==null?$link->title:''}}" style="color:#495057">
                    @if($link->image!=null)
                        <img class="link-img card-img" src="{{$link->image}}" alt="{{$link->title!==null?$link->title:'image'}}" onerror="this.onerror=null;this.src='/img/noimg.png';">
                            <div class="card-img-overlay" style="background:linear-gradient(rgba(0,0,0,.2),rgba(0,0,0,.2));">
                                <div class="link_overlay">
                                        @if($link->title!==null)<h5 class="card-title text-white mb-1">{{$link->title}}</h5>@endif
                                        <p class="card-text">
                                        @if($link->providerIcon!==null)
                                        <img src="{{$link->providerIcon}}" width="30" height="30" class="mr-2"/>
                                        @endif
                                        @if($link->providerUrl!==null)<small>{{$link->providerUrl}}</small>@endif
                                        </p>
                                </div>
                            </div>
                    @else
                        <div class="card-body bg-white mb-2">
                            @if($link->title!==null)<h5 class="card-title text-dark mb-1">{{$link->title}}</h5>@endif
                            <p class="card-text">
                            @if($link->providerIcon!==null)
                            <img src="{{$link->providerIcon}}" width="30" height="30" class="mr-2"/>
                            @endif
                            @if($link->providerUrl!==null)<small>{{$link->providerUrl}}</small>@endif
                        </div> 
                    @endif
                </a>
            </div>
            @elseif($link->type=='photo' || $link->type=='image')
                <a href="{{$link->url}}" target="_blank" title="{{$link->title!==null?$link->title:''}}" class="mb-2" style="color:#495057">
                <img class="card-img img-fluid" src="{{$link->image}}"/>
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
                <div class="card bg-white mb-2">
                    <a href="{{$link->url}}" target="_blank" title="{{$link->title!==null?$link->title:''}}">
                    <div class="card-body bg-white">
                        @if($link->title!==null)<h5 class="card-title text-dark mb-1">{{$link->title}}</h5>@endif
                        @if($link->description!==null)<p class="card-text text-secondary mb-1" style="font-size:14px;">{{str::limit($link->description,60,'...')}}</p>@endif
    
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