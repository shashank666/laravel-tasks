<div class="modal fade" id="AddImageModal" tabindex="-1" role="dialog" aria-labelledby="AddImageModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" role="document">
            <div class="modal-content" style="border:0px;">
                <div class="modal-header add-image-header text-white">
                    <h5 class="modal-title add-image-title">

                    </h5>
                    <button type="button" class="close" aria-label="Close" onclick="closeAddImageModal();">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="modal_from" value=""/>

                    <ul class="nav nav-pills nav-justified" role="tablist" id="image-tab">
                    <li  class="nav-item"> <a class="nav-link active" href="#tab_upload_image" role="tab" data-toggle="tab">Upload Image<span class="ml-2"><i class="fas fa-upload"></i></span></a></li>
                    <!--<li  class="nav-item"><a class="nav-link" href="#tab_image_link" role="tab" data-toggle="tab">Add Link From Web<span class="ml-2"><i class="fas fa-link"></i></span></a></li>-->
                    </ul>

                    <div class="tab-content">
                            <div role="tabpanel" class="tab-pane fade in active show" id="tab_upload_image">
                                    <div class="tab-body mt-4">

                                        <div id="uploadPreviewDiv"  style="display:none;height:240px;width:100%;">
                                        <img class="my-2 img-fluid" src=".." id="modalImagePreview" style="max-width:100%;height:100%;overflow:hidden;"/>
                                        </div>

                                            <form id="uploadImageForm" method="POST" enctype="multipart/form-data" >
                                            <div id="upload-response" class="mt-2"></div>
                                                    <div class="form-group">
                                                        <div class="custom-file">
                                                            <input type="file" class="custom-file-input" id="imagefile" name="coverimage" accept=".jpg,.jpeg,.png,.gif">
                                                            <span class="custom-file-control form-control-file"></span>
                                                            <label class="custom-file-label" for="imagefile">Upload Image (Maximum Size 2 MB)</label>
                                                        </div>
                                                    </div>
                                                    <div class="row" id="btnCropGroup" style="display:none">
                                                        <div class="col">
                                                                <button id="btnCropImage" type="button" class="btn btn-block btn-sm btn-secondary" onclick="CropImage();">Crop Image<i class="fas fa-crop ml-2"></i></button>
                                                        </div>
                                                        <div class="col">
                                                                <button id="btnSkipCrop" type="button" class="btn btn-block btn-sm btn-light" onclick="SkipCrop();">Skip<i class="ml-2 fas fa-forward"></i></button>
                                                        </div>
                                                    </div>

                                                    <button id="btnUploadImage" type="button" class="btn btn-block btn-sm btn-success" style="display:none">Upload Image</button>
                                            </form>
                                    </div>
                                </div>
                            <!--<div role="tabpanel" class="tab-pane fade" id="tab_image_link">
                                    <div class="tab-body mt-4">
                                            <div id="image-response" class="mt-2 alert alert-danger alert-dismissible fade show" role="alert" style="display:none;"></div>
                                            <div class="form-row">
                                                    <div class="col-md-10">
                                                        <div class="form-group">
                                                            <input type="url" class="form-control" id="imagelink" name="imagelink" placeholder="http://example.com/image.jpg" />
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                    <button class="btn btn-primary" id="btnAddImageLink" onclick="CheckImageLink();">Add</button>
                                                    </div>
                                            </div>
                                    </div>
                            </div>-->
                    </div>
                </div>
            </div>
    </div>
</div>
