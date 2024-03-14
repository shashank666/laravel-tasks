<div class="card d-flex flex-md-row mb-4 h-md-200 shadow mb-4 postcard" id="post-{{$post->id}}">
    <img class="card-img-left flex-auto d-md-block" src="{{$post->coverimage}}" alt="{{$post->title}}" height="220" width="250"/>
    <div class="card-body d-flex flex-column align-items-start">
        <h3 class="mb-1">
            <a class="post_title" href="/opinion/{{$post->slug}}">{{ str::limit($post->title,45,'...')}}</a>
        </h3>
               
        <small class="d-flex flex-sm-row flex-column justify-content-start text-secondary mb-2">
                <span data-toggle="tooltip" data-placement="top" title="published on {{$post->created_at}}" class="mr-3"><i class="far fa-calendar-alt mr-2"></i>{{$post->created_at}}</span>    
        </small>

    <div class="d-flex flex-md-row flex-column  justify-content-start mt-2">
        <div class="d-flex flex-row">
                <div class="stats-box rounded stats-views">
                        <i class="stats-icon fas fa-eye"></i>
                        <p class="stats-text">Views</p>
                        <h5 class="stats-total-count">{{$post->ViewsCount}}</h5>
                    </div>
                    <div class="stats-box rounded stats-likes">
                            <i class="stats-icon far fa-thumbs-up"></i>
                            <p class="stats-text">Likes</p>
                            <h5  class="stats-total-count">{{$post->LikesCount}}</h>
                    </div>
        </div>


    <div  class="d-flex flex-row">
                <div class="stats-box rounded stats-comments">
                        <i class="stats-icon far fa-comment"></i>
                        <p class="stats-text">Comments</p>
                        <h5  class="stats-total-count">{{$post->CommentsCount}}</h5>
                </div>

              {{--        @if($post->plagiarism_checked==0)
                <div class="stats-box rounded stats-plagarism stats-plagarism-yellow" data-toggle="tooltip" data-placement="top" title="Plagiarism test result is not available right now for this article .">
                        <i class="stats-icon fas fa-exclamation-circle"></i>
                        <p class="stats-text">Plagiarism</p>
                        <h5  class="stats-total-count"> - </h5>      
                </div>
                @else
                <div class="stats-box rounded stats-plagarism {{ $post->is_plagiarized==1 ?'stats-plagarism-red':'stats-plagarism-green' }}"  data-toggle="tooltip" data-placement="top" title="Your article contains {{  $post->plagiarism_percentage.' %'  }} plagiarism.">
                        @if($post->is_plagiarized==1)
                        <i class="stats-icon fas fa-exclamation-circle"></i>
                        @else
                        <i class="stats-icon fas fas fa-check"></i>
                        @endif
                        <p class="stats-text">Plagiarism</p>
                        <h5  class="stats-total-count">{{ $post->plagiarism_percentage.' %' }}</h5>
                </div>
                @endif
                 --}}
        </div> 


        
    </div>

    </div>
</div>
