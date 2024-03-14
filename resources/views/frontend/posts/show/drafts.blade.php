@extends('frontend.layouts.app')
@section('title','My Drafts - Opined')
@section('description','Your unpublished articles displayed here as a draft.')
@section('keywords','Draft articles,Unpublished articles')

@push('meta')
<link rel="canonical" href="https://www.weopined.com/me/drafts" />
<link href="https://www.weopined.com/me/drafts" rel="alternate" reflang="en" />

<!-- Twitter Card data -->
<meta name="twitter:card" content="summary">
<meta name="twitter:site" content="@weopined">
<meta name="twitter:title" content="My Drafts - Opined">
<meta name="twitter:description" content="Your unpublished articles displayed here as a draft.">
<meta name="twitter:creator" content="@weopined">
<meta name="twitter:image" content="https://www.weopined.com/favicon.png">

<!-- Open Graph data -->
<meta property="og:title" content="My Drafts - Opined" />
<meta property="og:type" content="website" />
<meta property="og:url" content="https://www.weopined.com/me/drafts" />
<meta property="og:image" content="https://www.weopined.com/favicon.png" />
<meta property="og:description" content="Your unpublished articles displayed here as a draft." />
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
<h1 class="mb-5">My Drafts</h1>

        <div class="row">
            <div class="col-md-8 col-12">
                    @if(count($posts)>0)
                    @include('frontend.posts.components.post_textonly')
                    {{ $posts->links('frontend.posts.components.pagination') }}
                    {!! $google_ad->ad_code !!}
                    @else
                        <p class="lead text-secondary mt-4">There Is No Draft Saved</p>
                        {!! $google_ad->ad_code !!}
                        @endif
            </div>
            <div class="col-md-4 col-12">
                    @if($company_ui_settings->show_google_ad=='1' && $google_ad)
                        {!! $google_ad->ad_code !!}
                        {!! $google_ad->ad_code !!}
                    @endif
            </div>
        </div>
        @include('frontend.posts.crud.delete')

@endsection

