@extends('admin.layouts.app')
@section('title','Edit Category')

@push('scripts')
<script>
        $(document).on('change','#imagefile',function(e){
            var fileName = e.target.files[0].name;
            $('.custom-file-label').html(fileName);
        });
</script>
@endpush

@section('content')
    <div class="container">
            <div class="row">
                    <div class="col-12 col-md-10 col-xl-8 offset-xl-2 offset-md-1">
                              @include('admin.partials.header_title',['header_title'=>'Edit Category'])

                              @include('admin.partials.message')
                              <form class="form-horizontal my-4 mx-3" method="POST" action="{{route('admin.save_category')}}" enctype="multipart/form-data">
                                {{csrf_field()}}
                                <input id="categoryid"  name="categoryid" type="hidden" value="{{$category->id}}" />
                                <div class="row">
                                    <div class="col-lg-3 col-md-3 col-sm-5 col-12">
                                        <label>Category Image</label>
                                    </div>
                                    <div class="col-lg-9 col-md-9 col-sm-7 col-12">
                                        <img src="{{$category->image}}" width="auto" height="172" class="rounded"/>
                                    </div>
                                </div>

                                <div class="form-group row mt-3">
                                    <label class="col-lg-3 col-md-3 col-sm-5 col-5 col-form-label">Category Name</label>
                                    <div class="col-lg-9 col-md-9 col-sm-7 col-12">
                                        <input type="text" id="name" name="name" class="form-control" value="{{$category->name}}" required />
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-lg-3 col-md-3 col-sm-5 col-12 col-form-label">Description</label>
                                    <div class="col-lg-9 col-md-9 col-sm-7 col-12">
                                        <input type="text" id="description" name="description" class="form-control" value="{{$category->description}}"  />
                                    </div>
                                </div>

                                  <div class="form-group row">
                                    <label class="col-lg-3 col-md-3 col-sm-5 col-12 col-form-label">Select Group</label>
                                    <div class="col-lg-9 col-md-9 col-sm-7 col-12">
                                                <select class="form-control show-tick" data-live-search="true" name="category_group" required>
                                                   {{--   @foreach($categorygroups as $group)
                                                        <option value="{{$group->name}}" {{$group->name==$category->category_group?'selected':''}}>{{$group->name}}</option>
                                                     @endforeach   --}}
                                                       <option value="{{$category->category_group}}" selected>{{$category->category_group}}</option>
                                                </select>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-lg-3 col-md-3 col-sm-5 col-12 col-form-label">Status</label>
                                    <div class="col-lg-9 col-md-9 col-sm-7 col-12">
                                        <div class="custom-control custom-switch">
                                            @if($category->is_active==1)
                                            <input type="checkbox" class="custom-control-input"  id="is_active" name="is_active" checked/>
                                            @else
                                            <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" />
                                            @endif
                                            <label class="custom-control-label" for="is_active">Enabled</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row" id="showImageURL">
                                    <label class="col-lg-3 col-md-3 col-sm-5 col-12 col-form-label">Image URL</label>
                                    <div class="col-lg-9 col-md-9 col-sm-7 col-12">
                                        <input type="text" id="imageurl" class="form-control" name="imageurl" value="{{$category->image}}"/>
                                    </div>
                                </div>

                                <div class="form-group" id="showFile">
                                    <label class="col-lg-3 col-md-3 col-sm-5 col-12 col-form-label">Change Image ?</label>
                                    <div class="col-lg-9 col-md-9 col-sm-7 col-12">
                                         <div class="custom-file">
                                                <input type="file" class="custom-file-input" id="imagefile" name="imagefile" accept=".png,.jpg,.jpeg" >
                                                <label class="custom-file-label" for="imagefile">Select file</label>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary btn-block mt-3">UPDATE</button>
                            </form>
                </div>
            </div>
    </div>
@endsection
