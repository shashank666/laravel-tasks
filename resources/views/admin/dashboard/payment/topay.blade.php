@extends('admin.layouts.app')
@section('title','Payment')

@push('styles')
<link rel="stylesheet" type="text/css" href="/public_admin/assets/libs/daterangepicker/daterangepicker.css" />
<style>
    .data-dbcount{
        display:none;
    }
</style>
@endpush

@push('scripts')
<script type="text/javascript" src="/public_admin/assets/libs/momentjs/moment.min.js"></script>
<script type="text/javascript" src="/public_admin/assets/libs/daterangepicker/daterangepicker.min.js"></script>
<script src="/public_admin/assets/libs/momentjs/moment.min.js"></script>
<script type="text/javascript" src="/public_admin/assets/libs/raphael/raphael.min.js"></script>
<script type="text/javascript" src="/public_admin/assets/libs/morrisjs/morris.js"></script>
<script type="text/javascript" src="https://code.highcharts.com/highcharts.js"></script>

<script type="text/javascript">
    $(document).on('click','.payment-done',function(){
    var id= $("#user_id").val();
    $('#user_pay').val(id);    
    $('#paymentModal').modal('show'); 

});

    $(document).on('click','.payment-info',function(){
        var name= $("#user_name").val();
        var number= $("#account_number").val();
        var ifsc= $("#account_ifsc").val();
        var type= $("#account_type").val(); 
        $('#user_account_name').val(name); 
        $('#user_account_number').val(number); 
        $('#user_account_ifsc').val(ifsc); 
        $('#user_account_type').val(type);
        
         
             
        $('#paymentDetailsModal').modal('show'); 

});
</script>

@endpush

@section('content')
@include('admin.dashboard.payment.modal.payment_modal')
@include('admin.dashboard.payment.modal.payment_details_modal')
<a href="#" id="scroll" style="display: none;"><span></span></a>
    <div class="header">
            <div class="container">
              <div class="header-body">
                <div class="row align-items-end">
                    <div class="col">
                        <h6 class="header-pretitle">
                        Overview
                        </h6>
                        <h1 class="header-title">
                        Payment Due
                        <span  class="ml-5 badge badge-primary" style="cursor:pointer" onclick="window.location.href=`/cpanel/payment/`"> RSM <a href="" style="color: #000; font-size: 75%" class="ml-2"></a></span>
                        <span  class="ml-2 badge badge-success" style="cursor:pointer" onclick="window.location.href=`/cpanel/payment/ad_analysis`"> ANALYSIS <a href="" style="color: #000; font-size: 75%" class="ml-2"></a></span>
                        </h1>
                    </div>
                   <div class="col-auto">
                    
                </div>
                </div>
              </div>
            </div>
    </div>

    <div class="container">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-12">
                <div class="card">
                    @if(count($user_earnings)>0)
                    <input id="path" type="hidden" value="{{Request::fullUrl()}}" />
                    <input id="totalpage" type="hidden" value="{{ceil($user_earnings->total()/$user_earnings->perPage())}}" />
                    @endif
                    <div class="table-responsive">
                        <table id="posts-table" class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>NAME</th>
                                    <th>MAIL</th>
                                    <th>COUNTRY CODE</th>
                                    <th>MOBILE</th>
                                    <th>PAYMENT DETAILS</th>
                                    <th>TOTAL DUES</th>
                                    <th>PAYMENT</th>
                                </tr>
                            </thead>
                            <tbody id="append-div">
                               @include('admin.dashboard.payment.topay_row')
                            </tbody>
                        </table>
                    </div>
                    @include('admin.partials.spinner')
                </div>
            </div>
        </div>
    </div>

    @if(count($user_earnings)>0)
    @include('admin.partials.loadmorescript')
    @endif

@endsection

