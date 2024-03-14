<div class="modal fade" id="AddImageModal" tabindex="-1" role="dialog" aria-labelledby="modal_add_image" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                    <div class="modal-header" style="background-color:#ff9800;color:#fff;">
                            <h5 class="modal-title">Add Cover Image<span class="ml-2"><i class="far fa-image"></i></span></h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                            </button>
                    </div>
                    <div class="modal-body">
                            <ul class="nav nav-pills nav-justified" role="tablist" id="cover-image-tab">
                                    <li  class="nav-item"> <a class="nav-link active" href="#tab_upload" role="tab" data-toggle="tab">Upload Image<span class="ml-2"><i class="fas fa-upload"></i></span></a></li>
                                    <li  class="nav-item"><a class="nav-link" href="#tab_weblink" role="tab" data-toggle="tab">Add Link From Web<span class="ml-2"><i class="fas fa-link"></i></span></a></li>
                            </ul>

                            <div class="tab-content">
                                    <div role="tabpanel" class="tab-pane fade in active" id="tab_upload">
                                            <form method="POST" enctype="multipart/form-data" action="{{ route('admin.upload_post_cover',['id'=>$post->id]) }}">
                                                     {{ csrf_field() }}
                                                   
                                                    <div class="custom-file my-3">
                                                            <input type="file" class="custom-file-input" id="coverimage" name="coverimage" accept=".jpg,.jpeg,.png,.gif">
                                                            <span class="custom-file-control form-control-file"></span>
                                                            <label class="custom-file-label" for="coverimage">Upload Image (Maximum Size 2 MB)</label>
                                                    </div>

                                                    <button type="submit" class="btn btn-success btn-block">Upload Image</button>
                                            </form>
                                    </div>
                                    <div role="tabpanel" class="tab-pane fade" id="tab_weblink">
                                            <div class="alert alert-danger" id="errorImageLink" style="display:none"></div>
                                            
                                                    <div class="form-group row my-3">
                                                        <div class="col-md-10">
                                                        <input type="url" class="form-control" id="imagelink" name="imagelink" placeholder="http://example.com/image.jpg" />
                                                        </div>
                                                        <div class="col-md-2">
                                                                <button class="btn btn-primary" id="btnAddImageLink" onclick="CheckImageLink();">Add</button>
                                                        </div>
                                                    </div>
                                               
                                                
                                            </div>
                                    </div>
                            </div>
                    </div>
            </div>
        </div>
    </div>