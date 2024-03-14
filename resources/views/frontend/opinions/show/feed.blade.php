@extends('frontend.layouts.app')
@section('title','My Feed - Opined')
@section('description','My Feed on Opined')
@section('keywords','MyFeed')

@push('meta')
<link rel="canonical" href="https://www.weopined.com/feed" />
<link href="https://www.weopined.com/feed" rel="alternate" reflang="en" />

<!-- Twitter Card data -->
<meta name="twitter:card" content="summary">
<meta name="twitter:site" content="@weopined">
<meta name="twitter:title" content="My Feed - Opined">
<meta name="twitter:description" content="">
<meta name="twitter:creator" content="@weopined">
<meta name="twitter:image" content="https://www.weopined.com/favicon.png">

<!-- Open Graph data -->
<meta property="og:title" content="My Feed - Opined" />
<meta property="og:type" content="website" />
<meta property="og:url" content="https://www.weopined.com/feed" />
<meta property="og:image" content="https://www.weopined.com/favicon.png" />
<meta property="og:description" content="" />
<meta property="og:site_name" content="Opined" />
<meta property="og:locale" content="en_US" />
<meta property="fb:app_id" content="1766000746745688" />
@endpush
@push('scripts')
<script type="text/javascript" src="/js/custom/profile.js?<?php echo time();?>"></script>
<script async src="/js/custom/comment_opinions.js?<?php echo time();?>" type="text/javascript"></script>
<script async src="/js/custom/like.js?<?php echo time();?>" type="text/javascript"></script>
<script async src="/js/custom/threads.js?<?php echo time();?>" type="text/javascript"></script>
<script src="/js/custom/opinion_comments.js?<?php echo time();?>" type="text/javascript"></script>
<script async src="/js/custom/delete_short_opinion.js?<?php echo time();?>" type="text/javascript"></script>

@endpush

@push('styles')
<link href="/vendor/emojionearea/emojionearea.min.css" type="text/css" rel="stylesheet" />
<link href='/css/jquery-ui.css' rel='stylesheet' type='text/css'>
<link href='/css/custom/user_card.css' rel='stylesheet' type='text/css'>
@endpush

@push('scripts')
{{--  @if($company_ui_settings->show_google_ad=='1')
{!! $company_ui_settings->google_adcode !!}
@endif  --}}
<script src="/vendor/emojionearea/emojionearea.min.js" type="text/javascript"></script>
<script type="text/javascript" src="/js/jquery.caret-atwho.min.js"></script>
<script src='/js/custom/user_short_card.js' type='text/javascript'></script>
<script src='/js/jquery-ui.js' type='text/javascript'></script>
<script type="text/javascript">
    $(document).ready(function () {
        $('#btn_post').attr('disabled', 'disabled');
        $("#opinion_comment_textarea").emojioneArea({
            pickerPosition: "bottom"
        });

        $('#write_opinion').atwho({
            at: "#",
            limit: 200,
            searchKey: 'name',
            data: 'https://weopined.com',
            callbacks: {
                remoteFilter: function (query, callback) {
                    $.getJSON("/search/threads", {
                        q: query
                    }, function (data) {
                        callback(data.threads);
                    });
                },
                afterMatchFailed: function (at, el) {
                    // 32 is spacebar
                    if (at == '#') {
                        tags.push(el.text().trim().slice(1));
                        this.model.save(tags);
                        this.insert(el.text().trim());
                        return false;
                    }
                }
            }
        });
    });

    $(document).on('keyup', '#write_opinion', function () {
        if ($(this).val().trim() != "" && $(this).val().trim().length > 0) {
            $("#btn_post").removeAttr('disabled');
        } else {
            $("#btn_post").attr('disabled', 'disabled');
        }
    });

</script>
@endpush


