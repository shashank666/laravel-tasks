<!--<style>
    .swal-button {background-color: #ff9800!important;} .blog hr:first-child{margin-top:0}.blog img{max-width:660px;margin:0 15px 15px 0;position:relative}.blog img:not(:last-child),.blog p:not(:last-child),.blog table:not(:last-child){margin-bottom:24px}.blog blockquote{display:block;border-width:2px 0;border-style:solid;border-color:#eee;padding:1.5em 0;font-size: larger;text-align: center;font-style: italic;margin:1.5em 0;position:relative}.blog blockquote::before{content:'\201C';position:absolute;top:0;left:50%;transform:translate(-50%,-50%);background:#fff;width:3rem;height:2rem;font:6em/1.08em 'PT Sans',sans-serif;color:#666;text-align:center}.blog blockquote::after{content:"\2013 \2003" attr(cite);display:none;text-align:right;font-size:.875em}.blog ol,.blog pre,.blog ul{background-color:#f3f3f5;color:#212121;padding:16px}.blog ol li,.blog ul li{margin-left:16px;margin-right:16px}.blog ol li:not(:first-child),.blog ul li:not(:first-child){margin-top:8px}@media (max-width:575px){.opinion-title{font-size:1.5rem}.opinion-cover{width:100%;height:250px}.blog img{width:100%;height:250px}}@media (min-width:576px) and (max-width:767px){.opinion-title{font-size:1.75rem}.blog img{width:100%;height:250px}}@media (min-width:768px) and (max-width:991px){.opinion-title{font-size:2rem}.blog img{width:100%;height:350px}}@media (min-width:992px) and (max-width:1199px){.opinion-title{font-size:2rem}.opinion-cover{width:100%;height:350px}.blog img{max-width:100%;height:350px}}@media (min-width:1200px){.opinion-title{font-size:2.5rem}.opinion-cover{width:100%;height:450px}.blog img{max-width:100%;height:350px}
</style>-->
<style>
    .swal-button {background-color: #ff9800!important;} .blog hr:first-child{margin-top:0}.blog img{max-width:660px;margin:0 15px 15px 0;position:relative}.blog img:not(:last-child),.blog p:not(:last-child),.blog table:not(:last-child){margin-bottom:24px}.blog blockquote{display:block;border-width:2px 0;border-style:solid;border-color:#eee;padding:1.5em 0;font-size: larger;text-align: center;font-style: italic;margin:1.5em 0;position:relative}.blog blockquote::before{content:'\201C';position:absolute;top:0;left:50%;transform:translate(-50%,-50%);background:#fff;width:3rem;height:2rem;font:6em/1.08em 'PT Sans',sans-serif;color:#666;text-align:center}.blog blockquote::after{content:"\2013 \2003" attr(cite);display:none;text-align:right;font-size:.875em}.blog ol,.blog pre,.blog ul{background-color:#f3f3f5;color:#212121;padding:16px}.blog ol li,.blog ul li{margin-left:16px;margin-right:16px}.blog ol li:not(:first-child),.blog ul li:not(:first-child){margin-top:8px}@media (max-width:575px){.opinion-title{font-size:1.5rem}.opinion-cover{width:100%;height:250px}.blog img{width:100%;}}@media (min-width:576px) and (max-width:767px){.opinion-title{font-size:1.75rem}.blog img{width:100%;}}@media (min-width:768px) and (max-width:991px){.opinion-title{font-size:2rem}.blog img{width:100%;}}@media (min-width:992px) and (max-width:1199px){.opinion-title{font-size:2rem}.opinion-cover{width:100%;height:350px}.blog img{max-width:100%;}}@media (min-width:1200px){.opinion-title{font-size:2.5rem}.opinion-cover{width:100%;height:450px}.blog img{max-width:100%;}
</style>
<style type="text/css">
    .medium-insert-images.medium-insert-images-left{    
    max-width: 33.33%;
    float: left;
    margin: 0 30px 0 0;
    text-align: center;
    }
    .medium-insert-images figure{
        position: relative;
    }
    .medium-insert-images.medium-insert-images-right{    
    max-width: 33.33%;
    float: right;
    margin: 0 20px 0 30px;
    text-align: center;
    }
    .medium-insert-images-grid{
    display: flex;
    -ms-flex-wrap: wrap;
    flex-wrap: wrap;
    -ms-flex-align: start;
    align-items: flex-start;
    -ms-flex-pack: center;
    justify-content: center;
    margin: 0.5em -0.5em;
    }
    .medium-insert-images-grid figure{
    width: 33.33%;
    display: inline-block;
    }
    .medium-insert-images-grid figure img{
    max-width: calc(100% - 1em);
    margin: 0.5em;
    }
    body{
        font-size: 1.2rem;
    }
    ::-moz-selection { /* Code for Firefox */
      
      background: #ff980052;
    }

    ::selection {
      
      background: #ff980052;
    }
</style>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script>
        $(window).on('load',function(){
            $('.blog-post span').removeAttr('style');
        });
        $(document).ready(function(){
            $(".disabled").click(function(){
            swal("We are sorry!", "Please Fill All The Required Fields Before Publishing The Article", "error");
            });
    });
</script>

@if($post->status != 1 && $post->user['id'] == Auth::user()->id)
<div class="blog">
    @php
    $counttopic = 0;
    @endphp
    <h1 class="mt-4 opinion-title" style="font-family: 'Lora', serif;">{{$post->title}}</h1>
    <div class="mt-2 mb-3">
            TOPICS  :&nbsp;&nbsp;
            @foreach($post->categories as $index=>$category)
                <a href="/topic/{{$category->slug}}" class="badge badge-light font-weight-normal p-2 mr-2 mt-1" style="color:#ff9800;font-size:14px;">{{$category->name}}</a>
                @php
                $counttopic = ++$index;
                @endphp
            @endforeach

    </div>
    <div class="media align-items-top">
            <a href="{{ route('user_profile', ['username' => $post->user['username']]) }}" data-toggle="tooltip" data-placement="right" title="Go to the profile of {{ucfirst($post->user['name'])}}"><img src="{{$post->user['image']==null?'/storage/profile/avatar.jpg':$post->user['image']}}"  class="rounded-circle mr-3" height="48" width="48" style="min-width:40px;max-width:40px;min-height:40px;max-height:40px;" alt="Go to the profile of {{ucfirst($post->user['name'])}}" onerror="this.onerror=null;this.src='/img/avatar.png';"></a>
            <div class="media-body">
                <div class="d-flex justify-content-between align-items-center w-100">
                   <div>
                     <a href="{{ route('user_profile', ['username' => $post->user['username']]) }}" class="lead text-dark">{{ucfirst($post->user['name'])}}</a>
                    <!-- @if(Auth::guest())
                            <button class="btn btn-sm btn-outline-success followbtn ml-2">Follow <span><i class="fas fa-user-plus ml-2"></i><span></button>
                        @else
                            @if( $post->user['id'] != Auth::user()->id)
                            <button data-userid="{{ $post->user['id'] }}" class="followbtn followbtn_{{ $post->user['id'] }} ml-2 btn btn-sm btn-outline-success" style="display:{{!in_array($post->user['id'],$followingids)?'inline':'none'}}" data-toggle="tooltip" data-placement="top" title="Follow">Follow <span><i class="fas fa-user-plus ml-2"></i><span></button>
                            <button data-userid="{{ $post->user['id'] }}" class="followingbtn followingbtn_{{$post->user['id']}} ml-2 btn btn-sm btn-success" style="display:{{in_array($post->user['id'],$followingids)?'inline':'none'}}">Following <span><i class="fas fa-check ml-2"></i></span></button>
                           @endif
                    @endif-->
                   </div>
                    <div>
                        
                            @if(str_word_count($post->plainbody)<300 || str_word_count($post->plainbody)>1200 || strlen($post->coverimage)<10 || $counttopic<1)
                              <button data-toggle="tooltip" data-placement="top" title="Publish Now" class="btn btn-success mb-md-0 mb-2 disabled" onclick="" >Publish Your Article<span class="ml-2"><i class="fas fa-check"></i></span></button>
                              @else
                               <button data-toggle="tooltip" data-placement="top" title="Publish Now" class="btn btn-success mb-md-0 mb-2" onclick="window.location.href='{{route('publish',['slug' => $post->slug])}}'" >Publish Your Article<span class="ml-2"><i class="fas fa-check"></i></span></button>
                              @endif
                              <button class="btn btn-warning mb-md-0 mb-2" data-toggle="tooltip" data-placement="top" title="Edit Your Opinion" onclick="window.location.href='/opinion/edit/{{$post->slug}}'">Edit<span class="ml-2"><i class="fas fa-pencil-alt" style="fontsize:20px;"></i></span></button>
                              
                    </div>
                </div>
            </div>
    </div>


        <div class="mb-3 mt-2 text-secondary border-bottom border-top pt-3 pb-3 d-flex flex-md-row flex-column justify-content-between">
        <span><i class="far fa-calendar-alt mr-2"></i>{{$post->created_at}}</span>
        <span><i class="far fa-clock mr-2"></i>{{$post->readtime}} min read</span>
       <span> <i class="fas fa-eye mr-2"></i>0 Views</span>
        <span> <i class="far fa-thumbs-up mr-2"></i><span class="likes_count_{{$post->id}}" data-count={{$post->likesCount}}>0</span> Likes</span>
        <span><a href="#comments" class="text-secondary"><i class="far fa-comments mr-2"></i>0 Comments</a></span>
        </div>


    <img class="img-fluid rounded opinion-cover" src="{{$post->coverimage}}" alt="{{$post->title}}" onerror="this.onerror=null;this.src='/img/noimg.png';" />

    <div class="lead d-flex flex-md-row flex-column align-items-center justify-content-between">
        
        <div class="text-md-left text-sm-center">
            <span style="color:#ff9800;cursor:pointer;" class="like_post mr-2" id="">
                    <i class="fas fa-thumbs-up likepost_{{$post->id}}_on" data-toggle="tooltip" data-placement="top" title="Liked" style="font-size:28px;display:{{in_array($post->id,$liked_posts)?'inline':'none'}}"></i>
                    <i class="far fa-thumbs-up likepost_{{$post->id}}_off" data-toggle="tooltip" data-placement="top" title="Like this Opinion" style="font-size:28px;display:{{!in_array($post->id,$liked_posts)?'inline':'none'}}"></i>
            </span>

            <span class="" style="font-size:18px;font-weight:bold;color:#ff9800;cursor:pointer;" data-posturl="">
                <span  class="" data-count="">0</span>
                <span> People Liked</span>
            </span>
            <span style="font-size:18px;font-weight:bold;color:#244363;cursor:pointer;">
                <span>0 Share</span>
            </span>
        </div>
        <div class="social-share text-center mt-md-0 mt-4 share-menu" data-post="{{$post->id}}">
        @if(Auth::user())
        <a class="btn btn-circle btn-twitter waves-effect waves-circle waves-float mr-2 disabled" role="button" href="" data-post="{{$post->id}}" data-user="{{Auth::user()->id}}" data-plateform="TWITTER"><span><i class="fab fa-twitter"></i></span></a>
        <a class="btn btn-circle btn-facebook waves-effect waves-circle waves-float mr-2 disabled" role="button" href="" data-post="{{$post->id}}" data-user="{{Auth::user()->id}}" data-plateform="FACEBOOK"><span><i class="fab fa-facebook-f"></i></span></a>
        <a class="btn btn-circle btn-linkedin waves-effect waves-circle waves-float mr-2 disabled" role="button" href="" data-post="{{$post->id}}" data-user="{{Auth::user()->id}}" data-plateform="LINKEDIN"><span><i class="fab fa-linkedin-in"></i></span></a>
        <a class="btn btn-circle btn-whatsapp  waves-effect waves-circle waves-float disabled" role="button" href="" data-post="{{$post->id}}" data-user="{{Auth::user()->id}}" data-plateform="WHATSAPP"><span><i class="fab fa-whatsapp"></i></span></a>
        @else
        <a class="btn btn-circle btn-twitter waves-effect waves-circle waves-float mr-2 disabled" role="button" href="" data-post="{{$post->id}}" data-plateform="TWITTER"><span><i class="fab fa-twitter"></i></span></a>
        <a class="btn btn-circle btn-facebook waves-effect waves-circle waves-float mr-2 disabled" role="button" href="" data-post="{{$post->id}}" data-plateform="FACEBOOK"><span><i class="fab fa-facebook-f"></i></span></a>
        <a class="btn btn-circle btn-linkedin waves-effect waves-circle waves-float mr-2 disabled" role="button" href="" data-post="{{$post->id}}" data-plateform="LINKEDIN"><span><i class="fab fa-linkedin-in"></i></span></a>
        <a class="btn btn-circle btn-whatsapp  waves-effect waves-circle waves-float disabled" role="button" href="" data-post="{{$post->id}}" data-plateform="WHATSAPP"><span><i class="fab fa-whatsapp"></i></span></a>
        @endif
        </div>
        
        
    </div>

    <hr>
    <div class="text-justify blog-post" style="font-family: 'Lora', serif;">
    {!!$post->body!!}
    </div>

    @if(count($post->keywords)>0)
    <div class="mb-1 mt-2">
        <span >Keywords :</span>
        @foreach($post->keywords as $keyword)
        <span class="badge badge-light p-2 mb-2">{{ $keyword->name}}</span>
        @endforeach
    </div>
    @endif

    @if(count($post->threads)>0)
    <div class="mb-1 mt-2">
     <span >Tags :</span>
     @foreach($post->threads as $thread)
     <a href="/thread/{{$thread->name}}" title="{{'#'.$thread->name}}" class="badge badge-success p-2 mb-2">{{'#'.$thread->name}}</a>
     @endforeach
    </div>
    @endif

    <div id="like-share" class="mt-4">
        <div class="d-flex flex-md-row flex-column align-items-center justify-content-between">
                <div class="text-md-left text-sm-center">
            <span style="color:#ff9800;cursor:pointer;" class="like_post mr-2" id="">
                    <i class="fas fa-thumbs-up likepost_{{$post->id}}_on" data-toggle="tooltip" data-placement="top" title="Liked" style="font-size:28px;display:{{in_array($post->id,$liked_posts)?'inline':'none'}}"></i>
                    <i class="far fa-thumbs-up likepost_{{$post->id}}_off" data-toggle="tooltip" data-placement="top" title="Like this Opinion" style="font-size:28px;display:{{!in_array($post->id,$liked_posts)?'inline':'none'}}"></i>
            </span>

            <span class="" style="font-size:18px;font-weight:bold;color:#ff9800;cursor:pointer;" data-posturl="">
                <span  class="" data-count="">0</span>
                <span> People Liked</span>
            </span>
            <span style="font-size:18px;font-weight:bold;color:#244363;cursor:pointer;">
                <span>0 Share</span>
            </span>
        </div>
                        @if(str_word_count($post->plainbody)<300 || str_word_count($post->plainbody)>1200 || strlen($post->coverimage)<10 || $counttopic<1)
                          <button data-toggle="tooltip" data-placement="top" title="Publish Now" class="btn btn-success mb-md-0 mb-2 disabled" onclick="" >Publish Your Article<span class="ml-2"><i class="fas fa-check"></i></span></button>
                          @else
                           <button data-toggle="tooltip" data-placement="top" title="Publish Now" class="btn btn-success mb-md-0 mb-2" onclick="window.location.href='{{route('publish',['slug' => $post->slug])}}'" >Publish Your Article<span class="ml-2"><i class="fas fa-check"></i></span></button>
                          @endif
                          <button class="btn btn-warning mb-md-0 mb-2" data-toggle="tooltip" data-placement="top" title="Edit Your Opinion" onclick="window.location.href='/opinion/edit/{{$post->slug}}'">Edit<span class="ml-2"><i class="fas fa-pencil-alt" style="fontsize:20px;"></i></span></button>
                    <div class="social-share text-center mt-md-0 mt-5 share-menu" data-post="{{$post->id}}">
                    @if(Auth::user())
                        <a class="btn btn-circle btn-twitter waves-effect waves-circle waves-float mr-2 disabled" role="button" href="" data-post="{{$post->id}}" data-user="{{Auth::user()->id}}" data-plateform="TWITTER"><span><i class="fab fa-twitter"></i></span></a>
                        <a class="btn btn-circle btn-facebook waves-effect waves-circle waves-float mr-2 disabled" role="button" href="" data-post="{{$post->id}}" data-user="{{Auth::user()->id}}" data-plateform="FACEBOOK"><span><i class="fab fa-facebook-f"></i></span></a>
                        <a class="btn btn-circle btn-linkedin waves-effect waves-circle waves-float mr-2 disabled" role="button" href="" data-post="{{$post->id}}" data-user="{{Auth::user()->id}}" data-plateform="LINKEDIN"><span><i class="fab fa-linkedin-in"></i></span></a>
                        <a class="btn btn-circle btn-whatsapp  waves-effect waves-circle waves-float disabled" role="button" href="" data-post="{{$post->id}}" data-user="{{Auth::user()->id}}" data-plateform="WHATSAPP"><span><i class="fab fa-whatsapp"></i></span></a>
                        @else
                        <a class="btn btn-circle btn-twitter waves-effect waves-circle waves-float mr-2 disabled" role="button" href="" data-post="{{$post->id}}" data-plateform="TWITTER"><span><i class="fab fa-twitter"></i></span></a>
                        <a class="btn btn-circle btn-facebook waves-effect waves-circle waves-float mr-2 disabled" role="button" href="" data-post="{{$post->id}}" data-plateform="FACEBOOK"><span><i class="fab fa-facebook-f"></i></span></a>
                        <a class="btn btn-circle btn-linkedin waves-effect waves-circle waves-float mr-2 disabled" role="button" href="" data-post="{{$post->id}}" data-plateform="LINKEDIN"><span><i class="fab fa-linkedin-in"></i></span></a>
                        <a class="btn btn-circle btn-whatsapp  waves-effect waves-circle waves-float disabled" role="button" href="" data-post="{{$post->id}}" data-plateform="WHATSAPP"><span><i class="fab fa-whatsapp"></i></span></a>
                        @endif
                 </div>
                        
            </div>
    </div>
    <hr/>

    <input type="hidden" id="postid" value="{{$post->id}}" />
</div>

<div class="card shadow-sm mb-5">
    <div class="card-body">
        <div class="d-flex flex-md-row flex-column align-items-center justify-content-start">
            <div style="min-width:100px;max-width:100px;width:100px;min-height:100px;max-height:100px;height:100px;">
                    <a href="{{ route('user_profile', ['username' => $post->user['username']]) }}">
                        <img src="{{ $post->user['image']!=null?$post->user['image']:'/img/avatar.png' }}" alt="{{ $post->user['name'] }}" onerror="this.onerror=null;this.src='/img/avatar.png';" style="width:100px;height:100px;margin:0px;border-radius:50%;"/>
                    </a>
            </div>

            <div class="pl-md-3 pl-0 flex-fill">
                <a href="{{ route('user_profile', ['username' => $post->user['username']]) }}">
                <p class="text-md-left text-center text-secondary font-weight-bold mb-1">WRITTEN BY</p>
                <h4 class="text-md-left text-center mb-0" style="color:#212121;">{{  $post->user['name'] }}</h4>
                <p class="text-md-left text-center text-secondary" style="font-size:16px;">{{ $post->user['bio']==null?'':$post->user['bio']}}</p>
                </a>
            </div>

            <div>
                @if(Auth::guest())
                        <button class="followbtn btn btn-sm btn-outline-success ml-2">Follow <span><i class="fas fa-user-plus ml-2"></i><span></button>
                @else
                        @if( $post->user['id'] != Auth::user()->id)
                        <button data-userid="{{ $post->user['id'] }}" class="followbtn followbtn_{{ $post->user['id'] }} ml-2 btn btn-sm btn-outline-success" style="display:{{!in_array($post->user['id'],$followingids)?'inline':'none'}}" data-toggle="tooltip" data-placement="top" title="Follow">Follow <span><i class="fas fa-user-plus ml-2"></i><span></button>
                        <button data-userid="{{ $post->user['id'] }}" class="followingbtn followingbtn_{{$post->user['id']}} ml-2 btn btn-sm btn-success" style="display:{{in_array($post->user['id'],$followingids)?'inline':'none'}}">Following <span><i class="fas fa-check ml-2"></i></span></button>
                        @endif
                @endif
            </div>

        </div>
    </div>
</div>
    @elseif($post->status == 2 || $post->status == 0)
    <script>
    newLocation();
    function newLocation() {
        window.location="/404";
    }
  </script>
    @else
    <div class="blog">
    <h1 class="mt-4 opinion-title" style="font-family: 'Lora', serif;">{{$post->title}}</h1>
    <div class="mt-2 mb-3">
            TOPICS  :&nbsp;&nbsp;
            @foreach($post->categories as $index=>$category)
                <a href="/topic/{{$category->slug}}" class="badge badge-light font-weight-normal p-2 mr-2 mt-1" style="color:#ff9800;font-size:14px;">{{$category->name}}</a>
            @endforeach
    </div>

    <div class="media align-items-top">
            <a href="{{ route('user_profile', ['username' => $post->user['username']]) }}" data-toggle="tooltip" data-placement="right" title="Go to the profile of {{ucfirst($post->user['name'])}}">

                @if($post->user['image']!=null)
             @php
              $ext = preg_match('/\./', $post->user['image']) ? preg_replace('/^.*\./', '', $post->user['image']) : '';
              $path=$post->user['image'];
$string="/profile";
$substring="profile-default-opined"
              @endphp
              <img class="rounded-circle" src="@php
    if(strpos($path, $string) !== false || strpos($path, $substring) !== false){
    @endphp
    {{preg_replace('/.[^.]*$/', '',$post->user['image']).'_100x100'.'.'.$ext}}
    @php
    }
    else{
    @endphp
    {{$post->user['image']}}
    @php
}
    @endphp" alt="{{$post->user['name']}}"  height="100" width="100" onerror="this.onerror=null;this.src='/img/avatar.png';" class="rounded-circle mr-3" height="48" width="48" style="min-width:40px;max-width:40px;min-height:40px;max-height:40px;" alt="Go to the profile of {{ucfirst($post->user['name'])}}" onerror="this.onerror=null;this.src='/img/avatar.png';">
            
            @endif


                <!--<img src="{{$post->user['image']==null?'/storage/profile/avatar.jpg':$post->user['image']}}"  class="rounded-circle mr-3" height="48" width="48" style="min-width:40px;max-width:40px;min-height:40px;max-height:40px;" alt="Go to the profile of {{ucfirst($post->user['name'])}}" onerror="this.onerror=null;this.src='/img/avatar.png';">--></a>
            <div class="media-body">
                <div class="d-flex justify-content-between align-items-center w-100">
                   <div>
                     <a href="{{ route('user_profile', ['username' => $post->user['username']]) }}" class="lead text-dark">{{ucfirst($post->user['name'])}}</a>
                     @if(Auth::guest())
                            <button class="btn btn-sm btn-outline-success followbtn ml-2">Follow <span><i class="fas fa-user-plus ml-2"></i><span></button>
                        @else
                            @if( $post->user['id'] != Auth::user()->id)
                            <button data-userid="{{ $post->user['id'] }}" class="followbtn followbtn_{{ $post->user['id'] }} ml-2 btn btn-sm btn-outline-success" style="display:{{!in_array($post->user['id'],$followingids)?'inline':'none'}}" data-toggle="tooltip" data-placement="top" title="Follow">Follow <span><i class="fas fa-user-plus ml-2"></i><span></button>
                            <button data-userid="{{ $post->user['id'] }}" class="followingbtn followingbtn_{{$post->user['id']}} ml-2 btn btn-sm btn-success" style="display:{{in_array($post->user['id'],$followingids)?'inline':'none'}}">Following <span><i class="fas fa-check ml-2"></i></span></button>
                           @endif
                    @endif
                   </div>
                    <div>
                            @if(Auth::guest())
                            <span data-toggle="tooltip" data-placement="top" title="Please Login Bookmark this Opinion"  style="font-size:20px;"  class="text-info mr-2"><i class="far fa-bookmark"></i></span>
                            <span data-toggle="tooltip" data-placement="top" title="Please Login to Report an Issue with this Opinion" style="font-size:20px;" class="text-danger"><i class="far fa-flag"></i></span>
                            @else
                                <span class="mr-2 text-info bookmark" id="bookmark_{{$post->id}}">
                                        <i class="fas fa-bookmark bookmark_{{$post->id}}_on" data-toggle="tooltip" data-placement="top" title="Bookmarked" style="display:{{in_array($post->id,$bookmarked_posts)?'inline':'none'}}"></i>
                                        <i class="far fa-bookmark bookmark_{{$post->id}}_off" data-toggle="tooltip" data-placement="top" title="Bookmark this Opinion" style="display:{{!in_array($post->id,$bookmarked_posts)?'inline':'none'}}"></i>
                                </span>
                                @if( $post->user['id'] != Auth::user()->id)
                                    <span class="text-danger report-button" id="report_{{$post->id}}" data-toggle="tooltip" data-placement="top" title="Report an Issue with this Opinion"><i class="far fa-flag" style="fontsize:20px;"></i></span>
                                @else
                                    <span class="text-dark" data-toggle="tooltip" data-placement="top" title="Edit Your Opinion" onclick="window.location.href='/opinion/edit/{{$post->slug}}'"><i class="fas fa-pencil-alt" style="fontsize:20px;"></i></span>
                                @endif
                            @endif
                    </div>
                </div>
            </div>
    </div>


    <div class="mb-3 mt-2 text-secondary border-bottom border-top pt-3 pb-3 d-flex flex-md-row flex-column justify-content-between">
        <span><i class="far fa-calendar-alt mr-2"></i>{{$post->created_at}}</span>
        <span><i class="far fa-clock mr-2"></i>{{$post->readtime}} min read</span>
        <span> <i class="fas fa-eye mr-2"></i>{{$post->ViewsCount}} Views</span>
        <span> <i class="far fa-thumbs-up mr-2"></i><span class="likes_count_{{$post->id}}" data-count={{$post->likesCount}}>{{$post->likesCount}}</span> Likes</span>
        <span><a href="#comments" class="text-secondary"><i class="far fa-comments mr-2"></i>{{$post->commentsCount}} Comments</a></span>
    </div>

                    @php
                    $ext_opinion = preg_match('/\./', $post->coverimage) ? preg_replace('/^.*\./', '', $post->coverimage) : '';
                    @endphp
                    <img class="img-fluid rounded opinion-cover lazy" src="/img/noimg.png" data-src="{{preg_replace('/.[^.]*$/', '',$post->coverimage).'_350x250'.'.'.$ext_opinion}}"  alt="{{$post->title}}" onerror="this.onerror=null;this.src='/img/noimg.png';"/>
    <!--<img class="img-fluid rounded opinion-cover" src="{{$post->coverimage}}" alt="{{$post->title}}" onerror="this.onerror=null;this.src='/img/noimg.png';" />-->

    <div class="lead d-flex flex-md-row flex-column align-items-center justify-content-between">
        @if(Auth::guest())
        <div id="likepost_{{$post->id}}">
                <span data-toggle="tooltip" data-placement="top" title="Please login to like this opinion"  onclick="openLoginModal();"  style="font-size:28px;color:#ff9800;"><i  class="far fa-thumbs-up"></i></span>
                <span style="margin-left:8px;color:#ff9800;font-weight:bold;font-size:18px;"  class="likes_count_{{$post->id}}">{{$post->likesCount}}</button>
        </div>
        @else
        <div class="text-md-left text-sm-center">
            <span style="color:#ff9800;cursor:pointer;" class="like_post mr-2" id="likepost_{{$post->id}}">
                    <i class="fas fa-thumbs-up likepost_{{$post->id}}_on" data-toggle="tooltip" data-placement="top" title="Liked" style="font-size:28px;display:{{in_array($post->id,$liked_posts)?'inline':'none'}}"></i>
                    <i class="far fa-thumbs-up likepost_{{$post->id}}_off" data-toggle="tooltip" data-placement="top" title="Like this Opinion" style="font-size:28px;display:{{!in_array($post->id,$liked_posts)?'inline':'none'}}"></i>
            </span>

            <span class="btn_post_likes" style="font-size:18px;font-weight:bold;color:#ff9800;cursor:pointer;" data-posturl="{{ $post->slug }}">
                <span  class="likes_count_{{$post->id}}" data-count="{{ $post->likesCount}}">{{ $post->likesCount}}</span>
                <span> People Liked</span>
            </span>
            <span class="ml-3" style="font-size:18px;font-weight:bold;color:#244363;">
                @if($post->sharesCount<2)
                <span>{{ $post->sharesCount}} Share</span>
                @else
                <span>{{ $post->sharesCount}} Shares</span>
                @endif
            </span>
        </div>
        @endif

        <div class="social-share text-center mt-md-0 mt-4 share-menu" data-post="{{$post->id}}">
        
        @if(Auth::user())
        <a class="btn btn-circle btn-twitter waves-effect waves-circle waves-float mr-2 sharethis" target="_blank" role="button" href="https://twitter.com/share?text={{$post->title}}&url=https://www.weopined.com/opinion/{{$post->slug}}" data-post="{{$post->id}}" data-user="{{Auth::user()->id}}" data-plateform="TWITTER"><span><i class="fab fa-twitter"></i></span></a>
        <a class="btn btn-circle btn-facebook waves-effect waves-circle waves-float mr-2 sharethis"  target="_blank" role="button" href="https://facebook.com/sharer/sharer.php?u=https://www.weopined.com/opinion/{{$post->slug}}" data-post="{{$post->id}}" data-user="{{Auth::user()->id}}" data-plateform="FACEBOOK"><span><i class="fab fa-facebook-f"></i></span></a>
        <a class="btn btn-circle btn-linkedin waves-effect waves-circle waves-float mr-2 sharethis"  target="_blank" role="button" href="https://www.linkedin.com/shareArticle?mini=true&url=https://www.weopined.com/opinion/{{$post->slug}}&title={{ $post->title }}&source=Opined" data-post="{{$post->id}}" data-user="{{Auth::user()->id}}" data-plateform="LINKEDIN"><span><i class="fab fa-linkedin-in"></i></span></a>
        <a class="btn btn-circle btn-whatsapp  waves-effect waves-circle waves-float sharethis"  target="_blank" role="button" href="https://api.whatsapp.com/send?&text={{$post->title}} .....Read more at Opined : https://www.weopined.com/opinion/{{$post->slug}}" data-post="{{$post->id}}" data-user="{{Auth::user()->id}}" data-plateform="WHATSAPP"><span><i class="fab fa-whatsapp"></i></span></a>
        @else
        <a class="btn btn-circle btn-twitter waves-effect waves-circle waves-float mr-2 sharethis" target="_blank" role="button" href="https://twitter.com/share?text={{$post->title}}&url=https://www.weopined.com/opinion/{{$post->slug}}" data-post="{{$post->id}}" data-plateform="TWITTER"><span><i class="fab fa-twitter"></i></span></a>
        <a class="btn btn-circle btn-facebook waves-effect waves-circle waves-float mr-2 sharethis"  target="_blank" role="button" href="https://facebook.com/sharer/sharer.php?u=https://www.weopined.com/opinion/{{$post->slug}}" data-post="{{$post->id}}" data-plateform="FACEBOOK"><span><i class="fab fa-facebook-f"></i></span></a>
        <a class="btn btn-circle btn-linkedin waves-effect waves-circle waves-float mr-2 sharethis"  target="_blank" role="button" href="https://www.linkedin.com/shareArticle?mini=true&url=https://www.weopined.com/opinion/{{$post->slug}}&title={{ $post->title }}&source=Opined" data-post="{{$post->id}}" data-plateform="LINKEDIN"><span><i class="fab fa-linkedin-in"></i></span></a>
        <a class="btn btn-circle btn-whatsapp  waves-effect waves-circle waves-float sharethis"  target="_blank" role="button" href="https://api.whatsapp.com/send?&text={{$post->title}} .....Read more at Opined : https://www.weopined.com/opinion/{{$post->slug}}" data-post="{{$post->id}}" data-plateform="WHATSAPP"><span><i class="fab fa-whatsapp"></i></span></a>
        @endif
        </div>
    </div>

    <hr>
    <input type="hidden" name="post_id" id="post_id" value="{{$post->id}}">
    <div class="text-justify blog-post" style="font-family: 'Lora', serif;">
        @php
        $str_body = "$post->body";
        $arr_body = (explode("<p>",$str_body));
        $ad= '<div class="mb-3 rsm_opined">
            <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
                <ins class="adsbygoogle"
                     style="display:block; text-align:center;"
                     data-ad-layout="in-article"
                     data-ad-format="fluid"
                     data-ad-client="ca-pub-9171805278522999"
                     data-ad-slot="1338547262"></ins>
                <script>
                     (adsbygoogle = window.adsbygoogle || []).push({});
                </script></div>';
            array_splice( $arr_body, 3, 0, $ad );
            array_splice( $arr_body, 6, 0, $ad );
           echo implode(" ",$arr_body) 
        @endphp
    {{--{!!$post->body!!}--}}
    </div>

    @if(count($post->keywords)>0)
    <div class="mb-1 mt-2">
        <span >Keywords :</span>
        @foreach($post->keywords as $keyword)
        <span class="badge badge-light p-2 mb-2">{{ $keyword->name}}</span>
        @endforeach
    </div>
    @endif

    @if(count($post->threads)>0)
    <div class="mb-1 mt-2">
     <span >Tags :</span>
     @foreach($post->threads as $thread)
     <a href="/thread/{{$thread->name}}" title="{{'#'.$thread->name}}" class="badge badge-success p-2 mb-2">{{'#'.$thread->name}}</a>
     @endforeach
    </div>
    @endif

    <div id="like-share" class="mt-4">
        <div class="d-flex flex-md-row flex-column align-items-center justify-content-between">
                @if(Auth::guest())
                <div id="likepost_{{$post->id}}">
                        <span data-toggle="tooltip" data-placement="top" title="Please login to like this opinion"  onclick="openLoginModal();"  style="font-size:28px;color:#ff9800;cursor:pointer;"><i  class="far fa-thumbs-up"></i></span>
                        <span style="margin-left:8px;color:#ff9800;font-weight:bold;font-size:18px;"  class="likes_count_{{$post->id}}">{{$post->likesCount}}</button>
                </div>
                @else
                <div class="text-md-left text-sm-center">
                        <span style="color:#ff9800;cursor:pointer;" class="like_post mr-2" id="likepost_{{$post->id}}">
                                <i class="fas fa-thumbs-up likepost_{{$post->id}}_on" data-toggle="tooltip" data-placement="top" title="Liked" style="font-size:32px;display:{{in_array($post->id,$liked_posts)?'inline':'none'}}"></i>
                                <i class="far fa-thumbs-up likepost_{{$post->id}}_off" data-toggle="tooltip" data-placement="top" title="Like this Opinion" style="font-size:32px;display:{{!in_array($post->id,$liked_posts)?'inline':'none'}}"></i>

                        </span>
                        <span class="btn_post_likes" style="font-size:18px;font-weight:bold;color:#ff9800;cursor:pointer;" data-posturl="{{ $post->slug }}">
                                <span  class="likes_count_{{$post->id}}" data-count="{{ $post->likesCount}}">{{ $post->likesCount}}</span>
                                <span> People Liked</span>
                        </span>
                        <span class="ml-3" style="font-size:18px;font-weight:bold;color:#244363;">
                            @if($post->sharesCount<2)
                            <span>{{ $post->sharesCount}} Share</span>
                            @else
                            <span>{{ $post->sharesCount}} Shares</span>
                            @endif
                        </span>
                </div>
                @endif
                <div class="social-share text-center mt-md-0 mt-5 share-menu" data-post="{{$post->id}}">
                @if(Auth::user())
                    <a class="btn btn-circle btn-twitter waves-effect waves-circle waves-float mr-2 sharethis" target="_blank" role="button" href="https://twitter.com/share?text={{$post->title}}&url=https://www.weopined.com/opinion/{{$post->slug}}" data-post="{{$post->id}}" data-user="{{Auth::user()->id}}" data-plateform="TWITTER"><span><i class="fab fa-twitter"></i></span></a>
                    <a class="btn btn-circle btn-facebook waves-effect waves-circle waves-float mr-2 sharethis"  target="_blank" role="button" href="https://facebook.com/sharer/sharer.php?u=https://www.weopined.com/opinion/{{$post->slug}}" data-post="{{$post->id}}" data-user="{{Auth::user()->id}}" data-plateform="FACEBOOK"><span><i class="fab fa-facebook-f"></i></span></a>
                    <a class="btn btn-circle btn-linkedin waves-effect waves-circle waves-float mr-2 sharethis"  target="_blank" role="button" href="https://www.linkedin.com/shareArticle?mini=true&url=https://www.weopined.com/opinion/{{$post->slug}}&title={{ $post->title }}&source=Opined" data-post="{{$post->id}}" data-user="{{Auth::user()->id}}" data-plateform="LINKEDIN"><span><i class="fab fa-linkedin-in"></i></span></a>
                    <a class="btn btn-circle btn-whatsapp  waves-effect waves-circle waves-float sharethis"  target="_blank" role="button" href="https://api.whatsapp.com/send?&text={{$post->title}} .....Read more at Opined : https://www.weopined.com/opinion/{{$post->slug}}" data-post="{{$post->id}}" data-user="{{Auth::user()->id}}" data-plateform="WHATSAPP"><span><i class="fab fa-whatsapp"></i></span></a>
                    @else
                    <a class="btn btn-circle btn-twitter waves-effect waves-circle waves-float mr-2 sharethis" target="_blank" role="button" href="https://twitter.com/share?text={{$post->title}}&url=https://www.weopined.com/opinion/{{$post->slug}}" data-post="{{$post->id}}" data-plateform="TWITTER"><span><i class="fab fa-twitter"></i></span></a>
                    <a class="btn btn-circle btn-facebook waves-effect waves-circle waves-float mr-2 sharethis"  target="_blank" role="button" href="https://facebook.com/sharer/sharer.php?u=https://www.weopined.com/opinion/{{$post->slug}}" data-post="{{$post->id}}" data-plateform="FACEBOOK"><span><i class="fab fa-facebook-f"></i></span></a>
                    <a class="btn btn-circle btn-linkedin waves-effect waves-circle waves-float mr-2 sharethis"  target="_blank" role="button" href="https://www.linkedin.com/shareArticle?mini=true&url=https://www.weopined.com/opinion/{{$post->slug}}&title={{ $post->title }}&source=Opined" data-post="{{$post->id}}" data-plateform="LINKEDIN"><span><i class="fab fa-linkedin-in"></i></span></a>
                    <a class="btn btn-circle btn-whatsapp  waves-effect waves-circle waves-float sharethis"  target="_blank" role="button" href="https://api.whatsapp.com/send?&text={{$post->title}} .....Read more at Opined : https://www.weopined.com/opinion/{{$post->slug}}" data-post="{{$post->id}}" data-plateform="WHATSAPP"><span><i class="fab fa-whatsapp"></i></span></a>
                    @endif
                </div>
        </div>
    </div>
    <hr/>

    <input type="hidden" id="postid" value="{{$post->id}}" />
</div>

<div class="card shadow-sm mb-5">
    <div class="card-body" style="padding: 0.55rem 1.25rem 0.55rem 1.25rem;">
        <div class="d-flex flex-md-row align-items-center justify-content-start">
            <!--<div class="d-flex flex-md-row flex-column align-items-center justify-content-start">-->
            <div style="min-width:50px;max-width:50px;width:50px;min-height:50px;max-height:50px;height:50px;">
                    <a href="{{ route('user_profile', ['username' => $post->user['username']]) }}">
                        
                        @if($post->user['image']!=null)
                                 @php
                                  $ext = preg_match('/\./', $post->user['image']) ? preg_replace('/^.*\./', '', $post->user['image']) : '';
                                  $path=$post->user['image'];
                    $string="/profile";
                    $substring="profile-default-opined"
                                  @endphp
                                  <img class="rounded-circle lazy" src="/img/profile-default-opined_100x100.png" data-src="@php
                        if(strpos($path, $string) !== false || strpos($path, $substring) !== false){
                        @endphp
                        {{preg_replace('/.[^.]*$/', '',$post->user['image']).'_100x100'.'.'.$ext}}
                        @php
                        }
                        else{
                        @endphp
                        {{$post->user['image']}}
                        @php
                    }
                        @endphp" alt="{{$post->user['name']}}"  height="50" width="50" onerror="this.onerror=null;this.src='/img/avatar.png';" class="rounded-circle mr-3" height="48" width="48" style="min-width:50px;max-width:50px;min-height:50px;max-height:50px;margin:0px;border-radius:50%;" alt="Go to the profile of {{ucfirst($post->user['name'])}}" onerror="this.onerror=null;this.src='/img/avatar.png';">
                                
                                @endif

                        <!--<img src="{{ $post->user['image']!=null?$post->user['image']:'/img/avatar.png' }}" alt="{{ $post->user['name'] }}" onerror="this.onerror=null;this.src='/img/avatar.png';" style="width:100px;height:100px;margin:0px;border-radius:50%;"/>-->
                    </a>
            </div>

            <div class="pl-md-3 pl-0 flex-fill">
                <a href="{{ route('user_profile', ['username' => $post->user['username']]) }}">
                <p class="text-md-left text-center text-secondary font-weight-bold mb-1 d-xl-inline d-lg-inline d-md-inline d-sm-none d-none">WRITTEN BY: <span style="color:#212121;">{{  $post->user['name'] }}</span></p>

                <p class="text-md-left text-center text-secondary mb-1 ml-2 d-md-none d-sm-inline d-inline">WRITTEN BY</p></br>
                <p class="text-md-left text-center font-weight-bold mb-0 ml-2 d-md-none d-sm-inline d-inline" style="color:#212121;">{{  $post->user['name'] }}</p>
                {{--<h4 class="text-md-left text-center mb-0" style="color:#212121;">{{  $post->user['name'] }}</h4>--}}
                {{--<p class="text-md-left text-center text-secondary" style="font-size:16px;">{{ $post->user['bio']==null?'':$post->user['bio']}}</p>--}}
                </a>
            </div>

            <div>
                @if(Auth::guest())
                        <button class="followbtn btn btn-sm btn-outline-success ml-2">Follow <span><i class="fas fa-user-plus ml-2"></i><span></button>
                @else
                        @if( $post->user['id'] != Auth::user()->id)
                        <button data-userid="{{ $post->user['id'] }}" class="followbtn followbtn_{{ $post->user['id'] }} ml-2 btn btn-sm btn-outline-success" style="display:{{!in_array($post->user['id'],$followingids)?'inline':'none'}}" data-toggle="tooltip" data-placement="top" title="Follow">Follow <span><i class="fas fa-user-plus ml-2"></i><span></button>
                        <button data-userid="{{ $post->user['id'] }}" class="followingbtn followingbtn_{{$post->user['id']}} ml-2 btn btn-sm btn-success" style="display:{{in_array($post->user['id'],$followingids)?'inline':'none'}}">Following <span><i class="fas fa-check ml-2"></i></span></button>
                        @endif
                @endif
            </div>

        </div>
    </div>
</div>

@endif
