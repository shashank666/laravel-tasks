@extends('frontend.layouts.app')
@section('title','My Articles - Opined')
@section('description','Your All articles displayed here.')
@section('keywords','Draft articles,published articles,Unpublished articles')

@push('meta')
<link rel="canonical" href="https://www.weopined.com/me/drafts" />
<link href="https://www.weopined.com/me/myallarticles" rel="alternate" reflang="en" />

<!-- Twitter Card data -->
<meta name="twitter:card" content="summary">
<meta name="twitter:site" content="@weopined">
<meta name="twitter:title" content="My Articles - Opined">
<meta name="twitter:description" content="Your all articles displayed here.">
<meta name="twitter:creator" content="@weopined">
<meta name="twitter:image" content="https://www.weopined.com/favicon.png">

<!-- Open Graph data -->
<meta property="og:title" content="My Articles - Opined" />
<meta property="og:type" content="website" />
<meta property="og:url" content="https://www.weopined.com/me/myallarticles" />
<meta property="og:image" content="https://www.weopined.com/favicon.png" />
<meta property="og:description" content="Your all articles displayed here." />
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




<h2 class="mb-5">My Articles</h2>

        <div class="row">
            <div class="col-md-12 col-12">
                    @if(count($posts)>0)
                    @include('frontend.posts.components.my_all_articles_card')
                    {{ $posts->links('frontend.posts.components.pagination') }}
                    {!! $google_ad->ad_code !!}
                    @else
                        <p class="lead text-secondary mt-4">There Is No Draft Saved</p>
                        {!! $google_ad->ad_code !!}
                        @endif
            </div>
            <!--<div class="col-md-4 col-12">
                    @if($company_ui_settings->show_google_ad=='1' && $google_ad)
                        {!! $google_ad->ad_code !!}
                        {!! $google_ad->ad_code !!}
                    @endif
            </div>-->
        </div>
        @include('frontend.posts.crud.delete')

@endsection

