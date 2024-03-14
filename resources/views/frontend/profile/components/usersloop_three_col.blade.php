@foreach($users as $user)
<div class="col-xl-4 col-lg-4 col-md-4 col-sm-6 col-12">
    @include('frontend.profile.components.minicard')
</div>
@endforeach
