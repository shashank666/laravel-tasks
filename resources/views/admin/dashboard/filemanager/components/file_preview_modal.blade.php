<div class="modal fade" id="filePreviewModal" tabindex="-1" role="dialog" aria-labelledby="filePreviewModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="filePreviewModalLabel"></h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
                <div class="preview"></div>
                <form action="{{ route('admin.filemanager.deleteFile') }}" method="POST">
                    {{ csrf_field() }}
                    <input type="hidden" name="file_id" id="file_id" value=""/>
                    <button type="submit" id="btnDeleteFile" class="mt-4 btn btn-danger btn-block" style="display:none">Delete File</button>
                </form>
            </div>
          </div>
        </div>
</div>
