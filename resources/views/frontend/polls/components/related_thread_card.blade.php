@php($colors=['#37474f','#ff9800','#0097a7','#5c007a','#00796b','#d32f2f','#5d4037','#827717','#689f38','#e91e63','#424242','#1565c0','#880e4f','#f57f17'])
<div class="card">
  <div class="card-body row" style="padding-top: 0.5rem;padding-bottom: 0.5rem;">
  	<div class="col-md-7 col-7">
  		<a  href="/thread/{{$thread->name}}" title="{{ '#'.$thread->name}}">
    		<h5 class="m-0 text-nowrap" style="width: 10vw;color:{{ $colors[array_rand($colors,1)] }};">{{'#'.str::limit($thread->name ,$limit = 13 , $end = '..')}}</h5></a>
    	</div>
	   
	       <div class="col-md-5 col-5 d-md-block d-sm-none d-none" style="font-size: 0.9vw">
	        	<a  href="/thread/{{$thread->name}}" title="{{ '#'.$thread->name}}"><i class="far fa-comment-alt mr-2"></i>{{ $thread->opinions_count }} Opinions</a>
	        </div>
	    
	    
	        <div class="col-md-5 col-5 d-xl-none d-lg-none d-md-none d-sm-block d-block">
	        	<a  href="/thread/{{$thread->name}}" title="{{ '#'.$thread->name}}"><i class="far fa-comment-alt mr-2"></i>{{ $thread->opinions_count }} Opinions</a>
	        </div>
	    

  </div>
</div>
