<div class="card">
        <div class="card-header">
            <h4 class="card-header-title">Stats</h4>
        </div>
        <div class="table-responsive">
        <table class="table">
            <thead>
            <tr><th>STATISTICS</th><th>ACTIVE</th><th>DISABLED</th></tr>
            </thead>
            <tbody>
                <tr onclick="window.location.href='/cpanel/user/{{ $user->id }}?tab=posts'" style="cursor:pointer"><th>POST PUBLISHED</th><td>{{ $count['post_published_active'] }}</td><td>{{ $count['post_published_disabled'] }}</td></tr>
                <tr onclick="window.location.href='/cpanel/user/{{ $user->id }}?tab=drafts'" style="cursor:pointer"><th>POST DRAFTS</th><td>{{ $count['post_drafts_active'] }}</td><td>{{ $count['post_drafts_disabled'] }}</td></tr>
                <tr onclick="window.location.href='/cpanel/user/{{ $user->id }}?tab=likes'" style="cursor:pointer"><th>POST LIKED</th><td>{{ $count['post_likes_active'] }}</td><td>{{ $count['post_likes_disabled'] }}</td></tr>
                <tr onclick="window.location.href='/cpanel/user/{{ $user->id }}?tab=post_comments'" style="cursor:pointer"><th>COMMENTED ON POSTS</th><td>{{ $count['post_comments_active'] }}</td><td>{{ $count['post_comments_disabled'] }}</td></tr>
                <tr onclick="window.location.href='/cpanel/user/{{ $user->id }}?tab=bookmarks'" style="cursor:pointer"><th>POST BOOKMARKS</th><td>{{ $count['post_bookmarks_active'] }}</td><td>{{ $count['post_bookmarks_disabled'] }}</td></tr>
                <tr onclick="window.location.href='/cpanel/user/{{ $user->id }}?tab=opinions'" style="cursor:pointer"><th>OPINIONS GIVEN</th><td>{{ $count['opinion_active'] }}</td><td>{{ $count['opinion_disabled'] }}</td></tr>
                <tr onclick="window.location.href='/cpanel/user/{{ $user->id }}?tab=opinion_likes'" style="cursor:pointer"><th>OPINIONS LIKES</th><td>{{ $count['opinion_likes_active'] }}</td><td>{{ $count['opinion_likes_disabled'] }}</td></tr>
                <tr onclick="window.location.href='/cpanel/user/{{ $user->id }}?tab=opinion_comments'" style="cursor:pointer"><th>COMMENTED ON OPINIONS</th><td>{{ $count['opinion_comments_active'] }}</td><td>{{ $count['opinion_comments_disabled'] }}</td></tr>
                <tr onclick="window.location.href='/cpanel/user/{{ $user->id }}?tab=followers'" style="cursor:pointer"><th>FOLLOWERS</th><td>{{ $count['followers_active'] }}</td><td>{{ $count['followers_disabled'] }}</td></tr>
                <tr onclick="window.location.href='/cpanel/user/{{ $user->id }}?tab=followings'" style="cursor:pointer"><th>FOLLOWINGS</th><td>{{ $count['followings_active'] }}</td><td>{{ $count['followings_disabled'] }}</td></tr>
                <tr onclick="window.location.href='/cpanel/user/{{ $user->id }}?tab=category'" style="cursor:pointer"><th>CATEGORY FOLLOWED</th><td>{{ $count['category_followed_active'] }}</td><td>{{ $count['category_followed_disabled'] }}</td></tr>
               {{--  <tr onclick="window.location.href='/cpanel/user/{{ $user->id }}?tab='" style="cursor:pointer"><th>THREADS LIKES</th><td></td><td></td></tr>
                <tr onclick="window.location.href='/cpanel/user/{{ $user->id }}?tab='" style="cursor:pointer"><th>THREADS FOLLOWED</th><td></td><td></td></tr> --}}
            </tbody>
        </table>
    </div>
</div>