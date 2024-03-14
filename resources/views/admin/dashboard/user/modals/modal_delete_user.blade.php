<div class="modal fade" id="delete_user" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="delete_user_label"> {{ 'Delete '.$user->name.' Account' }}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Are You Sure To Delete {{ $user->name }} Account ? </p>
                    <p>This will permenently delete all posts,opinions,likes,comments,bookmarks and other releted content of {{ $user->name }} form Database.</p>
                    <p>You can see {{ $user->name }} entry in Deleted Accounts.</p>
                    <form id="delete_account" method="POST" action="{{ route('admin.delete_user_account',['id'=>$user->id]) }}">
                            {{ csrf_field() }}
                        <button  class="btn btn-danger btn-block" type="submit">DELETE ACCOUNT</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
