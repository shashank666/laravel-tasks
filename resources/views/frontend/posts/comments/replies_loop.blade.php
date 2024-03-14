@foreach($replies as $comment)
@include('frontend.posts.comments.comment')
@endforeach
@if($replies->nextPageUrl() != null)
<button type="button" class="mb-3 btn btn-primary btn-sm btn-block btnloadMoreReplies" id="btnloadMore_{{ $parent_id }}" data-parentid="{{ $parent_id }}" data-nextpage="{{ explode('?page=',$replies->nextPageUrl())[1] }}">Load More Replies</button>
@endif
