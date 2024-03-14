<style type="text/css">
    .nav-pills .nav-link.active, .nav-pills .show>.nav-link {
    color: #fff !important;
    background-color: #495057;
}
.nav-tabs .nav-link:hover {
    border-color: #ff9800 #ff98006b #ff98006b;
</style>
<ul class="nav nav-tabs nav-justified nav-pills mb-2">
        <li class="nav-item">
            <a class="nav-link {{$section=='trending'?'active':''}}" href="/thread/{{$thread->name}}/trending" style="padding: 0px 0px 2px 0px;font-size: 80%; color: #495057">Trending Opinions</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{$section=='latest'?'active':''}}" href="/thread/{{$thread->name}}" style="padding: 0px 0px 2px 0px;font-size: 80%;color: #495057">Latest Opinions</a>
        </li>
        @if(Auth::user())
        <li class="nav-item">
            <a class="nav-link {{$section=='circle'?'active':''}}" href="/thread/{{$thread->name}}/circle" style="padding: 0px 0px 2px 0px;font-size: 80%;color: #495057">Opinions From Circle</a>
        </li>
        @endif
</ul>
  
  
