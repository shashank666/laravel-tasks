@extends('admin.layouts.app')
@section('title','Reported Issues')


@push('styles')
<link href="/public_admin/assets/libs/noty/noty.min.css" rel="stylesheet" type="text/css"/>
@endpush

@push('scripts')
<script src="/public_admin/assets/libs/noty/noty.min.js"></script>
<script>
    $(document).on('click','.close_issue',function(){
        var issueid=$(this).attr('data-closeid');
        $.ajax({
            url:"{{ route('admin.close_issue')}}",
            type:"POST",
            data:{'id':issueid},
            dataType:"json",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success:function(response){
                if(response.status=='success'){
                    location.reload();
                }
            },error:function(){
                new Noty({
                    theme:'sunset',
                    type:'error',
                    text: 'Failed To Close Issue',
                    timeout:3500,
                }).show();
            }
        });
    });
    $(document).on('click','.delete_issue',function(){
        var issueid=$(this).attr('data-deleteid');
        $.ajax({
            url:"{{ route('admin.delete_issue')}}",
            type:"POST",
            data:{'id':issueid},
            dataType:"json",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success:function(response){
                if(response.status=='success'){
                    location.reload();
                }
            },error:function(){
                new Noty({
                    theme:'sunset',
                    type:'error',
                    text: 'Failed To Close Issue',
                    timeout:3500,
                }).show();
            }
        });
    });
    $(document).on('click','.delete_all_issue',function(){
        $.ajax({
            url:"{{ route('admin.delete_all_issues')}}",
            type:"POST",
            dataType:"json",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success:function(response){
                if(response.status=='success'){
                    location.reload();
                }
            },error:function(){
                new Noty({
                    theme:'sunset',
                    type:'error',
                    text: 'Failed To Close Issue',
                    timeout:3500,
                }).show();
            }
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
                <h1 class="header-title">Reported Issues<span class="badge badge-danger ml-3">{{ $issues->total().' Open' }}</span></h1>
            </div>
            <div class="col-auto">
                @if($issues->total()>0 || $closed_issues->total()>0)
                <button class="btn btn-danger delete_all_issue">
                    <span><i class="fas fa-trash-alt mr-2"></i></span>Delete All Issues</button>
                @endif
            </div>
        </div>
     </div>
    </div>
</div>


<div class="container">
    <div class="row clearfix">
        <div class="col-12">
            <div class="card">

                <div class="card-body">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item active"><a href="#open" data-toggle="tab" class="nav-link active">Open Issues</a></li>
                        <li class="nav-item"><a href="#closed" data-toggle="tab" class="nav-link">Closed Issues</a></li>
                    </ul>
                    <div class="tab-content">
                            <div role="tabpanel" class="tab-pane in active" id="open">
                                    <div class="table-responsive">
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                    <th>Id</th>
                                                    <th>Reported By</th>
                                                    <th>Post</th>
                                                    <th>Report Reason</th>
                                                    <th>Reported At</th>
                                                    <th>Close</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($issues as $issue)
                                                    <tr>
                                                        <td>{{ $issue->id }}</td>
                                                        <td>
                                                            @if(isset($issue->user['id']))
                                                            <a  href="{{ route('admin.user_details',['id'=>$issue->user['id']]) }}">
                                                            @else
                                                            <a href=''>
                                                            @endif
                                                            
                                                                {{ $issue->user->name }}
                                                                <img src="{{ $issue->user['image'] }}" height="56" width="56" class="mt-3 rounded-circle"/>
                                                            </a>
                                                        </td>

                                                        <td>
                                                            @if(isset($issue->post["id"]))
                                                            <a href='{{ route("admin.blog_post",["id"=>$issue->post["id"]])  }}'>
                                                            @else
                                                            <a href='{{ route("admin.opinions" )}}'>
                                                            @endif 

                                                            @if((isset( $issue->post['title'])))
                                                            <p>{{ $issue->post['title'] }}</p>
                                                            <br/>
                                                            <img src="{{ $issue->post['coverimage'] }}" width="160" height="100" class="rounded"/>
                                                            @else
                                                            @if(isset($issue->opinion['body']))
                                                            <p>Opinion Id: {{ $issue->opinion['id'] }}</p>
                                                            <p>Post: {{ $issue->opinion['body'] }}</p>
                                                            @else
                                                            <p>null</p>
                                                            @endif
                                                            @endif
                                                            </a>
                                                        </td>
                                                        <td>{{ $issue->report_reason }}</td>
                                                        <td>{{ \Carbon\Carbon::parse($issue->created_at)->format('j F Y , h:i:s A') }}</td>
                                                        <td><button class="btn btn-danger close_issue" data-closeid="{{ $issue->id }}"><i class="fas fa-ban mr-2"></i>Close</button></td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                            @if($issues->total()>0)
                                            <div class="d-flex justify-content-center align-items-center">
                                                        {{ $issues->links('admin.partials.pagination') }}
                                            </div>
                                            @endif
                                    </div>
                            </div>
                            <div role="tabpanel" class="tab-pane fade" id="closed">
                                    <div class="table-responsive">
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th>Id</th>
                                                        <th>Reported By</th>
                                                        <th>Post</th>
                                                        <th>Report Reason</th>
                                                        <th>Closed At</th>
                                                        <th>Delete</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($closed_issues as $issue)
                                                    <tr>
                                                        <td>{{ $issue->id }}</td>
                                                        <td>
                                                            @if(isset($issue->user['id']))
                                                            <a  href="{{ route('admin.user_details',['id'=>$issue->user['id']]) }}">
                                                            @else
                                                            <a href=''>
                                                            @endif

                                                            @if(isset($issue->user['name'] ))
                                                                {{ $issue->user['name'] }}
                                                                <img src="{{ $issue->user['image'] }}" height="56" width="56" class="mt-3 rounded-circle"/>
                                                            @else
                                                            <p> Null Found</p>
                                                            @endif
                                                            </a>
                                                        </td>
                                                        <td>
                                                            @if(isset($issue->post["id"]))
                                                            <a href='{{ route("admin.blog_post",["id"=>$issue->post["id"]])  }}'>
                                                            @else
                                                            <a href=''>
                                                            @endif
                                                            
                                                            @if((isset( $issue->post['title'])))
                                                            <p>{{ $issue->post['title'] }}</p>
                                                            <br/>
                                                            <img src="{{ $issue->post['coverimage'] }}" width="160" height="100" class="rounded"/>
                                                            @else
                                                            @if(isset($issue->opinion['body']))
                                                            <p>{{ $issue->opinion['body'] }}</p>
                                                            <br/>
                                                            {{-- <img src="{{ $issue->opinion['cover'] }}" width="160" height="100" class="rounded"/> --}}
                                                            @else
                                                            <p> Null</p>
                                                            @endif
                                                            @endif
                                                            </a>
                                                            
                                                
                                                        </td>
                                                        <td>{{ $issue->report_reason }}</td>
                                                        <td>{{ \Carbon\Carbon::parse($issue->updated_at)->format('j F Y , h:i:s A') }}</td>
                                                        <td>
                                                            <button class="btn btn-danger delete_issue" data-deleteid="{{ $issue->id  }}"><i class="fas fa-trash mr-2"></i>Delete</button>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>

                                            </table>
                                            @if($closed_issues->total()>0)
                                            <div class="d-flex justify-content-center align-items-center">
                                                {{ $closed_issues->links('admin.partials.pagination') }}
                                            </div>
                                            @endif
                                    </div>
                            </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
