@extends('admin.layouts.auth')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
          <div class="col-12 my-5">
            <div class="text-center">
              <h6 class="text-uppercase text-muted mb-4">500 error</h6>
              <h1 class="display-4 mb-3">Internal Server Error 😭</h1>
              <p class="text-muted mb-4">Looks like something went wrong from backend side </p>
              <br/>
              <div class="jumbotron">
                <p class="text-justify">
                {{ $message }}
                </p>
              </div>

              <a href="{{route('admin.dashboard')}}" class="btn btn-lg btn-primary">Return to your dashboard</a>
            </div>
          </div>
        </div>
    </div>
@endsection
