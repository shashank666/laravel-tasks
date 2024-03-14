@extends('admin.layouts.app')
@section('title','Create Poll')
@push('styles')
<!--<style type="text/css">
fieldset {
  overflow: hidden
}

.radio-test {
  float: left;
  clear: none;
}

label {
  float: left;
  clear: none;
  display: block;
  padding: 2px 1em 0 0;
}

input[type=radio],
input.radio {
  float: left;
  clear: none;
  margin: 2px 0 0 2px;
}
</style>-->

@endpush
@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-colorpicker/2.5.3/js/bootstrap-colorpicker.min.js"></script>
    <script>
        $('.colorpicker').colorpicker();
    </script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/js/bootstrap.min.js"></script>
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
    $(document).ready(function () {
    $(function() {
   $("input[name='polltype']").click(function() {
     if ($("#polltype2").is(":checked")) {
       $(".mcps").show();
       
     } else {
       $(".mcps").hide();
       
     }
   });
 });
});
</script>
<script type="text/javascript">
    $(document).ready(function(){
    var maxField = 5; //Input fields increment limitation
    var x = 2;
    var opt = 3; //Initial field counter is 1
    var addButton = $('.add_button'); //Add button selector
    var wrapper = $('.field_wrapper'); //Input field wrapper
    
    //var fieldHTML = '<div><input type="text" name="field_name[]" value=""/><a href="javascript:void(0);" class="remove_button"><img src="remove-icon.png"/></a></div>'; //New input field html 
    
    //Once add button is clicked
    $(addButton).click(function(){
        //Check maximum number of input fields
        if(x <= maxField){ 
            var fieldHTML = '<label for="opt'+opt+'" class="col-lg-3 col-md-3 col-sm-5 col-12 col-form-label mb-2">Option '+opt+'</label><div class="col-lg-8 col-md-8 col-sm-6 col-10 mb-2"><input type="text" id="options['+x+']" name="options['+x+']" class="form-control"  /></div><div class="col-lg-1 col-md-1 col-sm-1 col-2 mb-2"><input type="color" id="color['+x+']" name="color['+x+']" class="form-control" /></div>';
            x++; //Increment field counter
            opt++;
            $(wrapper).append(fieldHTML); //Add field html
        }
        if(x > maxField){
            $("#add_more").hide();
        }
    });
    
    //Once remove button is clicked
    $(wrapper).on('click', '.remove_button', function(e){
        e.preventDefault();
        $(this).parent('div').remove(); //Remove field html
        x--; //Decrement field counter
        opt--;
    });
});
</script>

@endpush

@section('content')

<div class="container">
    <div class="row">
        <div class="col-12 col-md-10 col-xl-8 offset-xl-2 offset-md-1">
                        @include('admin.partials.header_title',['header_title'=>'Create New Poll'])

                        @include('admin.partials.message')
                        <form class="form-horizontal my-4 mx-3" method="POST" action="{{route('admin.add_poll')}}" enctype="multipart/form-data">
                        {{csrf_field()}}
                        <div class="form-group row">
                            <label for="title" class="col-md-3 col-sm-5 col-12  col-form-label">Poll Title</label>
                            <div class="col-lg-9 col-md-9 col-sm-7 col-12">
                                <input type="text" id="title" name="title" class="form-control" required />
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="description" class="col-lg-3 col-md-3 col-sm-5 col-12 col-form-label">Poll Description</label>
                            <div class="col-lg-9 col-md-9 col-sm-7 col-12">
                                    <textarea id="description" rows="6" name="description" class="form-control"></textarea>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-lg-3 col-md-3 col-sm-5 col-12 col-form-label">Type Of Poll</label>
                            <div class="col-lg-9 col-md-9 col-sm-7 col-12">
                                <div class="form-check form-check-inline">
                                            @foreach($polltypes as $polltype)
                                            <label class="form-check-label" for="polltype-{{$polltype->id}}">{{$polltype->type}}</label>
                                            <input class="form-check-input" type="radio" id="polltype{{$polltype->id}}" name="polltype" value="{{$polltype->type}}" class="form-control"  />
                                            @endforeach
                                </div>
                            </div>
                        </div>
                        <div class = "mcps" style="display: none;">
                        <div class="form-group row">
                            <label for="opt1" class="col-lg-3 col-md-3 col-sm-5 col-12 col-form-label mb-2">Option 1</label>
                            <div class="col-lg-8 col-md-8 col-sm-6 col-10 mb-2">
                                    <input type="text" id="options[0]" name="options[0]" class="form-control" />
                                </div>
                            <div class="col-lg-1 col-md-1 col-sm-1 col-2 mb-2">
                                    <input type="color" id="color[0]" name="color[0]" class="form-control" />
                            </div>
                            <label for="opt2" class="col-lg-3 col-md-3 col-sm-5 col-12 col-form-label mb-2">Option 2</label>
                            <div class="col-lg-8 col-md-8 col-sm-6 col-10 mb-2">
                                    <input type="text" id="options[1]" name="options[1]" class="form-control"  />
                                </div>
                                <div class="col-lg-1 col-md-1 col-sm-1 col-2 mb-2">
                                    <input type="color" id="color[1]" name="color[1]" class="form-control" />
                            </div>
                        </div>
                        <div class="field_wrapper form-group row">
                            
                        </div>
                        <a href="javascript:void(0);" class="add_button" title="Add More Options" id = "add_more">Add More</a>
                        </div>
                        <!--
                            <div class="field_wrapper form-group row">
                            
                                <label for="opt2" class="col-lg-3 col-md-3 col-sm-5 col-12 col-form-label mb-2">Option 3</label>
                                <div class="col-lg-9 col-md-9 col-sm-7 col-12 mb-2">
                                <input type="text" name="options[]" value="" class="form-control" />
                                </div>
                            
                        </div>-->
                        {{--<div class="form-group row opt 1">
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
                        </div>--}}


                        <button type="submit" class="btn btn-primary btn-block mt-3">CREATE</button>

                    </form>
        </div>
    </div>
</div>

@endsection
