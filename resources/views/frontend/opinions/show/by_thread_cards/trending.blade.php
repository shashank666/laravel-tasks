@foreach($trending_opinions as $index=>$opinion)
            @include('frontend.opinions.components.thread-opinion-card',['user'=>$opinion->user])

            {{--  @if($company_ui_settings->show_google_ad=='1' && $google_native_ad)
                @if($index%4==0)
                <div class="my-2">
                    {!! $google_native_ad->ad_code !!}
                </div>
                @endif
            @endif  --}}
@endforeach
