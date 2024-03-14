@extends('frontend.layouts.app')
@section('title', ucfirst(Auth::user()->name).' - Opined')
@section('description','Read opinions from '.ucfirst(Auth::user()->name).' on Opined.Everyday, '.ucfirst(Auth::user()->name).' and thousands of other read, write, and share their opinions on Opined.')
@section('keywords',ucfirst(Auth::user()->name).' - Opined')

@push('meta')
<link rel="canonical" href="http://www.weopined.com/{{'@'.Auth::user()->username}}" />
<link href="http://www.weopined.com/{{'@'.Auth::user()->username}}" rel="alternate" reflang="en" />

<!-- Twitter Card data -->
<meta name="twitter:card" content="summary">
<meta name="twitter:site" content="@weopined">
<meta name="twitter:title" content="{{ucfirst(Auth::user()->name)}} - Opined">
<meta name="twitter:description" content="Read opinions from {{ucfirst(Auth::user()->name)}} on Opined.Everyday, {{ucfirst(Auth::user()->name)}} and thousands of other read, write, and share their opinions on Opined.">
<meta name="twitter:creator" content="@weopined">
<meta name="twitter:image" content="http://www.weopined.com/favicon.png">

<!-- Open Graph data -->
<meta property="og:title" content="{{ucfirst(Auth::user()->name)}} - Opined"/>
<meta property="og:type" content="website" />
<meta property="og:url" content="http://www.weopined.com/{{'@'.Auth::user()->username}}" />
<meta property="og:image" content="http://www.weopined.com/favicon.png" />
<meta property="og:description" content="Read opinions from {{ucfirst(Auth::user()->name)}} on Opined.Everyday, {{ucfirst(Auth::user()->name)}} and thousands of other read, write, and share their opinions on Opined." />
<meta property="og:site_name" content="Opined" />
<meta property="og:locale" content="en_US" />
<meta property="fb:app_id" content="1766000746745688" />
@endpush


@push('styles')
<style>

    #profileurl{
      background-color:#fff;
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

    @media only screen and (max-width: 576px) {
        .modal {
            width: 100vw !important;
            height: 100vh !important;
        }
    }
</style>
   <link href="/css/custom/edit-profile.css?<?php echo time();?>" rel="stylesheet" type="text/css" />
@endpush

 @push('scripts')
 <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.17.0/jquery.validate.min.js"></script>
 <script src="/js/custom/profile.js?<?php echo time();?>" type="text/javascript"></script>
 <script src="/js/custom/comment_opinions.js?<?php echo time();?>" type="text/javascript"></script>
 <script src="/js/custom/like.js?<?php echo time();?>" type="text/javascript"></script>
 <script src="/js/custom/delete_short_opinion.js?<?php echo time();?>" type="text/javascript"></script>
 <script src="/js/autosize.js" type="text/javascript"></script>
 <script type='text/javascript' src='/js/custom/delete.js'></script>
