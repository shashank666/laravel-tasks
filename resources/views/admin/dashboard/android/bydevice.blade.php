@extends('admin.layouts.app')
@section('title',"Android Dashboard ".ucfirst($current_brand))

@section('content')
    @include('admin.partials.android_header_title',['header_title'=>'Android Dashboard','subtitle'=>$current_brand,'total'=>$count['users_installed_app']])
    <div class="container">
    
        <div class="row">
            <div class="col-md-3 col-sm-6 col-12">
                @include('admin.dashboard.components.infocard',[
                    'title'=>'Users Installed App',
                    'value'=>$count['users_installed_app'],
                    'icon_class'=>'fas fa-download'
                ])
            </div>
            <div class="col-md-3 col-sm-6 col-12">
                @include('admin.dashboard.components.infocard',[
                    'title'=>'Devices Registered',
                    'value'=>$count['total_devices'],
                    'icon_class'=>'fas fa-mobile-alt'
                ])
            </div>
            <div class="col-md-3 col-sm-6 col-12">
                @include('admin.dashboard.components.infocard',[
                    'title'=>'Installed In 30 days',
                    'value'=>$count['users_installed_app_30days'],
                    'icon_class'=>'fab fa-android'
                ])
            </div>
            <div class="col-md-3 col-sm-6 col-12">
                    @include('admin.dashboard.components.infocard',[
                        'title'=>'Logins in 30 Days',
                        'value'=>$count['user_logins_30days'],
                        'icon_class'=>'fas fa-sign-in-alt'
                    ])
            </div>
        </div> 
        <div class="row">
            {{--<div class="col-md-3 col-sm-6 col-12">
                    <div class="card">
                            <div class="card-header">
                                <h4 class="card-header-title">Top Device Models</h4>
                            </div>
                            <div class="card-body">
                                <ul class="list-group list-group-flush">
                                    @foreach($top_models as $top_model)
                                    <li class="list-group-item">
                                        {{ strtoupper($top_model->device_model) }}
                                        <span class="float-right">{{ $top_model->total }}</span>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                    </div>
            </div>--}}
            <div class="col-md-3 col-sm-3 col-12">
                    <div class="card">
                            <div class="card-header">
                                <h4 class="card-header-title">Installed {{ucfirst($current_brand)}}'s Model</h4>
                            </div>

                                <table class="table table-hover">
                                    <thead>
                                    <tr>
                                        <th>Model</th>
                                        <th>Installs</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($top_models as $top_model)
                                        <tr>

                                            <td><a href="{{route('admin.android_device_model',['brand'=>$current_brand,'model'=>$top_model->device_model])}}">{{ $top_model->device_model }}</a></td>
                                            <td>{{ $top_model->total }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                    </div>
            </div>
            <div class="col-md-6 col-sm-6 col-12">
                    <div class="card">
                            <div class="card-header">
                                <h4 class="card-header-title">Installs by OS Version</h4>
                            </div>

                                <table class="table table-hover">
                                    <thead>
                                    <tr>
                                        <th>OS Name</th>
                                        <th>OS Version</th>
                                        <th>Installs</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($os_version_installs as $os_install)
                                        <tr>
                                            <td>{{ $os_install->device_os_name }}</td>
                                            <td>{{ $os_install->device_os_version }}</td>
                                            <td>{{ $os_install->total }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                    </div>
            </div>
            <div class="col-md-3 col-sm-6 col-12">
                    <div class="card">
                            <div class="card-header">
                                <h4 class="card-header-title">Installs by APK Version</h4>
                            </div>
                            <div class="card-body">
                                <ul class="list-group list-group-flush">
                                    @foreach($app_version_installs as $install)
                                    <li class="list-group-item">
                                        {{ $install->app_version==null?'-':$install->app_version }}
                                        <span class="float-right">{{ $install->total }}</span>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                    </div>
            </div>
        </div>
    </div>
@endsection