@section('content')
@include('frontend.partials.modal_opinion_likes')
<div class="row">


    <div class="col-lg-3 col-md-3 col-12">
        <div class="d-xl-inline d-lg-inline d-md-inline d-sm-none d-none">
            @if(count($followed_threads)>0)
            
            <div class="row mb-5 trending-card">
                <h4 class="pb-3 mb-3 font-weight-normal header-card">Topics You Follow<span><a href="threads/followed" class="btn btn-sm float-right" style="background-color:white;color:rgba(36,67,99,255);outline:none;margin-left:25px">See All</a></span></h4>
                @php($colors=['rgba(36,67,99,255)'])
                @foreach($followed_threads as $followed_thread)
                    <div class="col-12">
                    @include('frontend.threads.components.follow_thread_card2',['thread'=>$followed_thread->thread])
                    </div>
                @endforeach
            </div>
            @endif

            @if(count($followed_threads)<1) 
                <div class="row mb-5 trending-card">
                    <h4 class="pb-3 mb-3 font-weight-normal header-card">Topics You Follow<span><a
                        href="threads/followed" class="btn btn-sm float-right"
                        style="background-color:white;color:rgba(36,67,99,255);outline:none;margin-left:25px">See All</a></span></h4>
                    <a class="btn btn-block btn-outline-none text-light" href="/threads/latest"
                        style="color: black !important">Threads You Follow Will Appear Here</a>
                </div>
                @endif


                {{--  @if(count($threads)>0)
                
               <div class="row mb-5 trending-card">
                <h4 class="pb-3 mb-4 font-weight-normal header-card">Trending Threads<span><a href="/threads/trending" class="btn btn-sm float-right" style="background-color:white;color:rgba(36,67,99,255);outline:none;margin-bottom:0; margin-left:45px;">See All</a></span></h4>
                    @php($colors=['rgba(36,67,99,255)'])
                    @foreach($threads as $trending_thread)
                        <div class="col-12">
                        @include('frontend.threads.components.thread_card2',['thread'=>$trending_thread->thread])
                        </div>
                    @endforeach
                </div>  
                @endif  --}}
                {{--  @if($company_ui_settings->show_google_ad=='1' && $google_ad)
                <div class="mt-3">
                    {!! $google_ad->ad_code !!}
                </div>
                @endif  --}}

                {{--  @if(count($latest_threads)>0)
                <div style="position:-webkit-sticky;position:sticky;top: 60px;">
                       
                        <div class="row trending-card">
                            <h4 class="pb-3 mb-3 font-weight-normal header-card pt-3">Latest Threads<span><a href="threads/latest" class="btn btn-sm float-right" style="background-color:white;color:rgba(36,67,99,255);outline:none; margin-left:65px">See All</a></span></h4>
                                @php($colors=['rgba(36,67,99,255)'])
                                @foreach($latest_threads as $thread)
                                <div class="col-12">
                                @include('frontend.threads.components.thread_card2')
                                </div>
                                @endforeach
                        </div>
                </div>
                @endif  --}}



        </div>
    </div>
    <div class="col-lg-3 col-md-3 col-12 d-xl-none d-lg-none d-md-none d-sm-inline d-inline">

    </div>
    <div class="col-lg-6 col-md-6 col-12">
        @include('frontend.opinions.crud.create_all')

        @if(count($opinions)>0)
        <input id="path" type="hidden" value="{{Request::path()}}" />
        <input id="totalpage" type="hidden" value="{{ceil($opinions->total()/$opinions->perPage())}}" />

        <div id="append-div">
            @include('frontend.opinions.components.opinions_loop')
        </div>
        @include('frontend.partials.spinner')

        @else
        <div class="card shadow">
            <div class="card-header" style="background-color: rgba(0, 0, 0, 0.12) !important; ">
            </div>
            <div class="card-body">
                <h4 style="text-align: center;">
                    <span style="color: #495057">Opinions From People For You And Topics You Follow Will Appear Hear</h4>
                <br>
                <h5 style="text-align: center;color: #495057">
                    Start Building Your Community & Follow Your Interests!
                </h5>
            </div>
            <div class="card-footer" style="background-color: rgba(0, 0, 0, 0.12) !important;"></div>
        </div>
                {{--  <div class="d-xl-none d-lg-none d-md-none d-sm-inline d-inline">
                    <h4 class="pb-3 mb-3 font-weight-normal" style="padding-top: 6%;">
                    Contributors <span style="font-size: 13px;">To Have In Your Circle</span></h4>
                   @include('frontend.profile.components.tofollow_mobile')
                </div>  --}}

        @endif

    </div>

    <div class="col-lg-3 col-md-3 col-12 d-xl-inline d-lg-inline d-md-inline d-sm-none d-none">
        <div class="row">
            <div class="col-12">
                <div class="trending-card  mb-5">
                    <h4 class="pb-3 mb-3 font-weight-normal header-card">
                        Influencers <span style="font-size: 13px;">To Follow</span></h4>
                    @include('frontend.profile.components.influencer')
                </div>
                {{--  <div class="trending-card row mb-5">
                    <h4 class="pb-3 mb-3 font-weight-normal header-card pt-5">Top
                        Contributors<span style="font-size: 13px;"> To Follow</span></h4>
                    @include('frontend.profile.components.tofollowcard')
                </div>  --}}
            </div>
        </div>
        {{--  @if($company_ui_settings->show_google_ad=='1' && $google_ad)
        <div class="mt-3">
            {!! $google_ad->ad_code !!}
        </div>
        @endif  --}}
        <div class="shadow" style="height: 1vw;position: -webkit-sticky;position: sticky;top: 60%;">
            @include('frontend.profile.components.feed_footer')
        </div>
    </div>
</div>

@include('frontend.opinions.comments.add_comment_modal')
@include('frontend.posts.modals.modal_add_gif')
@include('frontend.opinions.components.youtube_video_modal')
@include('frontend.opinions.components.embed_code_modal')
@include('frontend.opinions.components.message_modal')
@include('frontend.opinions.crud.delete')

@if(count($opinions)>0)
@include('frontend.partials.loadmorescript')
@endif
@endsection
