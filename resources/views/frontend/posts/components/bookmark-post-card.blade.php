
@foreach($posts as $post)
<div class="col-xl-4 col-lg-4 col-md-4 col-sm-6 col-12 portfolio-item mb-4" id="post-{{$post['post']->id}}">
 
       <div class="card h-100 box-shadow">
           <a href="/opinion/{{$post['post']->slug}}"><img class="card-img-top"  src="{{$post['post']->coverimage}}" alt="{{$post['post']->title}}" height="250" width="700"  onerror="this.onerror=null;this.src='/img/noimg.png';"></a>
           <div class="card-body">
               <h4 class="card-title">
                   <a class="post_title" href="/opinion/{{$post['post']->slug}}">{!!str::limit($post['post']->title,$limit = 80 , $end = '...')!!}</a>
               </h4>
       
               <small class="d-flex  flex-sm-row flex-column justify-content-start text-secondary pb-2">
                       <span data-toggle="tooltip" data-placement="top" title="published on {{$post['post']->created_at}}" class="mr-3"><i class="far fa-calendar-alt mr-2"></i>{{Carbon\Carbon::parse($post['post']->created_at)->format('jS F Y')}}</span>
                       <span data-toggle="tooltip" data-placement="top"  title="{{$post['post']->readtime}} minute read"  class="mr-3"><i class="far fa-clock mr-2"></i>{{$post['post']->readtime}} min </span>
                       <span data-toggle="tooltip" data-placement="top" title="{{$post['post']->ViewsCount}} Views"><i  class="fas fa-eye mr-2"></i>{{$post['post']->ViewsCount}}</span>
               </small>
       
               <a  href="/opinion/{{$post['post']->slug}}" class="post-body">
               <p class="card-text mt-2">
                     {!!str::limit($post['post']->plainbody,$limit = 100 , $end = '...')!!}
               </p>
               </a>        
           </div>
            <div class="card-footer bg-white">
                   
                   <div class="float-right"> 
                       <span style="font-size:20px;color:#007bff;margin-right:8px;" class="bookmark" id="bookmark_{{$post['post']->id}}">
                               <i class="fas fa-bookmark bookmark_{{$post['post']->id}}_on" data-toggle="tooltip" data-placement="top" title="Bookmarked" style="display:inline"></i>
                               <i class="far fa-bookmark bookmark_{{$post['post']->id}}_off" data-toggle="tooltip" data-placement="top" title="Bookmark this Article" style="display:none"></i>
                       </span>

                      {{--   @if(Auth::user() && Auth::user()->id!==$post->user_id)
                       <span class="report-button" id="report_{{$post->id}}" data-toggle="tooltip" data-placement="top" title="Report an Issue with this Opinion" style="font-size:20px;color:#dc3545;margin-right:6px"><i class="far fa-flag"></i></span>
                       @endif  --}}
                      
                       <span class="dropdown-toggle share-opinion" id="share-{{$post['post']->id}}" data-toggle="dropdown"  style="font-size:20px;color:#28A755;" aria-haspopup="true" aria-expanded="false"><i class="fas fa-share" data-toggle="tooltip" data-placement="top" title="Share this Article"></i></span>
                        <div class="dropdown-menu share-menu" id="share-menu-{{$post['post']->id}}" data-post="{{$post['post']->id}}">
                        
                        @if(Auth::user())
                           <a class="dropdown-item sharethis" target="_blank" href="https://facebook.com/sharer/sharer.php?u=https://www.weopined.com/opinion/{{$post['post']->slug}}&t={{$post['post']->title}}" style="color:#3b5998" data-post="{{$post['post']->id}}" data-user="{{Auth::user()->id}}" data-plateform="FACEBOOK"><i class="fab fa-facebook mr-2"></i>Share On Facebook</a>
                           <a class="dropdown-item sharethis" target="_blank" href="https://twitter.com/share?url=https://www.weopined.com/opinion/{{$post['post']->slug}}&text={{$post['post']->title}}&via=weopined" style="color:#1da1f2" data-post="{{$post['post']->id}}" data-user="{{Auth::user()->id}}" data-plateform="TWITTER"><i class="fab fa-twitter mr-2"></i>Share On Twitter</a>
                           <a class="dropdown-item sharethis" target="_blank" href="https://www.linkedin.com/shareArticle?mini=true&url=https://www.weopined.com/opinion/{{$post['post']->slug}}&title={{$post['post']->title}}" style="color:#0077b5" data-post="{{$post['post']->id}}" data-user="{{Auth::user()->id}}" data-plateform="LINKEDIN"><i class="fab fa-linkedin mr-2"></i>Share On Linkedin</a>
                           <a class="dropdown-item sharethis" target="_blank" href="https://api.whatsapp.com/send?&text={{$post['post']->title}}.....Read more at Opined https://www.weopined.com/opinion/{{$post['post']->slug}}" style="color:#128c7e" data-post="{{$post['post']->id}}" data-user="{{Auth::user()->id}}" data-plateform="WHATSAPP"><i class="fab fa-whatsapp mr-2"></i>Share On Whatsapp</a> 
                        @else
                        <a class="dropdown-item sharethis" target="_blank" href="https://facebook.com/sharer/sharer.php?u=https://www.weopined.com/opinion/{{$post['post']->slug}}&t={{$post['post']->title}}" style="color:#3b5998" data-post="{{$post['post']->id}}" data-plateform="FACEBOOK"><i class="fab fa-facebook mr-2"></i>Share On Facebook</a>
                           <a class="dropdown-item sharethis" target="_blank" href="https://twitter.com/share?url=https://www.weopined.com/opinion/{{$post['post']->slug}}&text={{$post['post']->title}}&via=weopined" style="color:#1da1f2" data-post="{{$post['post']->id}}" data-plateform="TWITTER"><i class="fab fa-twitter mr-2"></i>Share On Twitter</a>
                           <a class="dropdown-item sharethis" target="_blank" href="https://www.linkedin.com/shareArticle?mini=true&url=https://www.weopined.com/opinion/{{$post['post']->slug}}&title={{$post['post']->title}}" style="color:#0077b5" data-post="{{$post['post']->id}}" data-plateform="LINKEDIN"><i class="fab fa-linkedin mr-2"></i>Share On Linkedin</a>
                           <a class="dropdown-item sharethis" target="_blank" href="https://api.whatsapp.com/send?&text={{$post['post']->title}}.....Read more at Opined https://www.weopined.com/opinion/{{$post['post']->slug}}" style="color:#128c7e" data-post="{{$post['post']->id}}" data-plateform="WHATSAPP"><i class="fab fa-whatsapp mr-2"></i>Share On Whatsapp</a> 
                        @endif
                       </div>
                   </div>
                   
            </div>
</div>
</div>
@endforeach        
   
