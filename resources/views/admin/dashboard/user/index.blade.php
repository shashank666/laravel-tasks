@extends('admin.layouts.app')
@section('title','Users')

@push('styles')
<link href="/public_admin/assets/libs/morrisjs/morris.css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="/public_admin/assets/libs/daterangepicker/daterangepicker.css" />
@endpush

@push('scripts')
<script type="text/javascript" src="/public_admin/assets/libs/momentjs/moment.min.js"></script>
<script type="text/javascript" src="/public_admin/assets/libs/daterangepicker/daterangepicker.min.js"></script>
<script type="text/javascript" src="/public_admin/assets/libs/raphael/raphael.min.js"></script>
<script type="text/javascript" src="/public_admin/assets/libs/morrisjs/morris.js"></script>

<script>
    $(document).ready(function(){
        $(".clear-btn").click(function() {
             $("#searchQuery").val('');
         });
        var start =moment($('#from').val(),'YYYY-MM-DD HH:mm:ss');
        var end = moment($('#to').val(),'YYYY-MM-DD HH:mm:ss');

        function cb(start, end) {
            $('#reportrange span').html(start.format('DD MMMM , YYYY') + ' - ' + end.format('DD MMMM , YYYY'));
            $('#from').val(start.format('YYYY-MM-DD HH:mm:ss'));
            $('#to').val(end.format('YYYY-MM-DD HH:mm:ss'));
        }

        $('#reportrange').daterangepicker({
            startDate: start,
            endDate: end,
            maxDate: moment(),
            timePicker:false,
            alwaysShowCalendars:true,
            ranges: {
               'Today': [moment().startOf('day'), moment().endOf('day')],
               'Yesterday': [moment().subtract(1, 'days').startOf('day'), moment().subtract(1, 'days').endOf('day')],
               'Last 7 Days': [moment().subtract(6, 'days'), moment()],
               'Last 30 Days': [moment().subtract(29, 'days'), moment()],
               'This Month': [moment().startOf('month'), moment().endOf('month')],
               'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            }
        }, cb);
        cb(start, end);

        Morris.Bar({
            element: 'users_bar_chart',
            data: <?php echo json_encode($month_wise_count)?>,
            xkey: ['month_year'],
            ykeys: ['total'],
            yLabelFormat: function(y){return y != Math.round(y)?'':y;},
            labels: ['Users'],
            barColors: [ 'rgb(255, 152, 0)'],
        });

        Morris.Bar({
            element: 'today_bar_chart',
            xLabelMargin: 10,
            data: <?php echo json_encode($users_by_providers)?>,
            xkey: ['name'],
            ykeys: ['today'],
            yLabelFormat: function(y){return y != Math.round(y)?'':y;},
            labels: ['Users'],
            barColors: ['rgb(233, 30, 99)', 'rgb(0, 188, 212)', 'rgb(255, 152, 0)', 'rgb(0, 150, 136)', 'rgb(96, 125, 139)'],
        });
    });


        $(document).on('change','#searchBy',function(){
            $('#searchBy').val($(this).val());
        });

        $(document).on('click','#btnResetSearch',function(){
            if($('#searchQuery').val().trim().length>0){
                $('#searchQuery').val('');
                $('#searchBy').val('name');
                $('#apply').click();
            }
        });


        $(document).on('keypress',"#searchQuery",function(event) {
            if (event.which == 13) {
                event.preventDefault();
                $("#apply").click();
            }
        });

        $(document).on('click','#apply',function(){
            var from=$('#from').val();
            var to=$('#to').val();
            var searchBy=$('#searchBy').val();
            var searchQuery=$('#searchQuery').val();
            var sortBy=$('#sortBy').val();
            var sortOrder=$('#sortOrder').val();
            var is_active=$('input[type=radio][name=is_active]:checked').val();
            var email_verified=$('input[type=radio][name=email_verified]:checked').val();
            var mobile_verified=$('input[type=radio][name=mobile_verified]:checked').val();
            var registered_as_writer=$('input[type=radio][name=registered_as_writer]:checked').val();
            var provider=$('input[type=radio][name=provider]:checked').val();
            var platform=$('input[type=radio][name=platform]:checked').val();
            var limit=parseInt($('#limit').val());
            var page=parseInt($('#page').val());

           var URL=`/cpanel/user/all?page=${page}&sortBy=${sortBy}&sortOrder=${sortOrder}&searchBy=${searchBy}&searchQuery=${searchQuery}&from=${from}&to=${to}&is_active=${is_active}&email_verified=${email_verified}&mobile_verified=${mobile_verified}&registered_as_writer=${registered_as_writer}&provider=${provider}&platform=${platform}&limit=${limit}`;
            window.location.href=URL;
        });
    </script>
@endpush


@section('content')
<a href="#" id="scroll" style="display: none;"><span></span></a>
<div class="header">
        <div class="container">
          <div class="header-body">
            <div class="row align-items-end">
                <div class="col">
                    <h6 class="header-pretitle">
                    Overview
                    </h6>
                    <h1 class="header-title">
                    Users
                    <span  class="ml-2 badge badge-primary">{{ $user_count['all'].' Total'}}</span>
                    <span  class="ml-2 badge badge-success" style="cursor:pointer" onclick="window.location.href=`/cpanel/user/all?is_active=1`">{{$user_count['active'] .' Active'}}<a href="{{ route('admin.users_download',['format'=>'csv','is_active'=>1]) }}" style="color: #fff; font-size: 75%" class="ml-2"><i class="fa fa-download" aria-hidden="true"></i></a></span></span>
                    <span  class="ml-2 badge badge-warning" style="cursor:pointer" onclick="window.location.href=`/cpanel/user/all?is_active=0`">{{ $user_count['disabled'] .' Disabled'}} <a href="{{ route('admin.users_download',['format'=>'csv','is_active'=>0]) }}" style="color: #000; font-size: 75%" class="ml-2"><i class="fa fa-download" aria-hidden="true"></i></a></span>
                    <span  class="ml-2 badge badge-danger" style="cursor:pointer" onclick="window.location.href=`/cpanel/user/deleted`">{{$user_count['deleted'] .' Deleted'}}</span>
                    </h1>
                </div>
               <div class="col-auto">
                   <a class="btn btn-light" href="{{ route('admin.writers') }}">Registered Writers</a>
               </div>
            </div>
          </div>
        </div>
</div>

<div class="container">
    <!--<div class="row">
            <div class="col-md-8 col-12">
                    <div class="card">
                            <div class="card-header">
                                <h4 class="card-header-title">
                                    Month Wise Users Registered Graphview
                                </h4>
                            </div>
                            <div class="card-body">
                                <div id="users_bar_chart" class="graph"></div>
                            </div>
                    </div>
            </div>
            <div class="col-md-4 col-12">
                    <div class="card">
                    <div class="card-header">
                            <h4 class="card-header-title">
                                Today&apos;s  Registered Users <span style="float:right">
                                    <span class="badge badge-success">{{  $user_count['today_verified'].' verified' }}</span>
                                    <span class="badge badge-primary">{{  $user_count['today'] .' total'}}</span>
                                </span>
                            </h4>
                        </div>
                        <div class="card-body">
                            <div id="today_bar_chart" class="graph"></div>
                        </div>
                    </div>
            </div>
    </div>
-->
    <div class="row">

        <div class="col-lg-4 col-md-4 col-12">
            <div class="card">
                <div class="card-body">
                        <table class="table">
                            <h4 class="card-title">VERIFIED</h4>
                                <tbody>
                                    <tr style="cursor:pointer" onclick="window.location.href=`/cpanel/user/all`">
                                        <td>
                                            <span class="mr-2"><i class="fas fa-users" style="color:#E91E63;"></i></span>
                                            All Users
                                        </td>
                                        <td>{{ $user_count['all']}}</td>
                                        <td><a href="{{ route('admin.users_download',['format'=>'csv']) }}"><i class="fa fa-download" aria-hidden="true"></i></a></td>
                                    </tr>
                                    <tr style="cursor:pointer" onclick="window.location.href=`/cpanel/user/all?registered_as_writer=1`">
                                            <td>
                                                <span class="mr-2"><i class="fas fa-user-edit" style="color:#009688;"></i></span>
                                                    Writers
                                            </td>
                                            <td>{{ $user_count['registered_writer_count']}}</td>
                                            <td><a href="{{ route('admin.users_download',['format'=>'csv','registered_as_writer'=>1]) }}"><i class="fa fa-download" aria-hidden="true"></i></a></td>
                                    </tr>
                                    <tr style="cursor:pointer" onclick="window.location.href=`/cpanel/user/all?email_verified=1`">
                                            <td>
                                                    <span class="mr-2"><i class="fas fa-envelope" style="color:##7b1fa2;"></i></span>
                                                        Verified Emails
                                                </td>
                                            <td>{{ $user_count['email_verified']}}</td>
                                            <td><a href="{{ route('admin.users_download',['format'=>'csv','email_verified'=>1]) }}"><i class="fa fa-download" aria-hidden="true"></i></a></td>
                                    </tr>
                                    <tr style="cursor:pointer" onclick="window.location.href=`/cpanel/user/all?mobile_verified=1`">
                                            <td>
                                                    <span class="mr-2"><i class="fas fa-mobile" style="color:#cddc39;"></i></span>
                                                    Verified Mobiles
                                            </td>
                                            <td>{{ $user_count['mobile_verified']}}</td>
                                            <td><a href="{{ route('admin.users_download',['format'=>'csv','mobile_verified'=>1]) }}"><i class="fa fa-download" aria-hidden="true"></i></a></td>
                                    </tr>
                                    <tr style="cursor:pointer" onclick="window.location.href=`/cpanel/user/all?email_verified=1&mobile_verified=1`">
                                            <td>
                                                    <span class="mr-2"><i class="fas fa-user-check" style="color:#4caf50;"></i></span>
                                                    Fully Verified
                                            </td>
                                            <td>{{ $user_count['both_verified']}}</td>
                                            <td><a href="{{ route('admin.users_download',['format'=>'csv','mobile_verified'=>1,'email_verified'=>1]) }}"><i class="fa fa-download" aria-hidden="true"></i></a></td>
                                    </tr>

                                </tbody>
                            </table>
                </div>
            </div>

        </div>

        <div class="col-lg-4 col-md-4 col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">USING SOCIAL</h4>
                            <table class="table">
                                <tbody>
                                    @for($i=0;$i<(count($users_by_providers));$i++)
                                    <tr style="cursor:pointer" onclick="window.location.href=`/cpanel/user/all?provider={{ $users_by_providers[$i]['name'] }}`">
                                        <td>
                                            <span class="mr-2"><i class="{{$users_by_providers[$i]['icon']}}" style="color:{{ $users_by_providers[$i]['color'] }}"></i></span>
                                            {{'using '.ucfirst($users_by_providers[$i]['name'])}}
                                        </td>
                                        <td>
                                                {{$users_by_providers[$i]['total']}}
                                        </td>
                                        <td><a href="{{ route('admin.users_download',['format'=>'csv','provider'=>$users_by_providers[$i]['name']]) }}"><i class="fa fa-download" aria-hidden="true"></i></a></td>
                                    </tr>
                                    @endfor
                                </tbody>
                            </table>
                    </div>
                </div>
        </div>


        <div class="col-lg-4 col-md-4 col-12">
                {{--<div class="card">
                    <div class="card-body">
                            <table class="table">
                                <tbody>
                                    <tr style="cursor:pointer" onclick="window.location.href=`/cpanel/user/all?is_active=0`">
                                        <td>
                                            <span class="mr-2"><i class="fas fa-ban" style="color:#000;"></i></span>
                                            Blocked Accounts
                                        </td>
                                        <td>{{ $user_count['blocked']}}</td>
                                    </tr>
                                    <tr style="cursor:pointer" onclick="window.location.href=`/cpanel/user/deleted`">
                                        <td>
                                            <span class="mr-2"><i class="fas fa-trash" style="color:red"></i></span>
                                            Deleted Accounts
                                        </td>
                                        <td>{{ $user_count['deleted']}}</td>
                                    </tr>
                                </tbody>
                            </table>
                    </div>
                </div>
                --}}
                <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">REGISTERED USERS</h4>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item" style="cursor:pointer" onclick="window.location.href=`/cpanel/user/all?from={{$tody}}&to={{$todylast}}`">
                                    TODAY
                                    <span class="float-right mr-2"><b>{{$user_count['today']}}</b>
                                    <a href="user/download?format=csv&from={{$tody}}&to={{$todylast}}"><i class="fa fa-download" aria-hidden="true"></i></a></span>
                                </li>
                                <li class="list-group-item" style="cursor:pointer" onclick="window.location.href=`/cpanel/user/all?from={{$yesterdy}}&to={{$yesterdaylast}}`">
                                    YESTERDAY
                                    <span class="float-right mr-2"><b>{{$user_count['yesterday']}}</b>
                                    <a href="user/download?format=csv&from={{$yesterdy}}&to={{$yesterdaylast}}"><i class="fa fa-download" aria-hidden="true"></i></a></span></span>
                                </li>
                                <li class="list-group-item" style="cursor:pointer" onclick="window.location.href=`/cpanel/user/all?from={{$sevendays}}&to={{$todylast}}`">
                                    LAST 7 DAYS
                                    <span class="float-right mr-2"><b>{{$user_count['last_7_days']}}</b>
                                    <a href="user/download?format=csv&from={{$sevendays}}&to={{$todylast}}"><i class="fa fa-download" aria-hidden="true"></i></a></span></span>
                                </li>
                                {{--<li class="list-group-item" style="cursor:pointer" onclick="window.location.href=`/cpanel/user/all?from={{$tody}}&to={{$todylast}}`">
                                    THIS WEEK
                                    <span class="float-right mr-2"><b>{{$user_count['this_week']}}</b>
                                    <a href="user/download?format=csv&from={{$tody}}&to={{$todylast}}"><i class="fa fa-download" aria-hidden="true"></i></a></span></span>
                                </li>--}}
                                <li class="list-group-item" style="cursor:pointer" onclick="window.location.href=`/cpanel/user/all?from={{$thismonth}}&to={{$todylast}}`">
                                    THIS MONTH
                                    <span class="float-right mr-2"><b>{{$user_count['this_month']}}</b>
                                    <a href="user/download?format=csv&from={{$thismonth}}&to={{$todylast}}"><i class="fa fa-download" aria-hidden="true"></i></a></span></span>
                                </li>
                                <li class="list-group-item" style="cursor:pointer" onclick="window.location.href=`/cpanel/user/all?from={{$lastmonthstart}}&to={{$lastmonthend}}`">
                                    LAST MONTH
                                    <span class="float-right mr-2"><b>{{$user_count['last_month']}}</b>
                                    <a href="user/download?format=csv&from={{$lastmonthstart}}&to={{$lastmonthend}}"><i class="fa fa-download" aria-hidden="true"></i></a></span></span>
                                </li>
                                <li class="list-group-item" style="cursor:pointer" onclick="window.location.href=`/cpanel/user/all`">
                                    ALL
                                    <span class="float-right mr-2"><b>{{$user_count['all']}}</b>
                                        <a href="user/download?format=csv"><i class="fa fa-download" aria-hidden="true"></i></a></span></span>
                                </li>
                            </ul>
                        </div>
                </div>

        </div>

    </div>

    
    {{--<div class="card">
    <button data-toggle="collapse" data-target="#filters" style="border:none">APPLY FILTERS</button>
    </div>

    <div id="filters" class="card collapse">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 col-12">
                            <div class="form-group">
                                    <label>Account Status</label>
                                    <br/>
                                    <div class="custom-control custom-radio custom-control-inline">
                                            <input class="custom-control-input" name="is_active" type="radio" id="visibility_all" value="0,1"  {{ $is_active=='0,1'?'checked':'' }}>
                                            <label class="custom-control-label" for="visibility_all">ALL</label>
                                    </div>

                                    <div class="custom-control custom-radio custom-control-inline">
                                            <input class="custom-control-input" name="is_active" type="radio" id="visibility_visible" value="1"   {{ $is_active=='1'?'checked':'' }}>
                                            <label class="custom-control-label" for="visibility_visible">ACTIVE</label>
                                    </div>

                                    <div class="custom-control custom-radio custom-control-inline">
                                            <input class="custom-control-input" name="is_active" type="radio" id="visibility_hidden" value="0"   {{ $is_active=='0'?'checked':'' }}>
                                            <label class="custom-control-label" for="visibility_hidden">BLOCKED</label>
                                    </div>
                            </div>
                            <div class="form-group">
                                    <label>Email Verified</label>
                                    <br/>
                                    <div class="custom-control custom-radio custom-control-inline">
                                            <input class="custom-control-input" name="email_verified" type="radio" id="email_verified_all" value="0,1"  {{ $email_verified=='0,1'?'checked':'' }}>
                                            <label class="custom-control-label" for="email_verified_all">ALL</label>
                                    </div>

                                    <div class="custom-control custom-radio custom-control-inline">
                                            <input class="custom-control-input" name="email_verified" type="radio" id="email_verified_visible" value="1"   {{ $email_verified=='1'?'checked':'' }}>
                                            <label class="custom-control-label" for="email_verified_visible">VERIFIED</label>
                                    </div>

                                    <div class="custom-control custom-radio custom-control-inline">
                                            <input class="custom-control-input" name="email_verified" type="radio" id="email_verified_hidden" value="0"   {{ $email_verified=='0'?'checked':'' }}>
                                            <label class="custom-control-label" for="email_verified_hidden">NOT VERIFIED</label>
                                    </div>
                            </div>
                            <div class="form-group">
                                    <label>Mobile Verified</label>
                                    <br/>
                                    <div class="custom-control custom-radio custom-control-inline">
                                            <input class="custom-control-input" name="mobile_verified" type="radio" id="mobile_verified_all" value="0,1"  {{ $mobile_verified=='0,1'?'checked':'' }}>
                                            <label class="custom-control-label" for="mobile_verified_all">ALL</label>
                                    </div>
                                    <div class="custom-control custom-radio custom-control-inline">
                                            <input class="custom-control-input" name="mobile_verified" type="radio" id="mobile_verified_visible" value="1"   {{ $mobile_verified=='1'?'checked':'' }}>
                                            <label class="custom-control-label" for="mobile_verified_visible">VERIFIED</label>
                                    </div>
                                    <div class="custom-control custom-radio custom-control-inline">
                                            <input class="custom-control-input" name="mobile_verified" type="radio" id="mobile_verified_hidden" value="0"   {{ $mobile_verified=='0'?'checked':'' }}>
                                            <label class="custom-control-label" for="mobile_verified_hidden">NOT VERIFIED</label>
                                    </div>
                            </div>
                    </div>


                    <div class="col-md-4 col-12">
                            <div class="form-group">
                                    <label>Registerd Writers</label>
                                    <br/>
                                    <div class="custom-control custom-radio custom-control-inline">
                                            <input name="registered_as_writer" class="custom-control-input" type="radio" id="registered_as_writer_all" value="0,1"  {{ $registered_as_writer=='0,1'?'checked':'' }}>
                                            <label class="custom-control-label"  for="registered_as_writer_all">ALL</label>
                                    </div>

                                    <div class="custom-control custom-radio custom-control-inline">
                                            <input name="registered_as_writer" class="custom-control-input" type="radio" id="registered_as_writer_visible" value="1"   {{ $registered_as_writer=='1'?'checked':'' }}>
                                            <label class="custom-control-label"  for="registered_as_writer_visible">YES</label>
                                    </div>

                                    <div class="custom-control custom-radio custom-control-inline">
                                            <input name="registered_as_writer" class="custom-control-input" type="radio" id="registered_as_writer_hidden" value="0"   {{ $registered_as_writer=='0'?'checked':'' }}>
                                            <label class="custom-control-label"  for="registered_as_writer_hidden">NO</label>
                                    </div>
                            </div>

                            <div class="form-group">
                                    <label>Registerd Platform</label>
                                    <br/>
                                    <div class="custom-control custom-radio custom-control-inline">
                                            <input name="platform" type="radio" class="custom-control-input" id="platform_all" value="website,android"  {{ $platform=='website,android'?'checked':'' }}>
                                            <label class="custom-control-label" for="platform_all">ALL</label>
                                    </div>
                                    <div class="custom-control custom-radio custom-control-inline">
                                            <input name="platform" type="radio" class="custom-control-input" id="platform_website" value="website"   {{ $platform=='website'?'checked':'' }}>
                                            <label class="custom-control-label" for="platform_website">Website</label>
                                    </div>
                                    <div class="custom-control custom-radio custom-control-inline">
                                            <input name="platform" type="radio" class="custom-control-input" id="platform_android" value="android"   {{ $platform=='android'?'checked':'' }}>
                                            <label class="custom-control-label" for="platform_android">Android</label>
                                    </div>
                            </div>

                            <div class="form-group">
                                    <label>Register Provider</label>
                                    <br/>
                                    <div class="custom-control custom-radio">
                                            <input name="provider" type="radio" class="custom-control-input" id="provider_all" value="email,facebook,google,twitter,linkedin"  {{ $register_provider=='email,facebook,google,twitter,linkedin'?'checked':'' }}>
                                            <label class="custom-control-label" for="provider_all">ALL</label>
                                    </div>

                                    <div class="custom-control custom-radio">
                                            <input name="provider" type="radio" class="custom-control-input" id="provider_email" value="email"  {{ $register_provider=='email'?'checked':'' }}>
                                            <label class="custom-control-label" for="provider_email">EMAIL</label>
                                    </div>

                                    <div class="custom-control custom-radio">
                                            <input name="provider" type="radio" class="custom-control-input" id="provider_facebook" value="facebook" class="with-gap radio-col-blue" {{ $register_provider=='facebbok'?'checked':'' }}>
                                            <label class="custom-control-label" for="provider_facebook">FACEBOOK</label>
                                    </div>

                                    <div class="custom-control custom-radio">
                                            <input name="provider" type="radio" class="custom-control-input" id="provider_google" value="google"  {{ $register_provider=='google'?'checked':'' }}>
                                            <label class="custom-control-label" for="provider_google">GOOGLE</label>
                                    </div>

                                    <div class="custom-control custom-radio">
                                        <input name="provider" type="radio" class="custom-control-input" id="provider_twitter" value="twitter" class="with-gap radio-col-cyan" {{ $register_provider=='twitter'?'checked':'' }}>
                                        <label class="custom-control-label" for="provider_twitter">TWITTER</label>
                                    </div>

                                    <div class="custom-control custom-radio">
                                            <input name="provider" type="radio" class="custom-control-input" id="provider_linkedin" value="linkedin" class="with-gap radio-col-teal" {{ $register_provider=='linkedin'?'checked':'' }}>
                                            <label class="custom-control-label" for="provider_linkedin">LINKEDIN</label>
                                    </div>
                            </div>
                    </div>

                    <div class="col-md-4 col-12">
                        <div class="form-group">
                            <label>From - To</label>
                            <div id="reportrange" class="form-control">
                                    <i class="fe fe-calendar"></i>&nbsp;
                                    <span></span> <i class="fe fe-chevron-down"></i>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-6">
                                    <div class="form-group form-float">
                                        <label for="sortBy">Sort By</label>
                                        <select class="form-control show-tick" name="sortBy" id="sortBy">
                                            <option value="id"  {{  $sortBy=='user_id'?'selected':''}}>ID</option>
                                            <option value="name"  {{  $sortBy=='title'?'selected':''}}>NAME</option>
                                            <option value="created_at"  {{  $sortBy=='created_at'?'selected':''}}>REGISTER DATE</option>
                                            <option value="email" {{  $sortBy=='email'?'selected':''}}>EMAIL</option>
                                            <option value="mobile"  {{  $sortBy=='mobile'?'selected':''}}>MOBILE</option>
                                            <option value="provider" {{  $sortBy=='provider'?'selected':''}}>PROVIDER</option>
                                        </select>
                                    </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group form-float">
                                        <label for="sortOrder">Sort Order</label>
                                        <select class="form-control show-tick" name="sortOrder" id="sortOrder">
                                            <option value="asc" {{  $sortOrder=='asc'?'selected':''}}>ASCENDING</option>
                                            <option value="desc" {{  $sortOrder=='desc'?'selected':''}}>DESCENDING</option>
                                        </select>
                                </div>
                            </div>
                        </div>

                        <input type="hidden" id="from" name="from" value="{{ $from }}"/>
                        <input type="hidden" id="to" name="to" value="{{ $to }}"/>
                        <input type="hidden" name="page" id="page" value="{{ $users->currentPage() }}"/>

                        <div class="form-group">
                            <label>Limit</label>
                            <input type="number" class="form-control" min="12" max="100" name="limit" id="limit" value="{{$limit}}"/>
                        </div>
                    </div>

                </div>
                <button type="button" class="btn btn-success" id="apply">APPLY SORT AND FILTERS</button>

            </div>
    </div>
--}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-lg-2">
                                    <div class="form-group mt-3">
                                            <select class="form-control show-tick" data-toggle="select" name="searchBy" id="searchBy">
                                                <option value="id" {{$searchBy=='id' ? 'selected':''}}>Search By ID</option>
                                                <option value="name" {{$searchBy=='name' ? 'selected':''}}>Search By Name</option>
                                                <option value="email" {{ $searchBy=='email'?'selected':''}}>Search By Email</option>
                                                <option value="mobile" {{ $searchBy=='mobile'?'selected':''}}>Search By Mobile</option>
                                            </select>
                                    </div>
                            </div>

                            <div class="col-lg-5 mt-3">
                                <div class="input-group input-group-merge">
                                        <input type="text" placeholder="Search User ..." name="searchQuery" id="searchQuery" value="{{ $searchQuery }}" class="form-control form-control-prepended search"/>
                                        <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    <span class="fe fe-search"></span>
                                                </div>
                                        </div>
                                </div>
                            </div>
                            <div class="col-lg-2 mt-3 d-md-none d-sm-inline d-inline">
                                    <button type="button" class="btn btn-block btn-success" id="apply">Search</button>
                            </div>
                            
                            <div class="col-lg-2 mt-3">
                                    <button class="btn btn-light btn-block clear-btn">RESET SEARCH</button>
                                    <!--<button class="btn btn-light btn-block" id="btnResetSearch">RESET SEARCH</button>-->
                            </div>
                            <div class="col-lg-2 mt-3">
                                    <button class="btn btn-light btn-block" data-toggle="collapse" data-target="#filters">APPLY FILTERS</button>
                            </div>
                            <div class="col-lg-1" style=" text-align: center;">
                            <a href="{{'user/download?format=csv&'.$qry_download}}" style="color:#f5c337"><i class="fa fa-download" aria-hidden="true" style="margin-top: 30%; background: #000;font-size: 25px;border-radius: 8px;"></i></a>
                            </div>
                        </div>
                    </div>
                    <div id="filters" class="card collapse">
                     <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 col-12">
                                    <div class="form-group">
                                            <label>Account Status</label>
                                            <br/>
                                            <div class="custom-control custom-radio custom-control-inline">
                                                    <input class="custom-control-input" name="is_active" type="radio" id="visibility_all" value="0,1"  {{ $is_active=='0,1'?'checked':'' }}>
                                                    <label class="custom-control-label" for="visibility_all">ALL</label>
                                            </div>

                                            <div class="custom-control custom-radio custom-control-inline">
                                                    <input class="custom-control-input" name="is_active" type="radio" id="visibility_visible" value="1"   {{ $is_active=='1'?'checked':'' }}>
                                                    <label class="custom-control-label" for="visibility_visible">ACTIVE</label>
                                            </div>

                                            <div class="custom-control custom-radio custom-control-inline">
                                                    <input class="custom-control-input" name="is_active" type="radio" id="visibility_hidden" value="0"   {{ $is_active=='0'?'checked':'' }}>
                                                    <label class="custom-control-label" for="visibility_hidden">BLOCKED</label>
                                            </div>
                                    </div>
                                    <div class="form-group">
                                            <label>Email Verified</label>
                                            <br/>
                                            <div class="custom-control custom-radio custom-control-inline">
                                                    <input class="custom-control-input" name="email_verified" type="radio" id="email_verified_all" value="0,1"  {{ $email_verified=='0,1'?'checked':'' }}>
                                                    <label class="custom-control-label" for="email_verified_all">ALL</label>
                                            </div>

                                            <div class="custom-control custom-radio custom-control-inline">
                                                    <input class="custom-control-input" name="email_verified" type="radio" id="email_verified_visible" value="1"   {{ $email_verified=='1'?'checked':'' }}>
                                                    <label class="custom-control-label" for="email_verified_visible">VERIFIED</label>
                                            </div>

                                            <div class="custom-control custom-radio custom-control-inline">
                                                    <input class="custom-control-input" name="email_verified" type="radio" id="email_verified_hidden" value="0"   {{ $email_verified=='0'?'checked':'' }}>
                                                    <label class="custom-control-label" for="email_verified_hidden">NOT VERIFIED</label>
                                            </div>
                                    </div>
                                    <div class="form-group">
                                            <label>Mobile Verified</label>
                                            <br/>
                                            <div class="custom-control custom-radio custom-control-inline">
                                                    <input class="custom-control-input" name="mobile_verified" type="radio" id="mobile_verified_all" value="0,1"  {{ $mobile_verified=='0,1'?'checked':'' }}>
                                                    <label class="custom-control-label" for="mobile_verified_all">ALL</label>
                                            </div>
                                            <div class="custom-control custom-radio custom-control-inline">
                                                    <input class="custom-control-input" name="mobile_verified" type="radio" id="mobile_verified_visible" value="1"   {{ $mobile_verified=='1'?'checked':'' }}>
                                                    <label class="custom-control-label" for="mobile_verified_visible">VERIFIED</label>
                                            </div>
                                            <div class="custom-control custom-radio custom-control-inline">
                                                    <input class="custom-control-input" name="mobile_verified" type="radio" id="mobile_verified_hidden" value="0"   {{ $mobile_verified=='0'?'checked':'' }}>
                                                    <label class="custom-control-label" for="mobile_verified_hidden">NOT VERIFIED</label>
                                            </div>
                                    </div>
                            </div>


                            <div class="col-md-4 col-12">
                                    <div class="form-group">
                                            <label>Registerd Writers</label>
                                            <br/>
                                            <div class="custom-control custom-radio custom-control-inline">
                                                    <input name="registered_as_writer" class="custom-control-input" type="radio" id="registered_as_writer_all" value="0,1"  {{ $registered_as_writer=='0,1'?'checked':'' }}>
                                                    <label class="custom-control-label"  for="registered_as_writer_all">ALL</label>
                                            </div>

                                            <div class="custom-control custom-radio custom-control-inline">
                                                    <input name="registered_as_writer" class="custom-control-input" type="radio" id="registered_as_writer_visible" value="1"   {{ $registered_as_writer=='1'?'checked':'' }}>
                                                    <label class="custom-control-label"  for="registered_as_writer_visible">YES</label>
                                            </div>

                                            <div class="custom-control custom-radio custom-control-inline">
                                                    <input name="registered_as_writer" class="custom-control-input" type="radio" id="registered_as_writer_hidden" value="0"   {{ $registered_as_writer=='0'?'checked':'' }}>
                                                    <label class="custom-control-label"  for="registered_as_writer_hidden">NO</label>
                                            </div>
                                    </div>

                                    <div class="form-group">
                                            <label>Registerd Platform</label>
                                            <br/>
                                            <div class="custom-control custom-radio custom-control-inline">
                                                    <input name="platform" type="radio" class="custom-control-input" id="platform_all" value="website,android"  {{ $platform=='website,android'?'checked':'' }}>
                                                    <label class="custom-control-label" for="platform_all">ALL</label>
                                            </div>
                                            <div class="custom-control custom-radio custom-control-inline">
                                                    <input name="platform" type="radio" class="custom-control-input" id="platform_website" value="website"   {{ $platform=='website'?'checked':'' }}>
                                                    <label class="custom-control-label" for="platform_website">Website</label>
                                            </div>
                                            <div class="custom-control custom-radio custom-control-inline">
                                                    <input name="platform" type="radio" class="custom-control-input" id="platform_android" value="android"   {{ $platform=='android'?'checked':'' }}>
                                                    <label class="custom-control-label" for="platform_android">Android</label>
                                            </div>
                                    </div>

                                    <div class="form-group">
                                            <label>Register Provider</label>
                                            <br/>
                                            <div class="custom-control custom-radio">
                                                    <input name="provider" type="radio" class="custom-control-input" id="provider_all" value="email,facebook,google,twitter,linkedin"  {{ $register_provider=='email,facebook,google,twitter,linkedin'?'checked':'' }}>
                                                    <label class="custom-control-label" for="provider_all">ALL</label>
                                            </div>

                                            <div class="custom-control custom-radio">
                                                    <input name="provider" type="radio" class="custom-control-input" id="provider_email" value="email"  {{ $register_provider=='email'?'checked':'' }}>
                                                    <label class="custom-control-label" for="provider_email">EMAIL</label>
                                            </div>

                                            <div class="custom-control custom-radio">
                                                    <input name="provider" type="radio" class="custom-control-input" id="provider_facebook" value="facebook" class="with-gap radio-col-blue" {{ $register_provider=='facebbok'?'checked':'' }}>
                                                    <label class="custom-control-label" for="provider_facebook">FACEBOOK</label>
                                            </div>

                                            <div class="custom-control custom-radio">
                                                    <input name="provider" type="radio" class="custom-control-input" id="provider_google" value="google"  {{ $register_provider=='google'?'checked':'' }}>
                                                    <label class="custom-control-label" for="provider_google">GOOGLE</label>
                                            </div>

                                            <div class="custom-control custom-radio">
                                                <input name="provider" type="radio" class="custom-control-input" id="provider_twitter" value="twitter" class="with-gap radio-col-cyan" {{ $register_provider=='twitter'?'checked':'' }}>
                                                <label class="custom-control-label" for="provider_twitter">TWITTER</label>
                                            </div>

                                            <div class="custom-control custom-radio">
                                                    <input name="provider" type="radio" class="custom-control-input" id="provider_linkedin" value="linkedin" class="with-gap radio-col-teal" {{ $register_provider=='linkedin'?'checked':'' }}>
                                                    <label class="custom-control-label" for="provider_linkedin">LINKEDIN</label>
                                            </div>
                                    </div>
                            </div>

                            <div class="col-md-4 col-12">
                                <div class="form-group">
                                    <label>From - To</label>
                                    <div id="reportrange" class="form-control">
                                            <i class="fe fe-calendar"></i>&nbsp;
                                            <span></span> <i class="fe fe-chevron-down"></i>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-6">
                                            <div class="form-group form-float">
                                                <label for="sortBy">Sort By</label>
                                                <select class="form-control show-tick" name="sortBy" id="sortBy">
                                                    <option value="id"  {{  $sortBy=='user_id'?'selected':''}}>ID</option>
                                                    <option value="name"  {{  $sortBy=='title'?'selected':''}}>NAME</option>
                                                    <option value="created_at"  {{  $sortBy=='created_at'?'selected':''}}>REGISTER DATE</option>
                                                    <option value="email" {{  $sortBy=='email'?'selected':''}}>EMAIL</option>
                                                    <option value="mobile"  {{  $sortBy=='mobile'?'selected':''}}>MOBILE</option>
                                                    <option value="provider" {{  $sortBy=='provider'?'selected':''}}>PROVIDER</option>
                                                </select>
                                            </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group form-float">
                                                <label for="sortOrder">Sort Order</label>
                                                <select class="form-control show-tick" name="sortOrder" id="sortOrder">
                                                    <option value="asc" {{  $sortOrder=='asc'?'selected':''}}>ASCENDING</option>
                                                    <option value="desc" {{  $sortOrder=='desc'?'selected':''}}>DESCENDING</option>
                                                </select>
                                        </div>
                                    </div>
                                </div>

                                <input type="hidden" id="from" name="from" value="{{ $from }}"/>
                                <input type="hidden" id="to" name="to" value="{{ $to }}"/>
                                <input type="hidden" name="page" id="page" value="{{ $users->currentPage() }}"/>

                                <div class="form-group">
                                    <label>Limit</label>
                                    <input type="number" class="form-control" min="12" max="100" name="limit" id="limit" value="{{$limit}}"/>
                                </div>
                            </div>

                        </div>
                        <button type="button" class="btn btn-success" id="apply">APPLY SORT AND FILTERS</button>

                    </div>

            </div>

                    @if(count($users)>0)
                    <input id="path" type="hidden" value="{{Request::fullUrl()}}" />
                    <input id="totalpage" type="hidden" value="{{ceil($users->total()/$users->perPage())}}" />
                    @endif

                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>IMAGE</th>
                                    <th>NAME</th>
                                    <th>EMAIL</th>
                                    <th>MOBILE</th>
                                    <th>STATUS</th>
                                    <th>PROVIDER</th>
                                    <th>PLATFORM</th>
                                    <th>LOCATION</th>
                                    <th>REGISTERD AT</th>
                                </tr>
                            </thead>
                            <tbody id="append-div">
                            @include('admin.dashboard.user.user_row')
                            </tbody>
                        </table>
                    </div>
                    @include('admin.partials.spinner')
            </div>
        </div>
    </div>
</div>

@if(count($users)>0)
@include('admin.partials.loadmorescript')
@endif
@endsection
