@if(($thread->opinions_count)>0)
<div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 col-12 ">
    <div class="thread_card card bg-white text-center shadow-sm" style="border:1px solid #f8f9fa">
        <div class="card-body py-3">
        <a  href="/thread/{{$thread->name}}" title="{{ '#'.$thread->name}}">
        <h5 class="text-truncate" style="color:{{ $colors[array_rand($colors,1)] }};">{{'#'.$thread->name}}</h5>
        <div class="text-secondary"><small><i class="far fa-comment-alt mr-2"></i>{{ $thread->opinions_count }} Opinions</small></div>
        </a>
        </div>
        <div class="card-footer bg-light border-0">
            @if(Auth::guest())
                <button class="btn btn-sm btn-outline-primary" onclick=" $('#forgotPasswordModal').modal('show');">Follow Thread<i class="fas fa-user-plus ml-2"></i></button>
            @else
                <button class="btn btn-sm btn-outline-primary follow_thread follow_thread_off_{{$thread->id}}" data-thread="{{$thread->id}}" style="display:{{in_array($thread->id,$followed_threads)?'none':'inline'}}">Follow Thread<i class="fas fa-user-plus ml-2"></i></button>
                <button class="btn btn-sm btn-primary follow_thread follow_thread_on_{{$thread->id}}" data-thread="{{$thread->id}}" style="display:{{in_array($thread->id,$followed_threads)?'inline':'none'}}">Following <i class="fas fa-check ml-2"></i></button>
            @endif
        </div>
    </div>
</div>
@endif
