<div class="modal fade" id="AddImageModal" tabindex="-1" role="dialog" aria-labelledby="modal_add_image" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                    <div class="modal-header">
                            <h5 class="modal-title">Add Image<span class="ml-2"><i class="far fa-image"></i></span></h5>
                            <button type="button" class="close"  aria-label="Close" onclick="closeAddImageModal();">
                            <span aria-hidden="true">&times;</span>
                            </button>
                    </div>
                    <div class="modal-body">
                        <div id="uploadPreviewDiv"  style="display:none;height:240px;width:100%;">
                            <img class="my-2 img-fluid" src=".." id="modalImagePreview" style="max-width:100%;height:100%;overflow:hidden;"/>
                        </div>

                        <form id="uploadImageForm" method="POST" enctype="multipart/form-data" >
                            <div id="upload-response" class="mt-2"></div>
                            {{ csrf_field() }}
                            <div class="custom-file my-3">
                                    <input type="file" class="custom-file-input" id="image" name="image" accept=".jpg,.jpeg,.png,.gif">
                                    <span class="custom-file-control form-control-file"></span>
                                    <label class="custom-file-label" for="image">Upload Image (Maximum Size 2 MB)</label>
                            </div>
                            <button type="submit" id="btnUploadImage" class="btn btn-success btn-block" style="display:none">Upload Image</button>
                        </form>
                    </div>
            </div>
        </div>
    </div>
