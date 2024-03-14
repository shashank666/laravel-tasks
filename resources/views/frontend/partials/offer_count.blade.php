<div class="card shadow-sm border-0">
    <div class="p-0 card-body d-flex flex-row align-items-center justify-content-between">
        <div class="py-2 border-right-0" style="cursor: pointer;flex:1;color: #004085;background-color: #cce5ff;border:1px solid;border-color: #b8daff;" data-toggle="tooltip" data-title="{{ $eligible_count}} articles have became eligible under Opined Introductory Offer" data-placement="right">
            <h3 class="text-center">{{$eligible_count}}</h3>
            <p  class="m-0 text-center font-weight-light">Eligible articles</p>  
        </div>
        <div  class="py-2 border-left-0"  style="cursor: pointer;flex:1;color:#fb8c00;background-color:#fff3e0;border:1px solid;border-color:#ffe0b2" data-toggle="tooltip" data-title="{{$remaining_count}} articles still remaining for Opined Introductory Offer" data-placement="left">
            <h3 class="text-center">{{$remaining_count}}</h3>
            <p  class="m-0 text-center font-weight-light">More to go</p>
           
        </div>
    </div>
</div>