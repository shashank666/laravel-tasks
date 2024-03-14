@extends('admin.layouts.app')
@section('title','Polls')

@push('scripts')
<script src="/public_admin/assets/libs/list.js/dist/list.min.js"></script>
<script >
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

                var URL=`/cpanel/poll/all?page=${page}&searchBy=${searchBy}&searchQuery=${searchQuery}`;
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
                    Polls
                    <span  class="ml-2 badge badge-primary" style="cursor:pointer" onclick="window.location.href=`/cpanel/poll/all`">{{ $polls_count['total'].' Total Poll'}}</span>
                    <span  class="ml-2 badge badge-success" style="cursor:pointer" onclick="window.location.href=`/cpanel/poll/all?visibility=1`">{{$polls_count['active'] .' Active'}}</span>
                    <span  class="ml-2 badge badge-warning" style="cursor:pointer" onclick="window.location.href=`/cpanel/poll/all?visibility=0`">{{ $polls_count['disabled'] .' Paused'}}</span>
                    <span  class="ml-2 badge badge-secondary" style="cursor:pointer" onclick="window.location.href=`/cpanel/poll/votes`">{{$polls_vote_count['total'] .' Votes'}}</span>
                    </h1>
                </div>
               <div class="col-auto">
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
    @if(count($polls)>0)
        <input id="path" type="hidden" value="{{Request::fullUrl()}}" />
        <input id="totalpage" type="hidden" value="{{ceil($polls->total()/$polls->perPage())}}" />
        @endif
        <form method="POST" action="" id="poll_form" style="display:none">
                <input type="hidden" name="poll_id" value="" id="poll_id"/>
                {{ csrf_field() }}
        </form>
    <div class="card">
                <div class="card-header">
                    <div class="row mt-3">
                        <div class="col-lg-3 col-12">
                                <div class="form-group">
                                        <select class="form-control show-tick" data-toggle="select" name="searchBy" id="searchBy">
                                            <option value="id" {{$searchBy=='id' ? 'selected':''}}>Search By ID</option>
                                            {{--<option value="user_name" {{ $searchBy=='user_name'?'selected':''}}>Search By User Name</option>
                                            <option value="user_id" {{ $searchBy=='user_id'?'selected':''}}>Search By User ID</option>--}}
                                        </select>
                                </div>
                        </div>

                        <div class="col-lg-5 col-12">
                            <div class="input-group input-group-merge">
                                <input type="text" placeholder="Search Opinion ..." name="searchQuery" id="searchQuery" value="{{ $searchQuery }}" class="form-control form-control-prepended search"/>
                                <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <span class="fe fe-search"></span>
                                        </div>
                                    </div>
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <button type="button" class="btn btn-success" id="apply">APPLY SEARCH</button>
                        </div>
                            <div class="col-lg-2">
                            <button class="btn btn-light btn-block" id="btnResetSearch">RESET</button>
                                
                        </div>

                    </div>
                </div>
        </div>

        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-12">
              <div class="card">
                @if(count($polls)>0)
                    <input id="path" type="hidden" value="{{Request::fullUrl()}}" />
                    <input id="totalpage" type="hidden" value="{{ceil($polls->total()/$polls->perPage())}}" />
                   
                    @endif
                    <div class="table-responsive">
                        <table id="posts-table" class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>User</th>
                                    <th>Title</th>
                                    <th>Description</th>
                                    <th>Type</th>
                                    <th>Total Votes</th>
                                    <th>Operation</th>
                                    <th>Status</th>
                                    <th>Show Result</th>
                                    <!--<th>Location</th>-->
                                    <th>Created at</th>
                                    
                                    </tr>
                            </thead>
                            <tbody id="append-div">
                               
                               @include('admin.dashboard.poll.components.polls_row')
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
       {{ $polls->links('frontend.posts.components.pagination') }}  
    </div>
</div>


@endsection
