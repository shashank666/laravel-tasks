@extends('admin.layouts.auth')
@section('title','Add Informations')

@push('styles')
<link href="/public_admin/assets/libs/morrisjs/morris.css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="/public_admin/assets/libs/daterangepicker/daterangepicker.css" />
<style type="text/css">
  input[type=number]::-webkit-inner-spin-button, 
  input[type=number]::-webkit-outer-spin-button { 
    -webkit-appearance: none; 
    margin: 0; 
  }
</style>
<link rel="stylesheet" href="/vendor/datetimepicker/build/css/bootstrap-datetimepicker.min.css" />

@endpush

@push('scripts')
<script type="text/javascript" src="/public_admin/assets/libs/momentjs/moment.min.js"></script>
<script type="text/javascript" src="/public_admin/assets/libs/daterangepicker/daterangepicker.min.js"></script>
<script src="/vendor/datetimepicker/build/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
<script>
    $(document).ready(function() {

        $('#dob').datetimepicker({
            format:'DD-MM-YYYY',
            collapse: true,
            maxDate:moment().subtract(18, 'years'),
            useCurrent: true,
            //defaultDate:moment().subtract(18, 'years'),
            //maxDate:moment().subtract(18, 'years'),
        });
        $('#doj').datetimepicker({
            format:'DD/MM/YYYY',
            collapse: true,
            useCurrent: false,
        });

    });
</script>


   
@endpush


@section('content')

<div class="container">
    <div class="row">
        <div class="col-12 col-md-10 col-xl-8 offset-xl-2 offset-md-1">
                        @include('admin.partials.header_title',['header_title'=>'Fill the details'])

                        @include('admin.partials.message')
                        <form class="form-horizontal my-4 mx-3" method="POST" action="{{route('admin.reset_key')}}" enctype="multipart/form-data">
                        {{csrf_field()}}
                        <input type="hidden" id="empdata" name="empdata" class="form-control" value="{{$admins->id}}" />
                        <div class="form-group row">
                            <label for="name" class="col-md-3 col-sm-5 col-12  col-form-label">Your Name</label>
                            <div class="col-lg-9 col-md-9 col-sm-7 col-12">
                                <input type="text" id="name" name="name" class="form-control" required />
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="email" class="col-lg-3 col-md-3 col-sm-5 col-12 col-form-label"> Email Address</label>
                            <div class="col-lg-9 col-md-9 col-sm-7 col-12">
                                    <input type="email" id="email" name="email" class="form-control" required />
                            </div>
                        </div>
                        


                        <div class="input-group row mb-4">
                            <label for="mobile" class="col-lg-3 col-md-3 col-sm-5 col-12 col-form-label"> Mobile</label>
                            
                                <div class="input-group-prepend" style="padding-left: 18px;">
                                    <div class="input-group-text bg-white border-right-0 phonecode_label" data-toggle="modal" data-target="#phonecodeModal" style="cursor:pointer;border-top-left-radius: 15%;border-bottom-left-radius: 15%; color: #000;"></div>
                                </div>
                                <input id="phone_code" type="hidden" name="phone_code" class="form-control" />
                                <input type="number" id="mobile" name="mobile" style="margin-right: -12px" class="border-left-0 form-control"   placeholder="Mobile Number"  min="100000" max="999999999999999" required/>
                            </div>
                        
                        

                        <div class="form-group row">
                                <label class="col-lg-3 col-md-3 col-sm-5 col-12 col-form-label" for="birthdate">
                                            <!--<span class="mr-2"><i class="fas fa-birthday-cake"></i></span>-->
                                            Date of Birth</label>
                                    <div class="col-lg-9 col-md-9 col-sm-7 col-12">
                                        <input type="text" id="dob" name="dob" placeholder="DD/MM/YYYY" class="form-control text-secondary" value="" autocomplete="off" required/>
                                    </div>
                        </div>

                        <div class="form-group row">
                                <label class="col-lg-3 col-md-3 col-sm-5 col-12 col-form-label" for="joindate">
                                            <!--<span class="mr-2"><i class="fas fa-birthday-cake"></i></span>-->
                                            Joining Date</label>
                                    <div class="col-lg-9 col-md-9 col-sm-7 col-12">
                                        <input type="text" id="doj" name="doj" placeholder="DD/MM/YYYY" class="form-control text-secondary" value="" autocomplete="off" required/>
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
                        
                        <button type="submit"  class="btn btn-primary btn-block mt-3">SAVE</button>

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

<script type="text/javascript">
		function Validate(){
			var name = document.getElementById("name").value;
			var checkname = document.getElementById("checkname").value;
			var email = document.getElementById("email").value;
			var checkemail = document.getElementById("checkemail").value;
			var mobile = document.getElementById("mobile").value;
			var checkmobile = document.getElementById("checkmobile").value;
			var dob = document.getElementById("dob").value;
			var checkdob = document.getElementById("checkdob").value;
			var doj = document.getElementById("doj").value;
			var checkdoj = document.getElementById("checkdoj").value;

			if(name == checkname && email == checkemail && mobile == checkmobile && dob == checkdob && doj == checkdoj) {
			    alert("same data");

			} else {
			    alert("Not Correct");
			}
	}
</script>
-->
@endsection
