@extends('admin.layouts.app')
@section('title','Polls')

@push('scripts')
<script src="/public_admin/assets/libs/list.js/dist/list.min.js"></script>
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
                    Polls Dashboard
                    <span  class="ml-2 badge badge-primary" style="cursor:pointer" onclick="window.location.href=`/cpanel/poll/all`">{{ $polls_count['total'].' Total Poll'}}</span>
                    <span  class="ml-2 badge badge-success" style="cursor:pointer" onclick="window.location.href=`/cpanel/poll/all?visibility=1`">{{$polls_count['active'] .' Active'}}</span>
                    <span  class="ml-2 badge badge-warning" style="cursor:pointer" onclick="window.location.href=`/cpanel/poll/all?visibility=0`">{{ $polls_count['disabled'] .' Paused'}}</span>
                    <span  class="ml-2 badge badge-secondary" style="cursor:pointer" onclick="window.location.href=`/cpanel/poll/votes`">{{$polls_vote_count['total'] .' Votes'}}</span>
                    </h1>
                </div>
               <div class="col-auto">
                    <a href="{{route('admin.polls')}}"  class="btn btn-secondary lift">
                      All Polls
                    </a>
                    <a href="{{route('admin.show_select_poll_type')}}"  class="btn btn-primary lift">
                      Create New
                    </a>
                    <a href="{{route('admin.show_add_poll_type')}}"  class="btn btn-warning lift">
                      Add Type
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
                                <h4 class="card-header-title">Polls</h4>
                        </div>
                        <div class="card-body">
                            
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">
                                    TODAY
                                    <span class="float-right"><b>{{$polls_count['today']}}</b></span>
                                </li>
                                <li class="list-group-item">
                                    YESTERDAY
                                    <span class="float-right"><b>{{$polls_count['yesterday']}}</b></span>
                                </li>
                                <li class="list-group-item">
                                    LAST 7 DAYS
                                    <span class="float-right"><b>{{$polls_count['last_7_days']}}</b></span>
                                </li>
                                <li class="list-group-item">
                                    THIS MONTH
                                    <span class="float-right"><b>{{$polls_count['this_month']}}</b></span>
                                </li>
                                <li class="list-group-item">
                                    LAST MONTH
                                    <span class="float-right"><b>{{$polls_count['last_month']}}</b></span>
                                </li>
                                <li class="list-group-item">
                                    ALL
                                    <span class="float-right"><b>{{$polls_count['total']}}</b></span>
                                </li>
                            </ul>
                        </div>
                    </div>


            </div>
            <div class="col-md-6 col-sm-6 col-12">
                <div class="card">
                    <div class="card-header">
                                <h4 class="card-header-title">Trending Polls</h4>
                        </div>
                @if(count($top_polls)>0)
                    <input id="path" type="hidden" value="{{Request::fullUrl()}}" />
                    <input id="totalpage" type="hidden" value="{{ceil($top_polls->total()/$top_polls->perPage())}}" />
                   
                    @endif
                    <div class="table-responsive">
                        <table id="posts-table" class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Title</th>
                                    <th>Total Votes</th>
                                    </tr>
                            </thead>
                            <tbody id="append-div">
                                @foreach($top_polls_trending as $index=>$top_poll)
                                    @include('admin.dashboard.poll.components.home_polls_row')
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-12">
                    <div class="card">
                        <div class="card-header">
                                <h4 class="card-header-title">Trending Cities</h4>
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                            @if(count($top_locations_trending)>0)
                                @foreach($top_locations_trending as $top_location_trending)
                                <li class="list-group-item">
                                    {{ $top_location_trending->city }}, {{ $top_location_trending->country_code }}
                                    <span class="float-right"><b>{{$top_location_trending->count_city}}</b></span>
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
        </div>
        <div class="row">
            <div class="col-md-3 col-sm-6 col-12">
                    <div class="card">
                        <div class="card-header">
                                <h4 class="card-header-title">Vote on Polls</h4>
                        </div>
                        <div class="card-body">

                            <ul class="list-group list-group-flush">
                                <li class="list-group-item" style="cursor:pointer" onclick="window.location.href=`/cpanel/poll/votes?from={{$today}}&to={{$to}}`">
                                    TODAY
                                    <span class="float-right"><b>{{$polls_vote_count['today']}}</b></span>
                                </li>
                                <li class="list-group-item" style="cursor:pointer" onclick="window.location.href=`/cpanel/poll/votes?from={{$yesterday}}&to={{$yesterday_last}}`">
                                    YESTERDAY
                                    <span class="float-right"><b>{{$polls_vote_count['yesterday']}}</b></span>
                                </li>
                                <li class="list-group-item" style="cursor:pointer" onclick="window.location.href=`/cpanel/poll/votes?from={{$seven_days}}&to={{$to}}`">
                                    LAST 7 DAYS
                                    <span class="float-right"><b>{{$polls_vote_count['last_7_days']}}</b></span>
                                </li>
                                <li class="list-group-item" style="cursor:pointer" onclick="window.location.href=`/cpanel/poll/votes?from={{$this_month}}&to={{$to}}`">
                                    THIS MONTH
                                    <span class="float-right"><b>{{$polls_vote_count['this_month']}}</b></span>
                                </li>
                                <li class="list-group-item" style="cursor:pointer" onclick="window.location.href=`/cpanel/poll/votes?from={{$last_month_start}}&to={{$last_month_end}}`">
                                    LAST MONTH
                                    <span class="float-right"><b>{{$polls_vote_count['last_month']}}</b></span>
                                </li>
                                <li class="list-group-item" style="cursor:pointer" onclick="window.location.href=`/cpanel/poll/votes`">
                                    ALL
                                    <span class="float-right"><b>{{$polls_vote_count['total']}}</b></span>
                                </li>
                            </ul>
                        </div>
                    </div>


            </div>
            <div class="col-md-6 col-sm-6 col-12">
                <div class="card">
                    <div class="card-header">
                                <h4 class="card-header-title">Top Polls</h4>
                        </div>
                @if(count($top_polls)>0)
                    <input id="path" type="hidden" value="{{Request::fullUrl()}}" />
                    <input id="totalpage" type="hidden" value="{{ceil($top_polls->total()/$top_polls->perPage())}}" />
                   
                    @endif
                    <div class="table-responsive">
                        <table id="posts-table" class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Title</th>
                                    <th>Total Votes</th>
                                    </tr>
                            </thead>
                            <tbody id="append-div">
                                @foreach($top_polls as $index=>$top_poll)
                                    @include('admin.dashboard.poll.components.home_polls_row')
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                </div>
            </div>

            <div class="col-md-3 col-sm-6 col-12">
                    <div class="card">
                        <div class="card-header">
                                <h4 class="card-header-title">Top Cities</h4>
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                            @if(count($top_locations)>0)
                                @foreach($top_locations as $top_location)
                                <li class="list-group-item">
                                    {{ $top_location->city }}, {{ $top_location_trending->country_code }}
                                    <span class="float-right"><b>{{$top_location->count_city}}</b></span>
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
    </div>
</div>
    


@endsection
