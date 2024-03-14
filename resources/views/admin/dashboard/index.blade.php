@extends('admin.layouts.app')
@section('title',"Dashboard")

@push('styles')
<link rel="stylesheet" type="text/css" href="/public_admin/assets/libs/daterangepicker/daterangepicker.min.css" />
<link href="/public_admin/assets/libs/morrisjs/morris.css" rel="stylesheet" />
<style type="text/css">
    .card-body {
    padding: 0.7rem 1.5rem 0.7rem 1.5rem;
}
</style>
@endpush

@push('scripts')
<script src="/public_admin/assets/libs/momentjs/moment.min.js"></script>
<script type="text/javascript" src="/public_admin/assets/libs/raphael/raphael.min.js"></script>
<script type="text/javascript" src="/public_admin/assets/libs/morrisjs/morris.js"></script>
<script type="text/javascript" src="https://code.highcharts.com/highcharts.js"></script>
<script type="text/javascript" src="/public_admin/assets/libs/daterangepicker/daterangepicker.min.js"></script>
<script>
        $(document).ready(function(){
            var start = moment().subtract(29, 'days');
            var end = moment();

            function cb(start, end) {
                $('#reportrange span').html(start.format('DD MMMM , YYYY') + ' - ' + end.format('DD MMMM , YYYY'));
                $.ajax({
                    type: "POST",
                    dataType: "json",
                    data:{from:start.format('DD-MM-YYYY HH:mm:ss'),to:end.format('DD-MM-YYYY HH:mm:ss')},
                    url: "{{ route('admin.top_categories') }}",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success:function(data){
                        console.log(data);
                        Highcharts.chart('topcategories_bar_chart',{
                            credits: {
                                enabled: false
                            },
                            chart: {
                                type: 'bar'
                            },
                            title: {
                                text: 'Top Categories By Posts'
                            },
                             xAxis: {
                                 categories:data.map((item)=>item.name),
                                 title: {
                                    text: 'Categories'
                                },
                             },
                             yAxis: {
                                min: 0,
                                 title: {
                                     text: 'Number of Posts',
                                     align: 'high'
                                 },
                                labels: {
                                    overflow: 'justify'
                                },
                                 tickInterval: 1
                             },
                             tooltip: {
                                valueSuffix: ' posts'
                             },
                             legend: {
                                layout: 'vertical',
                                align: 'right',
                                verticalAlign: 'top',
                                x: -40,
                                y: 80,
                                floating: true,
                                borderWidth: 1,
                                backgroundColor: '#FFFFFF',
                                shadow: true
                            },
                             plotOptions: {
                                bar: {
                                     dataLabels: {
                                         enabled: true
                                     }
                                 },
                                 series: {
                                    point: {
                                        events: {
                                            click: function(){
                                                var category=data.find((item)=>{return item.name=this.category});
                                                console.log('category',category);
                                                window.location.href='/cpanel/posts/all?categories='+category.id;
                                            }
                                        }
                                    }
                                }
                             },
                             series: [{data:data.map(item=> Number(item.total)) }]
                        });
                    },
                    error: function(){ alert(" AJAX Request Failed.");}
                });

            }

            $('#reportrange').daterangepicker({
                startDate: start,
                endDate: end,
                maxDate: moment(),
                timePicker:false,
                alwaysShowCalendars:true,
                ranges: {
                   'Today': [moment(), moment()],
                   'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                   'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                   'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                   'This Month': [moment().startOf('month'), moment().endOf('month')],
                   'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                }
            }, cb);
            cb(start, end);

            var usertypesDonut = Morris.Donut({
                element: 'usertypes_donut_chart',
                data: [{
                    label: 'Normal Users',
                    value: "{{$user_count['normal_users']}}"
                },{
                    label: 'Registered Writers',
                    value: "{{$user_count['writers_users']}}"
                }],
                colors: [ 'rgb(255, 152, 0)','rgb(0, 150, 136)'],
                resize: true
            });
            usertypesDonut.select(1);

            var sharetypesDonut = Morris.Donut({
                element: 'sharetypes_donut_chart',
                data: [{
                    label: 'Facebook',
                    value: "{{$share_count['facebook']}}"
                },{
                    label: 'Whatsapp',
                    value: "{{$share_count['whatsapp']}}"
                },{
                    label: 'Twitter',
                    value: "{{$share_count['twitter']}}"
                },{
                    label: 'Linkedin',
                    value: "{{$share_count['linkedin']}}"
                },{
                    label: 'Opined',
                    value: "{{$share_count['opined']}}"
                }],
                colors: [ '#3b5998','#25d366','#00acee','#0e76a8','#ff9800'],
                resize: true
            });
            sharetypesDonut.select(1);

        });
</script>
@endpush


@section('content')
<a href="#" id="scroll" style="display: none;"><span></span></a>
@php
$last = strtotime(Auth::guard('admin')->user()->password_changed_at);
$todays = strtotime($today);
$days = ($todays-$last)/(60*60*24);
@endphp
@if($days<90)
<div class="header">
        <div class="container">
          <div class="header-body">
            <div class="row align-items-end">
                <div class="col">
                    <h6 class="header-pretitle">
                    Overview
                    </h6>
                    <h1 class="header-title">
                    Dashboard
                    </h1>
                </div>
               <div class="col-auto">
                    <a class="btn btn-primary"  href="{{ route('admin.unread_messages') }}">Unread Message <span class="badge badge-light ml-2">{{ $unread_count }}</span></a>
                    <a class="btn btn-danger"  href="{{ route('admin.report_issues') }}">Reported Issue<span class="badge badge-light ml-2">{{ $reported_post }}</span></a>
                    <a class="btn btn-success"  href="{{ route('admin.offer_posts') }}">Eligible Posts</a>
               </div>
            </div>
          </div>
        </div>
</div>


<div class="container">
    @if($days>85)
        <div class="rounded mb-4 p-2 d-flex flex-md-row flex-column justify-content-between align-items-center" style="color: #856404;background-color: #fff3cd;border-color: #ffeeba;">
                    <p class="align-center mb-0">Your Password will be expired in {{round(90-$days)}} day/s ,  Please change your password .</p>
                    
                    <a href="{{ route('admin.personal_settings') }}" class="button my-1 btn  btn-warning"><i class="far fa-bell mr-2"></i>Change Now</a>
                    
        </div>
    @endif
    <div class="row">
        {{--<div class="col-md-3 col-sm-6 col-12">
                @include('admin.dashboard.components.infocard_dashboard',[
                    'title'=>'Total Threads',
                    'icon_class'=>'fas fa-hashtag',
                    'total'=>$thread_count['total'],
                    'active'=>$thread_count['active'],
                    'disabled'=>$thread_count['disabled'],
                    'website_active'=>'-',
                    'android_active'=>'-',
                    'route_name'=>'admin.threads'
                ])
        </div>
        <div class="col-md-3 col-sm-6 col-12">
                @include('admin.dashboard.components.infocard_dashboard',[
                    'title'=>'Total Short Opinions',
                    'icon_class'=>'fas fa-bullhorn',
                    'total'=>$short_opinion_count['total'],
                    'active'=>$short_opinion_count['active'],
                    'disabled'=>$short_opinion_count['disabled'],
                    'website_active'=>$short_opinion_count['website_active'],
                    'android_active'=>$short_opinion_count['android_active'],
                    'route_name'=>'admin.opinions'
                ])
        </div>--}}
        <div class="col-md-3 col-sm-6 col-12">
                @include('admin.dashboard.components.infocard_dashboard_device',[
                    'title'=>'Total APK Installed',
                    'icon_class'=>'fab fa-android',
                    'total'=>$device_count['total'],
                    'today_device'=>$device_count['today'],
                    'yesterday_device'=>$device_count['yesterday'],
                    'route_name'=>'admin.android_all'
                ])
        </div>
        <div class="col-md-3 col-sm-6 col-12">
            @include('admin.dashboard.components.infocard_dashboard',[
                'title'=>'Total Opinions',
                'icon_class'=>'far fa-comment-alt',
                'total'=>$opinion_count['total'],
                'active'=>$opinion_count['active'],
                'disabled'=>$opinion_count['disabled'],
                'website_active'=>$opinion_count['website_active'],
                'android_active'=>$opinion_count['android_active'],
                'route_name'=>'admin.opinions'
            ])
        </div>
        <div class="col-md-3 col-sm-6 col-12">
                @include('admin.dashboard.components.infocard_dashboard',[
                    'title'=>'Total Articles',
                    'icon_class'=>'far fa-file-alt',
                    'total'=>$post_count['total'],
                    'active'=>$post_count['active'],
                    'disabled'=>$post_count['disabled'],
                    'website_active'=>$post_count['website_active'],
                    'android_active'=>$post_count['android_active'],
                    'route_name'=>'admin.posts'
                ])
        </div>
        <div class="col-md-3 col-sm-6 col-12">
                @include('admin.dashboard.components.infocard_dashboard',[
                    'title'=>'Total Users',
                    'icon_class'=>'fas fa-users',
                    'total'=>$user_count['total'],
                    'active'=>$user_count['active'],
                    'disabled'=>$user_count['disabled'],
                    'website_active'=>$user_count['website_active'],
                    'android_active'=>$user_count['android_active'],
                    'route_name'=>'admin.users'
                ])
        </div>
    </div>
    <div class="row">
            <div class="col-md-3 col-sm-6 col-12">
                <div class="card">
                        <div class="card-header">
                                <h4 class="card-header-title">Votes Given (Poll)</h4>
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">
                                    TODAY
                                    <span class="float-right"><b>{{$polls_vote_count['today']}}</b></span>
                                </li>
                                <li class="list-group-item">
                                    YESTERDAY
                                    <span class="float-right"><b>{{$polls_vote_count['yesterday']}}</b></span>
                                </li>
                                <li class="list-group-item">
                                    LAST 7 DAYS
                                    <span class="float-right"><b>{{$polls_vote_count['last_7_days']}}</b></span>
                                </li>
                                <li class="list-group-item">
                                    THIS MONTH
                                    <span class="float-right"><b>{{$polls_vote_count['this_month']}}</b></span>
                                </li>
                                <li class="list-group-item">
                                    LAST MONTH
                                    <span class="float-right"><b>{{$polls_vote_count['last_month']}}</b></span>
                                </li>
                                <li class="list-group-item">
                                    ALL
                                    <span class="float-right"><b>{{$polls_vote_count['total']}}</b></span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                    <!--<div class="card">
                        <div class="card-header">
                            <h4 class="card-header-title">Threads Created</h4>
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">
                                    TODAY
                                    <span class="float-right"><b>{{$thread_count['today']}}</b></span>
                                </li>
                                <li class="list-group-item">
                                    YESTERDAY
                                    <span class="float-right"><b>{{$thread_count['yesterday']}}</b></span>
                                </li>
                                <li class="list-group-item">
                                    LAST 7 DAYS
                                    <span class="float-right"><b>{{$thread_count['last_7_days']}}</b></span>
                                </li>
                                <li class="list-group-item">
                                    THIS MONTH
                                    <span class="float-right"><b>{{$thread_count['this_month']}}</b></span>
                                </li>
                                <li class="list-group-item">
                                    LAST MONTH
                                    <span class="float-right"><b>{{$thread_count['last_month']}}</b></span>
                                </li>
                                <li class="list-group-item">
                                    ALL
                                    <span class="float-right"><b>{{$thread_count['total']}}</b></span>
                                </li>
                            </ul>
                        </div>
                    </div>
            </div>-->
            <div class="col-md-3 col-sm-6 col-12">
                    <div class="card">
                        <div class="card-header">
                                <h4 class="card-header-title">Opinions Given</h4>
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">
                                    TODAY
                                    <span class="float-right"><b>{{$opinion_count['today']}}</b></span>
                                </li>
                                <li class="list-group-item">
                                    YESTERDAY
                                    <span class="float-right"><b>{{$opinion_count['yesterday']}}</b></span>
                                </li>
                                <li class="list-group-item">
                                    LAST 7 DAYS
                                    <span class="float-right"><b>{{$opinion_count['last_7_days']}}</b></span>
                                </li>
                                <li class="list-group-item">
                                    THIS MONTH
                                    <span class="float-right"><b>{{$opinion_count['this_month']}}</b></span>
                                </li>
                                <li class="list-group-item">
                                    LAST MONTH
                                    <span class="float-right"><b>{{$opinion_count['last_month']}}</b></span>
                                </li>
                                <li class="list-group-item">
                                    ALL
                                    <span class="float-right"><b>{{ $opinion_count['total'] }}</b></span>
                                </li>
                            </ul>
                        </div>
                    </div>
            </div>
            <div class="col-md-3 col-sm-6 col-12">
                    <div class="card">
                        <div class="card-header">
                                <h4 class="card-header-title">Posts Created</h4>
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">
                                    TODAY
                                    <span class="float-right"><b>{{$post_count['today']}}</b></span>
                                </li>
                                <li class="list-group-item">
                                    YESTERDAY
                                    <span class="float-right"><b>{{$post_count['yesterday']}}</b></span>
                                </li>
                                <li class="list-group-item">
                                    LAST 7 DAYS
                                    <span class="float-right"><b>{{$post_count['last_7_days']}}</b></span>
                                </li>
                                <li class="list-group-item">
                                    THIS MONTH
                                    <span class="float-right"><b>{{$post_count['this_month']}}</b></span>
                                </li>
                                <li class="list-group-item">
                                    LAST MONTH
                                    <span class="float-right"><b>{{$post_count['last_month']}}</b></span>
                                </li>
                                <li class="list-group-item">
                                    ALL
                                    <span class="float-right"><b>{{$post_count['total']}}</b></span>
                                </li>
                            </ul>
                        </div>
                    </div>
            </div>
            <div class="col-md-3 col-sm-6 col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-header-title">Registered Users</h4>
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">
                                    TODAY
                                    <span class="float-right"><b>{{$user_count['today']}}</b></span>
                                </li>
                                <li class="list-group-item">
                                    YESTERDAY
                                    <span class="float-right"><b>{{$user_count['yesterday']}}</b></span>
                                </li>
                                <li class="list-group-item">
                                    LAST 7 DAYS
                                    <span class="float-right"><b>{{$user_count['last_7_days']}}</b></span>
                                </li>
                                <li class="list-group-item">
                                    THIS MONTH
                                    <span class="float-right"><b>{{$user_count['this_month']}}</b></span>
                                </li>
                                <li class="list-group-item">
                                    LAST MONTH
                                    <span class="float-right"><b>{{$user_count['last_month']}}</b></span>
                                </li>
                                <li class="list-group-item">
                                    ALL
                                    <span class="float-right"><b>{{$user_count['total']}}</b></span>
                                </li>
                            </ul>
                        </div>
                    </div>
            </div>
    </div>

    <div class="row">
            <div class="col-md-3 col-sm-6 col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-header-title">Shares</h4>
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">
                                   <a href="{{route('admin.show_by_plateform',['plateform'=>'facebook'])}}"> Facebook </a>
                                    <span class="float-right"><b>{{$share_count['facebook']}}</b></span>
                                </li>
                                <li class="list-group-item">
                                  <a href="{{route('admin.show_by_plateform',['plateform'=>'whatsapp'])}}">  Whatsapp </a>
                                    <span class="float-right"><b>{{$share_count['whatsapp']}}</b></span>
                                </li>
                                <li class="list-group-item">
                                   <a href="{{route('admin.show_by_plateform',['plateform'=>'twitter'])}}"> Twitter </a>
                                    <span class="float-right"><b>{{$share_count['twitter']}}</b></span>
                                </li>
                                <li class="list-group-item">
                                   <a href="{{route('admin.show_by_plateform',['plateform'=>'linkedin'])}}"> Linkedin </a>
                                    <span class="float-right"><b>{{$share_count['linkedin']}}</b></span>
                                </li>
                                <li class="list-group-item">
                                   <a href="{{route('admin.show_by_plateform',['plateform'=>'embed'])}}"> Opined </a>
                                    <span class="float-right"><b>{{$share_count['opined']}}</b></span>
                                </li>
                                <li class="list-group-item"><a href="/cpanel/share">
                                    Total Active
                                    <span class="float-right"><b>{{$share_count['total']}}</a></span></b>
                                </li>
                                
                            </ul>
                        </div>
                    </div>

            </div>
            <div class="col-md-3 col-sm-6 col-12">
                    <div class="card">
                        <div class="card-header">
                                <h4 class="card-header-title">Shared by day</h4>
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">
                                    TODAY
                                    <span class="float-right"><b>{{$share_count['today']}}</b></span>
                                </li>
                                <li class="list-group-item">
                                    YESTERDAY
                                    <span class="float-right"><b>{{$share_count['yesterday']}}</b></span>
                                </li>
                                <li class="list-group-item">
                                    LAST 7 DAYS
                                    <span class="float-right"><b>{{$share_count['last_7_days']}}</b></span>
                                </li>
                                <li class="list-group-item">
                                    THIS WEEK
                                    <span class="float-right"><b>{{$share_count['this_week']}}</b></span>
                                </li>
                                <li class="list-group-item">
                                    THIS MONTH
                                    <span class="float-right"><b>{{$share_count['this_month']}}</b></span>
                                </li>
                                <li class="list-group-item">
                                    LAST MONTH
                                    <span class="float-right"><b>{{$share_count['last_month']}}</b></span>
                                </li>
                                </ul>
                        </div>
                    </div>
            </div>
            <div class="col-md-3 col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-header-title">Shares Graph</h4>
                    </div>
                    <div class="card-body">
                            <div id="sharetypes_donut_chart" style="margin-top: -30%;margin-bottom: -20%;"></div>
                            <a class="btn btn-light btn-block" href="{{ route('admin.share') }}">See Total Shares</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-header-title">Writers vs Normal Users Graph</h4>
                    </div>
                    <div class="card-body">
                            <div id="usertypes_donut_chart" style="margin-top: -30%;margin-bottom: -20%;"></div>
                            <a class="btn btn-light btn-block" href="{{ route('admin.writers') }}">See Registered Writers</a>
                    </div>
                </div>
            </div>
            
    </div>
    {{--<div class="row">

            <div class="col-md-8 col-12">
                    <div class="card">
                        <div class="card-header">
                                <h4 class="card-header-title">Top Categories
                                    <span style="float:right">
                                        <div id="reportrange" style="background:#fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
                                                <i class="far fa-calendar-alt"></i>&nbsp;
                                                <span></span> <i class="fa fa-caret-down"></i>
                                        </div>
                                    </span>
                                </h4>
                            </div>
                            <div class="card-body">
                                    <div id="topcategories_bar_chart">

                                    </div>
                            </div>
                    </div>
            </div>

        </div>

    <div class="row">
        <div class="col-md-12 col-12">

                <h2 class="header-title">Latest 12 Posts<h4>

                <div class="card-body">
                    @foreach($latest_posts as $post)
                    <div class="card">
                            <div class="card-body">
                                    <div class="mb-3">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                        <a href="{{ route('admin.user_details',['id'=>$post->user['id']]) }}" class="avatar">
                                            <img src="{{ $post->user['image'] }}" alt="..." class="avatar-img rounded-circle"  onerror="this.onerror=null;this.src='/img/profile-default-opined_100x100.png';">
                                        </a>
                                        </div>
                                        <div class="col ml-n2">
                                            <h4 class="card-title mb-1">
                                                <a href="{{ route('admin.user_details',['id'=>$post->user['id']]) }}">{{  ucfirst($post->user['name']) }}</a>
                                            </h4>
                                            <p class="card-text small text-muted">
                                                Post Publish DateTime : {{ \Carbon\Carbon::parse($post->created_at)->format('l , jS F Y , h:i:s A')   }} <br/>
                                                Post Last Updated : {{ \Carbon\Carbon::parse($post->updated_at)->format('l , jS F Y , h:i:s A')   }}
                                            </p>
                                        </div>
                                    </div>
                                    </div>
                                    <a href="{{ route('admin.blog_post',['id'=>$post->id]) }}" style="text-decoration: none;">
                                    <h2 class="text-center mt-4">{{ $post->title }}</h2>
                                    <p class="text-center my-4">
                                            <img  src="{{ $post->coverimage==NULL?'https://i.imgur.com/YfZfzno.png':$post->coverimage }}" alt="..." class="img-fluid rounded" style="max-height:500px;max-width:700px;">
                                    </p>
                                    </a>
                            </div>
                    </div>

                    @endforeach
                </div>

    </div>

</div>--}}
@else
<script>
    newLocation();
    function newLocation() {
        window.location="/cpanel/settings/expirepassword";
    }
</script>
@endif
@endsection
