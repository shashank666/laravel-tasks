@extends('admin.layouts.app')

@section('content')
    <div class="header">
            <div class="container">
            <div class="header-body">
                <div class="row align-items-center">
                    <div class="col">
                            <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                            <li  class="breadcrumb-item"><a href="{{route('admin.email.index')}}">Email</a></li>
                                            <li  class="breadcrumb-item active"> Email Preview : {{ '#'.$email->id }}</li>
                                    </ol>
                            </nav>
                    </div>
                    <div class="col-auto">
                        <a class="btn btn-primary mr-2" href="{{ route('admin.email.edit',['id'=>$email->id]) }}"><i class="fas fa-pencil-alt mr-2"></i>Edit Email</a>
                        @if($email->status=='not_scheduled')
                        <button class="btn btn-success" onclick="document.getElementById('schedule-email-form').submit();"><i class="fas fa-paper-plane mr-2"></i>Send Now</button>
                        <form id="schedule-email-form" style="display:none" method="POST" action="{{ route('admin.email.send') }}">
                            {{ csrf_field() }}
                            <input type="hidden" value="{{ $email->id }}" name="email_id" />
                        </form>
                        @elseif($email->status=='scheduled')
                        <button class="btn btn-danger" onclick="document.getElementById('cancel-send-form').submit();">Cancel Send</button>
                        <form id="cancel-send-form" style="display:none" method="POST" action="{{ route('admin.email.stop') }}">
                            {{ csrf_field() }}
                            <input type="hidden" value="{{ $email->id }}"  name="email_id" />
                        </form>
                        @endif
                    </div>
                </div>
            </div>
    </div>
    <div class="container my-5">
          @include('admin.email.send',['email_subject'=>$email->email_subject,'email_content'=>$email->email_content])
    </div>
@endsection
