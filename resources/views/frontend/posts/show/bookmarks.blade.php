@extends('frontend.layouts.app')
@section('title','Bookmarks - Opined')
@section('description','Bookmarked articles for you , read whenever you want on Opined - where every opinion matters!')
@section('keywords','Bookmarked articles,Favorite articles,Read Lateest articles,Opined')

@push('meta')
<link rel="canonical" href="https://www.weopined.com/me/bookmarks" />
<link href="https://www.weopined.com/me/bookmarks" rel="alternate" reflang="en" />

<!-- Twitter Card data -->
<meta name="twitter:card" content="summary">
<meta name="twitter:site" content="@weopined">
<meta name="twitter:title" content="Bookmarks - Opined">
<meta name="twitter:description" content="Bookmarked articles for you , read whenever you want on Opined - where every opinion matters!">
<meta name="twitter:creator" content="@weopined">
<meta name="twitter:image" content="https://www.weopined.com/favicon.png">

<!-- Open Graph data -->
<meta property="og:title" content="Bookmarks - Opined" />
<meta property="og:type" content="website" />
<meta property="og:url" content="https://www.weopined.com/me/bookmarks" />
<meta property="og:image" content="https://www.weopined.com/favicon.png" />
<meta property="og:description" content="Bookmarked articles for you , read whenever you want on Opined - where every opinion matters!" />
<meta property="og:site_name" content="Opined" />
<meta property="og:locale" content="en_US" />
<meta property="fb:app_id" content="1766000746745688" />
@endpush

@section('content')
<h1 class="mb-5">Bookmarked Articles</h1>
@if($posts->total()>0)
    <div class="row">
         @include('frontend.posts.components.bookmark-post-card')
    </div>

    <div class="row">
        <div class="col align-self-center">
            {{ $posts->links('frontend.posts.components.pagination') }}
        </div>
    </div>

@else
    <div class="row mt-4">
        <div class="col-12">
            <p class="lead text-secondary">You have not bookmarked any articles.</p>
        </div>
    </div>
@endif
@endsection

