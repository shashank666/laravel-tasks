

    <div class="card mb-4 box-shadow" id="post-{{$post->id}}">
     <a href="/opinion/{{$post->slug}}"><img class="card-img-top" src="{{$post->coverimage}}" alt="{{$post->title}}" height="300" width="500"  onerror="this.onerror=null;this.src='/img/noimg.png';"></a>
    <div class="card-body">
          <a class="post_title" href="/opinion/{{$post->slug}}"><h3 class="card-title">{{$post->title}}</h3></a>
        
        <div class="mb-2 text-secondary border-bottom border-top pt-2 pb-2 d-flex flex-lg-row flex-md-column flex-sm-column flex-column justify-content-between text-secondary">
            <span><i class="far fa-calendar-alt mr-2"></i>{{$post->created_at}}</span>
            <span><i class="far fa-clock mr-2"></i>{{$post->readtime}} min read</span>
            <span> <i class="fas fa-eye mr-2"></i>{{$post->ViewsCount}} Views</span> 
            <span> <i class="far fa-thumbs-up mr-2"></i>{{$post->likesCount}} Likes</span> 
            <span><i class="far fa-comments mr-2"></i>{{$post->commentsCount}} Comments</span> 
        </div>

        <div class="mt-3 mb-2 lead">
        Topics  :&nbsp;&nbsp;
        @foreach($post->categories as $index=>$category)
            <a href="/topic/{{$category->slug}}" style="color:#212121">{{$category->name}}</a>
            @if($index!=count($post->categories)-1),
            @endif
        @endforeach 
        </div>
  <a href="/opinion/{{$post->slug}}" class="post-body"><p class="card-text">{!!str::limit($post->plainbody,$limit = 260, $end = '...')!!}</p></a>
   </div>
    <div class="card-footer bg-white">
            <div class="float-left">
                    <button  id="share-{{$post->id}}"  type="button" class="btn btn-outline-success dropdown-toggle share-opinion" data-toggle="dropdown"  aria-haspopup="true" aria-expanded="false"><i class="fas fa-share mr-2"></i>{{$post->likesCount}} Share</button>
               
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
        @if(Auth::user() && $post->user->id==Auth::user()->id)
            <div class="float-right">
            <button type="button" class="btn btn-sm btn-outline-primary" onclick="window.location.href='/opinion/edit/{{$post->slug}}'"><i class="fas fa-pencil-alt mr-2"></i>Edit</button>
            <button type="button" class="btn btn-sm btn-outline-danger btn_delete_post" id="delete_{{$post->slug}}" name="delete_{{$post->id}}"><i class="far fa-trash-alt mr-2"></i>Delete</button>
            </div>
        @endif
    </div>
 </div>
