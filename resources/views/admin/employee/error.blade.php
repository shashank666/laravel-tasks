@extends('admin.layouts.auth')
@section('title','Wrong Details Informations')

@section('content')

<div class="container">
    <div class="row">
        <div class="col-12 col-md-10 col-xl-8 offset-xl-2 offset-md-1">
                        @include('admin.partials.header_title',['header_title'=>'Error'])
                       <h4> Your Data did not match.</h4>
                       <h5> Kindly, recheck your provided informations and try again</h5>
                       <br><br>
                    <button class="btn btn-large" onclick="goBack()" style="color: white;background: #ff9800;border: 1px solid;border-radius: 12px;">Go Back</button>

    
        </div>
    </div>
</div>
<script>
function goBack() {
  window.history.back();
}
</script>
@endsection
