@extends('admin.layouts.app')
@section('title','Edit and Provide relative Poll')
@push('styles')
<link rel="stylesheet" type="text/css" id="bootstrap-select" href="/vendor/bootstrap-select/bootstrap-select.min.css" />

@endpush
@push('scripts')
<script type="text/javascript" src="/vendor/bootstrap-select/bootstrap-select.min.js"></script>

<!--<script>
$(document).ready(function(){
    $("select").change(function(){
        $(this).find("option:selected").each(function(){
            var optionValue = $(this).attr("value");
            if(optionValue){
                $(".opt").not("." + optionValue).hide();
                $("." + optionValue).show();
            } else{
                $(".opt").hide();
            }
        });
    }).change();
});
</script>-->
<script>
    $(document).on('change','#enablenote',function(){
     if($('#enablenote').is(":checked")){
         $('#note').css('display','inline');
         $('#enablenote').val(1);
         
     }else{
         $('#note').css('display','none');
         $('#enablenote').val(0);
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
                        @include('admin.partials.header_title',['header_title'=>'Create New Poll'])

                        @include('admin.partials.message')
                        <form class="form-horizontal my-4 mx-3" method="POST" action="{{route('admin.update_poll')}}" enctype="multipart/form-data">
                        {{csrf_field()}}
                        <input type="hidden" id="poll_id" name="poll_id" class="form-control" value="{{$poll->id}}" />
                        <div class="form-group row">
                            <label for="title" class="col-md-3 col-sm-5 col-12  col-form-label">Poll Title</label>
                            <div class="col-lg-9 col-md-9 col-sm-7 col-12">
                                <input type="text" id="title" name="title" class="form-control" disabled="" value="{{$poll->title}}" style="cursor: no-drop;" />
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="description" class="col-lg-3 col-md-3 col-sm-5 col-12 col-form-label">Poll Description</label>
                            <div class="col-lg-9 col-md-9 col-sm-7 col-12">
                                    <textarea id="description" rows="6" name="description" class="form-control">{{$poll->description}}</textarea>
                            </div>
                        </div>
                    @if($poll->poll_type== "MCPS")
                        <div class="form-group row">
                            <label for="enablenote" class="col-lg-3 col-md-3 col-sm-5 col-12 col-form-label">Enable Note</label>
                            <div class="col-lg-9 col-md-9 col-sm-7 col-12">
                                    <div class="custom-control custom-switch">
                                        @if($poll->enablenote == 1)
                                            <input type="checkbox" class="custom-control-input" id="enablenote" name="enablenote" checked value="{{$poll->enablenote}}"/>
                                        @else
                                            <input type="checkbox" class="custom-control-input" id="enablenote" name="enablenote" value=""/>
                                        @endif
                                            <label class="custom-control-label" for="enablenote"></label>
                                    </div>
                            </div>
                        </div>
                        @if($poll->enablenote == 1)
                        <div class="form-group row" id="note">
                            <label for="note" class="col-lg-3 col-md-3 col-sm-5 col-12 col-form-label">Note</label>
                            <div class="col-lg-9 col-md-9 col-sm-7 col-12">
                                <div class="form-group">
                                        <textarea id="note" class="form-control" name="note"/>{{$poll->poll_result_note}}</textarea>
                                </div>
                            </div>
                        </div>
                        @else
                        <div class="form-group row" id="note" style="display: none">
                            <label for="note" class="col-lg-3 col-md-3 col-sm-5 col-12 col-form-label">Note</label>
                            <div class="col-lg-9 col-md-9 col-sm-7 col-12">
                                <div class="form-group">
                                        <textarea id="note" class="form-control" name="note"/>{{$poll->poll_result_note}}</textarea>
                                </div>
                            </div>
                        </div>
                        @endif
                    @endif

                        <div class="form-group row">
                                    <label class="col-lg-3 col-md-3 col-sm-5 col-12 col-form-label" for="thread">Select Threads</label>
                                    <div class="col-lg-9 col-md-9 col-sm-7 col-12">
                                    <select class="form-control selectpicker " data-live-search="true" id="thread" name="threads[]" multiple>
                                            @foreach($threads as $thread)
                                                @if(in_array($thread->id,$selected_threads))
                                                    <option value="{{$thread->id}}" selected>{{$thread->name}}</option>
                                                @else
                                                    <option value="{{$thread->id}}">{{$thread->name}}</option>
                                                @endif
                                            @endforeach
                                    </select>
                                </div>
                            </div>
                        <div class="form-group row">
                                    <label class="col-lg-3 col-md-3 col-sm-5 col-12 col-form-label" for="poll">Select Polls</label>
                                    <div class="col-lg-9 col-md-9 col-sm-7 col-12">
                                    <select class="form-control selectpicker " data-live-search="true" id="poll" name="polls[]" multiple>
                                            @foreach($polls as $poll)
                                                @if(in_array($poll->id,$selected_polls))
                                                    <option value="{{$poll->id}}" selected>{{$poll->title}}</option>
                                                @else
                                                    <option value="{{$poll->id}}">{{$poll->title}}</option>
                                                @endif
                                            @endforeach
                                    </select>
                                </div>
                            </div>
                        


                        <button type="submit" class="btn btn-primary btn-block mt-3">Update</button>

                    </form>
        </div>
    </div>
</div>

@endsection
