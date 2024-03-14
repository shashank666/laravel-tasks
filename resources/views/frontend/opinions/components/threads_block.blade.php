
<div class="row">
  @php($colors=['#495057','#495057','#495057','#495057','#495057','#495057','#495057','#495057','#495057','#495057','#495057','#495057','#495057','#495057'])
    @foreach($threads as $index=>$thread_with_count)
    @if(($thread_with_count->thread->opinions_count)>0)
    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-6 col-6 {{ $index == 8 ? 'd-lg-inline d-md-none d-none':''}}" style="padding-left:8px;padding-right:8px;">
    <a  href="/thread/{{$thread_with_count->thread->name}}" title="{{ '#'.$thread_with_count->thread['name']}}">
      <div class="thread_card card bg-light mb-1 p-2 pl-3 shadow-sm" style="border:0px; background: white !important;">
          <h6 class="text-truncate" style="color:{{$colors[array_rand($colors,1)]}}">{{'#'.$thread_with_count->thread->name}}</h4>

          <span class="text-secondary"><small><i class="far fa-comment-alt mr-2"></i>{{ $thread_with_count->thread->opinions_count }} Opinions</small></span>
          </a>
           <!-- @if(Auth::guest())
                <button class="btn btn-sm btn-outline-primary" onclick="openLoginModal();">Follow Thread<i class="fas fa-user-plus ml-2"></i></button>
            
            @endif-->

      </div>
            
    
  </div>
  @endif
  @endforeach
</div>


