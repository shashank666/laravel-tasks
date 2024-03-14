<div style="height:240px;left:-32px;">
        @php($colors=['#37474f','#ff9800','#0097a7','#5c007a','#00796b','#d32f2f','#5d4037','#827717','#689f38','#e91e63','#424242','#1565c0','#880e4f','#f57f17'])
        <ul id="marquee-vertical" style="width:100%;">
          @foreach($threads->chunk(3) as $index=>$thread)
          <li style="list-style-type:none;display:block;padding-top:8px;padding-bottom:8px;">
              @foreach($thread as $item)
              <div class="mr-2 d-xl-inline d-lg-inline d-md-inline d-block" style="text-align:center;">
                <a href="/thread/{{$item->name}}" style="text-decoration:none;font-size:20px;color:{{$colors[array_rand($colors,1)]}}" class="badge badge-light d-xl-inline d-lg-inline d-md-inline d-block">
                    <span>{{'#'.$item->name}}</span>
                </a>
              </div>  
              @endforeach
          </li>  
        @endforeach
        <ul>
</div>