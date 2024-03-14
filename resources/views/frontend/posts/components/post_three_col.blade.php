
@foreach($posts as $index=>$post)
 <div class="col-xl-4 col-lg-4 col-md-4 col-sm-6 col-12 portfolio-item mb-4" id="post-{{$post->id}}">
        @include('frontend.posts.components.post-card')
</div>

          {{--  @if($company_ui_settings->show_google_ad=='1' && $google_native_ad)
                @if($index%4==0)
                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-6 col-12 portfolio-item mb-4">
                        {!! $google_native_ad->ad_code !!}
                </div>
                @endif
        @endif    --}}
@endforeach
