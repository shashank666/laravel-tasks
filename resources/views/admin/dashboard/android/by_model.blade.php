@extends('admin.layouts.app')
@section('title',"Android Dashboard ".ucfirst($current_brand)." ".ucfirst($current_model))

@section('content')
    
    <div class="container">
        <div class="row">
            <div class="col-md-12 col-sm-12 col-12">
                    <div class="card">
                            <div class="card-header">
                                <h4 class="card-header-title">Installed {{ucfirst($current_brand)}} {{ucfirst($current_model)}}</h4>
                            </div>

                                <div class="table-responsive">
			                        <table class="table">
			                            <thead>
			                                <tr>
			                                    <th>USER</th>
			                                    <th>APK VERSION</th>
			                                    <th>OS</th>
			                                    <th>VERSION</th>
			                                    <th>MOBILE</th>
			                                    <th>LOCATION</th>
			                                    <th>REGISTERD AT</th>
			                                </tr>
			                            </thead>
			                            <tbody id="append-div">
			                            @include('admin.dashboard.android.components.user_row')
			                            </tbody>
			                        </table>
			                    </div>
			                    @include('admin.partials.spinner')
                    </div>
            </div>
            
            
        </div>
    </div>
@endsection
