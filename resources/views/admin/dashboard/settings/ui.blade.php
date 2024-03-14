@extends('admin.layouts.app')
@section('title','UI Settings')


@push('styles')
<link href="/public_admin/assets/libs/noty/noty.min.css" rel="stylesheet" type="text/css"/>
@endpush

@push('scripts')
<script src="/public_admin/assets/libs/noty/noty.min.js"></script>
<script>
    $(document).on('change','#adblocker',function(){
        if($(this).is(':checked')){
            var adblocker=1;
        }else{
            var adblocker=0;
        }
        $.ajax({
            url:"{{route('admin.ui.adblocker')}}",
            type:"POST",
            data:{'adblocker':adblocker},
            dataType:'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success:function(response){
                new Noty({
                    theme:'sunset',
                    type:response.status,
                    text: response.message,
                    timeout:3500,
                }).show();
            },error:function(err){
                new Noty({
                    theme:'sunset',
                    type:'error',
                    text: 'FAILED TO UPDATE ADBLOCKER SETTING',
                    timeout:3500,
                }).show();
            }
        });
    });

    $(document).on('change','#webpush_notification',function(){
        if($(this).is(':checked')){
            var webpush_notification=1;
        }else{
            var webpush_notification=0;
        }
        $.ajax({
            url:"{{route('admin.ui.webpush_notification')}}",
            type:"POST",
            data:{'webpush_notification':webpush_notification},
            dataType:'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success:function(response){
                new Noty({
                    theme:'sunset',
                    type:response.status,
                    text: response.message,
                    timeout:3500,
                }).show();
            },error:function(err){
                new Noty({
                    theme:'sunset',
                    type:'error',
                    text: 'FAILED TO UPDATE WEBPUSH NOTIFICATION SETTING',
                    timeout:3500,
                }).show();
            }
        });
    });

    $(document).on('change','#check_email_verified',function(){
        if($(this).is(':checked')){ var check_email_verified=1;}
        else{ var check_email_verified=0; }
        $.ajax({
            url:"{{route('admin.ui.verification',['field'=>'check_email_verified'])}}",
            type:"POST",
            data:{'switch':check_email_verified},
            dataType:'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success:function(response){
                new Noty({
                    theme:'sunset',
                    type:response.status,
                    text: response.message,
                    timeout:3500,
                }).show();
            },error:function(err){
                new Noty({
                    theme:'sunset',
                    type:'error',
                    text: 'FAILED TO UPDATE USER EMAIL VERIFICATION SETTING',
                    timeout:3500,
                }).show();
            }
        });
    });

    $(document).on('change','#check_mobile_verified',function(){
        if($(this).is(':checked')){ var check_mobile_verified=1;}
        else{ var check_mobile_verified=0; }
        $.ajax({
            url:"{{route('admin.ui.verification',['field'=>'check_mobile_verified'])}}",
            type:"POST",
            data:{'switch':check_mobile_verified},
            dataType:'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success:function(response){
                new Noty({
                    theme:'sunset',
                    type:response.status,
                    text: response.message,
                    timeout:3500,
                }).show();
            },error:function(err){
                new Noty({
                    theme:'sunset',
                    type:'error',
                    text: 'FAILED TO UPDATE USER MOBILE VERIFICATION SETTING',
                    timeout:3500,
                }).show();
            }
        });
    });

    $(document).on('change','#invite_btn',function(){
        if($(this).is(':checked')){
            var invite_btn=1;
        }else{
            var invite_btn=0;
        }
        $.ajax({
            url:"{{route('admin.ui.invite')}}",
            type:"POST",
            data:{'invite_btn':invite_btn},
            dataType:'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success:function(response){
                new Noty({
                    theme:'sunset',
                    type:response.status,
                    text: response.message,
                    timeout:3500,
                }).show();
            },error:function(err){
                new Noty({
                    theme:'sunset',
                    type:'error',
                    text: 'FAILED TO UPDATE INVITE BTN SETTING',
                    timeout:3500,
                }).show();
            }
        });
    });

    $(document).on('submit','.pagination-form',function(e){
        e.preventDefault();
        $.ajax({
            url:$(this).attr('action'),
            type:"POST",
            data:$(this).serialize(),
            dataType:'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success:function(response){
                new Noty({
                    theme:'sunset',
                    type:response.status,
                    text: response.message,
                    timeout:3500,
                }).show();
            },error:function(err){
                new Noty({
                    theme:'sunset',
                    type:'error',
                    text: 'FAILED TO UPDATE PAGINATION SETTING',
                    timeout:3500,
                }).show();
            }
        });
    });


    $(document).on('change','#show_google_ad',function(){
        if($(this).is(':checked')){
            var show_google_ad=1;
        }else{
            var show_google_ad=0;
        }
        $.ajax({
            url:"{{route('admin.ui.show_google_ad')}}",
            type:"POST",
            data:{'show_google_ad':show_google_ad},
            dataType:'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success:function(response){
                new Noty({
                    theme:'sunset',
                    type:response.status,
                    text: response.message,
                    timeout:3500,
                }).show();
            },error:function(err){
                new Noty({
                    theme:'sunset',
                    type:'error',
                    text: 'FAILED TO UPDATE GOOGLE-AD SETTING',
                    timeout:3500,
                }).show();
            }
        });
    });

    $(document).on('submit','#google_adcode_form',function(e){
        e.preventDefault();
        $.ajax({
            url:$(this).attr('action'),
            type:"POST",
            data:$(this).serialize(),
            dataType:'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success:function(response){
                new Noty({
                    theme:'sunset',
                    type:response.status,
                    text: response.message,
                    timeout:3500,
                }).show();
            },error:function(err){
                new Noty({
                    theme:'sunset',
                    type:'error',
                    text: 'FAILED TO UPDATE GOOGLE ADCODE',
                    timeout:3500,
                }).show();
            }
        });
    });


