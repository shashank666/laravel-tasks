@if (count($errors) > 0)
  @foreach ($errors->all() as $error)
    <p class="alert alert-danger alert-dismissible fade show" role="alert">{{ $error }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
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
