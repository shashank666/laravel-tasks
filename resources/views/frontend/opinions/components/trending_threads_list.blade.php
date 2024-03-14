<div class="card shadow-sm mt-4 mb-3">
    <h5 class="text-left mx-2 p-2">Trending Threads</h5>
    <div class="card-body py-0">
            @foreach($threads as $trending_thread)
            <a class="sidebar-thread-link" href="/thread/{{$trending_thread->thread->name}}" title="{{'#'.$trending_thread->thread['name']}}">
                <div class="sidebar-thread-name font-weight-normal" style="color:#495057;font-size:18px;">{{'#'.$trending_thread->thread->name}}</div>
                <div class="sidebar-opinion-count" style="margin-bottom:16px;"><p class="mb-0 text-secondary" style="font-size:12px;">{{ $trending_thread->thread->opinions_count }} opinions</p></div>
            </a>
            @endforeach
    </div>
    <div class="card-footer p-0 bg-light text-left">
        <a class="btn btn-block btn-default text-secondary"  href="/threads/trending">See All Trending Threads</a>
    </div>
</div>
