@extends('admin.layouts.app')

@section('content')
    <div class="header">
            <div class="container">
            <div class="header-body">
                <div class="row align-items-center">
                    <div class="col">
                        <h1 class="header-title">
                        Push Manager <span class="badge badge-success">{{ $devices['total']. ' Devices Found' }}</span>
                        </h1>
                    </div>
                    <div class="col-auto">
                    </div>
                </div>
            </div>
    </div>
    <div class="container my-5">
        <div class="row">
            <div class="col-md-5">
                <div class="card">
                    <form method="POST" action="{{ route('admin.push.send') }}" id="send_push_form">
                        {{ csrf_field() }}
                        <div class="card-body">
                                <div class="form-group">
                                    <label  for="title">Title (max 47 charaters)</label>
                                    <input type="text" name="title" id="title" placeholder="Title" maxlength="47" class="form-control" required/>
                                </div>

                                  <div class="form-group">
                                        <label>Message (max 97 charaters)</label>
                                        <textarea class="form-control" name="message" value="" id="message" maxlength="97" required></textarea>
                                </div>

                                <button type="submit" class="btn btn-success btn-block">Send Push Notification</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>


    </div>
@endsection
