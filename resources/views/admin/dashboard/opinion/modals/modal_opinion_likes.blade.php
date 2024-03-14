<div class="modal fade" id="likesOpinionModal" tabindex="-1" role="dialog" aria-labelledby="likesOpinionModal" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content" style="border:0px;">
                <div class="modal-header" style="background-color:#244363;color:#fff;">
                    <h5 class="modal-title text-white mx-auto" id="likesModal_title">People Who Liked This Opinion</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-left:0;">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                        @include('frontend.partials.loader')
                        <div class="opinion_likes">

                        </div>                               
                </div>
            </div>
        </div>
    </div>