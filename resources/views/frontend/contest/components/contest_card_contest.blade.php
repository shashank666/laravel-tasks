@if(($opinion->opinions_count)>0)
 		<div class="card shadow-sm bg-white mb-2">
		<div class="card-body d-flex flex-row justify-content-between align-items-center" style="margin-top: -3%;margin-bottom: -3%;">
		    <div class="media-body">
		        <a  href="/contest/{{$contest->title}}" title="{{ '#'.$contest->hash_tags}}">
		                <h5 class="text-truncate m-0 text-nowrap" style="width: 11rem;">{{'@'.$opinion->$user['username'}}</h5>
		                <h5 class="text-truncate m-0 text-nowrap" style="width: 11rem;">{{'#'.$opinion->hash_tags}}</h5>
		                <div class="text-secondary"><small><i class="far fa-file-alt mr-2"></i>{{ $opinion->plain_body }}</small></div>
		        </a>
		    </div>
		</div>
		</div>
@endif