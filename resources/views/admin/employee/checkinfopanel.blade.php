@extends('admin.layouts.auth')
@section('title','Set Authentication')


@push('scripts')
<script>
$(document).ready(function(){
    
    $(".msg").fadeIn(2000);
    $(".msg").fadeOut(10000);
  
});
    
</script>
@endpush
@section('content')

<div class="container">
    <div class="row">
        <div class="col-12 col-md-10 col-xl-8 offset-xl-2 offset-md-1">
                        @include('admin.partials.header_title',['header_title'=>'Set Your Password'])
                        <span class= "msg" style="color: #ff9800">Security Key has been sent to your mobile number</span>
                        @include('admin.partials.message')
                        <form class="form-horizontal my-4 mx-3" method="POST" action="{{route('admin.create_panel')}}" enctype="multipart/form-data">
                        {{csrf_field()}}
                        <input type="hidden" id="empdata" name="empdata" class="form-control" value="{{$employee->id}}" />
                        <div class="form-group row">
                            <label for="key" class="col-md-3 col-sm-5 col-12  col-form-label">Security Key</label>
                            <div class="col-lg-9 col-md-9 col-sm-7 col-12">
                                <input type="text" id="key" name="key" class="form-control" required/>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="password" class="col-md-3 col-sm-5 col-12  col-form-label">Password</label>
                            <div class="col-lg-9 col-md-9 col-sm-7 col-12">
                                <input type="password" id="password" name="password" class="form-control" pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$" title="Must contain at least one number and one uppercase and lowercase letter and one special character, and at least 8 or more characters" required />
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="cnfpassword" class="col-md-3 col-sm-5 col-12  col-form-label">Confirm Password</label>
                            <div class="col-lg-9 col-md-9 col-sm-7 col-12">
                                <input type="password" id="cnfpassword" name="cnfpassword" class="form-control" onkeyup="Validate()" required />
                            </div>
                        </div>
                        
                        <!--<div class="form-group row">
                            <label for="key" class="col-md-3 col-sm-5 col-12  col-form-label">Generate Key</label>
                            <div class="col-lg-9 col-md-9 col-sm-7 col-12">
                                <button class="btn btn-primary mt-3" OnClick="GetRandom()" type="button">Click Me!</button>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="key" class="col-md-3 col-sm-5 col-12  col-form-label">Key</label>
                            <div class="col-lg-9 col-md-9 col-sm-7 col-12">
                                <input type="text" id="key" name="key" class="form-control" value="{{substr(str_shuffle(str_repeat('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789',12)),0,12)}}" required readonly />
                            </div>
                        </div>-->
                        
                        <button type="button" class="btn btn-primary btn-block mt-3 disabled" id="desable">SAVE</button>
                        <button type="submit" class="btn btn-primary btn-block mt-3" id="enable" style="display: none">SAVE</button>

                    </form>
        </div>
    </div>
</div>
<!--
<script>
    function GetRandom()
    {
        var myKey = document.getElementById("key")
        var chars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXTZabcdefghiklmnopqrstuvwxyz";
        var string_length = 12;
        var randomstring = '';
        for (var i=0; i<string_length; i++) {
            var rnum = Math.floor(Math.random() * chars.length);
            randomstring += chars.substring(rnum,rnum+1);
        }
        myKey.value = randomstring ;
    }
</script>

-->

<script type="text/javascript">
        function Validate(){
            var password = document.getElementById("password").value;
            var cnfpassword = document.getElementById("cnfpassword").value;
            var desable = document.getElementById("desable")
            var enable = document.getElementById("enable")
            if(password == cnfpassword) {
                
                desable.style.display="none";
                enable.style.display="block";

            } else {
                desable.style.display="block";
                enable.style.display="none";
            }
    }
</script>

@endsection
