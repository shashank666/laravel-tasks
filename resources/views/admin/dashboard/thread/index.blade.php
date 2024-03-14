@extends('admin.layouts.app')
@section('title','Threads')

@push('styles')
<link rel="stylesheet" type="text/css" href="/public_admin/assets/libs/daterangepicker/daterangepicker.css" />
@endpush


@push('scripts')
<script type="text/javascript" src="/public_admin/assets/libs/momentjs/moment.min.js"></script>
<script type="text/javascript" src="/public_admin/assets/libs/daterangepicker/daterangepicker.min.js"></script>
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
                var categories=$('#category').val();
                var sortBy=$('#sortBy').val();
                var sortOrder=$('#sortOrder').val();
                var is_active=$('input[type=radio][name=is_active]:checked').val();
                var limit=parseInt($('#limit').val());
                var page=parseInt($('#page').val() || 0);

                var likes_count=$('#likes_count').val();
                var likes_operator=$('#likes_operator').val();
                var followers_count=$('#followers_count').val();
                var followers_operator=$('#followers_operator').val();
                var posts_count=$('#posts_count').val();
                var posts_operator=$('#posts_operator').val();
                var opinions_count=$('#opinions_count').val();
                var opinions_operator=$('#opinions_operator').val();

                var URL=`/cpanel/thread/all?page=${page}&categories=${categories}&searchBy=${searchBy}&searchQuery=${searchQuery}&sortBy=${sortBy}&sortOrder=${sortOrder}&from=${from}&to=${to}&is_active=${is_active}&likes_count=${likes_count}&likes_operator=${likes_operator}&followers_count=${followers_count}&followers_operator=${followers_operator}&posts_count=${posts_count}&posts_operator=${posts_operator}&opinions_count=${opinions_count}&opinions_operator=${opinions_operator}&limit=${limit}`;
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
                    Threads
                    <span  class="ml-2 badge badge-primary">{{ $count['total'].' Total'}}</span>
                    <span  class="ml-2 badge badge-success">{{$count['active'] .' Active'}}</span>
                    <span  class="ml-2 badge badge-danger">{{ $count['disabled'] .' Disabled'}}</span>
                    </h1>
                </div>
               <div class="col-auto">
                   <a href="{{route('admin.add_thread')}}" class="btn btn-primary">CREATE NEW THREAD</a>
               </div>
            </div>
          </div>
        </div>
