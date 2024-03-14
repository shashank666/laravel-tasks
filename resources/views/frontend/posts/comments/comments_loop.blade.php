@foreach($comments as $comment)
@include('frontend.posts.comments.comment')
@endforeach
@if($comments->nextPageUrl() != null)
<button type="button" class="btn btn-primary btn-sm btn-block" id="btnloadMore" data-nextpage="{{ explode('?page=',$comments->nextPageUrl())[1] }}">Load More Comments</button>
@endif
