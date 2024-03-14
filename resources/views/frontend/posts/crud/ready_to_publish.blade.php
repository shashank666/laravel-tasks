@extends('frontend.layouts.error')
@section('title',$post->title." - Opined")
@section('description',str::limit($post->plainbody,$limit = 150 , $end = '...'))
@if(count($post->keywords)>0)
@section('keywords', $post->keywords->pluck('name')->implode(', '))
@else
@section('keywords', $post->categories->pluck('name')->implode(', '))
@endif


@push('meta')
<link rel="canonical" href="https://www.weopined.com/opinion/{{$post->slug}}" />
<link href="https://www.weopined.com/opinion/{{$post->slug}}" rel="alternate" reflang="en" />

<meta name = "author" content = "{{ucfirst($post->user['name'])}}" />

<!-- Twitter Card data -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:site" content="@weopined">
<meta name="twitter:title" content="{{ str::limit($post->title,$limit = 64, $end = '...') }} |{{ ucfirst($post->user['name'])}} | Opined">
<meta name="twitter:description" content="{{str::limit($post->plainbody,$limit = 150 , $end = '...')}}">
<meta name="twitter:creator" content="@weopined">
<meta name="twitter:image:src" content="{{$post->coverimage}}">

<!-- Open Graph data -->
<meta property="og:title" content="{{  str::limit($post->title,$limit = 64, $end = '...') }} |{{ ucfirst($post->user['name'])}} | Opined"/>
<meta property="og:type" content="article" />
<meta property="og:url" content="https://www.weopined.com/opinion/{{$post->slug}}" />
<meta property="og:image" content="{{$post->coverimage}}" />
<meta property="og:image:alt" content="{{$post->title}}">
<meta property="og:site_name" content="Opined" />
<meta property="og:locale" content="en_US" />
<meta property="og:contentnid" content="{{$post->id}}" />
<meta property="og:contenttype" content="article_listing" />
<meta property="og:publishdt" content="{{$post->created_at}}" />
<meta property="og:uploadedby" content="opined" />

<meta property="article:author" content="{{ucfirst($post->user['name'])}}" />
<meta property="article:publisher" content="https://www.facebook.com/weopined" />
<meta property="article:published_time" content="{{$post->created_at}}" />
<meta property="article:modified_time" content="{{$post->updated_at}}" />
<meta property="article:section" content="{{$post->categories->pluck('name')->implode(', ')}}" />

<meta http-equiv="Last-Modified" content="{{Carbon\Carbon::parse($post->updated_at)->toDayDateTimeString()}}" />
<meta name="Last-Modified" content="{{Carbon\Carbon::parse($post->updated_at)->toDayDateTimeString()}}" />
<meta name="Last-Modified-Date" content="{{ Carbon\Carbon::parse($post->updated_at)->toFormattedDateString()}}" />
<meta name="Last-Modified-Time" content="{{ Carbon\Carbon::parse($post->updated_at)->format('h:i:s A') }} IST" />
<meta name="atdlayout" content="articlepage">

<meta property="fb:app_id" content="1766000746745688" />
<link href="/vendor/emojionearea/emojionearea.min.css" type="text/css" rel="stylesheet" />

@endpush


@push('scripts')
@if($company_ui_settings->show_google_ad=='1')
{!! $company_ui_settings->google_adcode !!}
@endif
<script src="/vendor/emojionearea/emojionearea.min.js" type="text/javascript"></script>
<script src="/js/custom/comments.js?<?php echo time();?>" type="text/javascript"></script>
<script src="/js/custom/profile.js?<?php echo time();?>" type="text/javascript"></script>
<script src="/vendor/swiper/swiper.min.js" type="text/javascript"></script>
<script>
      $(document).ready(function(){

        $("#comment_textarea").emojioneArea({
            pickerPosition: "bottom"
        });

        var mySwiper = new Swiper ('.swiper-container', {
          init:true,
          autoplay: {
            delay: 3000,
          },
          speed:300,
          direction: 'horizontal',
          loop: true,
          navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
        })
      });
</script>
@endpush

