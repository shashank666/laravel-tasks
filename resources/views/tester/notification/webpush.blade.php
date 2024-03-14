@extends('frontend.layouts.app')
@section('title',"Webpush Notification")

@push('scripts')
 @if(Auth::check())
    <script src="/js/enable-push.js?<?php echo time()?>" type="text/javascript"></script>
    <script>
        $(document).ready(function(){
            if(localStorage.getItem("notification_modal")===null && Notification.permission!="granted"){
                showNotificationModal();
            }else{
                let threshold=Number(localStorage.getItem("notification_modal")) + 24*60*60*1000;
                if(new Date().getTime()>=threshold){
                    showNotificationModal();
                }
            }
        });

        function showNotificationModal(){
            $('#notificationsModal').modal('show');
            localStorage.setItem("notification_modal",new Date().getTime())
        }
    </script>
@endif
@endpush

@section('content')

<button class="btn btn-primary" data-toggle="modal" data-target="#notificationsModal">Enable Push Notification</button>

<button class="btn btn-danger" onclick="unsubscribeUser();">Unsubscribe</button>

<h2>Push Notification Demo</h2>
@endsection
