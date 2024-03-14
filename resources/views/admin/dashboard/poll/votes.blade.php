@extends('admin.layouts.app')
@section('title','Polls')
@push('styles')

    <style type="text/css">
        .hidden{
            display: none;
        }
    </style>

@endpush
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

                var URL=`/cpanel/poll/votes?page=${page}&searchBy=${searchBy}&searchQuery=${searchQuery}&sortBy=${sortBy}&sortOrder=${sortOrder}&from=${from}&to=${to}&platform=${platform}&is_active=${is_active}&likes_count=${likes_count}&likes_operator=${likes_operator}&comments_count=${comments}&comments_operator=${comments_operator}&limit=${limit}`;
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
                    Votes
                    
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
    @if(count($polls)>0)
        <input id="path" type="hidden" value="{{Request::fullUrl()}}" />
        <input id="totalpage" type="hidden" value="{{ceil($polls->total()/$polls->perPage())}}" />
        @endif
        <form method="POST" action="" id="poll_form" style="display:none">
                <input type="hidden" name="poll_id" value="" id="poll_id"/>
                {{ csrf_field() }}
        </form>
    <div class="card hidden">
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
        <div class="hidden">
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
                                        <input type="hidden" name="page" id="page" value="{{ $polls->currentPage() }}"/>
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
                                    <th>Poll </th>
                                    <th>Voting Type</th>
                                    <th>Vote</th>
                                    <th>Location</th>
                                    <th>Voted at</th>
                                </tr>
                            </thead>
                            <tbody id="append-div">
                               @include('admin.dashboard.poll.components.votes_row')
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
