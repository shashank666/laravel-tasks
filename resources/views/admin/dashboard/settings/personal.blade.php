@extends('admin.layouts.app')
@section('title','Personal Settings')
@push('styles')
<link href="/public_admin/assets/libs/noty/noty.min.css" rel="stylesheet" type="text/css"/>
@endpush

@push('scripts')
<script src="/public_admin/assets/libs/noty/noty.min.js"></script>
<script>
    $(document).on('submit','#change-password-form',function(e){
        e.preventDefault();
        $.ajax({
            url:$(this).attr('action'),
            type:"POST",
            data:$(this).serialize(),
            dataType:'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success:function(response){
                $('#change-password-form').get(0).reset();
                new Noty({
                    theme:'sunset',
                    type:response.status,
                    text: response.message,
                    timeout:3500,
                }).show();
            },error:function(err){
                new Noty({
                    theme:'sunset',
                    type:'error',
                    text: 'FAILED TO UPDATE PASSWORD',
                    timeout:3500,
                }).show();
            }
        });
    });
</script>
@endpush

@section('content')

@include('admin.partials.header_title',['header_title'=>'Personal Settings'])
<div class="container">
    <div class="row">
            <div class="col-md-4 col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-header-title">Change Password</h4>
                    </div>
                    <div class="card-body">
                        <form id="change-password-form" method="POST" action="{{ route('admin.personal.change_password') }}">
                                {{ csrf_field() }}
                             <div class="form-group">
                                <label>Enter Current Password</label>
                                <div class="form-line">
                                    <input type="password" name="old-password" id="old-password" class="form-control" required/>
                                </div>
                            </div>
                             <div class="form-group">
                                <label>Enter New Password</label>
                                <div class="form-line">
                                     <input type="password" id="new-password" name="new-password" class="form-control" pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$" title="Must contain at least one number and one uppercase and lowercase letter and one special character, and at least 8 or more characters" required />
                                   <!-- <input type="text" name="new-password" id="new-password" class="form-control" minlength="8" required/>-->
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Confirm New Password</label>
                                <div class="form-line">
                                    <input type="password" name="cnf-password" id="cnf-password" class="form-control" onkeyup="Validate()" required/>
                                </div>
                            </div>
                            <button type="button" class="btn btn-primary mt-3 disabled" id="desable">Update Password</button>
                        <button type="submit" class="btn btn-primary mt-3" id="enable" style="display: none">Update Password</button>
                            
                        </form>
                    </div>
                </div>
            </div>
        </div>
</div>
<script type="text/javascript">
        function Validate(){
            var password = document.getElementById("new-password").value;
            var cnfpassword = document.getElementById("cnf-password").value;
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
