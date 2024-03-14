@if(count($trending_opinions)>0)
    @foreach($trending_opinions as $opinion)
        @include('frontend.contest.crud.leaderboard_card',['user'=>$opinion->user])
        <hr>
    @endforeach
@endif
