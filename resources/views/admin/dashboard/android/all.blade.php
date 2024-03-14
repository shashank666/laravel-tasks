@extends('admin.layouts.app')
@section('title',"Android Dashboard ALL")

@section('content')
    
    <div class="container">
        <div class="row">
            <div class="col-md-12 col-sm-12 col-12">
                    <div class="card">
                            <div class="card-header">
                                <h4 class="card-header-title">Installed Android</h4>
                            </div>
                            @if(count($model_users)>0)
	                    <input id="path" type="hidden" value="{{Request::fullUrl()}}" />
	                    <input id="totalpage" type="hidden" value="{{ceil($model_users->total()/$model_users->perPage())}}" />
	                    @endif
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
    <div class="row">
    <div class="col align-self-center">
       {{ $model_users->links('frontend.posts.components.pagination') }}  
    </div>
</div>
@endsection
