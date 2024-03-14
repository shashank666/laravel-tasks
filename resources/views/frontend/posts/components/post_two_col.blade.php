
@foreach($posts as $post)
 <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12 portfolio-item mb-4" id="post-{{$post->id}}">
        @include('frontend.posts.components.post-card-big')
</div>
@endforeach