<script>

    $(document).ready(function(){

        $('textarea').on('keydown', function(e){
        // ignore backspaces
        var key = e.keyCode;
        if (!((key == 8) || (key == 32) || (key == 46) || (key >= 35 && key <= 40) || (key >= 65 && key <= 90))) {
          e.preventDefault();
        }
        var that = $(this);
         // we only need to check total words on spacebar (i.e the end of a word)
        if(e.keyCode == 32){
         setTimeout(function(){ // timeout so we get textarea value on keydown
         var string = that.val();
        // remove multiple spaces and trailing space
          string = string.replace(/ +(?= )| $/g,'');
          var words = string.split(' ');
         if(words.length == 3){
          that.val(words.join(' '));
          alert("Maximum Three Words Are Allowed For Keywords");
         }
        }, 1);
         }
        });

        $(document).on('click','#choosecover',function(f){
        f.preventDefault();
        $('#cover_image').click();

        });

        function readCoverURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (f) {
                    $('#cover_image').attr('src', e.target.result);
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
        $(document).on('change',"#cover_image",function(){
            var _thiscover=this;
           /* if (this.files.length > 0) {
                $("#submitcover").show();
                    } else {
                 $("#submitcover").hide();
                }*/
            var form = $('#upload-cover_image-form')[0];
            var formData = new FormData(form);
            if($('#cover_image')[0].files[0] !== undefined){
                $.ajax({
                    url:'/file/upload/USER_COVER',
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    type:'POST',
                    dataType: 'json',
                    data:formData,
                    cache : false,
                    contentType: false,
                    processData: false,
                    success:function(response){
                        if (response.status == 'success'){
                            $('#cover_imageurl').val(response.image);
                            readCoverURL(_thiscover);
                            $('#edit_profile_form_cover').submit();
                        }
                    },error:function(err){

                    }
                });
            }
            
        });
        


        $(document).on('click','#choosefile',function(e){
        e.preventDefault();
        $('#profileimage').click();

        });

        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $('#avatar-img').attr('src', e.target.result);
                }
                reader.readAsDataURL(input.files[0]);
            }
        }


        $(document).on('change',"#profileimage",function(){
            var _this=this;
            if (this.files.length > 0) {
                $("#submitform").show();
                    } else {
                 $("#submitform").hide();
                }
            var form = $('#upload-profileimage-form')[0];
            var formData = new FormData(form);
            if($('#profileimage')[0].files[0] !== undefined){
                $.ajax({
                    url:'/file/upload/USER_PROFILE',
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    type:'POST',
                    dataType: 'json',
                    data:formData,
                    cache : false,
                    contentType: false,
                    processData: false,
                    success:function(response){
                        if (response.status == 'success'){
                            $('#profileimageurl').val(response.image);
                            readURL(_this);
                        }
                    },error:function(err){

                    }
                });
            }
        });


            $('#change-username-form').validate({
                rules: {
                    'username': {
                        required: true,
                    },
                },
                messages: {
                    username:{
                        required:"Username is required !",
                    }
                },
                highlight: function (input) {
                    $(input).addClass('is-invalid');
                },
                unhighlight: function (input) {
                    $(input).removeClass('is-invalid');
                },
                errorPlacement: function (error, element) {
                    $(element).next().append(error);
                },
                submitHandler: function(form) {
                  if($('#username').val().match(/\s/g)){
                    $('#username').val($('#username').val().replace(/\s/g,''));
                  }

                  var pattern= new RegExp(/([A-Za-z0-9_](?:(?:[A-Za-z0-9_]|(?:\.(?!\.))){0,28}(?:[A-Za-z0-9_]))?)/);
                  if(!$('#username').val().trim().match(pattern)){
                      $(".response").css("background-color", '#f2dede');
                      $(".response").css("color", '#a94442');
                      $(".response").css("visibility", 'visible');
                      $(".response").show();
                      $(".response").html('Please enter a valid username');
                      $(".response").fadeOut(2500);
                  }else{

                    $.ajax({
                        url:"{{route('update_username')}}",
                        type:'POST',
                        headers:{ 'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content') },
                        data:$(form).serialize(),
                        dataType: 'json',
                        success: function(data) {
                            if (data.status == "error") {
                                errorsHtml = '';
                                $.each(data.errors, function(key, value) {
                                    errorsHtml = errorsHtml + value[0] + '<br/>';
                                });
                                $(".response").css("background-color", '#f2dede');
                                $(".response").css("color", '#a94442');
                                $(".response").css("visibility", 'visible');
                                $(".response").show();
                                $(".response").html(errorsHtml);
                                $(".response").fadeOut(2500);
                            }else{
                                $(".response").css("background-color", '#dff0d8');
                                $(".response").css("color", '#3c763d');
                                $(".response").css("visibility", 'visible');
                                $(".response").show();
                                $(".response").html(data.message);
                                $(".response").fadeOut(1500, function() {
                                    window.location.reload();
                                });
                            }
                        },
                        error: function(data) {
                        $(".response").css("background-color", '#d9edf7');
                        $(".response").css("color", '#31708f');
                        $(".response").css("visibility", 'visible');
                        $(".response").show();
                        $(".response").html('Oops !! Something Went Wrong , Please Try Again Later.');
                        $(".response").fadeOut(2500);
                        }
                    });
                  }
                }
            });



            $('#change-keywords-form').validate({
                rules: {
                    'keywords': {
                        required: true,
                    },
                },
                messages: {
                    keywords:{
                        required:"Keywords are required !",
                    }
                },
                highlight: function (input) {
                    $(input).addClass('is-invalid');
                },
                unhighlight: function (input) {
                    $(input).removeClass('is-invalid');
                },
                errorPlacement: function (error, element) {
                    $(element).next().append(error);
                },
                submitHandler: function(form) {
                    $.ajax({
                        url:"{{route('update_keywords')}}",
                        type:'POST',
                        headers:{ 'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content') },
                        data:$(form).serialize(),
                        dataType: 'json',
                        success: function(data) {
                            if (data.status == "error") {
                                errorsHtml = '';
                                $.each(data.errors, function(key, value) {
                                    errorsHtml = errorsHtml + value[0] + '<br/>';
                                });
                                $(".response").css("background-color", '#f2dede');
                                $(".response").css("color", '#a94442');
                                $(".response").css("visibility", 'visible');
                                $(".response").show();
                                $(".response").html(errorsHtml);
                                $(".response").fadeOut(2500);
                            }else{
                                $(".response").css("background-color", '#dff0d8');
                                $(".response").css("color", '#3c763d');
                                $(".response").css("visibility", 'visible');
                                $(".response").show();
                                $(".response").html(data.message);
                                $(".response").fadeOut(1500, function() {
                                    window.location.reload();
                                });
                            }
                        },
                        error: function(data) {
                        $(".response").css("background-color", '#d9edf7');
                        $(".response").css("color", '#31708f');
                        $(".response").css("visibility", 'visible');
                        $(".response").show();
                        $(".response").html('Oops !! Something Went Wrong , Please Try Again Later.');
                        $(".response").fadeOut(2500);
                        }
                    });
                }
            });
             $('#change-name-form').validate({
                rules: {
                    'name': {
                        required: true,
                    },
                },
                messages: {
                    keywords:{
                        required:"Name is required !",
                    }
                },
                highlight: function (input) {
                    $(input).addClass('is-invalid');
                },
                unhighlight: function (input) {
                    $(input).removeClass('is-invalid');
                },
                errorPlacement: function (error, element) {
                    $(element).next().append(error);
                },
                submitHandler: function(form) {
                    $.ajax({
                        url:"{{route('update_name')}}",
                        type:'POST',
                        headers:{ 'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content') },
                        data:$(form).serialize(),
                        dataType: 'json',
                        success: function(data) {
                            if (data.status == "error") {
                                errorsHtml = '';
                                $.each(data.errors, function(key, value) {
                                    errorsHtml = errorsHtml + value[0] + '<br/>';
                                });
                                $(".response").css("background-color", '#f2dede');
                                $(".response").css("color", '#a94442');
                                $(".response").css("visibility", 'visible');
                                $(".response").show();
                                $(".response").html(errorsHtml);
                                $(".response").fadeOut(2500);
                            }else{
                                $(".response").css("background-color", '#dff0d8');
                                $(".response").css("color", '#3c763d');
                                $(".response").css("visibility", 'visible');
                                $(".response").show();
                                $(".response").html(data.message);
                                $(".response").fadeOut(1500, function() {
                                    window.location.reload();
                                });
                            }
                        },
                        error: function(data) {
                        $(".response").css("background-color", '#d9edf7');
                        $(".response").css("color", '#31708f');
                        $(".response").css("visibility", 'visible');
                        $(".response").show();
                        $(".response").html('Oops !! Something Went Wrong , Please Try Again Later.');
                        $(".response").fadeOut(2500);
                        }
                    });
                }
            });
            autosize($('#bio'));

        });

    $(document).on('click','#copyProfileLink',function(){
        var $temp = $("<input>");
        $("body").append($temp);
        $temp.val($('#profileurl').val()).select();
        document.execCommand("copy");
        $temp.remove();
    });
