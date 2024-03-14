@extends('admin.layouts.app')
@section('title','Opinions')


@push('styles')
<link rel="stylesheet" type="text/css" href="/public_admin/assets/libs/daterangepicker/daterangepicker.css" />
<style>
    .thread_link{
        color: #2c7be5;
        background-color: #d5e5fa;
        display: inline-block;
        padding: .33em .5em;
        font-size: 12px;
        font-weight: 400;
        line-height: 1;
        text-align: center;
        white-space: nowrap;
        vertical-align: baseline;
        border-radius: .375rem;
        transition: color .15s ease-in-out,background-color .15s ease-in-out,border-color .15s ease-in-out,box-shadow .15s ease-in-out;
    }
</style>
@endpush


@push('scripts')

<script src="/js/custom/comments_admin.js?<?php echo time();?>" type="text/javascript"></script>
<script async src="/js/custom/like_admin.js?<?php echo time();?>" type="text/javascript"></script>
<script type="text/javascript" src="/public_admin/assets/libs/momentjs/moment.min.js"></script>
<script type="text/javascript" src="/public_admin/assets/libs/daterangepicker/daterangepicker.min.js"></script>
<script type="text/javascript" src="/public_admin/assets/libs/sweetalert2/sweetalert2.min.js"></script>
<script async src="/js/custom/admin_opinion_delete.js?<?php echo time();?>" type="text/javascript"></script>

<script>
        $(document).ready(function(){

            $('.confirmation').on('click', function () {
                    return confirm('Are you sure?');
                });


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
                var sortBy=$('#sortBy').val();
                var sortOrder=$('#sortOrder').val();
                var likes_count=$('#likes').val();
                var likes_operator=$('#likes_operator').val();
                var comments=$('#comments').val();
                var comments_operator=$('#comments_operator').val();
                var platform=$('input[type=radio][name=platform]:checked').val();
                var is_active=$('input[type=radio][name=is_active]:checked').val();
                var limit=parseInt($('#limit').val());
                var page=parseInt($('#page').val());

                var URL=`/cpanel/opinion/all?page=${page}&searchBy=${searchBy}&searchQuery=${searchQuery}&sortBy=${sortBy}&sortOrder=${sortOrder}&from=${from}&to=${to}&platform=${platform}&is_active=${is_active}&likes_count=${likes_count}&likes_operator=${likes_operator}&comments_count=${comments}&comments_operator=${comments_operator}&limit=${limit}`;
                window.location.href=URL;
        });

        $(document).on('click','.btn-opinion',function(){
            var opinionID=$(this).attr('data-id');
            var action=$(this).attr('data-action');
            var event=$(this).attr('data-event');

            if(event=='enable'){
                $('#opinion_form').attr('action',action)
                $('#opinion_id').val(opinionID);
                $('#opinion_form').submit();
            }
            if(event=='disable'){
                $('#opinion_form').attr('action',action)
                $('#opinion_id').val(opinionID);
                $('#opinion_form').submit();
            }
            if(event=='delete'){
                $('#opinion_form').attr('action',action)
                $('#opinion_id').val(opinionID);
                $('#opinion_form').submit();
            }
        });
        $(document).on('click', '.btn-opinion-likes', function() {
            
            var opinion_id = parseInt($(this).attr('data-opinion'));

            $('.opinion_likes').empty();
            $('#likesOpinionModal').modal('show');
            $('.loader').css('display', 'block');
            $.ajax({
                url: '/cpanel/opinion/opinion_likes',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                type: 'POST',
                data: { opinion_id: opinion_id },
                dataType: 'text',
                success: function(response) {
                    $('.loader').css('display', 'none');
                    $('.opinion_likes').append(response);
                },
                error: function() {
                    $('.loader').css('display', 'none');
                }
            });

    
        
        });
        $(document).on('click', '.btn-opinion-shares', function() {
            
            var opinion_id = parseInt($(this).attr('data-opinion'));

            $('.opinion_shares').empty();
            $('#sharesOpinionModal').modal('show');
            $('.loader').css('display', 'block');
            $.ajax({
                url: '/cpanel/opinion/opinion_shares',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                type: 'POST',
                data: { opinion_id: opinion_id },
                dataType: 'text',
                success: function(response) {
                    $('.loader').css('display', 'none');
                    $('.opinion_shares').append(response);
                },
                error: function() {
                    $('.loader').css('display', 'none');
                }
            });

    
        
        });

</script>
@endpush


@section('content')

