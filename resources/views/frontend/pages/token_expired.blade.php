@extends('frontend.layouts.app')
@section('title','Login session has expired  - Opined')

@section('content')
<div class="row mt-20 mb-20">
    <div class="col-12">
    <div class="jumbotron">
        <div class="container">
                <h1 class="display-3">Login session has expired. :(</h1>
                <p>Seems you could not responded on site form a long time. Please try again</p>
                <p><a class="btn btn-outline-primary btn-lg" href="/" role="button">Goback to Homepage</a></p>
        </div>
    </div>
    </div>
</div>
@endsection