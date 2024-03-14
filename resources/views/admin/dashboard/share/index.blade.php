@extends('admin.layouts.app')
@section('title',"Shares")

@push('styles')
<link rel="stylesheet" type="text/css" href="/public_admin/assets/libs/daterangepicker/daterangepicker.min.css" />
<link href="/public_admin/assets/libs/morrisjs/morris.css" rel="stylesheet" />

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
                    url: "{{ route('admin.top_opinions_byshare') }}",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success:function(data){
                        console.log(data);
                        Highcharts.chart('topopinions_bar_chart',{
                            credits: {
                                enabled: false
                            },
                            chart: {
                                type: 'bar'
                            },
                            title: {
                                text: 'Top Opinions By Share'
                            },
                             xAxis: {
                                 categories:data.map((item)=>item.short_opinion_id),
                                 title: {
                                    text: 'Opinions id'
                                },
                             },
                             yAxis: {
                                min: 0,
                                 title: {
                                     text: 'Number of Time',
                                     align: 'high'
                                 },
                                labels: {
                                    overflow: 'justify'
                                },
                                 tickInterval: 1
                             },
                             tooltip: {
                                valueSuffix: ' shared'
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
                                                var opinion=data.find((item)=>{return item.name=this.opinion});
                                                console.log('opinion',opinion);
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

            

        });
</script>
@endpush


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
                    Shares
                    </h1>
                </div>
               <div class="col-auto">
                    
               </div>
            </div>
          </div>
        </div>
</div>


<div class="container">
    
      
    
    <div class="row">
            <div class="col-md-3 col-sm-6 col-12">
                    <div class="card">
                        <a href="{{route('admin.show_by_plateform',['plateform'=>'facebook'])}}"> <div class="card-header" style="background: #3b5998">
                           <h4 class="card-header-title" style="color:#fff">On Facebook</h4>
                        </div></a>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">
                                    TODAY
                                    <span class="float-right"><b>{{$facebook_count['today']}}</b></span>
                                </li>
                                <li class="list-group-item">
                                    YESTERDAY
                                    <span class="float-right"><b>{{$facebook_count['yesterday']}}</b></span>
                                </li>
                                <li class="list-group-item">
                                    LAST 7 DAYS
                                    <span class="float-right"><b>{{$facebook_count['last_7_days']}}</b></span>
                                </li>
                                <li class="list-group-item">
                                    THIS MONTH
                                    <span class="float-right"><b>{{$facebook_count['this_month']}}</b></span>
                                </li>
                                <li class="list-group-item">
                                    LAST MONTH
                                    <span class="float-right"><b>{{$facebook_count['last_month']}}</b></span>
                                </li>
                                <li class="list-group-item">
                                    ALL ACTIVE
                                    <span class="float-right"><b>{{$facebook_count['total']}}</b></span>
                                </li>
                            </ul>
                        </div>
                    </div>
            </div>
            <div class="col-md-3 col-sm-6 col-12">
                    <div class="card">
                        <a href="{{route('admin.show_by_plateform',['plateform'=>'whatsapp'])}}"><div class="card-header" style="background: #25d366">
                            <h4 class="card-header-title" style="color:#fff">On Whatsapp</h4>
                        </div></a>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">
                                    TODAY
                                    <span class="float-right"><b>{{$whatsapp_count['today']}}</b></span>
                                </li>
                                <li class="list-group-item">
                                    YESTERDAY
                                    <span class="float-right"><b>{{$whatsapp_count['yesterday']}}</b></span>
                                </li>
                                <li class="list-group-item">
                                    LAST 7 DAYS
                                    <span class="float-right"><b>{{$whatsapp_count['last_7_days']}}</b></span>
                                </li>
                                <li class="list-group-item">
                                    THIS MONTH
                                    <span class="float-right"><b>{{$whatsapp_count['this_month']}}</b></span>
                                </li>
                                <li class="list-group-item">
                                    LAST MONTH
                                    <span class="float-right"><b>{{$whatsapp_count['last_month']}}</b></span>
                                </li>
                                <li class="list-group-item">
                                    ALL ACTIVE
                                    <span class="float-right"><b>{{$whatsapp_count['total']}}</b></span>
                                </li>
                            </ul>
                        </div>
                    </div>
            </div>
        
        <div class="col-md-3 col-sm-6 col-12">
                    <div class="card">
                         <a href="{{route('admin.show_by_plateform',['plateform'=>'twitter'])}}"><div class="card-header" style="background: #00acee">
                           <h4 class="card-header-title" style="color:#fff">On Twitter</h4>
                        </div></a>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">
                                    TODAY
                                    <span class="float-right"><b>{{$twitter_count['today']}}</b></span>
                                </li>
                                <li class="list-group-item">
                                    YESTERDAY
                                    <span class="float-right"><b>{{$twitter_count['yesterday']}}</b></span>
                                </li>
                                <li class="list-group-item">
                                    LAST 7 DAYS
                                    <span class="float-right"><b>{{$twitter_count['last_7_days']}}</b></span>
                                </li>
                                <li class="list-group-item">
                                    THIS MONTH
                                    <span class="float-right"><b>{{$twitter_count['this_month']}}</b></span>
                                </li>
                                <li class="list-group-item">
                                    LAST MONTH
                                    <span class="float-right"><b>{{$twitter_count['last_month']}}</b></span>
                                </li>
                                <li class="list-group-item">
                                    ALL ACTIVE
                                    <span class="float-right"><b>{{$twitter_count['total']}}</b></span>
                                </li>
                            </ul>
                        </div>
                    </div>
            </div>
            <div class="col-md-3 col-sm-6 col-12">
                    <div class="card">
                        <a href="{{route('admin.show_by_plateform',['plateform'=>'linkedin'])}}"><div class="card-header" style="background: #0e76a8">
                            <h4 class="card-header-title" style="color:#fff">On Linkedin</h4>
                        </div></a>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">
                                    TODAY
                                    <span class="float-right"><b>{{$linkedin_count['today']}}</b></span>
                                </li>
                                <li class="list-group-item">
                                    YESTERDAY
                                    <span class="float-right"><b>{{$linkedin_count['yesterday']}}</b></span>
                                </li>
                                <li class="list-group-item">
                                    LAST 7 DAYS
                                    <span class="float-right"><b>{{$linkedin_count['last_7_days']}}</b></span>
                                </li>
                                <li class="list-group-item">
                                    THIS MONTH
                                    <span class="float-right"><b>{{$linkedin_count['this_month']}}</b></span>
                                </li>
                                <li class="list-group-item">
                                    LAST MONTH
                                    <span class="float-right"><b>{{$linkedin_count['last_month']}}</b></span>
                                </li>
                                <li class="list-group-item">
                                    ALL ACTIVE
                                    <span class="float-right"><b>{{$linkedin_count['total']}}</b></span>
                                </li>
                            </ul>
                        </div>
                    </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3 col-sm-6 col-12">
                    <div class="card">
                        <a href="{{route('admin.show_by_plateform',['plateform'=>'embed'])}}"><div class="card-header" style="background: #ff9800">
                            <h4 class="card-header-title" style="color:#fff">On Opined</h4>
                        </div></a>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">
                                    TODAY
                                    <span class="float-right"><b>{{$opined_count['today']}}</b></span>
                                </li>
                                <li class="list-group-item">
                                    YESTERDAY
                                    <span class="float-right"><b>{{$opined_count['yesterday']}}</b></span>
                                </li>
                                <li class="list-group-item">
                                    LAST 7 DAYS
                                    <span class="float-right"><b>{{$opined_count['last_7_days']}}</b></span>
                                </li>
                                <li class="list-group-item">
                                    THIS MONTH
                                    <span class="float-right"><b>{{$opined_count['this_month']}}</b></span>
                                </li>
                                <li class="list-group-item">
                                    LAST MONTH
                                    <span class="float-right"><b>{{$opined_count['last_month']}}</b></span>
                                </li>
                                <li class="list-group-item">
                                    ALL ACTIVE
                                    <span class="float-right"><b>{{$opined_count['total']}}</b></span>
                                </li>
                            </ul>
                        </div>
                    </div>
            </div>
            <div class="col-md-9 col-12">
                    <div class="card">
                        <div class="card-header">
                                <h4 class="card-header-title">Top Shared Opinions
                                    <span style="float:right">
                                        <div id="reportrange" style="background:#fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
                                                <i class="far fa-calendar-alt"></i>&nbsp;
                                                <span></span> <i class="fa fa-caret-down"></i>
                                        </div>
                                    </span>
                                </h4>
                            </div>
                            <div class="card-body">
                                    <div id="topopinions_bar_chart">

                                    </div>
                            </div>
                    </div>
            </div>
        
        
        </div>

@endsection
