
@foreach($comments as $comment)
@include('admin.dashboard.opinion.comments.comment')
@endforeach

<div class="d-flex justify-content-between align-items-center">
        @if($comments->previousPageUrl() != null)
        <button type="button" class="float-left btn btn-link btn-sm btnloadPrev" id="{{ 'btnloadPrev'.$opinion_id }}" data-opinion="{{ $opinion_id }}" data-prevpage="{{ explode('?page=',$comments->previousPageUrl())[1] }}">Load Previous Comments</button>
        @endif

        @if($comments->nextPageUrl() != null)
        <button type="button" class="float-right btn btn-link btn-sm btnloadMore" id="{{ 'btnloadMore_'.$opinion_id }}" data-opinion="{{ $opinion_id }}" data-nextpage="{{ explode('?page=',$comments->nextPageUrl())[1] }}">Load More Comments</button>
        @endif
</div>
