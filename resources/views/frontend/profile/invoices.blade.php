@extends('frontend.layouts.app')
@section('title', 'Your Invoices - Opined')
@section('description','View the statistics for all opinons you write on Opined.')
@section('keywords','Stats')

@push('meta')
<link rel="canonical" href="https://www.weopined.com/me/performance" />
<link href="https://www.weopined.com/me/performance" rel="alternate" reflang="en" />


<!-- Twitter Card data -->
<meta name="twitter:card" content="summary">
<meta name="twitter:site" content="@weopined">
<meta name="twitter:title" content="Your Article's Performance  - Opined">
<meta name="twitter:description" content="View the statistics for all opinons you write on Opined.">
<meta name="twitter:creator" content="@weopined">
<meta name="twitter:image" content="https://www.weopined.com/favicon.png">

<!-- Open Graph data -->
<meta property="og:title" content="Your Article's Performance  - Opined"/>
<meta property="og:type" content="website" />
<meta property="og:url" content="https://www.weopined.com/me/performance" />
<meta property="og:image" content="https://www.weopined.com/favicon.png" />
<meta property="og:description" content="View the statistics for all opinons you write on Opined." />
<meta property="og:site_name" content="Opined" />
<meta property="og:locale" content="en_US" />
<meta property="fb:app_id" content="1766000746745688" />
@endpush

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



@endpush

@section('content')
@include('frontend.profile.modals.auth_test')
<h3 class="mt-2">Invoices</h3>

    <div class="container">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-12">
                <div class="card">

                <div class="card-header">
                    <div class="row mt-3" style="text-align: center;">
                            <div class="col-lg-3">
                                    <div class="form-group">
                                            <a href="article_performance" class="btn btn-primary">Article Performance</a>
                                    </div>
                            </div>
                            <div class="col-lg-3">
                                    <div class="form-group">
                                            
                                            <button class="btn btn-success" data-toggle="modal" data-target="#authTestModal">Payment Details</button>
                                            
                                    </div>
                            </div>
                            <div class="col-lg-3">
                                    <div class="form-group">
                                       <span  class=" badge badge-primary" style="padding: 10px; font-size: 1.0rem;font-weight: 400;">Total Earning: $ {{$user_earning->total_earning}}</span>
                    
                                    </div>
                            </div>
                            <div class="col-lg-3">
                                    <div class="form-group">
                                      <span  class="badge badge-success" style="padding: 10px; font-size: 1.0rem;font-weight: 400;">Total Dues: $ {{$user_earning->total_earning - $user_earning->total_paid}}</span>
                    
                                    </div>
                            </div>
                        </div>
                    </div>
        
                    @if(count($user_invoices)>0)
                    <input id="path" type="hidden" value="{{Request::fullUrl()}}" />
                    <input id="totalpage" type="hidden" value="{{ceil($user_invoices->total()/$user_invoices->perPage())}}" />
                    @endif
                    <div class="table-responsive">
                        <table id="posts-table" class="table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>NAME</th>
                                    <th>PAYMENT</th>
                                    <th>CHECK INVOICE</th>
                                    <th>DATE</th>
                                </tr>
                            </thead>
                            <tbody id="append-div">
                               @include('frontend.profile.components.invoices_row')
                            </tbody>
                        </table>
                    </div>
                    @include('admin.partials.spinner')
                </div>
            </div>
        </div>
    </div>

    @if(count($user_invoices)>0)
    @include('admin.partials.loadmorescript')
    @endif

@endsection

