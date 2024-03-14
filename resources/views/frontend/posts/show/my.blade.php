@extends('frontend.layouts.app')
@section('title','My Articles - Opined')
@section('description','This is the list of articles that you have published on Opined.')
@section('keywords','My Articles,Published Articles')

@push('meta')
<link rel="canonical" href="https://www.weopined.com/me/opinions" />
<link href="https://www.weopined.com/me/opinions" rel="alternate" reflang="en" />

<!-- Twitter Card data -->
<meta name="twitter:card" content="summary">
<meta name="twitter:site" content="@weopined">
<meta name="twitter:title" content="My Articles - Opined">
<meta name="twitter:description" content="This is the list of articles that you have published on Opined.">
<meta name="twitter:creator" content="@weopined">
<meta name="twitter:image" content="https://www.weopined.com/favicon.png">

<!-- Open Graph data -->
<meta property="og:title" content="My Articles - Opined" />
<meta property="og:type" content="website" />
<meta property="og:url" content="https://www.weopined.com/me/opinions" />
<meta property="og:image" content="https://www.weopined.com/favicon.png" />
<meta property="og:description" content="This is the list of articles that you have published on Opined." />
<meta property="og:site_name" content="Opined" />
<meta property="og:locale" content="en_US" />
<meta property="fb:app_id" content="1766000746745688" />
@endpush

@push('scripts')
 <script type='text/javascript' src='/js/custom/delete.js'></script>

 @if($company_ui_settings->show_google_ad=='1')
 {!!  $company_ui_settings->google_adcode!!}
 @endif
@endpush



@section('content')
<h1 class="mb-5">My Articles</h1>
    <div class="row">
        
                @if(count($posts)>0)
                 @foreach($posts as $post)
                    <div class="col-md-6 col-12">
                        @include('frontend.posts.components.post_medium_article')
                    </div>
                @endforeach
                {!! $google_ad->ad_code !!}
                <div class="col-md-12 col-12">
                {{ $posts->links('frontend.posts.components.pagination') }}
                </div>
                @else
                <p class="lead text-secondary mt-4">You have not yet published any article .</p>
                {!! $google_ad->ad_code !!}
                @endif
    
        <div class="col-md-4 col-12">
            @if($company_ui_settings->show_google_ad=='1' && $google_ad)
                {!! $google_ad->ad_code !!}
                {!! $google_ad->ad_code !!}
            @endif
        </div>
    </div>
    @include('frontend.posts.crud.delete')
@endsection

