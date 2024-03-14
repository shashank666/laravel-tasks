<div class="card">
        <div class="card-header">
            <h4 class="card-header-title">Commented on Post <span class="ml-2 badge badge-primary">{{ $data->total() }}</span></h4>
        </div>
        @if(count($data)>0)
                    <input id="path" type="hidden" value="{{Request::fullUrl()}}" />
                    <input id="totalpage" type="hidden" value="{{ceil($data->total()/$data->perPage())}}" />
        @endif
        <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                        <th colspan="2">POST</th>
                        <th>COMMENT</th>
                        <th>MEDIA</th>
                        <th>COMMENTED AT</th>
                        <th>STATUS</th>
                </tr>
            </thead>
            <tbody>
                @include('admin.dashboard.user.components.user_post_comment_row')
            </tbody>
        </table>
    </div>
</div>

@if(count($data)>0)
@include('admin.partials.loadmorescript')
@endif
