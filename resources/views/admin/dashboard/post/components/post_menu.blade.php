<div class="row align-items-center">
    <div class="col">
        <ul class="nav nav-tabs nav-overflow header-tabs">
            <li class="nav-item">
            <a href="{{route('admin.blog_post',['id'=>$post->id])}}" class="nav-link {{$section=='blogpost'?'active':''}}">Overview</a>
            </li>
            <li class="nav-item">
            <a href="{{ route('admin.post_likes',['id'=>$post->id]) }}" class="nav-link {{$section=='likes'?'active':''}}">Likes</a>
            </li>
        </ul>
    </div>
</div>