@extends('frontend.layouts.app')
@section('title', ucfirst($profile_user->name).' - Opined')
@section('description','Read opinions from '.ucfirst($profile_user->name).' on Opined.Everyday, '.ucfirst($profile_user->name).' and thousands of other read, write, and share their opinions on Opined.')
@section('keywords',ucfirst($profile_user->name).' - Opined')

@push('meta')
<link rel="canonical" href="http://www.weopined.com/{{'@'.$profile_user->username}}" />
<link href="http://www.weopined.com/{{'@'.$profile_user->username}}" rel="alternate" reflang="en" />

<!-- Twitter Card data -->
<meta name="twitter:card" content="summary">
<meta name="twitter:site" content="@weopined">
<meta name="twitter:title" content="{{ucfirst($profile_user->name)}} - Opined">
<meta name="twitter:description" content="Read opinions from {{ucfirst($profile_user->name)}} on Opined.Everyday, {{ucfirst($profile_user->name)}} and thousands of other read, write, and share their opinions on Opined.">
<meta name="twitter:creator" content="@weopined">
<meta name="twitter:image" content="http://www.weopined.com/favicon.png">

<!-- Open Graph data -->
<meta property="og:title" content="{{ucfirst($profile_user->name)}} - Opined"/>
<meta property="og:type" content="website" />
<meta property="og:url" content="http://www.weopined.com/{{'@'.$profile_user->username}}" />
<meta property="og:image" content="http://www.weopined.com/favicon.png" />
<meta property="og:description" content="Read opinions from {{ucfirst($profile_user->name)}} on Opined.Everyday, {{ucfirst($profile_user->name)}} and thousands of other read, write, and share their opinions on Opined." />
<meta property="og:site_name" content="Opined" />
<meta property="og:locale" content="en_US" />
<meta property="fb:app_id" content="1766000746745688" />
@endpush


@push('scripts')
<script type="text/javascript" src="/js/custom/profile.js?<?php echo time();?>"></script>
<script async src="/js/custom/comment_opinions.js?<?php echo time();?>" type="text/javascript"></script>
<script async src="/js/custom/like.js?<?php echo time();?>" type="text/javascript"></script>
<script async src="/js/custom/threads.js?<?php echo time();?>" type="text/javascript"></script>
@endpush


@push('styles')
<style>
 .page-title-wrapper {
        display: none;
      }

 @media (max-width: 767px) {
 .card-columns {
        -webkit-column-count: 1;
        -moz-column-count: 1;
        column-count: 1;
    }
}

@media (min-width: 768px) {
    .card-columns {
           -webkit-column-count: 2;
           -moz-column-count: 2;
           column-count: 2;
       }
   }
</style>
@endpush

@section('content')
@include('frontend.profile.components.usercard')
@include('frontend.partials.modal_opinion_likes')
@include('frontend.auth.modal_forgot')
<div class="page-title-wrapper">
    <h1 class="page-title">
        <span data-ui-id="page-title-wrapper">Opinions by {{ucfirst($profile_user->name)}} | Opined</span></h1>
</div>
        @if($section=='profile')
            {{--  @if(count($posts)>0)
            <div class="row">
                <div class="offset-md-2 col-md-8 col-12">
                    <h5 class="pb-3 my-4 font-weight-normal" style="color:#244363;border-bottom:2px solid black;">Latest Articles
                        {{--   <span><a href="" class="btn btn-sm  float-right" style="background-color:#244363;color:#fff;outline:none;">
                                <i class="fas fa-arrow-right"></i></a>
                        </span>  
                        <span><a href="{{route('user_article',['username' => $profile_user->username])}}" class="btn btn-sm  float-right" style="background-color:white;color:#fff;outline:none;border:2px solid black;">
                            <i class="fas fa-arrow-right" style="color:black;"></i></a>
                    </span>
                    </h5>
                    <div class="row">
                        @foreach($posts as $post)
                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12 portfolio-item mb-4" id="post-{{$post->id}}">
                            @include('frontend.posts.components.post-card')
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            {{--  @endif  --}}  

            <div>
                @include('frontend.counter.user_counter',[$short_opinions,$followers,$following,$posts])
            </div>

            {{--  @if(count($achievements)>=0)
                <div class="row">
                    <div class="offset-md-2 col-md-8 col-12">
                        <h5 class="pb-2 my-4 font-weight-normal" style="color:#244363;border-bottom:2px solid black;">
                        Achievements
                        </h5>
                        <div class="container">
                            <div class="row">
                                @foreach($achievements as $achievement)
                                    @if(count($user_achievements)>0)
                                        @foreach($user_achievements as $user_achievement)
                                            @if($achievement->achievement_id == $user_achievement->achievements_id)
                                                @include('frontend.achievements.achievements_grid',[$achievement,'flag'=>true])
                                            @endif
                                        @endforeach
                    
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endif  --}}

            @if(count($short_opinions)>0)
                <input id="path" type="hidden" value="{{Request::path()}}" />
                <input id="totalpage" type="hidden" value="{{ceil($short_opinions->total()/$short_opinions->perPage())}}" />

                <div class="row">
                    <div class="offset-md-2 col-md-8 col-12">
                        <h5 class="pb-2 my-4 font-weight-normal" style="color:#244363;border-bottom:2px solid black;">Opinions</h5>
                        <div class="card-columns" style="display: flex; justify-content:center;align-items:center;flex-direction:column;" id="append-div">
                        @foreach($short_opinions as $opinion)
                            @include('frontend.opinions.components.profile_opinion_card',['user'=>$opinion->user])
                        @endforeach
                        </div>
                        @include('frontend.partials.spinner')
                    </div>
                </div>
                @include('frontend.partials.loadmorescript')
            @endif
        @endif


        @if($section=='in_circle' || $section=='circle')
        <input id="path" type="hidden" value="{{Request::path()}}" />
        <input id="totalpage" type="hidden" value="{{ceil($users->total()/$users->perPage())}}" />

        <div class="row">
            <div class="offset-md-2 col-md-8 col-sm-12 col-12">
                @if($section=='circle')
                
                    <h4 class="pb-3 mb-4 border-bottom">{{ucfirst($profile_user->name)}} Follows <div class="float-right"><i><a href="{{route('user_profile',['username' => $profile_user->username])}}">Opinions</a></i></div></h4>

                @endif

                @if($section=='in_circle')
                    <h4 class="pb-3 mb-4 border-bottom">{{ucfirst($profile_user->name)}} is followed by <div class="float-right"><i><a href="{{route('user_profile',['username' => $profile_user->username])}}">Opinions</a></i></div></h4>

                @endif
                <div class="row" id="append-div">
                        @include('frontend.profile.components.usersloop_three_col')
                </div>
            </div>
        </div>

        @include('frontend.partials.spinner')
        @include('frontend.partials.loadmorescript')
        @endif
@endsection
