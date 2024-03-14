<!-- Delete ShortOpinion Modal -->
<div class="modal fade" id="deleteMyOpinion" tabindex="-1" role="dialog" aria-labelledby="deleteMyOpinionTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="deleteMyOpinionTitle">Delete Opinion ?</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
          Are you sure to delete this opinion  ??  You wont be able to revert this!
          <input type="hidden" class="d-none" id="delete_id" name="deleteid"/>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-danger finaldelete"><i class="far fa-trash-alt mr-2"></i>Delete</button>
      </div>
    </div>
  </div>
</div>