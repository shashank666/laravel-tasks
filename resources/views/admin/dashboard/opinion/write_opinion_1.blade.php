@extends('admin.layouts.plain_1')
@section('title','Opinion write - Opined')
@section('description','Opined')
@push('meta')

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
@if($company_ui_settings->show_google_ad=='1')
{!! $company_ui_settings->google_adcode !!}
@endif
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
<div class="header">
        <div class="container">
          <div class="header-body">
            <div class="row align-items-end">
                <div class="col">
                    <h6 class="header-pretitle">
                    Write Opinion
                    </h6>
                    <h1 class="header-title">
                    
                    
                    </h1>
                </div>
               <div class="col-auto">
               </div>
            </div>
          </div>
        </div>
</div>
<div class="container">
    <div class="col-lg-10 col-md-10 col-12" style="margin-top: 25px;">
        @include('admin.dashboard.opinion.create_opinion')
    </div>  
</div>

@include('frontend.opinions.comments.add_comment_modal')
@include('frontend.posts.modals.modal_add_gif')
@include('frontend.opinions.components.youtube_video_modal')
@include('frontend.opinions.components.embed_code_modal')
@include('frontend.opinions.components.message_modal')
@include('frontend.opinions.crud.delete')

@endsection
