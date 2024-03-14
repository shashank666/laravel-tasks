@extends('admin.layouts.app')
@section('title','Create Poll Type')

@push('scripts')
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
@endpush

@section('content')

<div class="container">
    <div class="row">
        <div class="col-12 col-md-10 col-xl-8 offset-xl-2 offset-md-1">
                        @include('admin.partials.header_title',['header_title'=>'Create New Poll Type'])

                        @include('admin.partials.message')
                        <form class="form-horizontal my-4 mx-3" method="POST" action="{{route('admin.add_poll_type')}}" enctype="multipart/form-data">
                        {{csrf_field()}}
                        <div class="form-group row">
                            <label for="type" class="col-md-3 col-sm-5 col-12  col-form-label">Type Name</label>
                            <div class="col-lg-9 col-md-9 col-sm-7 col-12">
                                <input type="text" id="type" name="type" class="form-control" required />
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="description" class="col-lg-3 col-md-3 col-sm-5 col-12 col-form-label">Type Description</label>
                            <div class="col-lg-9 col-md-9 col-sm-7 col-12">
                                    <input type="text" id="description" name="description" class="form-control"  />
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-lg-3 col-md-3 col-sm-5 col-12 col-form-label">Number Of Options</label>
                            <div class="col-lg-9 col-md-9 col-sm-7 col-7">
                                <div class="form-group">
                                        <select class="form-control show-tick" data-live-search="true" name="no.opt" required>
                                            <option value="2" selected>Two</option>
                                            <option value="3" >Three</option>
                                            <option value="4" >Four</option>
                                            <option value="5" >Five</option>
                                            <option value="6" >Six</option>
                                        </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row opt 1">
                            <label for="opt1" class="col-lg-3 col-md-3 col-sm-5 col-12 col-form-label">Option 1</label>
                            <div class="col-lg-9 col-md-9 col-sm-7 col-12">
                                    <input type="text" id="opt1" name="opt1" class="form-control"  />
                            </div>
                        </div>
                        <div class="form-group row opt 2">
                            <label for="opt2" class="col-lg-3 col-md-3 col-sm-5 col-12 col-form-label">Option 2</label>
                            <div class="col-lg-9 col-md-9 col-sm-7 col-12">
                                    <input type="text" id="opt2" name="opt2" class="form-control"  />
                            </div>
                        </div>
                        <div class="form-group row opt 3">
                            <label for="opt3" class="col-lg-3 col-md-3 col-sm-5 col-12 col-form-label">Option 3</label>
                            <div class="col-lg-9 col-md-9 col-sm-7 col-12">
                                    <input type="text" id="opt3" name="opt3" class="form-control"  />
                            </div>
                        </div>


                        <button type="submit" class="btn btn-primary btn-block mt-3">CREATE</button>

                    </form>
        </div>
    </div>
</div>

@endsection
