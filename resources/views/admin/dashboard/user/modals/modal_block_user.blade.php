<div class="modal fade" id="block_user" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="block_user_label"> {{ 'Select a Reason To Block '.$user->name.' Account' }}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                    <form id="block_account" method="POST" action="{{ route('admin.block_user_account',['id'=>$user->id]) }}">
                            {{ csrf_field() }}
                            <p>
                                <div class="custom-control custom-radio">
                                <input name="reason" value="SUSPICIOUS ACTIVITY" type="radio" id="r1" class="custom-control-input" checked="">
                                <label class="custom-control-label" for="r1">SUSPICIOUS ACTIVITY</label>
                                </div>
                            </p>
                            <p>
                                <div class="custom-control custom-radio">
                                <input name="reason" value="OPINED POLICY VIOLATIONS" type="radio" id="r2" class="custom-control-input">
                                <label class="custom-control-label" for="r2">OPINED POLICY VIOLATIONS</label>
                                </div>
                            </p>
                            <p>
                                <div class="custom-control custom-radio">
                                <input name="reason" value="COPYRIGHT / LEGAL VIOLATION" type="radio" id="r3" class="custom-control-input">
                                <label class="custom-control-label" for="r3">COPYRIGHT / LEGAL VIOLATION</label>
                                </div>
                            </p>
                            <p>
                                <div class="custom-control custom-radio">
                                <input name="reason" value="PORNOGRAPHY OR NUDITY CONTENT" type="radio" id="r4" class="custom-control-input">
                                <label class="custom-control-label" for="r4">PORNOGRAPHY OR NUDITY CONTENT</label>
                                </div>
                            </p>
                            <p>
                                <div class="custom-control custom-radio">
                                <input name="reason" value="VIOLENT CONTENT" type="radio" id="r5" class="custom-control-input">
                                <label class="custom-control-label" for="r5">VIOLENT CONTENT</label>
                                </div>
                            </p>
                            <p>
                                <div class="custom-control custom-radio">
                                <input name="reason" value="HATEFUL OR VIOLENT TOWARDS A GROUP" type="radio" id="r5" class="custom-control-input">
                                <label class="custom-control-label" for="r5">HATEFUL OR VIOLENT TOWARDS A GROUP</label>
                                </div>
                            </p>
                            <p>
                            <div class="custom-control custom-radio">
                                <input name="reason" value="DANGEROUS THREATS FOUND" type="radio" id="r6" class="custom-control-input">
                                <label class="custom-control-label" for="r6">DANGEROUS THREATS FOUND</label>
                            </div>
                            </p>
                            <button  class="btn btn-dark btn-block" type="submit">BLOCK / DEACTIVATE ACCOUNT</button>
                    </form>
            </div>
        </div>
    </div>
</div>
