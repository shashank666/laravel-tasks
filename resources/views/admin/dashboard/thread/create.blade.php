@extends('admin.layouts.app')
@section('title','Create Thread')

@push('scripts')
<script>
    $(document).ready(function(){
        $('#choose').attr('checked','true');
        $('#showImageURL').css('display','none');
        $('#showFile').css('display','block');
    });

    $(document).on('change','#choose',function(){
        if($('#choose').is(":checked")){
            $('#showFile').css('display','block');
            $('#showImageURL').css('display','none');
            $('#imageurl').val('');
        }else{
            $('#showFile').css('display','none');
            $('#imagefile').val('');
            $('#showImageURL').css('display','block');
        }
    });

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
                        @include('admin.partials.header_title',['header_title'=>'Create New Thread'])

                        @include('admin.partials.message')
                        <form class="form-horizontal my-4 mx-3" method="POST" action="{{route('admin.create_thread')}}" enctype="multipart/form-data">
                        {{csrf_field()}}
                        <div class="form-group row">
                                <label for="name" class="col-md-3 col-sm-5 col-12  col-form-label">Thread Name</label>
                                <div class="col-lg-9 col-md-9 col-sm-7 col-12">
                                    <input type="text" id="name" name="name" class="form-control" required />
                                </div>
                        </div>

                        <div class="form-group row">
                                <label for="description" class="col-lg-3 col-md-3 col-sm-5 col-12 col-form-label">Thread Description</label>
                                <div class="col-lg-9 col-md-9 col-sm-7 col-12">
                                        <input type="text" id="description" name="description" class="form-control"  />
                                </div>
                        </div>

                        <div class="form-group row">
                                <label class="col-lg-3 col-md-3 col-sm-5 col-12 col-form-label">Select Category</label>
                                <div class="col-lg-9 col-md-9 col-sm-7 col-7">
                                    <div class="form-group">
                                            <select class="form-control show-tick" data-toggle="select" name="categories[]"  multiple required>
                                                    @foreach($categories as $category)
                                                    <option value="{{$category->id}}">{{$category->name}}</option>
                                                    @endforeach
                                            </select>
                                    </div>
                                </div>
                            </div>


                            <div class="form-group row">
                                    <label class="col-lg-3 col-md-3 col-sm-5 col-12 col-form-label">Select Image</label>
                                    <div class="col-lg-9 col-md-9 col-sm-7 col-12">
                                            <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input" id="choose">
                                                    <label class="custom-control-label" for="choose">Choose File or Image URL </label>
                                            </div>
                                    </div>
                            </div>

                            <div class="form-group row" id="showImageURL">
                                    <label class="col-lg-3 col-md-3 col-sm-5 col-5 col-form-label">Image URL</label>
                                    <div class="col-lg-9 col-md-9 col-sm-7 col-12">
                                        <div class="form-group">
                                                <input type="url" id="imageurl" class="form-control" name="imageurl"/>
                                        </div>
                                    </div>
                            </div>

                            <div class="form-group row" id="showFile">
                                    <label class="col-lg-3 col-md-3 col-sm-5 col-5 col-form-label">Choose File</label>
                                    <div class="col-lg-9 col-md-9 col-sm-7 col-12">
                                        <div class="custom-file">
                                                <input type="file" class="custom-file-input" id="imagefile" name="imagefile" accept=".png,.jpg,.jpeg" >
                                                <label class="custom-file-label" for="imagefile">Select file</label>
                                        </div>
                                    </div>
                            </div>
                            <button type="submit" class="btn btn-primary mt-3 btn-block">CREATE</button>
                    </form>

        </div>
    </div>
</div>
@endsection









