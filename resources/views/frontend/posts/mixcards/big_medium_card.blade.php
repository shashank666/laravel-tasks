
@foreach($posts as $index=>$post)

@if($index%5==0)
<div class="col-md-8 col-12 portfolio-item mb-4" id="post-{{$post->id}}">
    @include('frontend.posts.components.post-card-big')
</div>
@else
<div class="col-md-4 col-12 portfolio-item mb-4" id="post-{{$post->id}}">
    @include('frontend.posts.components.post-card')
</div>
@endif

@endforeach
