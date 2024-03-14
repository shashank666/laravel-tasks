@extends('frontend.layouts.app')
@section('title','Trending Topics - Opined')
@section('description','Explore various tranding threads on Opined . Write an opinions for thread your choices.')
@section('keywords','Explore Threads,Business,Current Affairs,Movies,Personalities,Politics,Sports')

@push('meta')
<link rel="canonical" href="https://www.weopined.com/threads/trending" />
<link href="https://www.weopined.com/threads/trending" rel="alternate" reflang="en" />

<!-- Twitter Card data -->
<meta name="twitter:card" content="summary">
<meta name="twitter:site" content="@weopined">
<meta name="twitter:title" content="Trending Topics - Opined">
<meta name="twitter:description" content="Explore trending topics on Opined . Write an opinion for topic your choices.">
<meta name="twitter:creator" content="@weopined">
<meta name="twitter:image" content="https://www.weopined.com/favicon.png">

<!-- Open Graph data -->
<meta property="og:title" content="Trending Topics  - Opined" />
<meta property="og:type" content="website" />
<meta property="og:url" content="https://www.weopined.com/threads/trending" />
<meta property="og:image" content="https://www.weopined.com/favicon.png" />
<meta property="og:description" content="Explore various topics on Opined . Write an opinion for topic your choices." />
<meta property="og:site_name" content="Opined" />
<meta property="og:locale" content="en_US" />
<meta property="fb:app_id" content="1766000746745688" />
@endpush


@push('styles')
<style type="text/css">
    .page-title-wrapper {
        display: none;
      }
</style>
@endpush

@push('scripts')
<script  src="/js/custom/threads.js?<?php echo time();?>" type="text/javascript"></script>
@endpush

@section('content')
<div class="page-title-wrapper">
    <h1 class="page-title">
        <span data-ui-id="page-title-wrapper">Trending Topics on Opined</span></h1>
</div>
<input id="path" type="hidden" value="{{Request::path()}}" />
<input id="totalpage" type="hidden" value="{{ceil($threads->total()/$threads->perPage())}}" />

    @include('frontend.threads.components.tabs',['section'=>'trending'])
    <div class="tab-content" id="threads-tab">
            <div class="tab-pane fade show active"  role="tabpanel">
                        <div class="row" id="append-div">
                            @foreach($trending_threads as $thread_with_count)
                            @include('frontend.threads.components.thread_card',['thread'=>$thread_with_count->thread])
                            @endforeach

                            @include('frontend.threads.components.threads_loop')

                        </div>
                    @include('frontend.partials.spinner')
            </div>
    </div>

@include('frontend.partials.loadmorescript')

@endsection
