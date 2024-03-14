
@foreach($posts as $index=>$post)

@if($index%5==0)
<div class="col-md-12 col-12 portfolio-item mb-4" id="post-{{$post->id}}">
    @include('frontend.posts.components.post-card-big')
</div>
@else
<div class="col-md-12 col-12 portfolio-item mb-4" id="post-{{$post->id}}">
    @include('frontend.posts.components.post-card-horizontal')
</div>
@endif

@endforeach
