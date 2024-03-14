@extends('admin.layouts.app')
@section('title','Deleted User Accounts')

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
                    Deleted User Accounts
                    <span  class="ml-2 badge badge-primary">{{  $users->total() }}</span>
                    </h1>
                </div>
               <div class="col-auto">
                <a href="{{ route('admin.deleted_download',['format'=>'csv']) }}" class="btn btn-primary">Download</a>
               </div>
            </div>
          </div>
        </div>
</div>

<div class="container">

    <div class="row">
        <div class="col-12">
            <div class="card">

                    @if(count($users)>0)
                    <input id="path" type="hidden" value="{{Request::fullUrl()}}" />
                    <input id="totalpage" type="hidden" value="{{ceil($users->total()/$users->perPage())}}" />
                    @endif

                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>IMAGE</th>
                                    <th>NAME</th>
                                    <th>EMAIL</th>
                                    <th>MOBILE</th>
                                    <th>STATUS</th>
                                    <th>PROVIDER</th>
                                    <th>PLATFORM</th>
                                    <th>REASON</th>
                                    <th>REGISTERD AT</th>
									<th>DELETED AT</th>
                                </tr>
                            </thead>
                            <tbody id="append-div">
                            @include('admin.dashboard.user.deleted_user_row')
                            </tbody>
                        </table>
                    </div>
                    @include('admin.partials.spinner')
            </div>
        </div>
    </div>
</div>

@if(count($users)>0)
@include('admin.partials.loadmorescript')
@endif
@endsection
