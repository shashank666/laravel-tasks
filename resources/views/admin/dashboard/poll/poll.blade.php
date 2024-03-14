@extends('admin.layouts.app')
@section('title','Polls')

@push('scripts')
<script src="/public_admin/assets/libs/list.js/dist/list.min.js"></script>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

<script type="text/javascript">
      google.charts.load("current", {packages:["corechart"]});
      google.charts.setOnLoadCallback(drawChart);
      function drawChart() {
      //$result = (array) json_decode($json);
       var record={!! json_encode($poll_options_chart) !!};
       
       //console.log(record);
      
       // Create our data table.
       var data = new google.visualization.DataTable();
        data.addColumn('string', 'Options');
       data.addColumn('number', 'Votes');
       for(var k in record){
            var v = record[k];
           
             data.addRow([k,v]);
          //console.log(v);
          }
        var options = {
          title: 'Result',
          //is3D: true,
          //pieHole: 0.4,
          fontSize: 17,
          //chartArea:{left:20,top:20,width:'100%',height:'100%'},
          chartArea:{left:30,width:'100%',height:'300'},
          backgroundColor: '#fff8ed',
          /*colors: ['#e2431e', '#d3362d', '#e7711b',
                   '#e49307', '#e49307', '#b9c246'],*/
          //colors: [""],
        };
        //console.log(options)
        var chart = new google.visualization.PieChart(document.getElementById('piechart_3d'));
        chart.draw(data, options);
      }
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
                    <h4 class="header-title">
                    {{$poll->title}}
                    <span class="badge {{ $poll->visibility==1?'badge-success':'badge-danger' }}">{{ $poll->visibility==1?'Active':'Paused' }}</span>
                    </h4>
                </div>
               <div class="col-auto">

                    <a href="{{route('admin.poll_edit',['id'=>$poll->id])}}"  class="btn btn-primary lift">
                      EDIT
                    </a>
                    <a href="{{route('admin.poll_visibility',['id'=>$poll->id])}}"  class="btn btn-warning lift" style="background: {{ $poll->visibility==1?'red':'green' }}; color: #fff">
                      {{ $poll->visibility==1?'PAUSE':'START' }}

                    </a>
                </div>
            </div>
          </div>
        </div>
</div>
<div class="container">
        <div class="row">
            <div class="col-md-3 col-sm-6 col-12">
                    <div class="card">
                        <div class="card-header">
                                <h4 class="card-header-title">Vote on This Poll</h4>
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">
                                    TODAY
                                    <span class="float-right"><b>{{$polls_result_count['today']}}</b></span>
                                </li>
                                <li class="list-group-item">
                                    YESTERDAY
                                    <span class="float-right"><b>{{$polls_result_count['yesterday']}}</b></span>
                                </li>
                                <li class="list-group-item">
                                    LAST 7 DAYS
                                    <span class="float-right"><b>{{$polls_result_count['last_7_days']}}</b></span>
                                </li>
                                <li class="list-group-item">
                                    THIS MONTH
                                    <span class="float-right"><b>{{$polls_result_count['this_month']}}</b></span>
                                </li>
                                <li class="list-group-item">
                                    LAST MONTH
                                    <span class="float-right"><b>{{$polls_result_count['last_month']}}</b></span>
                                </li>
                                <li class="list-group-item">
                                    ALL
                                    <span class="float-right"><b>{{$polls_result_count['total']}}</b></span>
                                </li>
                            </ul>
                        </div>
                    </div>
            </div>
        <div class="col-md-6 col-sm-6 col-12">
            <div id="piechart_3d" style="width: 100%; height: 390px;"></div>
        </div>
        <div class="col-md-3 col-sm-6 col-12">
                    <div class="card">
                        <div class="card-header">
                                <h4 class="card-header-title">Top City</h4>
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                            @if(count($poll_locations)>0)
                                @foreach($poll_locations as $poll_location)
                                <li class="list-group-item">
                                    {{ $poll_location->city }}
                                    <span class="float-right"><b>{{$poll_location->count_city}}</b></span>
                                </li>
                                @endforeach
                            @else
                            <li class="list-group-item">
                                <span class="float-left"><b>No Data</b></span>
                            </li>
                            @endif
                            </ul>
                        </div>
                    </div>
            </div>
            <div class="col-lg-12 col-md-12 col-sm-12 col-12">
              <div class="card">
                @if(count($pollresults)>0)
                    <input id="path" type="hidden" value="{{Request::fullUrl()}}" />
                    <input id="totalpage" type="hidden" value="{{ceil($pollresults->total()/$pollresults->perPage())}}" />
                   
                    @endif
                    <div class="table-responsive">
                        <table id="posts-table" class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>User</th>
                                    @if($poll->poll_type == "UDN")
                                    <th>Voting Type</th>
                                    @endif
                                    <th>Vote</th>
                                    <th>Location</th>
                                    <th>Voted at</th>
                                    
                                    </tr>
                            </thead>
                            <tbody id="append-div">
                            @if($poll->poll_type == "UDN")
                                @include('admin.dashboard.poll.components.udnpollsvote_row')
                            @elseif($poll->poll_type == "MCPS")
                                @include('admin.dashboard.poll.components.mcpspollsvote_row')
                            @endif
                            </tbody>
                        </table>
                    </div>
                    @include('admin.partials.spinner')
                </div>
            </div>
        </div>
    </div>
    <div class="row">
    <div class="col align-self-center">
       {{ $pollresults->links('frontend.posts.components.pagination') }}  
    </div>
</div>


@endsection
