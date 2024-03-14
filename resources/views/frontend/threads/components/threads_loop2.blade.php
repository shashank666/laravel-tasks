@foreach($threads as $thread)
@include('frontend.threads.components.thread_card',['thread'=>$thread->thread])
@endforeach
