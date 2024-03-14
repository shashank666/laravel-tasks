@extends('frontend.layouts.app')
@section('title',$profile_user->name."'s Articles - Opined")
@section('description',"This is the list of articles that ".$profile_user->name." have published on Opined.")
@section('keywords',$profile_user->name.", ".$profile_user->name."'s Articles, ".$profile_user->name."'s Published Articles on opined")

@push('meta')
<link rel="canonical" href="https://www.weopined.com/{{'@'.$profile_user->username}}/article" />
<link href="https://www.weopined.com/{{'@'.$profile_user->username}}/article" rel="alternate" reflang="en" />

<!-- Twitter Card data -->
<meta name="twitter:card" content="summary">
<meta name="twitter:site" content="@weopined">
<meta name="twitter:title" content="{{$profile_user->name}}'s Articles - Opined">
<meta name="twitter:description" content="This is the list of articles that {{$profile_user->name}} have published on Opined.">
<meta name="twitter:creator" content="@weopined">
<meta name="twitter:image" content="https://www.weopined.com/favicon.png">

<!-- Open Graph data -->
<meta property="og:title" content="{{$profile_user->name}}'s Articles - Opined" />
<meta property="og:type" content="website" />
<meta property="og:url" content="https://www.weopined.com/{{'@'.$profile_user->username}}/article" />
<meta property="og:image" content="https://www.weopined.com/favicon.png" />
<meta property="og:description" content="This is the list of articles that {{$profile_user->name}} have published on Opined." />
<meta property="og:site_name" content="Opined" />
<meta property="og:locale" content="en_US" />
<meta property="fb:app_id" content="1766000746745688" />
@endpush

@push('scripts')
 <script type='text/javascript' src='/js/custom/delete.js'></script>

 
@endpush



@section('content')
<h1 class="mb-5">{{$profile_user->name}}'s Articles</h1>
    <div class="row">
        
                @if(count($posts)>0)
                 @foreach($posts as $post)
                    <div class="col-md-6 col-12">
                        @include('frontend.posts.components.post_medium_article')
                    </div>
                @endforeach
                
                <div class="col-md-12 col-12">
                {{ $posts->links('frontend.posts.components.pagination') }}
                </div>
                @else
                <p class="lead text-secondary mt-4">{{$profile_user->name}} has not yet published any article .</p>
                
                @endif
    
        
    </div>
    @include('frontend.posts.crud.delete')
@endsection

