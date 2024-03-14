@foreach($more_posts_by_category as $category_posts)
@if(count($category_posts->top_5_posts)>2)
<h4 class="pb-3 mt-5 mb-4 font-weight-normal" style="color:#244363;border-bottom:1px solid #ff9800;">More In {{ucfirst($category_posts->name)}}<span><a href="/topic/{{$category_posts->slug}}"  class="btn btn-sm waves-effect waves-float float-right" style="background-color:#244363;color:#fff;outline:none;"> <i class="fas fa-arrow-right"></i></a></span></h4>
<div class="row">
    @if(in_array($post->id,$category_posts->top_5_posts->pluck('id')->toArray()))
            @foreach($category_posts->top_5_posts as $toppost)
            @if($toppost->id!==$post->id)
            <div class="col-md-6 col-12">
            @include('frontend.posts.components.post-card-extra-small',['post'=>$toppost])
            </div>
            @endif
            @endforeach
    @else
            @foreach($category_posts->top_5_posts as $index=>$toppost)
            <div class="col-md-6 col-12">
                @include('frontend.posts.components.post-card-extra-small',['post'=>$toppost])
            </div>
            @endforeach
    @endif

</div>
@endif
@endforeach
