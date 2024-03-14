@extends('admin.layouts.auth')
@section('title','Sucurity Key Expired')

@section('content')

<div class="container">
    <div class="row">
        <div class="col-12 col-md-10 col-xl-8 offset-xl-2 offset-md-1">
                        @include('admin.partials.header_title',['header_title'=>'Error'])
                       <h4>Security Key has been expired</h4>
                       <h5> Click on the button below to resend key</h5>
                       <br><br>
                       <form class="form-horizontal my-4 mx-3" method="POST" action="{{route('admin.resend_key')}}" enctype="multipart/form-data">
                        {{csrf_field()}}
                        <input type="hidden" id="empdata" name="empdata" class="form-control" value="{{$employee->id}}" />
                    <button class="btn btn-large" type="submit" style="color: white;background: #ff9800;border: 1px solid;border-radius: 12px;">Get key</button>

    
        </div>
    </div>
</div>

@endsection
