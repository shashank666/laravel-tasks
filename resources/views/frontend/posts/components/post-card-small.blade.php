<div class="card h-100 shadow-sm">
    <a href="/opinion/{{$smallpost->slug}}"><img class="card-img-top"  src="{{$smallpost->coverimage}}"  onerror="this.onerror=null;this.src='/img/noimg.png';" alt="{{ $smallpost->title}}" height="200"width="700"></a>
    <div class="card-body">
        <h4 class="card-title">
            <a class="post_title" href="/opinion/{{$smallpost->slug}}">{!!str::limit($smallpost->title,$limit = 80 , $end = '...')!!}</a>
        </h4>
        <small class="d-flex  flex-sm-row flex-column justify-content-start text-secondary pb-2">
            <span data-toggle="tooltip" data-placement="top" title="published on {{$smallpost->created_at}}" class="mr-3"><i class="far fa-calendar-alt mr-2"></i>{{$smallpost->created_at}}</span>
            <span data-toggle="tooltip" data-placement="top" title="{{$smallpost->views}} Views"><i  class="fas fa-eye mr-2"></i>{{$smallpost->views}}</span>
        </small>
    </div>
    <div class="card-footer bg-white">
        <div class="media align-items-center">
                <a class="mr-2" href="{{ route('user_profile',['username' =>$smallpost->user->username])}}"><img class="rounded-circle" src="{{$smallpost->user->image}}" height="40" width="40" alt="Go to the profile of {{ucfirst($smallpost->user->name)}}"  onerror="this.onerror=null;this.src='/img/avatar.png';"></a>
                <div class="media-body">
                    <div class="d-flex justify-content-between align-items-center w-100">
                            <a href="{{ route('user_profile',['username' =>$smallpost->user->username])}}" style="color:#212121;">{{ucfirst($smallpost->user->name)}}</a>
                    </div>
                </div>
        </div>
    </div>
</div>
