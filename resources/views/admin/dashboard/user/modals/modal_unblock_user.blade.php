<div class="modal fade" id="unblock_user" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="unblock_user_label"> {{ 'Unblock (Activate) '.$user->name.' Account' }}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                        <form id="unblock_account" method="POST" action="{{ route('admin.unblock_user_account',['id'=>$user->id]) }}">
                                {{ csrf_field() }}
                                <button  class="btn btn-success btn-block" type="submit">UNBLOCK / ACTIVATE ACCOUNT</button>
                        </form>
                </div>
            </div>
        </div>
    </div>
