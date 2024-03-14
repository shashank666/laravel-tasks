@extends('frontend.layouts.app')
@section('title','Interested Articles - Opined')
@section('description','Read articles of your interest on Opined . Every day, thousands of people share, read, discuss and write their opinion on Opined.')
@section('keywords','Interested Articles,Opined,Business,Current AffairsMovies,Personalities,Politics,Sports')

@push('meta')
<link rel="canonical" href="https://www.weopined.com/interested" />
<link href="https://www.weopined.com/interested" rel="alternate" reflang="en" />

<!-- Twitter Card data -->
<meta name="twitter:card" content="summary">
<meta name="twitter:site" content="@weopined">
<meta name="twitter:title" content="Interested Articles - Opined">
<meta name="twitter:description" content="Read articles of interest on Opined . Every day, thousands of people share, read, discuss and write
their opinion on Opined.">
<meta name="twitter:creator" content="@weopined">
<meta name="twitter:image" content="https://www.weopined.com/favicon.png">

<!-- Open Graph data -->
<meta property="og:title" content="Interested Articles - Opined" />
<meta property="og:type" content="website" />
<meta property="og:url" content="https://www.weopined.com/interested" />
<meta property="og:image" content="https://www.weopined.com/favicon.png" />
<meta property="og:description" content="Read articles of interest on Opined . Every day, thousands of people share, read, discuss and write
their opinion on Opined." />
<meta property="og:site_name" content="Opined" />
<meta property="og:locale" content="en_US" />
<meta property="fb:app_id" content="1766000746745688" />
@endpush


@push('scripts')
@if($company_ui_settings->show_google_ad=='1')
    {!! $company_ui_settings->google_adcode !!}
@endif
@endpush

@section('content')
<h1 class="mb-5">You may like</h1>
@if($category_posts->total()>0)
<input id="path" type="hidden" value="{{Request::path()}}" />
<input id="totalpage" type="hidden" value="{{ceil($category_posts->total()/$category_posts->perPage())}}" />

<div class="row" id="append-div">
        @include('frontend.posts.components.post_three_col')
</div>
@include('frontend.partials.spinner')
@include('frontend.partials.loadmorescript')
@endif
@endsection

