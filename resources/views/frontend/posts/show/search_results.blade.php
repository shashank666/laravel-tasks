@extends('frontend.layouts.app')
@section('title','Search and Find  - Opined')
@section('description','Search on Opined and find the most popular opinions about topics that matter.')
@section('keywords','Search,Find')

@push('meta')
<link rel="canonical" href="https://www.weopined.com/search" />
<link href="https://www.weopined.com/search" rel="alternate" reflang="en" />

<!-- Twitter Card data -->
<meta name="twitter:card" content="summary">
<meta name="twitter:site" content="@weopined">
<meta name="twitter:title" content="Search and Find - Opined">
<meta name="twitter:description" content="Search on Opined and find the most popular opinions about topics that matter.">
<meta name="twitter:creator" content="@weopined">
<meta name="twitter:image" content="https://www.weopined.com/favicon.png">

<!-- Open Graph data -->
<meta property="og:title" content="Search and Find - Opined" />
<meta property="og:type" content="website" />
<meta property="og:url" content="https://www.weopined.com/search" />
<meta property="og:image" content="https://www.weopined.com/favicon.png" />
<meta property="og:description" content="Search on Opined and find the most popular opinions about topics that matter." />
<meta property="og:site_name" content="Opined" />
<meta property="og:locale" content="en_US" />
<meta property="fb:app_id" content="1766000746745688" />
@endpush

@push('scripts')
<script type="text/javascript" src="/js/custom/profile.js?<?php echo time();?>"></script>
<script  src="/js/custom/threads.js?<?php echo time();?>" type="text/javascript"></script>

{{--  <script type="text/javascript">
$(document).on('keyup','#q',function(){
 var query=$('#q').val();
 if(query.length > 0){
    $('#search_opinions').attr('href','/search?q='+query);
    $('#search_threads').attr('href','/search/threads?q='+query);
    $('#search_users').attr('href','/search/users?q='+query);
 }
});
</script>  --}}
@endpush

@section('content')
<form method="GET" action="{{'/'.Request::path()}}">
<input class="form-control form-control-lg mb-3 mt-4" id="q" name="q"  type="text" value={{$query}} placeholder="Search Opined" autofocus required/>
</form>

<div class="">
    <ul class="nav nav-tabs" role="tablist">
        @if(count($posts_result)>0)
        <li class="nav-item {{ $active_tab=='posts'?'active':'' }}">
            <a class="nav-link {{ $active_tab=='posts'?'active':''  }}" data-toggle="tab"  href="#search_articles_tab">Articles</a>
        </li>
        @endif
        @if(count($threads_result)>0)
        <li class="nav-item {{ $active_tab=='threads'?'active':''  }}">
            <a class="nav-link {{ $active_tab=='threads'?'active':''  }}"  data-toggle="tab" href="#search_threads_tab">Threads</a>
        </li>
        @endif
        @if(count($users_result)>0)
        <li class="nav-item {{ $active_tab=='users'?'active':''  }}">
            <a class="nav-link {{ $active_tab=='users'?'active':''  }}"  data-toggle="tab" href="#search_users_tab">Users</a>
        </li>
        @endif
    </ul>
    <div class="tab-content">
        @if(count($posts_result)>0)
        <div  role="tabpanel" class="{{ $active_tab=='posts'?'tab-pane in active':'tab-pane fade' }}" id="search_articles_tab">
            <div class="row mt-4">
                    @include('frontend.posts.components.post_three_col', ['posts' => $posts_result])
            </div>
        </div>
        @endif

        @if(count($threads_result)>0)
        <div  role="tabpanel" class="{{ $active_tab=='threads'?'tab-pane in active':'tab-pane fade' }}"  id="search_threads_tab">
            <div class="row mt-4">
            @include('frontend.threads.components.threads_loop',['threads'=>$threads_result,'followed_threads'=>$followed_threads])
            </div>
        </div>
        @endif

        @if(count($users_result)>0)
        <div role="tabpanel" class="{{ $active_tab=='users'?'tab-pane in active':'tab-pane fade' }}"  id="search_users_tab">
            <div class="row mt-4">
                @include('frontend.profile.components.usersloop_four_col',['users'=>$users_result])
            </div>
        </div>
        @endif

        @if($active_tab=='no_result')
        <div class="mt-5 alert alert-primary" role="alert">
                No Search Result Found
        </div>
        @endif

    </div>
</div>

@endsection
