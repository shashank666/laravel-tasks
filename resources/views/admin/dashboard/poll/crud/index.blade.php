@extends('admin.layouts.app')
@section('title','Create Poll')
@push('styles')

@endpush
@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-colorpicker/2.5.3/js/bootstrap-colorpicker.min.js"></script>
    <script>
        $('.colorpicker').colorpicker();
    </script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/js/bootstrap.min.js"></script>

<script>
    $(document).ready(function () {
    $(function() {
   $("input[name='polltype']").click(function() {
     if ($("#polltype2").is(":checked")) {
       $(".mcps").show();
       $(".udn").hide();
       
     } else {
       $(".mcps").hide();
       $(".udn").show();
       
     }
   });
 });
});
</script>

@endpush

@section('content')

<div class="container">
    <div class="row">
        <div class="col-12 col-md-10 col-xl-8 offset-xl-2 offset-md-1">
                        @include('admin.partials.header_title',['header_title'=>'Select Type of New Poll'])

                        @include('admin.partials.message')
                        <form class="form-horizontal my-4 mx-3" method="POST" action="{{route('admin.show_add_poll')}}" enctype="multipart/form-data">
                        {{csrf_field()}}
                        
                        
                        <div class="form-group row">
                            <label class="col-lg-3 col-md-3 col-sm-5 col-12 col-form-label">Type Of Poll</label>
                            <div class="col-lg-9 col-md-9 col-sm-7 col-12">
                                <div class="form-check form-check-inline">
                                            @foreach($polltypes as $polltype)
                                            <label class="form-check-label" for="polltype-{{$polltype->id}}">{{$polltype->type}}</label>
                                            <input class="form-check-input pr-2" type="radio" id="polltype{{$polltype->id}}" name="polltype" value="{{$polltype->type}}" class="form-control"  />
                                            @endforeach
                                </div>
                            </div>
                        </div>
                        
                        

                        <button type="submit" class="btn btn-primary btn-block mt-3">Select</button>

                    </form>
                    
                    <div class="row">

                    <div class="col-md-2"></div>
                    <div class = "col-md-8 mcps" style="display: none;">
                        <div style="text-align: center;">Preview (Multiple choice poll with single selection)</div>
                        <img src="/img/mcps.PNG" alt="MCPS" width="500" height="auto" style="opacity: 0.5;">
                        
                    </div>
                    <div class = "col-md-8 udn" style="display: none;">
                        <div style="text-align: center;">Preview (Upvote / Downvote upto 3 votes or Neutral)</div>
                        <img src="/img/udn.PNG" alt="UDN" width="500" height="auto" style="opacity: 0.5;">

                    </div>
                    </div>
        </div>

    </div>
</div>

@endsection
