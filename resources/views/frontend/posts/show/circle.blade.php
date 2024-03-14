@extends('frontend.layouts.app')
@section('title','Circle Articles - Opined')
@section('description','Read circle articles of business,current affairs,movies,personalities,politics,sports on Opined . Every day, thousands of people share, read, discuss and write their opinion on Opined.')
@section('keywords','Circle Articles,Opined,Business,Current AffairsMovies,Personalities,Politics,Sports')

@push('meta')
<link rel="canonical" href="https://www.weopined.com/circle" />
<link href="https://www.weopined.com/circle" rel="alternate" reflang="en" />

<!-- Twitter Card data -->
<meta name="twitter:card" content="summary">
<meta name="twitter:site" content="@weopined">
<meta name="twitter:title" content="Circle Articles - Opined">
<meta name="twitter:description" content="Read circle articles of business,current affairs,movies,personalities,politics,sports on Opined . Every day, thousands of people share, read, discuss and write
their opinion on Opined.">
<meta name="twitter:creator" content="@weopined">
<meta name="twitter:image" content="https://www.weopined.com/favicon.png">

<!-- Open Graph data -->
<meta property="og:title" content="Circle Articles - Opined" />
<meta property="og:type" content="website" />
<meta property="og:url" content="https://www.weopined.com/circle" />
<meta property="og:image" content="https://www.weopined.com/favicon.png" />
<meta property="og:description" content="Read circle articles of business,current affairs,movies,personalities,politics,sports on Opined . Every day, thousands of people share, read, discuss and write
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

<h1 class="mb-5">From Your Circle</h1>
@if(count($posts)>0)
<input id="path" type="hidden" value="{{Request::path()}}" />
<input id="totalpage" type="hidden" value="{{ceil($posts->total()/$posts->perPage())}}" />

<div class="row" id="append-div">
        @include('frontend.posts.components.post_three_col')
</div>
@include('frontend.partials.spinner')
@include('frontend.partials.loadmorescript')
@endif
@endsection

