@extends('frontend.layouts.app')
@section('title','Threads From Your Circle- Opined')
@section('description','Explore various threads on Opined . Write an opinions for thread your choices.')
@section('keywords','Explore Threads,Business,Current Affairs,Movies,Personalities,Politics,Sports')

@push('meta')
<link rel="canonical" href="https://www.weopined.com/threads/circle" />
<link href="https://www.weopined.com/threads/circle" rel="alternate" reflang="en" />

<!-- Twitter Card data -->
<meta name="twitter:card" content="summary">
<meta name="twitter:site" content="@weopined">
<meta name="twitter:title" content="Threads From Your Circle - Opined">
<meta name="twitter:description" content="Explore various threads from your circle on Opined . Write an opinion for thread your choices.">
<meta name="twitter:creator" content="@weopined">
<meta name="twitter:image" content="https://www.weopined.com/favicon.png">

<!-- Open Graph data -->
<meta property="og:title" content="Threads From Your Circle - Opined" />
<meta property="og:type" content="website" />
<meta property="og:url" content="https://www.weopined.com/threads/circle" />
<meta property="og:image" content="https://www.weopined.com/favicon.png" />
<meta property="og:description" content="Explore various threads on Opined . Write an opinion for thread your choices." />
<meta property="og:site_name" content="Opined" />
<meta property="og:locale" content="en_US" />
<meta property="fb:app_id" content="1766000746745688" />
@endpush

@push('scripts')
<script  src="/js/custom/threads.js?<?php echo time();?>" type="text/javascript"></script>
@endpush

@section('content')

<input id="path" type="hidden" value="{{Request::path()}}" />
<input id="totalpage" type="hidden" value="{{ceil($threads->total()/$threads->perPage())}}" />
    @include('frontend.threads.components.tabs',['section'=>'circle'])
    <div class="tab-content" id="threads-tab">
            <div class="tab-pane fade show active"  role="tabpanel">
                        <div class="row" id="append-div">
                            @include('frontend.threads.components.threads_loop')
                        </div>
                    @include('frontend.partials.spinner')
            </div>
    </div>
@include('frontend.partials.loadmorescript')
@endsection
