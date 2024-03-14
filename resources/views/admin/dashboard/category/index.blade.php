@extends('admin.layouts.app')
@section('title','Categories')

@push('scripts')
<script src="/public_admin/assets/libs/list.js/dist/list.min.js"></script>
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
                    Categories
                    <span class="ml-2 badge badge-success">{{count($categories)}}</span>
                    </h1>
                </div>
               <div class="col-auto">
                    <a href="{{route('admin.add_category')}}"  class="btn btn-primary lift">
                      Create New
                    </a>
                </div>
            </div>
          </div>
        </div>
</div>
<div class="container">
    <div class="row">
        <div class="col-12">
                <div class="card" id="categories_table">
                    <div class="card-header">
                        <div class="row">
                          <div class="col-12">
                            <form>
                              <div class="input-group input-group-flush input-group-merge">
                                <input type="search" class="form-control form-control-prepended search" placeholder="Search" autocomplete="off">
                                <div class="input-group-prepend">
                                  <div class="input-group-text">
                                    <span class="fe fe-search"></span>
                                  </div>
                                </div>
                              </div>
                            </form>
                          </div>
                        </div>
                      </div>

                        <div class="table-responsive mb-0">
                          <table class="table table-nowrap card-table">
                            <thead>
                              <tr>
                                    <th class="text-muted sort" data-sort="category-id">ID</th>
                                    <th class="text-muted">CATEGORY IMAGE</th>
                                    <th class="text-muted sort" data-sort="category-name">CATEGORY NAME</th>
                                    <th class="text-muted sort" data-sort="category-posts">POSTS</th>
                                    <th class="text-muted sort" data-sort="category-threads">THREADS</th>
                                    <th class="text-muted sort" data-sort="category-followers">FOLLOWERS</th>
                                    <th class="text-muted sort" data-sort="category-status">STATUS</th>
                                    <th  class="text-muted">EDIT</th>
                              </tr>
                            </thead>
                            <tbody class="list">
                                    @foreach($categories as $index=>$category)
                                    <tr>
                                        <td class="category-id">{{$category->id}}</td>
                                        <td ><img src="{{$category->image}}" height="100" width="150" class="rounded" alt="..."/></td>
                                        <td  class="category-name">{{$category->name}}</td>
                                        <td>
                                            <a target="_blank" href="{{route('admin.posts',['categories'=>$category->id])}}" class="category-posts btn btn-secondary {{$category->posts_count>0?'':'disabled'}}">{{$category->posts_count}}</a>
                                        </td>
                                        <td>
                                            <a target="_blank" href="{{route('admin.threads',['categories'=>$category->id])}}" class="category-threads btn btn-secondary {{$category->threads_count>0?'':'disabled'}}">{{$category->threads_count}}</a>
                                        </td>
                                        <td>
                                            <a href="" class="category-followers btn btn-secondary {{$category->followers_count>0?'':'disabled'}}">{{$category->followers_count}}</a>
                                        </td>
                                        <td>@if($category->is_active==1)<p class="category-status badge badge-success p-2">Active</p>@else<p class="category-status badge badge-danger p-2">Disabled</p>@endif</td>
                                        <td>
                                        <a class="btn btn-primary" href="{{route('admin.edit_category',['id'=>$category->id])}}"><i class="fe fe-edit-2"></i></a>
                                        </td>
                                    </tr>
                                    @endforeach
                            </tbody>
                          </table>
                        </div>

                </div>
        </div>
    </div>
</div>
<script>
    var options = {
      valueNames: [ 'category-id', 'category-name', 'category-posts','category-threads','category-followers','category-status' ]
    };

    var categoriesList = new List('categories_table', options);
</script>
@endsection
