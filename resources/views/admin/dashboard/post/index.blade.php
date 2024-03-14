@extends('admin.layouts.app')
@section('title','Articles')

@push('styles')
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
<script src="/public_admin/assets/libs/momentjs/moment.min.js"></script>
<script type="text/javascript" src="/public_admin/assets/libs/raphael/raphael.min.js"></script>
<script type="text/javascript" src="/public_admin/assets/libs/morrisjs/morris.js"></script>
<script type="text/javascript" src="https://code.highcharts.com/highcharts.js"></script>


<script>
        $(document).ready(function(){

            $(".clear-btn").click(function() {
             $("#searchQuery").val('');
         });
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

/*
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
            */
        });


        $(document).on('change','#searchBy',function(){
            $('#searchBy').val($(this).val());
        });

        $(document).on('click','#btnResetSearch',function(){
            if($('#searchQuery').val().trim().length>0){
                $('#searchQuery').val('');
                $('#searchBy').val('title');
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
            var categories=$('#category').val();
            var sortBy=$('#sortBy').val();
            var sortOrder=$('#sortOrder').val();
            var platform=$('input[type=radio][name=platform]:checked').val();
            var status=$('input[type=radio][name=status]:checked').val();
            var is_active=$('input[type=radio][name=is_active]:checked').val();
            var plagiarism_checked=$('input[type=radio][name=plagiarism_checked]:checked').val();
            var likes=$('#likes').val();
            var likes_operator=$('#likes_operator').val();
            var views=$('#views').val();
            var views_operator=$('#views_operator').val();
            var comments=$('#comments').val();
            var comments_operator=$('#comments_operator').val();
            var limit=parseInt($('#limit').val());
            var page=parseInt($('#page').val());

           var URL=`/cpanel/posts/all?page=${page}&categories=${categories}&sortBy=${sortBy}&sortOrder=${sortOrder}&searchBy=${searchBy}&searchQuery=${searchQuery}&from=${from}&to=${to}&status=${status}&platform=${platform}&is_active=${is_active}&plagiarism_checked=${plagiarism_checked}&likes=${likes}&likes_operator=${likes_operator}&views=${views}&views_operator=${views_operator}&comments_count=${comments}&comments_operator=${comments_operator}&limit=${limit}`;
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
                        Articles
                        <span  class="ml-2 badge badge-primary" style="cursor:pointer" onclick="window.location.href=`/cpanel/posts/all`">{{ $count['total'].' Total'}}</span>
                        <span  class="ml-2 badge badge-success" style="cursor:pointer" onclick="window.location.href=`/cpanel/posts/all?is_active=1`">{{$count['active'] .' Active'}}</span>
                        <span  class="ml-2 badge badge-danger" style="cursor:pointer" onclick="window.location.href=`/cpanel/posts/all?is_active=0`">{{ $count['disabled'] .' Disabled'}}</span>
                        <span class="ml-2 badge badge-warning" style="cursor: n-resize;" data-toggle="collapse" data-target="#chart">Show Chart</span>
                        </h1>
                    </div>
                   <div class="col-auto">
                    <a href="allcomment"  class="btn btn-primary lift">
                      Comments
                    </a>
                </div>
                </div>
              </div>
            </div>
    </div>
<div id="chart" class="container collapse">
    
<div class="row">

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

</div>
    <div class="container">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row mt-3">
                            <div class="col-lg-3 col-12">
                                    <div class="form-group">
                                            <select class="form-control show-tick" data-toggle="select" name="searchBy" id="searchBy">
                                                <option value="id" {{$searchBy=='id' ? 'selected':''}}>Search By Post ID</option>
                                                <option value="title" {{$searchBy=='title' ? 'selected':''}}>Search By Post Title</option>
                                                <option value="user_name" {{ $searchBy=='user_name'?'selected':''}}>Search By User Name</option>
                                                <option value="user_id" {{ $searchBy=='user_id'?'selected':''}}>Search By User ID</option>
                                            </select>
                                    </div>
                            </div>

                            <div class="col-lg-5 col-12">
                                <div class="input-group input-group-merge">
                                    <input type="text" placeholder="Search Post ..." name="searchQuery" id="searchQuery" value="{{ $searchQuery }}" class="form-control form-control-prepended search"/>
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
                            <div class="col-lg-2 col-12">
                                    <button class="btn btn-light btn-block clear-btn">RESET SEARCH</button>
                            </div>
                            <!--
                                <div class="col-lg-2 col-12">
                                    <button class="btn btn-light btn-block" id="btnResetSearch">RESET SEARCH</button>
                            </div>
                             -->
                            <div class="col-lg-2 col-12">
                                    <button class="btn btn-light btn-block" data-toggle="collapse" data-target="#filters">APPLY FILTERS</button>
                            </div>
                        </div>
                    </div>
                    
                    <div  id="filters" class="card collapse">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 col-12">
                                    <div class="form-group">
                                            <label>Post Status</label>
                                            <br/>
                                             <div class="custom-control custom-radio custom-control-inline">
                                                    <input name="status" type="radio" id="status_all" value="0,1" class="custom-control-input" {{ $status=='0,1'?'checked':'' }}>
                                                    <label for="status_all"  class="custom-control-label">ALL</label>
                                            </div>

                                            <div class="custom-control custom-radio custom-control-inline">
                                                    <input name="status" type="radio" id="status_published" value="1" class="custom-control-input" {{ $status=='1'?'checked':'' }}>
                                                    <label for="status_published"  class="custom-control-label">PUBLISHED</label>
                                            </div>

                                            <div class="custom-control custom-radio custom-control-inline">
                                                    <input name="status" type="radio" id="status_draft" value="0" class="custom-control-input" {{ $status=='0'?'checked':'' }}>
                                                    <label for="status_draft" class="custom-control-label">DRAFT</label>
                                            </div>
                                    </div>


                                    <div class="form-group">
                                            <label>Post Visibility</label>
                                            <br/>
                                            <div class="custom-control custom-radio custom-control-inline">
                                                    <input name="is_active" type="radio" id="visibility_all" value="0,1" class="custom-control-input" {{ $is_active=='0,1'?'checked':'' }}>
                                                    <label for="visibility_all" class="custom-control-label">ALL</label>
                                            </div>

                                            <div class="custom-control custom-radio custom-control-inline">
                                                    <input name="is_active" type="radio" id="visibility_visible" value="1" class="custom-control-input"  {{ $is_active=='1'?'checked':'' }}>
                                                    <label for="visibility_visible" class="custom-control-label">VISIBLE</label>
                                            </div>

                                            <div class="custom-control custom-radio custom-control-inline">
                                                    <input name="is_active" type="radio" id="visibility_hidden" value="0" class="custom-control-input"  {{ $is_active=='0'?'checked':'' }}>
                                                    <label for="visibility_hidden" class="custom-control-label">HIDDEN</label>
                                            </div>
                                    </div>

                                    <div class="form-group">
                                            <label>Post Plagiarism Status</label>
                                            <br/>
                                            <div class="custom-control custom-radio custom-control-inline">
                                                    <input name="plagiarism_checked" type="radio" id="plagiarism_all" value="0,1" class="custom-control-input"  {{ $plagiarism_checked=='0,1'?'checked':'' }}>
                                                    <label  class="custom-control-label" for="plagiarism_all">ALL</label>
                                            </div>

                                            <div class="custom-control custom-radio custom-control-inline">
                                                    <input name="plagiarism_checked" type="radio" id="plagiarism_checked" value="1" class="custom-control-input" {{ $plagiarism_checked=='1'?'checked':'' }}>
                                                    <label  class="custom-control-label" for="plagiarism_checked">CHECKED</label>
                                            </div>

                                            <div class="custom-control custom-radio custom-control-inline">
                                                    <input name="plagiarism_checked" type="radio" id="plagiarism_pending" value="0" class="custom-control-input" {{  $plagiarism_checked=='0'?'checked':''}}>
                                                    <label  class="custom-control-label" for="plagiarism_pending">PENDING</label>
                                            </div>
                                    </div>

                                    <div class="form-group">
                                            <label>Platform</label>
                                            <br/>
                                            <div class="custom-control custom-radio custom-control-inline">
                                                    <input name="platform" type="radio" id="platform_all" value="website,android" class="custom-control-input"  {{ $platform=='website,android'?'checked':'' }}>
                                                    <label  class="custom-control-label" for="platform_all">ALL</label>
                                            </div>

                                            <div class="custom-control custom-radio custom-control-inline">
                                                    <input name="platform" type="radio" id="platform_website" value="website" class="custom-control-input" {{ $platform=='website'?'checked':'' }}>
                                                    <label  class="custom-control-label" for="platform_website">WEBSITE</label>
                                            </div>

                                            <div class="custom-control custom-radio custom-control-inline">
                                                    <input name="platform" type="radio" id="platform_android" value="android" class="custom-control-input" {{  $platform=='android'?'checked':''}}>
                                                    <label  class="custom-control-label" for="platform_android">ANDROID</label>
                                            </div>
                                    </div>
                            </div>


                            <div class="col-md-3 col-12">
                                <div class="form-group">
                                        <label>Filter By Category</label>
                                        <select class="form-control show-tick" id="category" name="categories[]" data-live-search="true"  dropupAuto="false" data-toggle="select" data-size="20" multiple>
                                                @foreach($categories as $category)
                                                    @if(in_array($category->id,$filter_categories))
                                                        <option value="{{$category->id}}" selected>{{$category->name}}</option>
                                                    @else
                                                        <option value="{{$category->id}}">{{$category->name}}</option>
                                                    @endif
                                                @endforeach
                                        </select>
                                </div>

                                <div class="input-group input-group-merge mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Likes</span>
                                        </div>
                                        <select class="custom-select form-control-appended form-control-prepended" name="likes_operator" id="likes_operator">
                                                <option value=">="  {{ $likes_operator=='>='?'selected':'' }}>&gt;=</option>
                                                <option value="<="  {{ $likes_operator=='<='?'selected':'' }}>&lt;=</option>
                                        </select>
                                        <input type="number" id="likes" name="likes" value="{{ $likes }}" min="0" class="form-control form-control-prepended"/>
                                </div>


                                <div class="input-group input-group-merge mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Views</span>
                                        </div>
                                        <select class="custom-select form-control-appended form-control-prepended"  name="views_operator" id="views_operator">
                                                <option value=">=" {{ $views_operator=='>='?'selected':'' }}>&gt;=</option>
                                                <option value="<=" {{ $views_operator=='<='?'selected':'' }}>&lt;=</option>
                                        </select>
                                        <input type="number" id="views" name="views" value="{{ $views }}" min="0" class="form-control form-control-prepended"/>
                                </div>

                                <div class="input-group input-group-merge mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Comments</span>
                                        </div>
                                        <select class="custom-select form-control-appended form-control-prepended"  name="comments_operator" id="comments_operator">
                                                <option value=">="  {{ $comments_operator=='>='?'selected':'' }}>&gt;=</option>
                                                <option value="<="  {{ $comments_operator=='<='?'selected':'' }}>&lt;=</option>
                                        </select>
                                        <input type="number" id="comments" name="comments_count" value="{{ $comments }}" min="0" class="form-control form-control-prepended"/>
                                </div>

                            </div>


                            <div class="col-md-5 col-12">
                                    <div class="form-group">
                                    <label>From - To</label>
                                    <div id="reportrange" class="form-control">
                                            <i class="fe fe-calendar"></i>&nbsp;
                                            <span></span> <i class="fe fe-chevron-down"></i>
                                    </div>
                                    </div>

                                    <div class="row">
                                            <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label for="sortBy">Sort By</label>
                                                        <select class="form-control show-tick" name="sortBy" id="sortBy">
                                                            <option value="id"  {{  $sortBy=='id'?'selected':''}}>POST ID</option>
                                                            <option value="title"  {{  $sortBy=='title'?'selected':''}}>POST TITLE</option>
                                                            <option value="created_at"  {{  $sortBy=='created_at'?'selected':''}}>CREATED DATE</option>
                                                            <option value="user_id"  {{  $sortBy=='user_id'?'selected':''}}>CREATED BY</option>
                                                            <option value="likes" {{  $sortBy=='likes'?'selected':''}}>LIKES</option>
                                                            <option value="views"  {{  $sortBy=='views'?'selected':''}}>VIEWS</option>
                                                            <option value="comments_count" {{  $sortBy=='comments_count'?'selected':''}}>COMMENTS</option>
                                                        </select>
                                                    </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                        <label for="sortOrder">Sort Order</label>
                                                        <select class="form-control show-tick" name="sortOrder" id="sortOrder">
                                                            <option value="asc" {{  $sortOrder=='asc'?'selected':''}}>ASCENDING</option>
                                                            <option value="desc" {{  $sortOrder=='desc'?'selected':''}}>DESCENDING</option>
                                                        </select>
                                                </div>
                                            </div>
                                    </div>

                                    <input type="hidden" name="page" id="page" value="{{ $posts->currentPage() }}"/>
                                    <input type="hidden" id="from" name="from" value="{{ $from }}"/>
                                    <input type="hidden" id="to" name="to" value="{{ $to }}"/>

                                    <div class="form-group">
                                        <label>Limit</label>
                                            <input type="number" class="form-control" min="0" max="100" name="limit" id="limit" value="{{$limit}}"/>
                                    </div>
                            </div>
                        </div>

                        <button type="button" class="btn btn-success waves-effect" id="apply">APPLY SORT AND FILTERS</button>
                    </div>
                </div>

                    @if(count($posts)>0)
                    <input id="path" type="hidden" value="{{Request::fullUrl()}}" />
                    <input id="totalpage" type="hidden" value="{{ceil($posts->total()/$posts->perPage())}}" />
                    @endif
                    <div class="table-responsive">
                        <table id="posts-table" class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>TITLE</th>
                                    <th>CREATED_BY</th>
                                    <th>CREATED_AT</th>
                                    <th>PLATFORM</th>
                                    <th>STATUS</th>
                                    <th>VISIBILITY</th>
                                    <th>PLAGIARISM CHECKED</th>
                                    <th>MONETISED</th>
                                    <th>LIKES</th>
                                    <th>VIEWS</th>
                                    <th>COMMENTS</th>
                                    <th>EDIT</th>
                                </tr>
                            </thead>
                            <tbody id="append-div">
                               @include('admin.dashboard.post.post_row')
                            </tbody>
                        </table>
                    </div>
                    @include('admin.partials.spinner')
                </div>
            </div>
        </div>
    </div>

    @if(count($posts)>0)
    @include('admin.partials.loadmorescript')
    @endif

@endsection