</script>
@endpush


@section('content')
    <div class="row">
        <div class="offset-md-2 col-md-8 col-12">
        @include('frontend.partials.message')
        </div>
    </div>
@include('frontend.profile.modals.change_username')
@include('frontend.profile.modals.change_keywords')
@include('frontend.profile.modals.change_name')
@include('frontend.profile.components.profilecard')
@include('frontend.posts.crud.delete')
@include('frontend.partials.modal_opinion_likes')
@if(Request::path()=='me/profile')
    {{--  @if(count($latest_posts)>0)
        <div class="row">
            <div class="offset-md-2 col-md-8 col-12">
                <h5 class="pb-3 my-4 font-weight-normal" style="color:#244363;border-bottom:2px solid black;">Articles
                    <span><a href="/me/opinions" class="btn btn-sm float-right" style="background-color:white;color:#fff;outline:none;border:2px solid black;">
                            <i class="fas fa-arrow-right" style="color:black;"></i></a>
                    </span>
                </h5>
                <div class="row">
                    @foreach($latest_posts as $post)
                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12 portfolio-item mb-4" id="post-{{$post->id}}">
                        @include('frontend.posts.components.post-card')
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif  s--}}

    <div>
        @include('frontend.counter.counter',[$short_opinions,$followers,$following,$posts])
    </div>

    {{--  @if(count($achievements)>0)
    <div class="row">
        <div class="offset-md-2 col-md-8 col-12">
            <h5 class="pb-2 my-4 font-weight-normal" style="color:#244363;border-bottom:2px solid black;">
            Achievements
            </h5>
            <div class="container">
                <div class="row">
                    @if(count($unlock_achievements)>0)
                        @foreach($unlock_achievements as $achievement)
                            @include('frontend.achievements.achievements_grid',[$achievement,'flag'=>true])
                        @endforeach
                    @endif
                    @foreach($achievements as $achievement)
                        @include('frontend.achievements.achievements_grid',[$achievement,'flag'=>false])
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
                <div class="col" id="append-div">
                @foreach($short_opinions as $opinion)
                    @include('frontend.opinions.components.profile_opinion_card',['user'=>$opinion->user])
                @endforeach
                </div>
                @include('frontend.partials.spinner')
            </div>
        </div>
        @include('frontend.opinions.crud.delete')
        @include('frontend.partials.loadmorescript')
    @endif
@endif


@if(Request::path()=='me/circle' || Request::path()=='me/in_circle')
        <input id="path" type="hidden" value="{{Request::path()}}" />
        <input id="totalpage" type="hidden" value="{{ceil($users->total()/$users->perPage())}}" />

        <div class="row">
            <div class="offset-md-2 col-md-8 col-sm-12 col-12">
                @if(Request::path()=='me/circle')
                <h4 class="pb-3 mb-4 border-bottom">{{ucfirst(Auth::user()->name)}} Follows</h4>
                @endif

                @if(Request::path()=='me/in_circle')
                    <h4 class="pb-3 mb-4 border-bottom">{{ucfirst(Auth::user()->name)}} is followed by</h4>
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
