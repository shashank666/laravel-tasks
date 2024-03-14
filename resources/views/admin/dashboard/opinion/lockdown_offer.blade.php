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
                <a class="btn btn-dark" href="{{ route('admin.opinions') }}">Latest Opinions</a>
                <a class="btn btn-light" href="{{ route('admin.write.opinion') }}">Write Opinion</a>
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
                @if(count($opinions)>0)
                    <input id="path" type="hidden" value="{{Request::fullUrl()}}" />
                    <input id="totalpage" type="hidden" value="{{ceil($opinions->total()/$opinions->perPage())}}" />
                   
                    @endif
                    <div class="table-responsive">
                        <table id="posts-table" class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>User</th>
                                    <th>Opinion</th>
                                    <th>Likes</th>
                                    <th>Created at</th>
                                    <!--<th>IP</th>-->
                                    </tr>
                            </thead>
                            <tbody id="append-div">
                               @include('admin.dashboard.opinion.lockdown_offer_row')
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
       {{ $opinions->links('frontend.posts.components.pagination') }}  
    </div>
</div>

@endsection
