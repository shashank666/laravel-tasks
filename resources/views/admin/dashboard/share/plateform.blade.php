@extends('admin.layouts.app')
@section('title','Shares')

@push('styles')
<link rel="stylesheet" type="text/css" href="/public_admin/assets/libs/daterangepicker/daterangepicker.css" />
<link href="/public_admin/assets/libs/morrisjs/morris.css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="/public_admin/assets/libs/daterangepicker/daterangepicker.css" />
<style>
    .data-dbcount{
        display:none;
    }
</style>
@endpush


@push('scripts')
<script type="text/javascript" src="/public_admin/assets/libs/momentjs/moment.min.js"></script>
<script type="text/javascript" src="/public_admin/assets/libs/daterangepicker/daterangepicker.min.js"></script>
<script type="text/javascript" src="/public_admin/assets/libs/raphael/raphael.min.js"></script>
<script type="text/javascript" src="/public_admin/assets/libs/morrisjs/morris.js"></script>

<script>
    $(document).ready(function(){
        var start =moment($('#from').val(),'YYYY-MM-DD');
        var end = moment($('#to').val(),'YYYY-MM-DD');

        function cb(start, end) {
            $('#reportrange span').html(start.format('DD MMMM , YYYY') + ' - ' + end.format('DD MMMM , YYYY'));
            $('#from').val(start.format('YYYY-MM-DD'));
            $('#to').val(end.format('YYYY-MM-DD'));
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

        Morris.Bar({
            element: 'shares_bar_chart',
            data: <?php echo json_encode($month_wise_count)?>,
            xkey: ['month_year'],
            ykeys: ['total'],
            yLabelFormat: function(y){return y != Math.round(y)?'':y;},
            labels: ['Shares'],
            barColors: [ 'rgb(255, 152, 0)'],
        });

        
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
                        Shares on {{ucfirst($plateform)}}
                        
                        </h1>
                    </div>
                   <div class="col-auto">
                    <a href="/cpanel/share"  class="btn btn-primary lift">
                      All Shares
                    </a>
                </div>
                </div>
              </div>
            </div>
    </div>

  <div class="container">
        <div class="row">
            <div class="col-md-12 col-12">
                    <div class="card">
                            <div class="card-header">
                                <h4 class="card-header-title">
                                    Month Wise Shares on {{ucfirst($plateform)}} in Graph 
                                </h4>
                            </div>
                            <div class="card-body">
                                <div id="shares_bar_chart" class="graph"></div>
                            </div>
                    </div>
            </div>
            <div class="col-lg-12 col-md-12 col-sm-12 col-12">
              <div class="card">
    			@if(count($shares)>0)
                    <input id="path" type="hidden" value="{{Request::fullUrl()}}" />
                    <input id="totalpage" type="hidden" value="{{ceil($shares->total()/$shares->perPage())}}" />
                   
                    @endif
                    <div class="table-responsive">
                        <table id="posts-table" class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>User</th>
                                    <th>Opinion</th>
                                    <th>Type</th>
                                    <th>When</th>
                                    <!--<th>IP</th>-->
                                    <th>OPINION STATUS</th>
                                   	</tr>
                            </thead>
                            <tbody id="append-div">
                               
                               @include('admin.dashboard.share.share_row')
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
       {{ $shares->links('frontend.posts.components.pagination') }}  
    </div>
</div>

@endsection

