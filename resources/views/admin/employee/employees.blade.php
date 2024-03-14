@extends('admin.layouts.app')
@section('title','Employees')

@push('styles')
<link href="/public_admin/assets/libs/morrisjs/morris.css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="/public_admin/assets/libs/daterangepicker/daterangepicker.css" />
@endpush

@push('scripts')
<script type="text/javascript" src="/public_admin/assets/libs/momentjs/moment.min.js"></script>
<script type="text/javascript" src="/public_admin/assets/libs/daterangepicker/daterangepicker.min.js"></script>
<script type="text/javascript" src="/public_admin/assets/libs/raphael/raphael.min.js"></script>


<script>
$(document).ready(function(){
    
    $(".msg").fadeIn(2000);
    $(".msg").fadeOut(5000);
  
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
                    Overview
                    </h6>
                    <h1 class="header-title">
                    Employees
                    
                    </h1>
                </div>
                <div class="msg">@include('admin.partials.message')</div>
                
                <div class="col-auto">
                    <a href="{{route('admin.EmployeeForm')}}"  class="btn btn-primary lift">
                      Create New
                    </a>
                </div>
            </div>
          </div>
        </div>
</div>
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="card">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>IMAGE</th>
                                    <th>NAME</th>
                                    <th>EMAIL</th>
                                    <th>COMPANY EMAIL</th>
                                    <th>MOBILE</th>
                                    <th>DATE OF BIRTH</th>
                                    <th>POSITION</th>
                                    <th>JOINING DATE</th>
                                    <th>RELEASE DATE</th>
                                    <th>PANEL</th>
                                    <th>CREATED AT</th>
                                    <th colspan="3">ACTION</th>

                                    
                                </tr>
                            </thead>
                            <tbody id="append-div">
                            @include('admin.employee.employee_row')
                            </tbody>
                        </table>
                    </div>
                   
            </div>
        </div>
    </div>
</div>


@endsection