@section('content')
@if($post->is_active==1)
<div class="row">
    <div class="col-xl-8 col-lg-8 col-md-12 col-sm-12 col-12">
        @include('frontend.posts.components.blog')
        @include('frontend.posts.modals.modal_post_likes')

        @if($company_ui_settings->show_google_ad=='1' && $google_ad1)
        <div class="mt-3">
            {!! $google_ad1->ad_code !!}
        </div>
        @endif

        <div id="comments" class="mb-5 mt-5">
            <h3 class="mb-3 pb-3 border-bottom">Comments<span class="ml-2 badge badge-primary commentsTotalCount">{{ $post->commentsCount }}</span></h3>
            @include('frontend.posts.comments.add_comment')

            <div class="comments-div"></div>
            <button type="button" class="btn btn-primary btn-sm" style="display:none;width:100%;" id="btnloadMore" data-nextpage="">Load More Comments</button>
        </div>


        @if($company_ui_settings->show_google_ad=='1' && $google_ad1)
        <div class="mt-3">
            {!! $google_ad1->ad_code !!}
        </div>
        @endif

        @include('frontend.posts.components.more_posts')

    </div>

    <div class="col-xl-4 col-lg-4 col-md-12 col-sm-12 col-12">
        {{--  OFFER POSTER AND COUNT
        <div class="mt-3">
            <a href="{{ route('offer') }}"><img src="/img/offer_sm.png" class="img-fluid rounded"/></a>
        </div>

        <div class="mt-3 mr-2">
            @include('frontend.partials.offer_count')
        </div>
        --}}

        {{--     <div class="mt-3">
            @include('frontend.social.facebook_page')
        </div>
--}}


        <div  style="position: -webkit-sticky; position: sticky;top: -310px;">
            @if($company_ui_settings->show_google_ad=='1' && $google_ad1)
        <div class="mt-3">
            {!! $google_ad1->ad_code !!}
        </div>
        <div class="mt-2">
            {!! $google_ad1->ad_code !!}
        </div>
        @endif
        <div class="mt-3 mb-2"><h4 class="border-bottom pb-2">Latest Articles</h4></div>
        <div class="swiper-container card bg-light shadow-sm">
            <div class="swiper-wrapper">
                @foreach($latest_posts as $latest_post)
                <div class="swiper-slide">
                    <div>
                    @php
                    $ext_opinion = preg_match('/\./', $latest_post->coverimage) ? preg_replace('/^.*\./', '', $latest_post->coverimage) : '';
                    @endphp
                    <a href="{{ route('blog_post',['slug'=>$latest_post->slug]) }}">
                        
                        <img class="lazy" src="/img/noimg.png" data-src="{{preg_replace('/.[^.]*$/', '',$latest_post->coverimage).'_314x240'.'.'.$ext_opinion}}" alt="{{ $latest_post->title }}" height="250" width="100%;"/>
                    </a>
                    <div class="card-body">
                        <a href="{{ route('blog_post',['slug'=>$latest_post->slug]) }}"><h5 class="text-dark">{{ str::limit($latest_post->title,$limit = 60, $end = '...') }}</h5></a>
                    </div>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="swiper-button-prev bg-opined-orange">
                <i class="fas fa-chevron-left text-white"></i>
            </div>
            <div class="swiper-button-next bg-opined-orange">
                <i class="fas fa-chevron-right text-white"></i>
            </div>
        </div>
        </div>
        </div>


    </div>
</div>

<div id="fb-root"></div>
<script>(function(d, s, id) {
var js, fjs = d.getElementsByTagName(s)[0];
if (d.getElementById(id)) return;
js = d.createElement(s); js.id = id;
js.src = 'https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.12&appId=1766000746745688&autoLogAppEvents=1';
fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>


@include('frontend.posts.comments.add_comment_modal')
@include('frontend.posts.modals.modal_add_gif')

{{-- <script src="https://apis.google.com/js/platform.js" async defer></script>
 <script src="https://platform.linkedin.com/in.js" type="text/javascript"></script> --}}

@endsection
@else
<script>
    newLocation();
    function newLocation() {
        window.location="/404";
    }
  </script>
@endif