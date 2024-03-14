@extends('admin.layouts.app')
@section('title','Edit Thread')

@push('scripts')
<script>
    $(document).on('change','#imagefile',function(e){
        var fileName = e.target.files[0].name;
        $('.custom-file-label').html(fileName);
    });

    $(document).on('click','#btn-visibility-thread',function(){
        $('#visibility-thread-form').submit();
    });

    $(document).on('click','#btn-delete-thread',function(){
        $('#delete-thread-form').submit();
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
                    Edit
                    </h6>
                    <h1 class="header-title">
                    Edit Thread {{ '#'.$thread->id  }}
                    </h1>
                </div>
               <div class="col-auto">
                    @if($thread->is_active==1)
                    <button class="mr-2 btn btn-warning" id="btn-visibility-thread"><i class="fas fa-eye-slash mr-2"></i>Disabled Thread & All Its Opinions</button>
                    @else
                    <button class="mr-2 btn btn-success" id="btn-visibility-thread"><i class="fas fa-eye mr-2"></i>Enable Thread & All Its Opinions</button>
                    @endif
                    <button class="mr-2 btn btn-danger" id="btn-delete-thread"><i class="fas fa-trash-alt mr-2"></i>Delete Thread & All Its Opinion</button>

                    <form id="visibility-thread-form" style="display:none" method="POST" action="{{ route('admin.thread_visibility') }}">
                            <input type="hidden" name="thread_id" value="{{ $thread->id }}"/>
                            <input type="hidden" name="is_active" id="is_active" value="{{ $thread->is_active }}" />
                            {{ csrf_field() }}
                     </form>
                     <form id="delete-thread-form" style="display:none" method="POST" action="{{ route('admin.delete_thread')}}">
                            <input type="hidden" name="thread_id" value="{{ $thread->id }}"/>
                            {{ csrf_field() }}
                     </form>
               </div>
            </div>
          </div>
        </div>
</div>

<div class="container">
        <div class="row">
            <div class="col-12 col-md-10 col-xl-8 offset-xl-2 offset-md-1">
                    @include('admin.partials.header_title',['header_title'=>'Edit Thread'])
                    @include('admin.partials.message')
                    <form class="form-horizontal my-4 mx-3" method="POST" action="{{route('admin.save_thread')}}" enctype="multipart/form-data">
                        {{csrf_field()}}
                        <input id="threadid"  name="threadid" type="hidden" value="{{$thread->id}}" />
                        <div class="row clearfix">
                                <div class="col-lg-3 col-md-3 col-sm-5 col-12">
                                        <label>Thread Image</label>
                                </div>
                                <div class="col-lg-9 col-md-9 col-sm-7 col-12">
                                    <img src="{{$thread->image}}" width="auto" height="172" class="rounded"/>
                                </div>
                        </div>


                        <div class="form-group row">
                                <label for="name" class="col-md-3 col-sm-5 col-12  col-form-label">Thread Name</label>
                                <div class="col-lg-9 col-md-9 col-sm-7 col-12">
                                        <input type="text" id="name" name="name" class="form-control" value="{{$thread->name}}" required />
                                </div>
                        </div>

                        <div class="form-group row">
                                <label for="description" class="col-lg-3 col-md-3 col-sm-5 col-12 col-form-label">Thread Description</label>
                                <div class="col-lg-9 col-md-9 col-sm-7 col-12">
                                        <input type="text" id="description" name="description" class="form-control" value="{{$thread->description}}"  />
                                </div>
                        </div>
                        <div class="form-group row">
                                <label class="col-lg-3 col-md-3 col-sm-5 col-12 col-form-label">Categories</label>
                                <div class="col-lg-9 col-md-9 col-sm-7 col-12">
                                        <select class="form-control show-tick"  data-toggle="select" name="categories[]" multiple required>
                                            @foreach($categories as $category)
                                                <option value="{{$category->id}}" {{in_array($category->id,$selectedCategories)?'selected':''}}>{{$category->name}}</option>
                                                @endforeach
                                        </select>
                                </div>
                        </div>
                        <div class="form-group row">
                                <label class="col-lg-3 col-md-3 col-sm-5 col-12 col-form-label">Status</label>
                                <div class="col-lg-9 col-md-9 col-sm-7 col-12">
                                    <div class="custom-control custom-switch">
                                        @if($thread->is_active==1)
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
                                        <input type="text" id="imageurl" class="form-control" name="imageurl" value="{{$thread->image}}"/>
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
    </section>
@endsection


