@extends('frontend.layouts.app')
@section('title','#'.ucfirst($tag->name).' - Opined')
@section('description','#'.ucfirst($tag->name).' on Opined')
@section('keywords',ucfirst($tag->name))

@push('meta')
<link rel="canonical" href="https://www.weopined.com/tag/{{$tag->slug}}" />
<link href="https://www.weopined.com/tag/{{$tag->slug}}" rel="alternate" reflang="en" />

<!-- Twitter Card data -->
<meta name="twitter:card" content="summary">
<meta name="twitter:site" content="@weopined">
<meta name="twitter:title" content="{{'#'.ucfirst($tag->name)}} - Opined">
<meta name="twitter:description" content="{{'#'.ucfirst($tag->name)}} on Opined.">
<meta name="twitter:creator" content="@weopined">
<meta name="twitter:image" content="https://www.weopined.com/favicon.png">

<!-- Open Graph data -->
<meta property="og:title" content="{{'#'.ucfirst($tag->name)}} - Opined" />
<meta property="og:type" content="website" />
<meta property="og:url" content="https://www.weopined.com/tag/{{$tag->slug}}" />
<meta property="og:image" content="https://www.weopined.com/favicon.png" />
<meta property="og:description" content="{{'#'.ucfirst($tag->name)}} on Opined." /> 
<meta property="og:site_name" content="Opined" />
<meta property="og:locale" content="en_US" />
<meta property="fb:app_id" content="1766000746745688" />
@endpush


@section('content')
<h1 class="mb-5">{{'#'.ucfirst($tag->name)}}</h1>

@if(count($posts)>0)
<div class="row">
        @include('frontend.posts.components.post_three_col')
</div>   

<div class="row">
    <div class="col align-self-center">
       {{ $posts->links('frontend.posts.components.pagination') }}  
    </div>
</div>
@endif
@endsection

