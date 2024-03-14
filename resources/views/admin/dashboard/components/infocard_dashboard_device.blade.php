<div class="card">
    <div class="card-body">
    <a href="{{ route($route_name) }}">
        <div class="row align-items-center">
            <div class="col">
                <h6 class="card-title text-uppercase text-muted mb-2">
                    {{ $title }}
                </h6>
                <span class="h2 mb-0">
                    {{ $total }}
                </span>
            </div>
            <div class="col-auto">
                <i class="{{ $icon_class }} text-muted mb-0"></i>
            </div>
        </div>
    </a>

        <hr/>
        <!--<a href="{{ route($route_name,['platform'=>'website','is_active'=>1]) }}" class="text-dark">-->
        <div class="row align-items-center">
            <div class="col">
                 <p class="mb-2"><span class="mr-2"><i class="fa fa-bolt" aria-hidden="true"></i></span> Today <span class="ml-2 badge badge-success p-2"> </span></p>
            </div>
            <div class="col-auto">
                {{ $today_device }}
            </div>
        </div>
        </a>
       <!-- <a href="{{ route($route_name,['platform'=>'android','is_active'=>1]) }}" class="text-dark">-->
        <div class="row align-items-center">
            <div class="col">
                <p class="mb-2"><span class="mr-2"><i class="fa fa-calendar" aria-hidden="true"></i></span> Yesterday <span class="ml-2 badge badge-success p-2"> </span></p>
            </div>
            <div class="col-auto">
                {{ $yesterday_device }}
            </div>
        </div>
        </a>
        {{--<hr/>
        <a href="{{ route($route_name,['is_active'=>1]) }}" class="text-dark">
        <div class="row align-items-center">
            <div class="col">
                 <p class="mb-2"><span class="badge badge-success p-2"> </span> Active </p>
            </div>
            <div class="col-auto">
                    {{ $active }}
            </div>
        </div>
        </a>
        <a href="{{ route($route_name,['is_active'=>0]) }}" class="text-dark">
        <div class="row align-items-center">
            <div class="col">
                 <p class="mb-2"><span class="badge badge-danger p-2"> </span> Disabled </p>
            </div>
            <div class="col-auto">
                    {{ $disabled }}
            </div>
        </div>
        </a>--}}
    </div>
</div>
