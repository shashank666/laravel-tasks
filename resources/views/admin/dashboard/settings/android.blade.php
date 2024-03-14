@extends('admin.layouts.app')
@section('title','App Settings')


@push('styles')
<link href="/public_admin/assets/libs/noty/noty.min.css" rel="stylesheet" type="text/css"/>
@endpush

@push('scripts')
<script src="/public_admin/assets/libs/noty/noty.min.js"></script>
<script>
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
                },
                error:function(err){
                    new Noty({
                        theme:'sunset',
                        type:'error',
                        text: 'FAILED TO UPDATE PAGINATION SETTING',
                        timeout:3500,
                    }).show();
                }
            });
        });
</script>
@endpush


@section('content')
@include('admin.partials.header_title',['header_title'=>'App Settings'])
<div class="container">
    <div class="row">
        <div class="col-md-4 col-12">
            <div class="card">
                    <div class="card-header">
                        <h4 class="card-header-title">Force Logout</h4>
                    </div>
                    <div class="card-body">
                        <form id="force-logout-form" action="{{ route('admin.android_force_logout') }}">
                                {{ csrf_field() }}
                                <button class="btn btn-danger" type="submit">FORCE LOGOUT ALL APP USERS</button>
                        </form>
                    </div>
            </div>
        </div>
    </div>

    <div class="card">
            <div class="card-header">
                    <h4 class="card-header-title">APP PAGINATION SETTINGS</h2>
            </div>
            <div class="card-body">
                <div class="row">
                        <div class="col-md-4">
                                <form method="POST" class="pagination-form"  action="{{ route('admin.app.pagination',['field'=>'latest_posts_pagination']) }}">
                                    {{ csrf_field() }}
                                    <label>LATEST POSTS PER PAGE LIMIT</label>
                                    <div class="input-group mb-3">
                                            <input class="form-control" type="number" name="pagination" min="12" step="2" value="{{ $company_app_settings->latest_posts_pagination }}" />
                                            <div class="input-group-append">
                                                <button type="submit" class="btn btn-primary"><i class="fe fe-check"></i></button>
                                            </div>
                                    </div>
                                </form>

                                <form method="POST" class="pagination-form" action="{{ route('admin.app.pagination',['field'=>'trending_posts_pagination']) }}">
                                        {{ csrf_field() }}
                                        <label>TRENDING POSTS PER PAGE LIMIT</label>
                                            <div class="input-group mb-3">
                                                <input class="form-control" type="number" name="pagination" min="12" step="2" value="{{ $company_app_settings->trending_posts_pagination }}" />
                                                <div class="input-group-append">
                                                <button type="submit"  class="btn btn-primary"><i class="fe fe-check"></i></button>
                                                </div>
                                            </div>
                                </form>

                                <form method="POST" class="pagination-form"  action="{{ route('admin.app.pagination',['field'=>'mostliked_posts_pagination']) }}">
                                        {{ csrf_field() }}
                                        <label>MOSTLIKED POSTS PER PAGE LIMIT</label>
                                        <div class="input-group mb-3">
                                                <input class="form-control" type="number" name="pagination" min="12" step="2" value="{{ $company_app_settings->mostliked_posts_pagination }}"  />
                                                <div class="input-group-append">
                                                    <button type="submit"  class="btn btn-primary"><i class="fe fe-check"></i></button>
                                                </div>
                                        </div>
                                </form>

                                <form method="POST" class="pagination-form"  action="{{ route('admin.app.pagination',['field'=>'latest_opinions_pagination']) }}">
                                        {{ csrf_field() }}
                                        <label>LATEST OPINIONS PER PAGE LIMIT</label>
                                        <div class="input-group mb-3">
                                            <input class="form-control" type="number" name="pagination" min="12" step="2" value="{{ $company_app_settings->latest_opinions_pagination }}" />
                                            <div class="input-group-append">
                                                    <button type="submit"  class="btn btn-primary"><i class="fe fe-check"></i></button>
                                            </div>
                                        </div>
                                </form>

                                <form method="POST" class="pagination-form"  action="{{ route('admin.app.pagination',['field'=>'trending_opinions_pagination']) }}">
                                    {{ csrf_field() }}
                                    <label>TRENDING OPINIONS PER PAGE LIMIT</label>
                                        <div class="input-group mb-3">
                                            <input class="form-control" type="number" name="pagination" min="12" step="2" value="{{ $company_app_settings->trending_opinions_pagination }}" />
                                            <div class="input-group-append">
                                                <button type="submit"  class="btn btn-primary"><i class="fe fe-check"></i></button>
                                            </div>
                                        </div>
                                </form>


                            <form method="POST" class="pagination-form"  action="{{ route('admin.app.pagination',['field'=>'circle_opinions_pagination']) }}">
                                {{ csrf_field() }}
                                        <label>CIRCLE OPINIONS PER PAGE LIMIT</label>
                                        <div class="input-group mb-3">
                                                <input class="form-control" type="number" name="pagination" min="12" step="2" value="{{ $company_app_settings->circle_opinions_pagination }}" />
                                                <div class="input-group-append">
                                                    <button type="submit"  class="btn btn-primary"><i class="fe fe-check"></i></button>
                                                </div>
                                        </div>
                            </form>


                        </div>
                        <div class="col-md-4">

                                <form method="POST" class="pagination-form" action="{{ route('admin.app.pagination',['field'=>'my_posts_pagination']) }}">
                                    {{ csrf_field() }}
                                    <label>MY POSTS PER PAGE LIMIT</label>
                                    <div class="input-group mb-3">
                                        <input class="form-control" type="number" name="pagination" min="12" step="2" value="{{ $company_app_settings->my_posts_pagination }}" />
                                        <div class="input-group-append">
                                            <button type="submit"  class="btn btn-primary"><i class="fe fe-check"></i></button>
                                        </div>
                                    </div>
                                </form>

                                <form method="POST"  class="pagination-form" action="{{ route('admin.app.pagination',['field'=>'my_drafts_pagination']) }}">
                                        {{ csrf_field() }}
                                    <label>MY DRAFTS PER PAGE LIMIT</label>
                                    <div class="input-group mb-3">
                                            <input class="form-control" type="number" name="pagination" min="12" step="2" value="{{ $company_app_settings->my_drafts_pagination }}"  />
                                            <div class="input-group-append">
                                                    <button type="submit"  class="btn btn-primary"><i class="fe fe-check"></i></button>
                                            </div>
                                    </div>
                                </form>

                                <form method="POST"  class="pagination-form" action="{{ route('admin.app.pagination',['field'=>'my_bookmarked_posts_pagination']) }}">
                                        {{ csrf_field() }}
                                        <label>MY BOOKMARKS PER PAGE LIMIT</label>
                                        <div class="input-group mb-3">
                                            <input class="form-control" type="number" name="pagination" min="12" step="2" value="{{ $company_app_settings->my_bookmarked_posts_pagination }}" />
                                                <div class="input-group-append">
                                                        <button type="submit"  class="btn btn-primary"><i class="fe fe-check"></i></button>
                                                </div>
                                        </div>
                                </form>

                                <form method="POST" class="pagination-form" action="{{ route('admin.app.pagination',['field'=>'my_performance_posts_pagination']) }}">
                                        {{ csrf_field() }}
                                        <label>MY PERFORMANCE PER PAGE LIMIT</label>
                                        <div class="input-group mb-3">
                                                <input class="form-control" type="number" name="pagination" min="12" step="2" value="{{ $company_app_settings->my_performance_posts_pagination }}" />
                                                <div class="input-group-append">
                                                        <button type="submit" class="btn btn-primary"><i class="fe fe-check"></i></button>
                                                </div>
                                        </div>
                                </form>

                                <form method="POST" class="pagination-form" action="{{ route('admin.app.pagination',['field'=>'user_posts_pagination']) }}">
                                    {{ csrf_field() }}
                                    <label>USER POSTS PER PAGE LIMIT</label>
                                    <div class="input-group mb-3">
                                            <input class="form-control" type="number" name="pagination" min="12" step="2" value="{{ $company_app_settings->user_posts_pagination }}" />
                                            <div class="input-group-append">
                                                <button type="submit" class="btn btn-primary"><i class="fe fe-check"></i></button>
                                            </div>
                                    </div>
                                </form>


                            <form method="POST" class="pagination-form" action="{{ route('admin.app.pagination',['field'=>'my_notifications_pagination']) }}">
                                    {{ csrf_field() }}
                                    <label>MY NOTIFICATIONS PER PAGE LIMIT</label>
                                    <div class="input-group mb-3">
                                            <input class="form-control" type="number" name="pagination" min="12" step="2" value="{{ $company_app_settings->my_notifications_pagination }}" />
                                            <div class="input-group-append">
                                                    <button type="submit" class="btn btn-primary"><i class="fe fe-check"></i></button>
                                            </div>
                                    </div>
                            </form>

                        </div>
                        <div class="col-md-4">
                                <form method="POST" class="pagination-form" action="{{ route('admin.app.pagination',['field'=>'my_followings_pagination']) }}">
                                        {{ csrf_field() }}
                                        <label>MY FOLLOWINGS PER PAGE LIMIT</label>
                                                <div class="input-group mb-3">
                                                        <input class="form-control" type="number" name="pagination" min="12" step="2" value="{{ $company_app_settings->my_followings_pagination }}" />
                                                        <div class="input-group-append">
                                                                <button type="submit" class="btn btn-primary"><i class="fe fe-check"></i></button>
                                                        </div>
                                                </div>
                                  </form>
                                <form method="POST" class="pagination-form" action="{{ route('admin.app.pagination',['field'=>'my_followers_pagination']) }}">
                                        {{ csrf_field() }}
                                    <label>MY FOLLOWERS PER PAGE LIMIT</label>
                                    <div class="input-group mb-3">
                                            <input class="form-control" type="number" name="pagination" min="12" step="2" value="{{ $company_app_settings->my_followers_pagination }}" />
                                            <div class="input-group-append">
                                                    <button type="submit" class="btn btn-primary"><i class="fe fe-check"></i></button>
                                            </div>
                                    </div>
                                </form>
                                <form method="POST" class="pagination-form" action="{{ route('admin.app.pagination',['field'=>'user_followings_pagination']) }}">
                                        {{ csrf_field() }}
                                        <label>USER FOLLOWINGS PER PAGE LIMIT</label>
                                        <div class="input-group mb-3">
                                                <input class="form-control" type="number" name="pagination" min="12" step="2" value="{{ $company_app_settings->user_followings_pagination }}" />
                                                <div class="input-group-append">
                                                    <button type="submit" class="btn btn-primary"><i class="fe fe-check"></i></button>
                                                </div>
                                        </div>
                                </form>
                                <form method="POST" class="pagination-form" action="{{ route('admin.app.pagination',['field'=>'user_followers_pagination']) }}">
                                        {{ csrf_field() }}
                                    <label>USER FOLLOWERS PER PAGE LIMIT</label>
                                    <div class="input-group mb-3">

                                            <input class="form-control" type="number" name="pagination" min="12" step="2" value="{{ $company_app_settings->user_followers_pagination }}" />
                                            <div class="input-group-append">
                                                <button type="submit" class="btn btn-primary"><i class="fe fe-check"></i></button>
                                            </div>
                                    </div>
                                </form>
                                <form method="POST" class="pagination-form" action="{{ route('admin.app.pagination',['field'=>'post_comments_pagination']) }}">
                                        {{ csrf_field() }}
                                    <label>POST COMMENTS PER PAGE LIMIT</label>
                                    <div class="input-group mb-3">
                                                <input class="form-control" type="number" name="pagination" min="12" step="2" value="{{ $company_app_settings->post_comments_pagination }}" />
                                                <div class="input-group-append">
                                                        <button type="submit" class="btn btn-primary"><i class="fe fe-check"></i></button>
                                                </div>
                                    </div>
                                </form>
                                <form method="POST" class="pagination-form" action="{{ route('admin.app.pagination',['field'=>'post_comments_reply_pagination']) }}">
                                        {{ csrf_field() }}
                                        <label>POST COMMENTS REPLY PER PAGE LIMIT</label>
                                        <div class="input-group mb-3">
                                            <input class="form-control" type="number" name="pagination" min="12" step="2" value="{{ $company_app_settings->post_comments_reply_pagination }}" />
                                                <div class="input-group-append">
                                                    <button type="submit" class="btn btn-primary"><i class="fe fe-check"></i></button>
                                                </div>
                                        </div>
                                </form>
                                <form method="POST" class="pagination-form" action="{{ route('admin.app.pagination',['field'=>'opinion_comments_pagination']) }}">
                                        {{ csrf_field() }}
                                        <label>OPINION COMMENTS PER PAGE LIMIT</label>
                                        <div class="input-group mb-3">
                                                <input class="form-control" type="number" name="pagination" min="12" step="2" value="{{ $company_app_settings->opinion_comments_pagination }}" />
                                                <div class="input-group-append">
                                                    <button type="submit" class="btn btn-primary"><i class="fe fe-check"></i></button>
                                                </div>
                                        </div>
                                </form>

                                <form method="POST" class="pagination-form" action="{{ route('admin.app.pagination',['field'=>'opinion_comments_reply_pagination']) }}">
                                        {{ csrf_field() }}
                                    <label>OPINION COMMENTS REPLY PER PAGE LIMIT</label>
                                    <div class="input-group mb-3">
                                            <input class="form-control" type="number" name="pagination" min="12" step="2" value="{{ $company_app_settings->opinion_comments_reply_pagination }}" />
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
@endsection
