@if (count($errors) > 0)
  @foreach ($errors->all() as $error)
      @if($error=="Your account has been Deactivated")
      <p class="alert alert-danger fade show" style="padding: .75rem 0.25rem;" role="alert">{{ $error }}
      <button class="btn btn-sm btn-success float-right waves-effect waves-float " data-toggle="modal" href="#activateModal" style="line-height: 1;" type="button">
      Activate Now</button>
      @else
      <p class="alert alert-danger alert-dismissible fade show" role="alert">{{ $error }}
      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
      @endif
    </button>
  </p>
  @endforeach
@endif

@if (session()->has('message'))
	<p class="alert alert-success alert-dismissible fade show" role="alert">{{ session('message') }}
  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
  </p>
@endif