</div>
<div class="container">
    <!--{{--<div class="row">
        <div class="col-lg-12">
            <div id="filters" class="card collapse">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-4">
                                <div class="form-group">
                                        <label>Status</label>
                                        <br/>
                                        <div class="custom-control custom-radio custom-control-inline">
                                                <input name="is_active" type="radio" id="visibility_all" value="0,1" class="custom-control-input" {{ $is_active=='0,1'?'checked':'' }}>
                                                <label for="visibility_all"  class="custom-control-label">ALL</label>
                                        </div>

                                        <div class="custom-control custom-radio custom-control-inline">
                                                <input name="is_active" type="radio" id="visibility_visible" value="1" class="custom-control-input"  {{ $is_active=='1'?'checked':'' }}>
                                                <label for="visibility_visible"  class="custom-control-label">VISIBLE</label>
                                        </div>

                                        <div class="custom-control custom-radio custom-control-inline">
                                                <input name="is_active" type="radio" id="visibility_hidden" value="0" class="custom-control-input"  {{ $is_active=='0'?'checked':'' }}>
                                                <label for="visibility_hidden"  class="custom-control-label">HIDDEN</label>
                                        </div>
                                </div>

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
                        </div>
                        <div class="col-lg-4">
                                <div class="input-group input-group-merge mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Likes</span>
                                        </div>
                                        <select class="custom-select form-control-appended form-control-prepended" name="likes_operator" id="likes_operator">
                                                <option value=">="  {{ $likes_operator=='>='?'selected':'' }}>&gt;=</option>
                                                <option value="<="  {{ $likes_operator=='<='?'selected':'' }}>&lt;=</option>
                                        </select>
                                        <input type="number" id="likes_count" name="likes_count" value="{{ $likes_count }}" min="0" class="form-control form-control-prepended"/>
                                </div>


                                <div class="input-group input-group-merge mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Followers</span>
                                        </div>
                                        <select class="custom-select form-control-appended form-control-prepended"  name="followers_operator" id="followers_operator">
                                                <option value=">=" {{ $followers_operator=='>='?'selected':'' }}>&gt;=</option>
                                                <option value="<=" {{ $followers_operator=='<='?'selected':'' }}>&lt;=</option>
                                        </select>
                                        <input type="number" id="followers_count" name="followers_count" value="{{ $followers_count }}" min="0" class="form-control form-control-prepended"/>
                                </div>

                                <div class="input-group input-group-merge mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Posts</span>
                                        </div>
                                        <select class="custom-select form-control-appended form-control-prepended"  name="posts_operator" id="posts_operator">
                                                <option value=">="  {{ $posts_operator=='>='?'selected':'' }}>&gt;=</option>
                                                <option value="<="  {{ $posts_operator=='<='?'selected':'' }}>&lt;=</option>
                                        </select>
                                        <input type="number" id="posts_count" name="posts_count" value="{{ $posts_count }}" min="0" class="form-control form-control-prepended"/>
                                </div>

                                <div class="input-group input-group-merge mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Opinions</span>
                                        </div>
                                        <select class="custom-select form-control-appended form-control-prepended"  name="opinions_operator" id="opinions_operator">
                                                <option value=">="  {{ $opinions_operator=='>='?'selected':'' }}>&gt;=</option>
                                                <option value="<="  {{ $opinions_operator=='<='?'selected':'' }}>&lt;=</option>
                                        </select>
                                        <input type="number" id="opinions_count" name="opinions_count" value="{{ $opinions_count }}" min="0" class="form-control form-control-prepended"/>
                                </div>



                        </div>

                        <div class="col-lg-4">

                                <div class="form-group">
                                        <label>From - To</label>
                                        <div id="reportrange" class="form-control">
                                                <i class="fe fe-calendar"></i>&nbsp;
                                                <span></span> <i class="fe fe-chevron-down"></i>
                                        </div>
                                        <input type="hidden" id="from" name="from" value="{{ $from }}"/>
                                        <input type="hidden" id="to" name="to" value="{{ $to }}"/>
                                        <input type="hidden" name="page" id="page" value="{{ $threads->currentPage() }}"/>
                                </div>

                                <div class="row">
                                        <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label for="sortBy">Sort By</label>
                                                    <select class="form-control show-tick" name="sortBy" id="sortBy">
                                                        <option value="id"  {{  $sortBy=='id'?'selected':''}}>ID</option>
                                                        <option value="name"  {{  $sortBy=='title'?'selected':''}}>ALPHABETICALLY</option>
                                                        <option value="posts_count" {{  $sortBy=='posts_count'?'selected':''}}>POSTS</option>
                                                        <option value="opinions_count"  {{  $sortBy=='opinions_count'?'selected':''}}>OPINIONS</option>
                                                        <option value="likes_count"  {{  $sortBy=='likes_count'?'selected':''}}>LIKES</option>
                                                        <option value="followers_count"  {{  $sortBy=='followers_count'?'selected':''}}>FOLLOWERS</option>
                                                        <option value="created_at"  {{  $sortBy=='created_at'?'selected':''}}>CREATED DATE</option>
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

                                <div class="form-group">
                                        <label>Limit</label>
                                        <input type="number" class="form-control" min="1"  name="limit" id="limit" value="{{$limit}}"/>
                                </div>
                        </div>
                    </div>



                    <button type="button" class="btn btn-success" id="apply">APPLY SORT AND FILTERS</button>

                </div>
            </div>
        </div>
    </div>
--}}-->
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row mt-3">
                            <div class="col-lg-3">
                                    <div class="form-group">
                                            <select class="form-control show-tick" data-toggle="select" name="searchBy" id="searchBy">
                                                <option value="id" {{$searchBy=='id' ? 'selected':''}}>Search By ID</option>
                                                <option value="name" {{$searchBy=='name' ? 'selected':''}}>Search By Name</option>
                                            </select>
                                    </div>
                            </div>

                        <div class="col-lg-5">
                            <div class="input-group input-group-merge">
                                    <input type="text"  class="form-control form-control-prepended search" name="searchQuery" id="searchQuery" value="{{ $searchQuery }}" placeholder="Search Thread ..."/>
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
                        <div class="col-lg-2">
                            <button class="btn btn-light btn-block" id="btnResetSearch">RESET SEARCH</button>
                        </div>
                        <div class="col-lg-2">
                                    <button class="btn btn-light btn-block" data-toggle="collapse" data-target="#filters">APPLY FILTERS</button>
                            </div>
                    </div>
                </div>
                <div id="filters" class="card collapse">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-4">
                                <div class="form-group">
                                        <label>Status</label>
                                        <br/>
                                        <div class="custom-control custom-radio custom-control-inline">
                                                <input name="is_active" type="radio" id="visibility_all" value="0,1" class="custom-control-input" {{ $is_active=='0,1'?'checked':'' }}>
                                                <label for="visibility_all"  class="custom-control-label">ALL</label>
                                        </div>

                                        <div class="custom-control custom-radio custom-control-inline">
                                                <input name="is_active" type="radio" id="visibility_visible" value="1" class="custom-control-input"  {{ $is_active=='1'?'checked':'' }}>
                                                <label for="visibility_visible"  class="custom-control-label">VISIBLE</label>
                                        </div>

                                        <div class="custom-control custom-radio custom-control-inline">
                                                <input name="is_active" type="radio" id="visibility_hidden" value="0" class="custom-control-input"  {{ $is_active=='0'?'checked':'' }}>
                                                <label for="visibility_hidden"  class="custom-control-label">HIDDEN</label>
                                        </div>
                                </div>

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
                        </div>
                        <div class="col-lg-4">
                                <div class="input-group input-group-merge mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Likes</span>
                                        </div>
                                        <select class="custom-select form-control-appended form-control-prepended" name="likes_operator" id="likes_operator">
                                                <option value=">="  {{ $likes_operator=='>='?'selected':'' }}>&gt;=</option>
                                                <option value="<="  {{ $likes_operator=='<='?'selected':'' }}>&lt;=</option>
                                        </select>
                                        <input type="number" id="likes_count" name="likes_count" value="{{ $likes_count }}" min="0" class="form-control form-control-prepended"/>
                                </div>


                                <div class="input-group input-group-merge mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Followers</span>
                                        </div>
                                        <select class="custom-select form-control-appended form-control-prepended"  name="followers_operator" id="followers_operator">
                                                <option value=">=" {{ $followers_operator=='>='?'selected':'' }}>&gt;=</option>
                                                <option value="<=" {{ $followers_operator=='<='?'selected':'' }}>&lt;=</option>
                                        </select>
                                        <input type="number" id="followers_count" name="followers_count" value="{{ $followers_count }}" min="0" class="form-control form-control-prepended"/>
                                </div>

                                <div class="input-group input-group-merge mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Posts</span>
                                        </div>
                                        <select class="custom-select form-control-appended form-control-prepended"  name="posts_operator" id="posts_operator">
                                                <option value=">="  {{ $posts_operator=='>='?'selected':'' }}>&gt;=</option>
                                                <option value="<="  {{ $posts_operator=='<='?'selected':'' }}>&lt;=</option>
                                        </select>
                                        <input type="number" id="posts_count" name="posts_count" value="{{ $posts_count }}" min="0" class="form-control form-control-prepended"/>
                                </div>

                                <div class="input-group input-group-merge mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Opinions</span>
                                        </div>
                                        <select class="custom-select form-control-appended form-control-prepended"  name="opinions_operator" id="opinions_operator">
                                                <option value=">="  {{ $opinions_operator=='>='?'selected':'' }}>&gt;=</option>
                                                <option value="<="  {{ $opinions_operator=='<='?'selected':'' }}>&lt;=</option>
                                        </select>
                                        <input type="number" id="opinions_count" name="opinions_count" value="{{ $opinions_count }}" min="0" class="form-control form-control-prepended"/>
                                </div>



                        </div>

                        <div class="col-lg-4">

                                <div class="form-group">
                                        <label>From - To</label>
                                        <div id="reportrange" class="form-control">
                                                <i class="fe fe-calendar"></i>&nbsp;
                                                <span></span> <i class="fe fe-chevron-down"></i>
                                        </div>
                                        <input type="hidden" id="from" name="from" value="{{ $from }}"/>
                                        <input type="hidden" id="to" name="to" value="{{ $to }}"/>
                                        <input type="hidden" name="page" id="page" value="{{ $threads->currentPage() }}"/>
                                </div>

                                <div class="row">
                                        <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label for="sortBy">Sort By</label>
                                                    <select class="form-control show-tick" name="sortBy" id="sortBy">
                                                        <option value="id"  {{  $sortBy=='id'?'selected':''}}>ID</option>
                                                        <option value="name"  {{  $sortBy=='title'?'selected':''}}>ALPHABETICALLY</option>
                                                        <option value="posts_count" {{  $sortBy=='posts_count'?'selected':''}}>POSTS</option>
                                                        <option value="opinions_count"  {{  $sortBy=='opinions_count'?'selected':''}}>OPINIONS</option>
                                                        <option value="likes_count"  {{  $sortBy=='likes_count'?'selected':''}}>LIKES</option>
                                                        <option value="followers_count"  {{  $sortBy=='followers_count'?'selected':''}}>FOLLOWERS</option>
                                                        <option value="created_at"  {{  $sortBy=='created_at'?'selected':''}}>CREATED DATE</option>
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

                                <div class="form-group">
                                        <label>Limit</label>
                                        <input type="number" class="form-control" min="1"  name="limit" id="limit" value="{{$limit}}"/>
                                </div>
                        </div>
                    </div>



                    <button type="button" class="btn btn-success" id="apply">APPLY SORT AND FILTERS</button>

                </div>
            </div>
        

                @if(count($threads)>0)
                <input id="path" type="hidden" value="{{Request::fullUrl()}}" />
                <input id="totalpage" type="hidden" value="{{ceil($threads->total()/$threads->perPage())}}" />
                @endif

                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>THREAD</th>
                                <th>CATEGORIES</th>
                                <th>POSTS</th>
                                <th>OPINIONS</th>
                                <th>LIKES</th>
                                <th>FOLLOWERS</th>
                                <th>STATUS</th>
                                <th>CREATED DATE</th>
                                <th>EDIT</th>
                            </tr>
                        </thead>
                        <tbody id="append-div">


                        @include('admin.dashboard.thread.thread_row')
                        </tbody>
                    </table>
                </div>
                @include('admin.partials.spinner')
            </div>
        </div>
    </div>
</div>

@if(count($threads)>0)
@include('admin.partials.loadmorescript')
@endif

@endsection
