@extends('admin.layouts.app')
@section('title','Like')

@section('content')
@push('styles')
<link href="/public_admin/assets/libs/morrisjs/morris.css" rel="stylesheet" />
<style>
    .tbody tr:hover{ cursor:pointer;}
    .active_row{background:#fff8e1;}
</style>
@endpush
@push('scripts')

<script type="text/javascript" src="/public_admin/assets/libs/momentjs/moment.min.js"></script>
<script type="text/javascript" src="/public_admin/assets/libs/raphael/raphael.min.js"></script>
<script type="text/javascript" src="/public_admin/assets/libs/morrisjs/morris.js"></script>

<script>
    $(document).ready(function(){
        Morris.Line({
            element: 'likes_bar_chart',
            data: <?php echo json_encode($date_wise_count)?>,
            xkey: ['date'],
            xLabelFormat:function(x){return moment(x,'YYYY-MM-DD').format('DD-MM-YYYY')},
            ykeys: ['total'],
            yLabelFormat: function(y){return y != Math.round(y)?'':y;},
            labels: ['Likes'],
            lineColors: [ 'rgb(255, 152, 0)'],
            lineWidth: 3
        });
    });

    window.selectedIDS=[];

    $(document).on('click','.deleteLike',function(){
            var deleteID=$(this).attr('data-deleteid');
            $('#deleteid').val(deleteID);
            $('#deleteLikeForm').submit();
    });

    $(document).on('click','#btnDisableLikes',function(){
        if(selectedIDS.length==0){
                alert('No Rows Selected , Please click on row to update');
        }else{
                $('#updateid').val(selectedIDS);
                $('#updateLikeForm').submit();
        }
    });

    $(document).on('click','.tbody tr',function(){
        if($(this).hasClass('active_row'))
        {
            $(this).removeClass('active_row');
            selectedIDS.splice(selectedIDS.indexOf($(this).attr('id')), 1);
        }else{
            $(this).addClass('active_row');
            selectedIDS.push($(this).attr('id'));
        }
    });


</script>
@endpush
<div class="header">
        <div class="container">
          <div class="header-body">
            <div class="row align-items-end">
                <div class="col">
                    <h1 class="header-title">{{ '#'.$post->id}}</h1>
                </div>
          </div>

           @include('admin.dashboard.post.components.post_menu',['section'=>'likes'])

         </div>
        </div>
</div>


    <div class="container">
        <div class="row">
                <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-header-title">Date Wise Post Active Likes Graph</h4>
                        </div>
                        <div class="card-body">
                            <div id="likes_bar_chart" class="graph"></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-12 col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h6 class="card-title text-uppercase text-muted mb-2">
                                        Total Active Likes
                                        </h6>
                                        <span class="h2 mb-0">
                                        {{ $count['active_likes'] }}
                                        </span>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-check text-muted mb-0"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col">
                                            <h6 class="card-title text-uppercase text-muted mb-2">
                                            Total Disabled Likes
                                            </h6>
                                            <span class="h2 mb-0">
                                            {{ $count['disabled_likes'] }}
                                            </span>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-ban text-muted mb-0"></i>
                                        </div>
                                    </div>
                                </div>
                        </div>
                </div>
        </div>
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-header-title">Likes of {{ '#'.$post->id.' - '.$post->title }}
                            <span style="float:right">
                                <button class="btn btn-primary btn-sm" id="btnDisableLikes">Enable / Disable Likes</button>
                            </span>
                        </h4>
                    </div>
                    <input id="path" type="hidden" value="{{Request::fullUrl()}}" />
                    <input id="totalpage" type="hidden" value="{{ceil($likes->total()/$likes->perPage())}}" />

                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Id</th>
                                        <th colspan="2">User</th>
                                        <th>Liked At</th>
                                        <th>IP Adress</th>
                                        <th>User Agent</th>
                                        <th>Status</th>
                                        <th>Delete</th>
                                    </tr>
                                </thead>
                                <tbody class="tbody" id="append-div">
                                   @include('admin.dashboard.post.post_like_row')
                                </tbody>
                            </table>
                            @include('admin.partials.spinner')
                            <form style="display:none" id="deleteLikeForm" method="POST" action="{{ route('admin.delete_post_like',['id'=>$post->id]) }}">
                                {{csrf_field()}}
                                <input type="hidden" name="deleteid" id="deleteid" value=""/>
                            </form>
                            <form style="display:none" id="updateLikeForm" method="POST" action="{{ route('admin.update_post_like',['id'=>$post->id]) }}">
                                    {{csrf_field()}}
                                    <input type="hidden" name="updateid" id="updateid" value=""/>
                            </form>
                        </div>
                </div>
            </div>
        </div>
    </div>

    @if(count($likes)>0)
    @include('admin.partials.loadmorescript')
    @endif

@endsection
