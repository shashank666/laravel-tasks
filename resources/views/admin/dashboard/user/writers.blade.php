@extends('admin.layouts.app')
@section('title','Writers')


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
                     Registerd Writers
                     <span class="badge badge-success ml-2">{{$writers->total()}}</span>
                    </h1>
                </div>
               <div class="col-auto">
                    <a href="{{ route('admin.writers_download',['format'=>'excel']) }}" class="btn btn-success">Download EXCEL</a>
                    <a href="{{ route('admin.writers_download',['format'=>'csv']) }}" class="btn btn-primary">Download CSV</a>
                    <a href="{{ route('admin.writers_download',['format'=>'json']) }}" class="btn btn-warning">Download JSON</a>
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
                                <table class="table table-hover">
                                <thead>
                                    <tr> 
                                        <th>Account Holder</th> 
                                        <th>Bank Name</th>
                                        <th>Account No.</th>
                                        <th>IFSC Code</th>
                                        <th>Acc. Type</th>
                                        <th>Address</th>
                                        <th>City , State</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($writers as $writer)
                                    <tr>   
                                        <td>
                                            <p>{{ $writer->account_holdername }}</p>
                                            <p>{{  $writer->user_email}}</p>
                                            <p>{{ $writer->mobile }}</p>
                                        </td>
                                        <td>{{ $writer->bank_name }}</td>
                                        <td>{{ $writer->account_no }}</td>
                                        <td>{{ $writer->bank_ifsc_code }}</td>  
                                        <td>
                                            <span class="badge {{ $writer->account_type=='saving'?'badge-success':'badge-primary' }}">{{ $writer->account_type }}</span>                  
                                        </td>
                                        <td>{{ $writer->address }}</td>
                                        <td>{{ $writer->city}}<br/>{{ $writer->state  }}</td>                    
                                    </tr>   
                                    @endforeach
                                </tbody>
                                </table>
                                </div>
                            <div class="card-footer d-flex justify-content-center align-items-center">
                                {{ $writers->links('admin.partials.pagination') }}
                            </div>
                    </div>
            </div>
    </div>
</div>
@endsection