@foreach($users as $user)
<div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 col-12">
        @include('frontend.profile.components.minicard')
</div>
@endforeach
