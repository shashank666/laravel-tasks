@extends('admin.layouts.app')

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

                    $(document).on('click','#apply',function(){
                        var from=$('#from').val();
                        var to=$('#to').val();
                        var event=$('#event').val();
                        var extension=$('#extension').val();
                        var searchBy=$('#searchBy').val();
                        var searchQuery=$('#searchQuery').val();
                        var sortBy=$('#sortBy').val();
                        var sortOrder=$('#sortOrder').val();
                        var limit=parseInt($('#limit').val());
                        var page=parseInt($('#page').val());

                        var URL=`/cpanel/filemanager?page=${page}&sortBy=${sortBy}&sortOrder=${sortOrder}&from=${from}&to=${to}&event=${event}&extension=${extension}&limit=${limit}`;
                        window.location.href=URL;
                    });
                });

                $(document).on('click','table tr',function(){
                    let id=$(this).attr('id');
                    let filePath=$(this).attr('data-path');
                    let fileInUse=$(this).attr('data-inuse');
                    let fileSize=$(this).attr('data-size');
                    let fileName=$(this).attr('data-filename');
                    let fileExtension=$(this).attr('data-extension').toLowerCase();
                    let modalTitle=fileName+'<span class="ml-2 badge badge-success">'+fileSize+'</span>';
                    $('#filePreviewModalLabel').html(modalTitle);
                    $('#file_id').val(id);
                    if(fileInUse=='1'){
                        $('#btnDeleteFile').css('display','none');
                    }else{
                        $('#btnDeleteFile').css('display','block');
                    }
                    $('.preview').empty();
                    let ImageExtensions=['jpg','png','gif','jpeg'];
                    if(ImageExtensions.indexOf(fileExtension)>-1){
                        $('.preview').append(
                            '<img src="'+filePath+'" class="img-fluid" height="" width=""/>'
                        );
                    }else{
                        let url='https://weopined.com/opinion/stream/video/'+fileName;
                        var video = $('<video id="videoPreview" controls autoplay style="max-width:400px;max-height:500px"><source src="'+url+'" type="video/mp4"></video>');
                        $('.preview').append(video);
                    }
                    $('#filePreviewModal').modal('show');
                });

                $(document).on('hidden.bs.modal','#filePreviewModal', function () {
                    $('.preview').empty();
                })
        </script>
@endpush

