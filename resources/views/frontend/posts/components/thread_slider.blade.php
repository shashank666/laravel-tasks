
    <div class="row mb-3">
        <div class="col-md-12">        
            <div id="trending_threads" class="carousel slide" data-ride="carousel">
                <div class="carousel-inner">         
                    @foreach($threads->chunk(4) as $index=>$thread)
                    <div class="carousel-item {{$index==0?'active':''}}">
                        <div class="row">
                        @php($colors=['#37474f','#ff9800','#0097a7','#5c007a','#00796b','#d32f2f','#5d4037','#827717','#689f38','#e91e63','#424242','#1565c0','#880e4f','#f57f17'])
                        
                        @foreach($thread as $item)
                        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 col-12 mb-2">
                                <div class="card h-100 box-shadow">
                                    {{-- <img class="card-img-top" <img class="img-fluid" src="{{$item->image}}" alt="{{$item->name}}" height="200">--}}
                                    <div class="card-body p-0">
                                        <a href="/thread/{{$item->name}}" style="text-decoration:none;">
                                        <h5 class="card-title text-center mb-2 mt-2" style="color:{{$colors[array_rand($colors,1)]}}">{{'#'.$item->name}}</h5>
                                    {{--<p class="card-text text-muted text-center mb-2" style="font-size:14px;">{{$item->opinionCount}} Opinions</p>  --}}    
                                        </a>
                                    </div> 
                                </div>
                        </div>
                        @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>      
    </div>     
   {{--   <div class="row mb-4 mt-2">
       <div class="col-12">
        <center>
           <a class="btn btn-outline-primary" href="#trending_threads" role="button" data-slide="prev"><i class="fas fa-arrow-left"></i></a>
           <a class="btn btn-outline-primary" href="#trending_threads" role="button" data-slide="next"><i class="fas fa-arrow-right"></i></a>
        </center>
        </div>
    </div>  --}}

   