@extends('frontend.layouts.app')
@section('title',"Opinion by ".$opinion->user['name']." | Opined")
@if($opinion->links ==null)
@section('description',"Read opinion of ".$opinion->user['name'] ." on ".$opinion->hash_tags." on Opined | ".substr($opinion->plain_body,0,50)."...opined")
@section('keywords',str_replace("#"," ",$opinion->hash_tags))
@else
@section('description',"Read opinion of ".$opinion->user['name'] ." on ".strip_tags($opinion->body)." on Opined | Discuss your views on ".substr(strip_tags($opinion->body),0,50)."...opined")
@section('keywords',str_replace("#"," ",strip_tags($opinion->body)))
@endif

@push('meta')
<link rel="canonical" href="https://www.weopined.com/{{ '@'.$opinion->user['username'].'/opinion/'.$opinion->uuid}}" />
<link href="https://www.weopined.com/{{ '@'.$opinion->user['username'].'/opinion/'.$opinion->uuid}}" rel="alternate" reflang="en" />

<!-- Twitter Card data -->
<meta name="twitter:card" content="summary">
<meta name="twitter:site" content="@weopined">
<meta name="twitter:title" content="{{$opinion->user['name'].' | Opined'}}">
<meta name="twitter:description" content="{{strip_tags($opinion->body)}}">
<meta name="twitter:creator" content="@weopined">
<meta property="twitter:image" content="{{$opinion->cover}}" />
<meta name="twitter:image" content="http://www.weopined.com/img/Mobile-opined.png">

<!-- Open Graph data -->
<meta property="og:title" content="{{$opinion->user['name'].' | Opined '}}" />
<meta property="og:type" content="website" />
<meta property="og:url" content="https://www.weopined.com/{{ '@'.$opinion->user['username'].'/opinion/'.$opinion->uuid}}" />

<meta property="og:image" content="{{$opinion->cover}}" />
<meta property="og:image" content="{{$opinion->thumbnail}}" />
<meta property="og:image" content="http://www.weopined.com/favicon.png" />
<meta property="og:description" content="{{strip_tags($opinion->body)}}" />
<meta property="og:site_name" content="Opined" />
<meta property="og:locale" content="en_US" />
<meta property="fb:app_id" content="1766000746745688" />
@endpush

@push('styles')
<link href="/vendor/emojionearea/emojionearea.min.css" type="text/css" rel="stylesheet" />
<style type="text/css">
    .page-title-wrapper {
        display: none;
      }
</style>

@endpush

@push('scripts')
{{-- @if($company_ui_settings->show_google_ad=='1')
    {!! $company_ui_settings->google_adcode !!}
@endif --}}
<script src="/vendor/emojionearea/emojionearea.min.js" type="text/javascript"></script>
<script type="text/javascript">
  $(document).ready(function() {
      $("#opinion_comment_textarea").emojioneArea({
         pickerPosition: "bottom"
      });
});
</script>
<script src="/js/custom/opinion_comments.js?<?php echo time();?>" type="text/javascript"></script>
<script src="/js/custom/like.js" type="text/javascript"></script>
<script async src="/js/custom/threads.js" type="text/javascript"></script>
<script src="/js/custom/delete_short_opinion.js" type="text/javascript"></script>
<script defer src="/js/custom/profile.js?<?php echo time();?>" type="text/javascript"></script>

<script type="text/javascript">
   $(document).ready(function(){
    loadOpinionComments('{{$opinion->id}}',1,'next');
});
</script>
@endpush

@section('content')
@include('frontend.partials.modal_opinion_likes')
@if($opinion->is_active==1)
<div class="page-title-wrapper">
    <h1 class="page-title">
        <span data-ui-id="page-title-wrapper">Opinion by {{ucfirst($opinion->user['name'])}} | Opined</span></h1>
</div>

<div class="row">
    <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 col-12 d-xl-inline d-lg-inline d-md-inline d-sm-none d-none">
            {{-- @if($company_ui_settings->show_google_ad=='1' && $google_ad)
            <div class="mt-3">
                {!! $google_ad->ad_code !!}
            </div>
            @endif --}}
    </div>

    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12">
        @include('frontend.opinions.components.thread-opinion-card-individual',['user'=>$opinion->user])
    </div>

    <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 col-12">
            {{-- @if($company_ui_settings->show_google_ad=='1' && $google_ad)
            <div class="mt-3">
                {!! $google_ad->ad_code !!}
            </div>
            @endif --}}
    </div>

</div>

@include('frontend.opinions.comments.add_comment_modal')
@include('frontend.posts.modals.modal_add_gif')
@include('frontend.opinions.crud.delete')
@include('frontend.auth.new_login_modal')
@include('frontend.auth.modal_register')
@include('frontend.auth.modal_forgot')



@endsection
@else
<script>
    newLocation();
    function newLocation() {
        window.location="https://weopined.com";
    }
</script>
@endif