@section('content')
    <div class="header">
            <div class="container">
            <div class="header-body">
                <div class="row align-items-center">
                    <div class="col">
                        <h1 class="header-title">
                         File Manager
                         <span class="badge badge-primary mx-2">{{ $total_files.' Files' }}</span>
                         <span class="badge badge-success">{{ $total_storage }}</span>

                        </h1>
                    </div>

                </div>
            </div>
    </div>
    <div class="container my-5">
        <div class="row">
            @foreach($all_dirs as $dir)
            <div class="col-md-3">
                    <div class="card">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h6 class="card-title text-uppercase text-muted mb-2">
                                            {{ $dir['dir_name']}}
                                        </h6>
                                        <span class="h2 mb-0">
                                            {{ $dir['dir_size'] }}
                                        </span>
                                    </div>
                                    <div class="col-auto">
                                        {{ $dir['dir_count'] }}
                                    </div>
                                </div>
                            </div>
                    </div>
            </div>
            @endforeach
        </div>
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-12">

                    <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3 col-12">
                                        <div class="form-group">
                                                <label>Filter By Event</label>
                                                <select class="form-control show-tick" id="event" name="event" data-live-search="true"  dropupAuto="false" data-toggle="select">
                                                        <option value="all"  {{  $event=='all'?'selected':''}}>ALL EVENTS</option>
                                                        <option value="USER_PROFILE"  {{  $event=='USER_PROFILE'?'selected':''}}>USER_PROFILE</option>
                                                        <option value="CATEGORY_IMAGE"  {{  $event=='CATEGORY_IMAGE'?'selected':''}}>CATEGORY_IMAGE</option>
                                                        <option value="POST_COVER"  {{  $event=='POST_COVER'?'selected':''}}>POST_COVER</option>
                                                        <option value="BLOG_POST"  {{  $event=='BLOG_POST'?'selected':''}}>BLOG_POST</option>
                                                        <option value="POST_COMMENT"  {{  $event=='POST_COMMENT'?'selected':''}}>POST_COMMENT</option>
                                                        <option value="OPINION_COMMENT"  {{  $event=='OPINION_COMMENT'?'selected':''}}>OPINION_COMMENT</option>
                                                        <option value="OPINION_COVER_IMAGE"  {{  $event=='OPINION_COVER_IMAGE'?'selected':''}}>OPINION_COVER_IMAGE</option>
                                                        <option value="OPINION_COVER_VIDEO"  {{  $event=='OPINION_COVER_VIDEO'?'selected':''}}>OPINION_COVER_VIDEO</option>
                                                </select>
                                        </div>
                                        <div class="form-group">
                                                <label>Filter By File Extension</label>
                                                <select class="form-control show-tick" id="extension" name="extension" data-live-search="true"  dropupAuto="false" data-toggle="select">
                                                        <option value="all"  {{  $extension=='all'?'selected':''}}>ALL EXTENSIONS</option>
                                                        <option value="JPG"  {{  $extension=='JPG'?'selected':''}}>JPG</option>
                                                        <option value="JPEG"  {{  $extension=='JPEG'?'selected':''}}>JPEG</option>
                                                        <option value="GIF"  {{  $extension=='GIF'?'selected':''}}>GIF</option>
                                                        <option value="PNG"  {{  $extension=='PNG'?'selected':''}}>PNG</option>
                                                        <option value="MP4"  {{  $extension=='MP4'?'selected':''}}>MP4</option>
                                                        <option value="WEBM"  {{  $extension=='WEBM'?'selected':''}}>WEBM</option>
                                                </select>
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
                                                                    <option value="id"  {{  $sortBy=='id'?'selected':''}}>FILE ID</option>
                                                                    <option value="created_at"  {{  $sortBy=='created_at'?'selected':''}}>UPLOAD DATE</option>
                                                                    <option value="size"  {{  $sortBy=='size'?'selected':''}}>FILE SIZE</option>
                                                                    <option value="extension" {{  $sortBy=='extension'?'selected':''}}>EXTENSION</option>
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

                                            <input type="hidden" name="page" id="page" value="{{ $db_entries->currentPage() }}"/>
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

                    <div class="card">
                        <div class="card-header">
                            <h2 class="card-header-title">
                                Datebase Entries Of Uploaded Files
                            </h2>
                        </div>
                        <div class="card-body">

                            @if(count($db_entries)>0)
                            <input id="path" type="hidden" value="{{Request::fullUrl()}}" />
                            <input id="totalpage" type="hidden" value="{{ceil($db_entries->total()/$db_entries->perPage())}}" />
                            @endif
                            <div class="table-responsive">
                                <table id="files-table" class="table">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>EXTENSION</th>
                                            <th>PREVIEW</th>
                                            <th>EVENT</th>
                                            <th>FILE SIZE</th>
                                            <th>FILE INUSE</th>
                                            <th>UPLOADED_BY</th>
                                            <th>UPLOADED_AT</th>
                                            <th>FILE PATH</th>
                                        </tr>
                                    </thead>
                                    <tbody id="append-div">
                                    @include('admin.dashboard.filemanager.components.file_row')
                                    </tbody>
                                </table>
                            </div>
                            @include('admin.partials.spinner')
                        </div>
                    </div>
            </div>
        </div>
    </div>

    @include('admin.dashboard.filemanager.components.file_preview_modal')

    @if(count($db_entries)>0)
    @include('admin.partials.loadmorescript')
    @endif
@endsection
