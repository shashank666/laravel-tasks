@extends('frontend.layouts.app')
@section('title',"Trending opinions about ".ucfirst($thread->name). " - Opined")
@section('description',ucfirst($thread->name)." on Opined. Your opinion has power. Opined aims to put power back to common people where everyone can express and discuss opinions on #".ucfirst($thread->name))
@section('keywords',ucfirst($thread->name).", discuss opinion on ".ucfirst($thread->name))

@push('meta')
<link rel="canonical" href="https://www.weopined.com/thread/{{$thread->slug}}" />
<link href="https://www.weopined.com/thread/{{$thread->slug}}" rel="alternate" reflang="en" />

<!-- Twitter Card data -->
<meta name="twitter:card" content="summary">
<meta name="twitter:site" content="@weopined">
<meta name="twitter:title" content="{{'#'.ucfirst($thread->name)}} - Opined">
<meta name="twitter:description" content="What is your opinion about {{'#'.ucfirst($thread->name)}} ? Write your opinion only on Opined - where every opinion matters !">
<meta name="twitter:creator" content="@weopined">
<meta name="twitter:image" content="https://www.weopined.com/favicon.png">

<!-- Open Graph data -->
<meta property="og:title" content="{{'#'.ucfirst($thread->name)}} - Opined" />
<meta property="og:type" content="website" />
<meta property="og:url" content="https://www.weopined.com/thread/{{$thread->slug}}" />
<meta property="og:image" content="https://www.weopined.com/favicon.png" />
<meta property="og:description" content="What is your opinion about {{'#'.ucfirst($thread->name)}} ? Write your opinion only on Opined - where every opinion matters !" />
<meta property="og:site_name" content="Opined" />
<meta property="og:locale" content="en_US" />
<meta property="fb:app_id" content="1766000746745688" />
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
  $(document).ready(function() {
      $('#btn_post').attr('disabled','disabled');
      $("#opinion_comment_textarea").emojioneArea({
        pickerPosition: "bottom"
      });

      $('#write_opinion').atwho({
          at: "#",
          limit: 200,
          searchKey : 'name',
          data: 'https://weopined.com',
          callbacks: {
              remoteFilter: function(query, callback) {
                  $.getJSON("/search/threads", {
                      q: query
                  }, function(data) {
                      callback(data.threads);
                  });
              },
              afterMatchFailed: function(at, el) {
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

  $(document).on('keyup','#write_opinion', function () {
    if ($(this).val().trim() != "" && $(this).val().trim().length>0) {
      $("#btn_post").removeAttr('disabled');
    } else {
      $("#btn_post").attr('disabled','disabled');
    }
  });

</script>
<script src="/js/custom/opinion_comments.js?<?php echo time();?>" type="text/javascript"></script>
<script async src="/js/custom/like.js?<?php echo time();?>" type="text/javascript"></script>
<script async src="/js/custom/threads.js?<?php echo time();?>" type="text/javascript"></script>
<script async src="/js/custom/delete_short_opinion.js?<?php echo time();?>" type="text/javascript"></script>
<script defer src="/js/custom/profile.js?<?php echo time();?>" type="text/javascript"></script>
@endpush



@section('content')
@include('frontend.partials.modal_opinion_likes')
<div class="row">
        <div class="col-lg-3">
            @include('frontend.opinions.components.thread_header')
            <div class="d-xl-block d-lg-block d-md-block d-sm-none d-none">
               <h4 class="pb-3 mb-4 font-weight-normal" style="color:#244363;border-bottom:1px solid #495057;">Trending Threads<span><a href="/threads/trending" class="btn btn-sm waves-effect waves-float float-right" style="background-color:#fff;color:#fff;outline:none;"><i class="fas fa-arrow-right"></i></a></span></h4>

                @include('frontend.opinions.components.threads_block')
            </div>

        </div>

        <div class="col-lg-6">
            @include('frontend.opinions.crud.create_thread_page')

            
            <input id="path" type="hidden" value="{{Request::path()}}" />
            <input id="totalpage" type="hidden" value="{{ceil($thread_opinions->total()/$thread_opinions->perPage())}}" />
            @include('frontend.opinions.components.tabs',['section'=>'trending'])
            <div class="tab-content">
                <div id="latest" class="tab-pane fade">
                  <!--<h3>Latest Opinions</h3>-->
                  
                </div>
                <div id="trending" class="tab-pane fade show active">
                 <!-- <h3>Trending Opinions</h3>-->
                 @if(count($trending_opinions)>0)
                   <div id="append-div">
                   @include('frontend.opinions.show.by_thread_cards.trending')
                	</div>
                      @include('frontend.partials.spinner')
                  @else
                  <h5>No Opinion to Show</h5>
                  @endif
                </div>
                <div id="circle" class="tab-pane fade">
                  <!--<h3>In Your Circle</h3>-->
                  
                </div>
              </div>

            

        </div>

        <div class="col-lg-3">
                {{-- OFFER POSTER AND COUNT
                <div>
                    <a href="{{ route('offer') }}"><img src="/img/offer_sm.png" class="img-fluid rounded"/></a>
                </div>

                <div class="mt-3">
                    @include('frontend.partials.offer_count')
                </div>
                 --}}

                 <div class="d-xl-none d-lg-none d-md-none d-sm-block d-block">
                        @include('frontend.opinions.components.trending_threads_list')
                </div>
                <div style="position: -webkit-sticky;position: sticky;top:2vh;">
                <div class="mt-3">
                        {{--  @if($company_ui_settings->show_google_ad=='1' && $google_ad)
                        <div class="mt-3">
                            {!! $google_ad->ad_code !!}
                        </div>
                        @endif  --}}
                </div>
              </div>
        </div>
</div>

@include('frontend.opinions.comments.add_comment_modal')
@include('frontend.posts.modals.modal_add_gif')
@include('frontend.opinions.components.youtube_video_modal')
@include('frontend.opinions.components.embed_code_modal')
@include('frontend.opinions.components.message_modal')
@include('frontend.opinions.crud.delete')
@include('frontend.auth.new_login_modal')
@include('frontend.auth.modal_register')
@include('frontend.auth.modal_forgot')


@if(count($thread_opinions)>0)
@include('frontend.partials.loadmorescript')
@endif
@endsection


