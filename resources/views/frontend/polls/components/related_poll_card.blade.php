<div class="card">
  
  <div class="card-body row" style="padding-top: 0.5rem;padding-bottom: 0.5rem;">
  	<div class="col-md-9 col-9">
    <h6 class="card-title" onclick="window.location.href='{{ '/polls/'.$poll->slug}}'" style="cursor: pointer;">{!! $poll->title !!}
    </h6>
</div>
    <div class="col-md-3 col-3" style="text-align: right;">
    <a href="{{ '/polls/'.$poll->slug}}" class="btn btn-sm" style="background-color: #ff9800; color: #fff">Vote <i class="fas fa-paper-plane ml-2" style="color: #244363"></i></a>
    </div>
  </div>
</div>