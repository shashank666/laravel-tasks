
@foreach($posts as $index=>$post)

@if($index%5==0)
<div class="col-md-12 col-12 portfolio-item mb-4" id="post-{{$post->id}}">
    @include('frontend.posts.components.post-card-big')
    
</div>
@elseif($index%3==0)
    @if($company_ui_settings->show_google_ad=='1' && $google_ad)
            <div class="mt-3">
                {!! $google_ad->ad_code !!}
            </div>
            @endif
@else
<div class="col-md-6 col-12 portfolio-item mb-4" id="post-{{$post->id}}">
    @include('frontend.posts.components.post-card')
</div>
@endif

@endforeach