<a href="#" id="scroll" style="display: none; z-index: 100"><span></span></a>
<div class="header">
        <div class="container">
          <div class="header-body">
            <div class="row align-items-end">
                <div class="col">
                    <h6 class="header-pretitle">
                    Overview
                    </h6>
                    <h1 class="header-title">
                    Opinions
                    <span  class="ml-2 badge badge-primary" style="cursor:pointer" onclick="window.location.href=`/cpanel/opinion/all`">{{ $count['total'].' Total'}}</span>
                    <span  class="ml-2 badge badge-success" style="cursor:pointer" onclick="window.location.href=`/cpanel/opinion/all?is_active=1`">{{$count['active'] .' Active'}}</span>
                    <span  class="ml-2 badge badge-danger" style="cursor:pointer" onclick="window.location.href=`/cpanel/opinion/desable`">{{ $count['disabled'] .' Disabled'}}</span>
                    </h1>
                </div>
               <div class="col-auto">
                <a class="btn btn-small btn-primary" href="{{ route('admin.opinions.lockdown_offer') }}">Contest</a>
                <a class="btn btn-dark" href="{{ route('admin.opinions.trending') }}">Trending Opinions</a>
                <a class="btn btn-warning" href="{{ route('admin.opinions.updated') }}">Updated Opinions</a>
                <a class="btn btn-light" href="{{ route('admin.write.opinion') }}">Write Opinion</a>
               </div>
            </div>
          </div>
        </div>
</div>
@include('admin.dashboard.opinion.modals.modal_opinion_likes')
@include('admin.dashboard.opinion.modals.modal_opinion_shares')
<div class="container">
        @if(count($opinions)>0)
        <input id="path" type="hidden" value="{{Request::fullUrl()}}" />
        <input id="totalpage" type="hidden" value="{{ceil($opinions->total()/$opinions->perPage())}}" />
        @endif
        <form method="POST" action="" id="opinion_form" style="display:none">
                <input type="hidden" name="opinion_id" value="" id="opinion_id"/>
                {{ csrf_field() }}
        </form>


        <div class="card">
                <div class="card-header">
                    <div class="row mt-3">
                        <div class="col-lg-3 col-12">
                                <div class="form-group">
                                        <select class="form-control show-tick" data-toggle="select" name="searchBy" id="searchBy">
                                            <option value="id" {{$searchBy=='id' ? 'selected':''}}>Search By ID</option>
                                            <option value="thread_name" {{$searchBy=='thread_name' ? 'selected':''}}>Search By Thread</option>
                                            <option value="thread_id" {{$searchBy=='thread_id' ? 'selected':''}}>Search By Thread ID</option>
                                            <option value="user_name" {{ $searchBy=='user_name'?'selected':''}}>Search By User Name</option>
                                            <option value="user_id" {{ $searchBy=='user_id'?'selected':''}}>Search By User ID</option>
                                        </select>
                                </div>
                        </div>

                        <div class="col-lg-6 col-12">
                            <div class="input-group input-group-merge">
                                <input type="text" placeholder="Search Opinion ..." name="searchQuery" id="searchQuery" value="{{ $searchQuery }}" class="form-control form-control-prepended search"/>
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
                        <div class="col-lg-3 col-12">
                                <button class="btn btn-light btn-block" id="btnResetSearch">RESET SEARCH</button>
                        </div>
                    </div>
                </div>
        </div>


    <div class="row">

        <div class="col-md-8 col-12" id="append-div">
            @include('admin.dashboard.opinion.opinion')
            @include('admin.partials.spinner')
        </div>

        <div class="col-md-4 col-12">
                <div class="card">
                    <div class="card-body">
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

                                    <div class="form-group">
                                        <label>From - To</label>
                                        <div id="reportrange" class="form-control">
                                                <i class="fe fe-calendar"></i>&nbsp;
                                                <span></span> <i class="fe fe-chevron-down"></i>
                                        </div>
                                        <input type="hidden" id="from" name="from" value="{{ $from }}"/>
                                        <input type="hidden" id="to" name="to" value="{{ $to }}"/>
                                        <input type="hidden" name="page" id="page" value="{{ $opinions->currentPage() }}"/>
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
                                                <span class="input-group-text">Comments</span>
                                            </div>
                                            <select class="custom-select form-control-appended form-control-prepended"  name="comments_operator" id="comments_operator">
                                                    <option value=">="  {{ $comments_operator=='>='?'selected':'' }}>&gt;=</option>
                                                    <option value="<="  {{ $comments_operator=='<='?'selected':'' }}>&lt;=</option>
                                            </select>
                                            <input type="number" id="comments" name="comments_count" value="{{ $comments }}" min="0" class="form-control form-control-prepended"/>
                                    </div>


                                    <div class="row">
                                            <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label for="sortBy">Sort By</label>
                                                        <select class="form-control show-tick" name="sortBy" id="sortBy">
                                                            <option value="id"  {{  $sortBy=='id'?'selected':''}}>ID</option>
                                                            <option value="likes_count"  {{  $sortBy=='likes_count'?'selected':''}}>LIKES</option>
                                                            <option value="comments_count"  {{  $sortBy=='comments_count'?'selected':''}}>COMMENTS</option>
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
                                            <input class="form-control" type="number" min="24" name="limit" id="limit" value="{{$limit}}"/>
                                    </div>

                                    </div>


                                    <button type="button" class="btn btn-success" id="apply">APPLY SORT AND FILTERS</button>
                    </div>
                </div>
            </div>
    </div>

</div>


<div class="row">
    <div class="col align-self-center">
       {{ $opinions->links('frontend.posts.components.pagination') }}  
    </div>
</div>
<!--
@if(count($opinions)>0)
@include('admin.partials.loadmorescript')
@endif
-->
@include('admin.dashboard.opinion.comments.add_comment_modal')

@endsection
