<div class="card">
        <div class="card-header">
            <h4 class="card-header-title">Followed Category <span class="ml-2 badge badge-primary">{{ count($data) }}</span></h4>
        </div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th colspan="2">CATEGORY</th>
                        <th>CATEGORY FOLLOWED AT</th>
                        <th>STATUS</th>
                    </tr>
                </thead>
                <tbody>
                 @foreach($data as $category_follow)
                 <tr>
                    <td><img src="{{ $category_follow->category['image'] }}" height="100" width="100" class="rounded"/></td>
                    <td>{{ '#'.$category_follow->category['id'].' - '.$category_follow->category['name'] }}</td>
                    <td>{{ \Carbon\Carbon::parse($category_follow->created_at)->format('l, j M Y , h:i:s A') }}</td>
                    <td><span class="badge {{ $category_follow->is_active==1?'badge-success':'badge-danger' }}">{{ $category_follow->is_active==1?'Active':'Disabled' }}</span></td>
                 </tr>
                 @endforeach
                </tbody>
            </table>
        </div>
</div>
