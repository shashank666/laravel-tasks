@extends('admin.layouts.app')
@section('title','Email Settings')


@push('scripts')
<script>
    $(document).ready(function(){
        var username=$('#sms_username').val();
        var password=$('#sms_password').val();
        var apikey=$('#sms_apikey').val().trim();
        $.ajax({
            url:`https://api.textlocal.in/balance/?apikey=${apikey}&username=${username}&password=${password}`,
            dataType:'json',
            method:'GET',
            success:function(response){
                if(response.status=='success'){
                    var smsBalance=response.balance.sms;
                    $('#sms-balance').text(smsBalance+' SMS Credits');
                }else{
                    $('#sms-balance').text('-');
                }
            },
            error:function(err){
                $('#sms-balance').text('-');
            }
        });
    });
</script>
@endpush


@section('content')
@include('admin.partials.header_title',['header_title'=>'Email Settings'])

<div class="container">
    <div class="row">
        <div class="col-lg-4 col-md-4 col-sm-12 col-12">
                <div class="card">
                        <div class="card-header">
                             <h4 class="card-header-title">SMS INTEGRATION<i class="fe fe-smartphone ml-2"></i>
                                <br/><span class="mt-2 badge badge-success" id="sms-balance"></span>
                            </h4>

                        </div>
                        <div class="card-body">
                                <form method="POST">
                                        {{ csrf_field() }}
                                        <label for="sms_username">SMS Username</label>
                                        <div class="form-group">
                                            <div class="form-line">
                                                <input type="text" id="sms_username" class="form-control" placeholder="SMS Username" value="{{$company->sms_username}}" disabled>
                                            </div>
                                        </div>
                                        <label for="sms_password">SMS Password</label>
                                        <div class="form-group">
                                            <div class="form-line">
                                                <input type="text" id="sms_password" class="form-control" placeholder="SMS Password" value="{{$company->sms_password}}" disabled>
                                            </div>
                                        </div>
                                        <label for="sms_apikey">SMS Api Key</label>
                                        <div class="form-group">
                                            <div class="form-line">
                                                <textarea rows="3"  id="sms_apikey" class="form-control" placeholder="SMS Apikey"  disabled>
                                                        {{$company->sms_apikey}}
                                                </textarea>
                                            </div>
                                        </div>
                                        <label for="sms_apiurl">SMS Send URL</label>
                                        <div class="form-group">
                                            <div class="form-line">
                                                <input type="text" id="sms_apiurl" class="form-control" placeholder="SMS Apiurl" value="{{$company->sms_apiurl}}" disabled>
                                            </div>
                                        </div>
                                </form>
                        </div>
                </div>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-12 col-12">
                <div class="card">
                        <div class="card-header">
                            <h4 class="card-header-title">NOTIFICATION EMAIL<i class="fe fe-bell ml-2"></i></h4>
                        </div>
                        <div class="card-body">
                            <form method="POST">
                                {{ csrf_field() }}
                                <label for="notification_email_address">Notification Email</label>
                                <div class="form-group">
                                    <div class="form-line">
                                        <input type="text" id="notification_email_address" class="form-control" placeholder="Notification Email Address" value="{{$company->notification_email}}" disabled>
                                    </div>
                                </div>

                                <label for="notification_password">Password</label>
                                <div class="form-group">
                                    <div class="form-line">
                                        <input type="text" id="notification_password" class="form-control" placeholder="Password" value="{{$company->notification_password}}" disabled>
                                    </div>
                                </div>
                            </form>
                        </div>
                </div>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-12 col-12">
                <div class="card">
                        <div class="card-header">
                             <h4 class="card-header-title">CONTACT-US EMAIL <i class="fe fe-inbox ml-2"></i></h4>
                        </div>
                        <div class="card-body">
                                <form method="POST">
                                        {{ csrf_field() }}
                                        <label for="contact_email_address">ContactUs Email</label>
                                        <div class="form-group">
                                            <div class="form-line">
                                                <input type="text" id="contact_email_address" class="form-control" placeholder="Contact Email Address" value="{{$company->contact_email}}" disabled>
                                            </div>
                                        </div>

                                        <label for="contact_password">Password</label>
                                        <div class="form-group">
                                            <div class="form-line">
                                                <input type="text" id="contact_password" class="form-control" placeholder="Password" value="{{$company->contact_password}}" disabled>
                                            </div>
                                        </div>
                                </form>
                        </div>
                </div>
        </div>
    </div>
</div>
@endsection
