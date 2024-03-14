<div class="card mb-3 shadow-sm">
    <div class="card-body">
        <a  href="/thread/{{$thread->name}}" class="text-center text-dark"><h5 class="text-truncate mb-0">{{'#'.$thread->name}}</h5></a>
        <p class="mb-0 text-secondary text-center" style="font-size:12px;">
         <span id="thread_opinions_count">{{$total_opinions}}</span> opinions ,
         <span id="thread_followers_count">{{ $thread->followers->count() }}</span> followers
        </p>
    </div>
    <div class="card-footer bg-light">
        <div class="d-flex justify-content-center">
            @if(Auth::guest())
            <button class="btn btn-sm btn-outline-primary"  data-placement="top" title="Please Login To Follow #{{$thread->name}}" onclick="openLoginModal();">Follow<i class="fas fa-user-plus ml-2"></i></button>
            @else
            <button class="btn btn-sm btn-outline-primary follow_thread follow_thread_off_{{$thread->id}}" data-thread="{{$thread->id}}" style="display:{{in_array($thread->id,$followed_threads)?'none':'inline'}}"   data-placement="top" title="Follow #{{$thread->name}}">Follow<i class="fas fa-user-plus ml-2"></i></button>
            <button class="btn btn-sm btn-primary follow_thread follow_thread_on_{{$thread->id}}" data-thread="{{$thread->id}}" style="display:{{in_array($thread->id,$followed_threads)?'inline':'none'}}">Following <i class="fas fa-check ml-2"></i></button>
            @endif
            <span class="mx-1"></span>
            <div class="dropdown">
                <button class="btn btn-sm btn-outline-success dropdown-toggle share-opinion" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span  data-placement="top" title="Share #{{$thread->name}}">Share <i class="ml-2 fas fa-share"></i></span>
                </button>
                <div class="dropdown-menu share-menu dropdown-menu-right">
                    <a class="dropdown-item" target="_blank" href="https://facebook.com/sharer/sharer.php?u=https://www.weopined.com/thread/{{$thread->name}}&t=What is your opinion about {{$thread->name}} , Write your opinion on Opined - Where Every Opinion Matters !" style="color:#3b5998"><i class="fab fa-facebook mr-2"></i> Share On Facebook</a>
                    <a class="dropdown-item" target="_blank" href="https://twitter.com/share?url=https://www.weopined.com/thread/{{$thread->name}}&text=What is your opinion about {{$thread->name}} , Write your opinion on Opined - Where Every Opinion Matters !&via=weopined&hashtags={{$thread->name}}" style="color:#1da1f2"><i class="fab fa-twitter mr-2"></i> Share On Twitter</a>
                    <a class="dropdown-item" target="_blank" href="https://www.linkedin.com/shareArticle?mini=true&url=https://www.weopined.com/thread/{{$thread->name}}&title=What is your opinion about {{$thread->name}} , Write your opinion on Opined - Where Every Opinion Matters !" style="color:#0077b5"><i class="fab fa-linkedin mr-2"></i> Share On Linkedin</a>
                    <a class="dropdown-item" target="_blank" href="https://api.whatsapp.com/send?&text=https://www.weopined.com/thread/{{$thread->name}}" style="color:#128c7e"><i class="fab fa-whatsapp mr-2"></i>Share On Whatsapp</a>
                </div>
            </div>
        </div>
    </div>
</div>
