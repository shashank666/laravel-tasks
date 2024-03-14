@extends('admin.layouts.app')
@section('title','Article Comments')

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
<script type="text/javascript" src="/public_admin/assets/libs/sweetalert2/sweetalert2.min.js"></script>

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

           var URL=`/cpanel/posts/all?page=${page}&categories=${categories}&sortBy=${sortBy}&sortOrder=${sortOrder}&searchBy=${searchBy}&searchQuery=${searchQuery}&from=${from}&to=${to}&platform=${platform}&status=${status}&is_active=${is_active}&plagiarism_checked=${plagiarism_checked}&likes=${likes}&likes_operator=${likes_operator}&views=${views}&views_operator=${views_operator}&comments_count=${comments}&comments_operator=${comments_operator}&limit=${limit}`;
            window.location.href=URL;
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
                        Article Comments
                        
                        </h1>
                    </div>
                   <div class="col-auto">
                    <a href="all"  class="btn btn-primary lift">
                      Articles
                    </a>
                </div>
                </div>
              </div>
            </div>
    </div>

  <div class="container">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-12">
              <div class="card">
    			@if(count($comments)>0)
                    <input id="path" type="hidden" value="{{Request::fullUrl()}}" />
                    <input id="totalpage" type="hidden" value="{{ceil($comments->total()/$comments->perPage())}}" />
                   
                    @endif
                    <div class="table-responsive">
                        <table id="posts-table" class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>COMMENT</th>
                                    <th>BY</th>
                                    <th>ON ARTICLE</th>
                                    <th>COMMENTED AT</th>
                                    <th>STATUS</th>
                                   	<th>DESABLE
                                   	/ENABLE</th>
                                    <th>DELETE</th>
                                </tr>
                            </thead>
                            <tbody id="append-div">
                               
                               @include('admin.dashboard.post.components.comments_row')
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
       {{ $comments->links('frontend.posts.components.pagination') }}  
    </div>
</div>

@endsection

