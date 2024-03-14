@extends('admin.layouts.app')
@section('title','User #'.$user->id.' '.$user->name)


@section('content')
<div class="header">
        <div class="container">
          <div class="header-body">
            <div class="row align-items-end">
                <div class="col">
                    <h6 class="header-pretitle">
                    Overview
                    </h6>
                    <h1 class="header-title">
                        {{ 'User #'.$user->id .': '.ucfirst($user->name)  }}
                    </h1>
                </div>
               <div class="col-auto">
                    @if($user->is_active==1)
                    <button class="btn btn-dark" data-toggle="modal" data-target="#block_user"><i class="fas fa-ban mr-2"></i><span>Block User Account</span></button>
                    @else
                    <button class="btn btn-success"  data-toggle="modal" data-target="#unblock_user"><i class="fas fa-check mr-2"></i><span>Unblock User Account</span></button>
                    @endif
                    <button class="btn btn-danger"  data-toggle="modal" data-target="#delete_user"><i class="fas fa-trash-alt mr-2"></i><span>Permently Delete Account</span></button>
                </div>
            </div>
          </div>
        </div>
</div>

<div class="container">
  <div class="row">
    <div class="col-md-3 col-12">
        <div class="card">
            @include('admin.dashboard.user.components.user_menu')
        </div>
    </div>
    <div class="col-md-9 col-12">
        @if($tab=='profile')
        @include('admin.dashboard.user.components.user_overview')
        @include('admin.dashboard.user.components.user_stats')
        @endif
        @if($tab=='payment')
        @include('admin.dashboard.user.components.user_payment')
        @endif
        @if($tab=='likes')
        @include('admin.dashboard.user.components.user_post_likes')
        @endif

        @if($tab=='bookmarks')
        @include('admin.dashboard.user.components.user_post_bookmarks')
        @endif

        @if($tab=='post_comments')
        @include('admin.dashboard.user.components.user_post_comments')
        @endif

        @if($tab=='opinion_comments')
        @include('admin.dashboard.user.components.user_opinion_comments')
        @endif

        @if($tab=='category')
        @include('admin.dashboard.user.components.user_category')
        @endif
        @if($tab=='followings')
        @include('admin.dashboard.user.components.user_followings')
        @endif
        @if($tab=='followers')
        @include('admin.dashboard.user.components.user_followers')
        @endif
    </div>
  </div>

@include('admin.dashboard.user.modals.modal_block_user')
@include('admin.dashboard.user.modals.modal_unblock_user')
@include('admin.dashboard.user.modals.modal_delete_user')
@endsection
