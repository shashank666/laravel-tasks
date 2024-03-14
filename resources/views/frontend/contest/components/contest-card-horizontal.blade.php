@php
$ext = preg_match('/\./', $contest->image) ? preg_replace('/^.*\./', '', $contest->image) : '';
@endphp
<div class="card flex-md-column mb-3 h-lg-250 box-shadow" id="contest-{{$contest->id}}">
            <!-- Image location to be changed -->
    <a href="/contest/{{$contest->slug}}" ><img class="card-img-top flex-auto d-md-block responsive-img" style="width:100%;"  src="{{$contest->image}}" alt="{{$contest->title}}"  onerror="this.onerror=null;this.src='/img/noimg.png';"></a>
    
    <div class="card-body d-flex flex-column align-items-start p-0">
            <h3 class="mb-0 pl-3 pr-3 pt-2" style="max-height:72px;min-height:72px;overflow:hidden;">
                <a class="post_title" href="/contest/{{$contest->slug}}">{!!str::limit($contest->title,$limit = 80,$end = '...')!!}</a>
            </h3>



        <medium class="mr-3 pl-3 pr-3 mt-2 d-flex flex-md-row flex-column justify-content-start text-secondary">
                <span data-toggle="tooltip"  data-placement="top" title="published on {{$contest->created_at}}" class="mr-3">Start Date:   <i class="far fa-calendar-alt mr-2"></i>{{$contest->start_date}}</span><br>
        </medium>
        <medium class="mr-3 pl-3 pr-3 mt-2 d-flex flex-md-row flex-column justify-content-start text-secondary">
                <span data-toggle="tooltip"  data-placement="top" title="published on {{$contest->created_at}}" class="mr-3">
        </medium>

        <medium class="mr-3 pl-3 pr-3 mt-2 d-flex flex-md-row flex-column justify-content-start text-secondary">
                <span data-toggle="tooltip" data-placement="top" title="published on {{$contest->created_at}}" class="mr-5 ">End Date:   <i class="far fa-calendar-alt mr-2"></i>{{$contest->end_date}}</span>
        </medium>

            <div class="mt-2 pl-3 pr-3" style="max-height:50px;min-height:50px;overflow:hidden;">
              <a class="post-body" href="/contest/{{$contest->slug}}"><p>{!!str::limit($contest->body,$limit = 68, $end = '...')!!}</p></a>
            </div>
         
            <!-- Image location to be changed -->
            <div class="bg-white" style="width:100%;border-top: 1px solid rgba(0,0,0,.125);">
                    
                    <div class="float-right pr-3 pt-1">
                            <span class="dropdown-toggle share-opinion" id="share-{{$contest->id}}" data-toggle="dropdown"  style="font-size:20px;color:#28A755;" aria-haspopup="true" aria-expanded="false"><i class="fas fa-share mr-1" data-toggle="tooltip" data-placement="top" title="Share this Article"></i>{{$contest->sharesCount}}</span>
                        <div class="dropdown-menu share-menu dropdown-menu-right" id="share-menu-{{$contest->id}}" data-post="{{$contest->id}}">
                     @if(Auth::user())
                        <a class="dropdown-item sharethis" target="_blank" href="https://facebook.com/sharer/sharer.php?u=https://www.weopined.com/opinion/{{$contest->slug}}&t={{$contest->title}}" style="color:#3b5998" data-post="{{$contest->id}}" data-user="{{Auth::user()->id}}" data-plateform="FACEBOOK"><i class="fab fa-facebook mr-2"></i>Share On Facebook</a>
                        <a class="dropdown-item sharethis" target="_blank" href="https://twitter.com/share?url=https://www.weopined.com/opinion/{{$contest->slug}}&text={{$contest->title}}&via=weopined" style="color:#1da1f2" data-post="{{$contest->id}}" data-user="{{Auth::user()->id}}" data-plateform="TWITTER"><i class="fab fa-twitter mr-2"></i>Share On Twitter</a>
                        <a class="dropdown-item sharethis" target="_blank" href="https://www.linkedin.com/shareArticle?mini=true&url=https://www.weopined.com/opinion/{{$contest->slug}}&title={{$contest->title}}" style="color:#0077b5" data-post="{{$contest->id}}" data-user="{{Auth::user()->id}}" data-plateform="LINKEDIN"><i class="fab fa-linkedin mr-2"></i>Share On Linkedin</a>
                        <a class="dropdown-item sharethis" target="_blank" href="https://api.whatsapp.com/send?&text={{$contest->title}}.....Read more at Opined https://www.weopined.com/opinion/{{$contest->slug}}" style="color:#128c7e" data-post="{{$contest->id}}" data-user="{{Auth::user()->id}}" data-plateform="WHATSAPP"><i class="fab fa-whatsapp mr-2"></i>Share On Whatsapp</a>
                    @else
                        <a class="dropdown-item sharethis" target="_blank" href="https://facebook.com/sharer/sharer.php?u=https://www.weopined.com/opinion/{{$contest->slug}}&t={{$contest->title}}" style="color:#3b5998" data-post="{{$contest->id}}" data-plateform="FACEBOOK"><i class="fab fa-facebook mr-2"></i>Share On Facebook</a>
                        <a class="dropdown-item sharethis" target="_blank" href="https://twitter.com/share?url=https://www.weopined.com/opinion/{{$contest->slug}}&text={{$contest->title}}&via=weopined" style="color:#1da1f2" data-post="{{$contest->id}}" data-plateform="TWITTER"><i class="fab fa-twitter mr-2"></i>Share On Twitter</a>
                        <a class="dropdown-item sharethis" target="_blank" href="https://www.linkedin.com/shareArticle?mini=true&url=https://www.weopined.com/opinion/{{$contest->slug}}&title={{$contest->title}}" style="color:#0077b5" data-post="{{$contest->id}}" data-plateform="LINKEDIN"><i class="fab fa-linkedin mr-2"></i>Share On Linkedin</a>
                        <a class="dropdown-item sharethis" target="_blank" href="https://api.whatsapp.com/send?&text={{$contest->title}}.....Read more at Opined https://www.weopined.com/opinion/{{$contest->slug}}" style="color:#128c7e" data-post="{{$contest->id}}" data-plateform="WHATSAPP"><i class="fab fa-whatsapp mr-2"></i>Share On Whatsapp</a>
                @endif
                                </div>
                    </div>
            </div>

        </div>


</div>
