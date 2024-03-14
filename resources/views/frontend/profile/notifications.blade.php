@extends('frontend.layouts.app')
@section('title','Notifications - Opined')
@section('description','Notifications , Alerts and Messages on Opined')
@section('keywords','Notifications,Alert,Messages')

@push('meta')
<link rel="canonical" href="http://www.weopined.com/me/notifications" />
<link href="http://www.weopined.com/me/notifications" rel="alternate" reflang="en" />

<!-- Twitter Card data -->
<meta name="twitter:card" content="summary">
<meta name="twitter:site" content="@weopined">
<meta name="twitter:title" content="Notifications - Opined">
<meta name="twitter:description" content="Notifications , Alerts and Messages on Opined.">
<meta name="twitter:creator" content="@weopined">
<meta name="twitter:image" content="http://www.weopined.com/favicon.png">

<!-- Open Graph data -->
<meta property="og:title" content="Notifications - Opined"/>
<meta property="og:type" content="website" />
<meta property="og:url" content="http://www.weopined.com/me/notifications" />
<meta property="og:image" content="http://www.weopined.com/favicon.png" />
<meta property="og:description" content="Notifications , Alerts and Messages on Opined." />
<meta property="og:site_name" content="Opined" />
<meta property="og:locale" content="en_US" />
<meta property="fb:app_id" content="1766000746745688" />
@endpush

@push('scripts')
@if($company_ui_settings->show_google_ad=='1')
    {!! $company_ui_settings->google_adcode !!}
@endif
@endpush

@section('content')

@include('frontend.partials.message')

<h1 class="mb-5 font-weight-light">Notifications</h1>

<div class="row">
    <div class="col-xl-8 col-lg-8 col-md-8 col-sm-12 col-12">
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                        <h5 class="font-weight-light mb-0">All Notifications
                                @if($notifications->total()>0)
                                <button class="btn btn-sm btn-outline-danger float-right" role="button" data-toggle="tooltip" data-placement="top" title="Delete All Notifications" onclick="event.preventDefault();document.getElementById('delete_notifications_form').submit();"><span><i class="far fa-trash-alt"></i></span></button>
                                <form id="delete_notifications_form" class="d-none" action="/me/delete_notifications" method="POST">
                                 {{ csrf_field() }}
                                </form>
                                @endif
                        </h5>
            </div>
                <div class="card-body">
                    @if($notifications->total()>0)
                        <ul class="list-group list-group-flush" id="notifications">
                            @foreach($notifications as $notification)
                                <li class="list-group-item" id="{{$notification->id}}">
                                    <a href="{{route('user_profile',['username'=>$notification->sender_username])}}">
                                        <img class="float-left mr-2 rounded-circle" src="{{$notification->sender_image!=null?$notification->sender_image:'/storage/profile/avatar.jpg'}}" alt="" height="40" width="40"/>
                                    </a>
                                    <a href="{{$notification->data->notification->action_url}}" class="text-muted">
                                        <div class="notification-content">
                                            <small class="notification-timestamp float-right text-muted">
                                                {{ \Carbon\Carbon::parse($notification->created_at)->format('jS F , Y')}}
                                            </small>
                                            <div class="notification-heading">
                                                    {{$notification->sender_name}}
                                            </div>
                                            <div class="notification-text">
                                                  {{$notification->data->notification->message}}
                                            </div>
                                        </div>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="lead text-secondary">No Notifications.</p>
                    @endif
                </div>
            @if($notifications->total()>0)
            <div class="card-footer text-center bg-white">
                    {{ $notifications->links('frontend.posts.components.pagination') }}
            </div>
            @endif
        </div>
        @if($company_ui_settings->show_google_ad=='1' && $google_ad)
            <div class="mt-3">
                {!! $google_ad->ad_code !!}
            </div>
        @endif
    </div>
    <div class="col-md-4 col-12">
            @if($company_ui_settings->show_google_ad=='1' && $google_ad)
            <div class="mt-3">
                {!! $google_ad->ad_code !!}
            </div>
            <div class="mt-3">
                {!! $google_ad->ad_code !!}
            </div>
            @endif
    </div>
</div>
@endsection
