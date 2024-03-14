@if(($thread->opinions_count)>0 && !in_array($thread->id, $followed_threadids))
<div class="bg-white mb-2">
<div class="card-body d-flex flex-row justify-content-between align-items-center trending-card-individual" style="margin-top: -6%;margin-bottom: -6%;">
    
    <div class="media-body col-8" style="margin-left: -11%">
        <a  href="/thread/{{$thread->name}}" title="{{ '#'.$thread->name}}">
                <h6 class="text-truncate m-0 text-nowrap" style="width: 10vw;color:{{$colors[array_rand($colors,1)]}}">{{'#'.$thread->name}}</h6>
                <div class="text-secondary" style="font-size: 1.1vw"><small><i class="far fa-comment-alt mr-2"></i>{{ $thread->opinions_count }} Opinions</small></div>
        </a>
    </div>
    <button class="btn btn-sm btn-outline-primary follow_thread follow_thread_off_{{$thread->id}}" data-thread="{{$thread->id}}" style="display:{{in_array($thread->id,$followed_threadids)?'none':'inline'}}" data-toggle="tooltip" data-placement="top" title="Follow {{ '#'.$thread->name}}"><i class="fas fa-user-plus"></i></button>
    <button class="btn btn-sm btn-primary follow_thread follow_thread_on_{{$thread->id}}" data-thread="{{$thread->id}}" style="display:{{in_array($thread->id,$followed_threadids)?'inline':'none'}}"><i class="fas fa-check"></i></button>
    
</div>
</div>
@endif