</script>
@endpush

@section('content')
@include('admin.partials.header_title',['header_title'=>'UI Settings'])
<div class="container">
    <div class="row">
        <div class="col-md-6 col-12">
                <div class="card">
                        <div class="card-header">
                                <h4 class="card-header-title">UI SETTINGS</h4>
                        </div>
                        <div class="card-body">

                                        <form method="POST" action="{{route('admin.ui.adblocker')}}">
                                            {{csrf_field()}}
                                            <div class="custom-control custom-switch mb-3">
                                                    @if($company_ui_settings->adblocker==1)
                                                            <input type="checkbox" class="custom-control-input" id="adblocker" name="adblocker" checked />
                                                    @else
                                                    <input type="checkbox" class="custom-control-input" id="adblocker" name="adblocker"  />
                                                    @endif
                                                    <label class="custom-control-label" for="adblocker">AD-BLOCKER</label>
                                            </div>
                                        </form>

                                        <form method="POST" action="{{route('admin.ui.invite')}}">
                                                {{csrf_field()}}

                                                <div class="custom-control custom-switch mb-3">
                                                    @if($company_ui_settings->invite_btn==1)
                                                    <input type="checkbox" class="custom-control-input" id="invite_btn" name="invite_btn" checked/>
                                                    @else
                                                    <input type="checkbox" class="custom-control-input" id="invite_btn" name="invite_btn" />
                                                    @endif
                                                    <label  class="custom-control-label" for="invite_btn">INVITE-BUTTON</label>
                                                </div>

                                        </form>

                                    <form method="POST" action="{{route('admin.ui.verification',['field'=>'check_email_verified'])}}">
                                            {{csrf_field()}}
                                                <div class="custom-control custom-switch mb-3">
                                                    @if($company_ui_settings->check_email_verified==1)
                                                    <input type="checkbox" class="custom-control-input" id="check_email_verified" name="check_email_verified" checked />
                                                    @else
                                                    <input type="checkbox" class="custom-control-input" id="check_email_verified" name="check_email_verified"  />
                                                    @endif
                                                    <label for="check_email_verified" class="custom-control-label">USER EMAIL VERIFICATION</label>
                                                </div>
                                    </form>

                                    <form method="POST" action="{{route('admin.ui.verification',['field'=>'check_mobile_verified'])}}">
                                            {{csrf_field()}}
                                                <div class="custom-control custom-switch mb-3">
                                                    @if($company_ui_settings->check_mobile_verified==1)
                                                    <input type="checkbox" class="custom-control-input" id="check_mobile_verified" name="check_mobile_verified" checked />
                                                    @else
                                                    <input type="checkbox" class="custom-control-input" id="check_mobile_verified" name="check_mobile_verified"  />
                                                    @endif
                                                    <label for="check_mobile_verified"  class="custom-control-label">USER MOBILE VERIFICATION</label>
                                                </div>
                                    </form>

                                    <form method="POST" action="{{route('admin.ui.webpush_notification')}}">
                                            {{csrf_field()}}
                                            <div class="form-group">
                                                <div class="custom-control custom-switch mb-3">
                                                    @if($company_ui_settings->webpush_notification==1)
                                                    <input type="checkbox" class="custom-control-input" id="webpush_notification" name="webpush_notification" checked />
                                                    @else
                                                    <input type="checkbox" class="custom-control-input" id="webpush_notification" name="webpush_notification"  />
                                                    @endif
                                                    <label class="custom-control-label"  for="webpush_notification">WEBPUSH NOTIFICATION</label>
                                                </div>
                                            </div>
                                    </form>
                        </div>
                </div>
        </div>
        <div class="col-md-6 col-12">
                <div class="card">
                        <div class="card-header">
                                <h4 class="card-header-title">GOOGLE AD SETTING</h4>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{route('admin.ui.show_google_ad')}}">
                            {{csrf_field()}}
                            <div class="form-group">
                                <div class="custom-control custom-switch">
                                    @if($company_ui_settings->show_google_ad==1)
                                    <input type="checkbox" class="custom-control-input" id="show_google_ad" name="show_google_ad" checked />
                                    @else
                                    <input type="checkbox" class="custom-control-input" id="show_google_ad" name="show_google_ad"  />
                                    @endif
                                    <label class="custom-control-label" for="show_google_ad">SHOW GOOGLE AD</label>
                                </div>
                            </div>
                            </form>
                        <form id="google_adcode_form" method="POST" action="{{ route('admin.ui.google_adcode') }}">
                            {{ csrf_field() }}
                            <div class="form-group">
                            <label>GOOGLE ADCODE</label>

                                <textarea class="form-control" name="google_adcode" rows="8" required>
                                    {!! trim($company_ui_settings->google_adcode) !!}
                                </textarea>
                            </div>
                            </div>
                            <button class="btn btn-primary btn-block" id="btn_google_adcode">Update ADCODE</button>
                        </form>
                        </div>
                </div>
        </div>

        <div class="card">
                <div class="card-header">
                        <h4 class="card-header-title">PAGINATION SETTINGS</h4>
                </div>
                <div class="card-body">
                        <div class="row">
                                <div class="col-md-4">
                                        <form method="POST" class="pagination-form"  action="{{ route('admin.ui.pagination',['field'=>'latest_posts_pagination']) }}">
                                            {{ csrf_field() }}
                                                <label>LATEST POSTS PER PAGE LIMIT</label>
                                                <div class="input-group mb-3">
                                                    <input class="form-control" type="number" name="pagination" min="12" step="2" value="{{ $company_ui_settings->latest_posts_pagination }}" />
                                                    <div class="input-group-append">
                                                            <button type="submit" class="btn btn-primary"><i class="fe fe-check"></i></button>
                                                    </div>
                                                </div>
                                        </form>

                                        <form method="POST" class="pagination-form" action="{{ route('admin.ui.pagination',['field'=>'trending_posts_pagination']) }}">
                                                {{ csrf_field() }}
                                                <label>TRENDING POSTS PER PAGE LIMIT</label>
                                                <div class="input-group mb-3">
                                                    <input class="form-control" type="number" name="pagination" min="12" step="2" value="{{ $company_ui_settings->trending_posts_pagination }}" />
                                                    <div class="input-group-append">
                                                    <button type="submit"  class="btn btn-primary"><i class="fe fe-check"></i></button>
                                                    </div>
                                                </div>
                                        </form>

                                        <form method="POST" class="pagination-form"  action="{{ route('admin.ui.pagination',['field'=>'category_latest_posts_pagination']) }}">
                                                {{ csrf_field() }}
                                                <label>CATEGORY LATEST POSTS PER PAGE LIMIT</label>
                                                <div class="input-group mb-3">
                                                        <input class="form-control" type="number" name="pagination" min="12" step="2" value="{{ $company_ui_settings->category_latest_posts_pagination }}"  />
                                                        <div class="input-group-append">
                                                            <button type="submit"  class="btn btn-primary"><i class="fe fe-check"></i></button>
                                                        </div>
                                                </div>
                                        </form>

                                        <form method="POST" class="pagination-form"  action="{{ route('admin.ui.pagination',['field'=>'category_latest_threads_pagination']) }}">
                                                {{ csrf_field() }}
                                                <label>CATEGORY THREADS PER PAGE LIMIT</label>
                                                <div class="input-group mb-3">
                                                        <input class="form-control" type="number" name="pagination" min="4" step="2" value="{{ $company_ui_settings->category_latest_threads_pagination }}" />
                                                        <div class="input-group-append">
                                                                <button type="submit"  class="btn btn-primary"><i class="fe fe-check"></i></button>
                                                        </div>
                                                </div>
                                        </form>

                                        <form method="POST" class="pagination-form" action="{{ route('admin.ui.pagination',['field'=>'all_threads_pagination']) }}">
                                            {{ csrf_field() }}
                                            <label>ALL THREADS PER PAGE LIMIT</label>
                                            <div class="input-group mb-3">
                                                    <input class="form-control" type="number" name="pagination" min="32" step="2" value="{{ $company_ui_settings->all_threads_pagination }}" />
                                                    <div class="input-group-append">
                                                            <button type="submit"  class="btn btn-primary"><i class="fe fe-check"></i></button>
                                                    </div>
                                            </div>
                                        </form>
                                </div>
                                <div class="col-md-4">

                                        <form method="POST" class="pagination-form" action="{{ route('admin.ui.pagination',['field'=>'my_posts_pagination']) }}">
                                                {{ csrf_field() }}
                                                    <label>MY POSTS PER PAGE LIMIT</label>
                                                    <div class="input-group mb-3">
                                                            <input class="form-control" type="number" name="pagination" min="12" step="2" value="{{ $company_ui_settings->my_posts_pagination }}" />
                                                            <div class="input-group-append">
                                                                    <button type="submit"  class="btn btn-primary"><i class="fe fe-check"></i></button>
                                                            </div>
                                                    </div>
                                        </form>

                                        <form method="POST"  class="pagination-form" action="{{ route('admin.ui.pagination',['field'=>'my_drafts_pagination']) }}">
                                                {{ csrf_field() }}
                                                <label>MY DRAFT POSTS PER PAGE LIMIT</label>
                                                <div class="input-group mb-3">
                                                    <input class="form-control" type="number" name="pagination" min="12" step="2" value="{{ $company_ui_settings->my_drafts_pagination }}"  />
                                                    <div class="input-group-append">
                                                            <button type="submit"  class="btn btn-primary"><i class="fe fe-check"></i></button>
                                                    </div>
                                                </div>
                                        </form>

                                        <form method="POST"  class="pagination-form" action="{{ route('admin.ui.pagination',['field'=>'my_bookmarked_posts_pagination']) }}">
                                                {{ csrf_field() }}
                                                <label>MY BOOKMARKS POSTS PER PAGE LIMIT</label>
                                                <div class="input-group mb-3">
                                                    <input class="form-control" type="number" name="pagination" min="12" step="2" value="{{ $company_ui_settings->my_bookmarked_posts_pagination }}" />
                                                    <div class="input-group-append">
                                                        <button type="submit"  class="btn btn-primary"><i class="fe fe-check"></i></button>
                                                    </div>
                                                </div>
                                        </form>

                                        <form method="POST" class="pagination-form" action="{{ route('admin.ui.pagination',['field'=>'my_performance_posts_pagination']) }}">
                                                {{ csrf_field() }}
                                            <label>MY PERFORMANCE POSTS PER PAGE LIMIT</label>
                                                <div class="input-group mb-3">

                                                        <input class="form-control" type="number" name="pagination" min="12" step="2" value="{{ $company_ui_settings->my_performance_posts_pagination }}" />
                                                        <div class="input-group-append">
                                                                <button type="submit" class="btn btn-primary"><i class="fe fe-check"></i></button>
                                                        </div>
                                                </div>
                                        </form>

                                        <form method="POST" class="pagination-form" action="{{ route('admin.ui.pagination',['field'=>'user_posts_pagination']) }}">
                                                {{ csrf_field() }}
                                                    <label>USER POSTS PER PAGE LIMIT</label>
                                                    <div class="input-group mb-3">
                                                            <input class="form-control" type="number" name="pagination" min="12" step="2" value="{{ $company_ui_settings->user_posts_pagination }}" />
                                                            <div class="input-group-append">
                                                                    <button type="submit" class="btn btn-primary"><i class="fe fe-check"></i></button>
                                                            </div>
                                                    </div>
                                        </form>

                                </div>
                        </div>
                </div>
        </div>

    </div>

</div>
@endsection
