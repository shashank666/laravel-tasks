<div class="modal fade" id="addCommentModal" tabindex="-1" role="dialog" aria-labelledby="addCommentModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content" style="border:0px;">
        <div class="modal-header" style="background-color:#244363;color:#fff;">
          <h5 class="modal-title" id="addCommentModalLabel"></h5>
          <button type="button" class="close" aria-label="Close" data-dismiss="modal">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
                <form id="add_comment_form" action="/comments/create"  method="post" enctype="multipart/form-data">
                    {{ csrf_field() }}
                   <input type="hidden" id="comment_id" name="comment_id" value="" />
                   <input type="hidden" id="comment_post_id" name="post_id"  value="" />
                   <input type="hidden"  id="comment_parent_id" name="parent_id"  value="" />
                   <input type="hidden" id="commentImage" name="comment_image"/>
                   <input type="file" id="commentMedia"  name="comment_media" accept=".jpg,.jpeg,.png,.gif"  style="display:none;outline:none;"/>
                   <div class="form-group">
                           <textarea id="comment_textarea" class="form-control" rows="2" name="comment" placeholder="add your comment ..." minlength="6"></textarea>
                   </div>
                   <div class="d-flex justify-content-between align-items-center">
                        <div class="btn-group btn-group-sm" role="group">
                            <button type="button" class="btn btn-light" id="btn_add_comment_image"><i class="fas fa-image"></i></button>
                            <button type="button" class="btn btn-light" id="btn_add_comment_gif"><i class="far fa-sticky-note"></i></button>
                        </div>
                        <button type="submit" class="btn btn-success btn-sm text-white float-right">Post <i class="fas fa-paper-plane ml-2"></i></button>
                   </div>
               </form>
               <div class="row mt-3" id="commentImagePreview" style="display:none"></div>
               <div class="mt-3 mb-0 alert alert-danger" role="alert" id="commentError" style="display:none;"></div>
        </div>
      </div>
    </div>
  </div>
