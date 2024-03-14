@if(count($short_opinions)>0)
    @foreach($short_opinions as $opinion)
        @include('frontend.opinions.components.profile_opinion_card',['user'=>$opinion->user])
    @endforeach
@endif
