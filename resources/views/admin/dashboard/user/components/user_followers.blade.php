<div class="card">
        <div class="card-header">
            <h4 class="card-header-title">Followers <span class="ml-2 badge badge-primary">{{ $data->total() }}</span></h4>
        </div>
        @if(count($data)>0)
                    <input id="path" type="hidden" value="{{Request::fullUrl()}}" />
                    <input id="totalpage" type="hidden" value="{{ceil($data->total()/$data->perPage())}}" />
        @endif
        <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th colspan="2">USER</th>
                    <th>FOLLOWED AT</th>
                    <th>STATUS</th>
                </tr>
            </thead>
            <tbody id="append-div">
                @include('admin.dashboard.user.components.user_follower_row')
            </tbody>
        </table>
        @include('admin.partials.spinner')
</div>
</div>

@if(count($data)>0)
@include('admin.partials.loadmorescript')
@endif
