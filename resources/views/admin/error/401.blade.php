@extends('admin.layouts.auth')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
          <div class="col-12 col-md-5 col-xl-4 my-5"> 
            <div class="text-center">
              <h6 class="text-uppercase text-muted mb-4">401 error</h6>
              <h1 class="display-4 mb-3">Access Denide</h1>             
              <p class="text-muted mb-4">Looks like you trying to access routes which are not available to you</p>
              <a href="{{route('admin.dashboard')}}" class="btn btn-lg btn-primary">Return to your dashboard</a>
            </div>
          </div>
        </div> 
    </div>
@endsection
