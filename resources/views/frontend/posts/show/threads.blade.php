@extends('frontend.layouts.app')
@section('title','All Threads - Opined')
@section('description','Explore various threads on Opined . Write an opinions for thread your choices.')
@section('keywords','Explore Threads,Business,Current Affairs,Movies,Personalities,Politics,Sports')

@push('meta')
<link rel="canonical" href="https://www.weopined.com/threads" />
<link href="https://www.weopined.com/threads" rel="alternate" reflang="en" />

<!-- Twitter Card data -->
<meta name="twitter:card" content="summary">
<meta name="twitter:site" content="@weopined">
<meta name="twitter:title" content="All Threads - Opined">
<meta name="twitter:description" content="Explore various threads on Opined . Write an opinion for thread your choices.">
<meta name="twitter:creator" content="@weopined">
<meta name="twitter:image" content="https://www.weopined.com/favicon.png">

<!-- Open Graph data -->
<meta property="og:title" content="All Threads  - Opined" />
<meta property="og:type" content="website" />
<meta property="og:url" content="https://www.weopined.com/threads" />
<meta property="og:image" content="https://www.weopined.com/favicon.png" />
<meta property="og:description" content="Explore various threads on Opined . Write an opinion for thread your choices." /> 
<meta property="og:site_name" content="Opined" />
<meta property="og:locale" content="en_US" />
<meta property="fb:app_id" content="1766000746745688" />
@endpush


@section('content')
    <ul class="nav nav-tabs mb-5">
        <li class="nav-item">
        <a class="nav-link {{ $section=='trending'?'active':'' }}" href="/threads/trending">Trending Threads</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $section=='latest'?'active':'' }}" href="/threads/latest">Latest Threads</a>
        </li>
    </ul>
    <div class="tab-content" id="threads-tab">
            <div class="tab-pane fade show active"  role="tabpanel">
                    <div class="row">  
                            @php($colors=['#37474f','#ff9800','#0097a7','#5c007a','#00796b','#d32f2f','#5d4037','#827717','#689f38','#e91e63','#424242','#1565c0','#880e4f','#f57f17'])
                           
                            @foreach($all_threads as $thread)
                            <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 col-12" style="padding-left:8px;padding-right:8px;">
                                <a  href="/thread/{{$thread->name}}" title="{{ '#'.$thread->name}}">
                                <div class="thread_card card bg-light mb-3 p-2 text-center shadow-sm" style="border:0px">
                                  <div class="card-body p-1">
                                    <h5 class="text-truncate" style="color:{{ $colors[array_rand($colors,1)] }}">{{'#'.$thread->name}}</h5>
                                    <span class="text-secondary"><small><i class="far fa-comment-alt mr-2"></i>{{ $thread->opinionCount }} Opinions</small></span>
                                </div>
                            </div>
                                </a> 
                            </div>
                            @endforeach
                        </div>
                
                        <div class="row mt-3">
                            <div class="col align-self-center">
                            {{ $all_threads->links('frontend.posts.components.pagination') }}  
                            </div>
                        </div>
            </div>
    </div>

           
@endsection