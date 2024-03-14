<div class="card box-shadow mb-3">
    <div class="card-body bg-light bg-card" style="padding-bottom: 3px;">
        <form id="write_thread" role="form" action="{{ route('admin.write_short_opinion2') }}" method="POST" enctype="multipart/form-data">
            {{ csrf_field() }}
            <div class="form-group">
                <input type="radio" id="male" name="gender" value="Male">
                <label for="male">Male</label>
                <input type="radio" id="female" name="gender" value="Female">
                <label for="female">Female</label>
                <div class="row">
                    <div class="col-xl-10 col-lg-10 col-md-10 col-sm-12 col-12" style="margin-bottom: 10px;">
                        <input type="text" name="title" class="form-control create_opinion" id="write_opinion_title" placeholder="&quot;Opinion Title&quot;" required>
                    </div>
                    <div class="col-xl-10 col-lg-10 col-md-10 col-sm-12 col-12">
                        <textarea name="body" class="form-control create_opinion" id="write_opinion" rows="3" placeholder="&quot;Let The World Know Your Opinions&quot; Get Started!" onfocus="this.placeholder = ''" onblur="this.placeholder = '&quot;Let The World Know Your Opinions&quot; Get Started!'" maxlength="2000" required></textarea>
                    </div>
                          
                            <div class="col-xl-2 col-lg-2 col-md-2 d-xl-inline d-lg-inline d-md-inline d-sm-none d-none">
                               
                                 <span style="font-size:1.3vw;" id="btn_add_image" class="mr-2 text-primary" data-toggle="tooltip" title="Add Photos" data-placement="top" onclick="openFileChooser();"><i class="far fa-image" style="color:#ff9800"></i></span>
                                <span style="font-size:1.2vw;" id="btn_add_gif" class="mr-2 text-success" data-toggle="tooltip" title="Add GIF" data-placement="top" onclick="openAddGIFModal('writeopinion');"><i class="far fa-sticky-note" style="color:#ff9800"></i></span>
                                 
                                 <span style="font-size:1vw;" id="btn_add_embed" class="mr-2 text-dark" data-toggle="tooltip" title="Add Embed Code" data-placement="top" onclick="openAddEmbedCodeModal();"><i class="fas fa-code" style="color:#ff9800"></i></span>
                                <button type="button" id="btn_admin_submit_opinion" style="width: 80%;height: 40%;margin-top: 28%;margin-right: 16px;" class="btn btn-sm btn-success float-right">Post <i class="fas fa-paper-plane"></i></button>
                    
                            </div>
                            <!-- FOR MOBILE -->

                                 <div class="col-sm-12 col-12 d-xl-none d-lg-none d-md-none d-sm-inline d-inline">
                               
                                 <span style="font-size:2rem;" id="btn_add_image" class="mr-2 text-primary" data-toggle="tooltip" title="Add Photos" data-placement="top" onclick="openFileChooser();"><i class="far fa-image" style="color:#ff9800"></i></span>
                                <span style="font-size:1.9rem;" id="btn_add_gif" class="mr-2 text-success" data-toggle="tooltip" title="Add GIF" data-placement="top" onclick="openAddGIFModal('writeopinion');"><i class="far fa-sticky-note" style="color:#ff9800"></i></span>
                                 <span style="font-size:1.8rem;" id="btn_add_embed" class="mr-2 text-dark" data-toggle="tooltip" title="Add Embed Code" data-placement="top" onclick="openAddEmbedCodeModal();"><i class="fas fa-code" style="color:#ff9800"></i></span>
                                <button type="button" id="btn_admin_submit_opinion" style="width: 84px;height: 36px;margin-top: 16px;margin-right: 16px;" class="btn btn-sm btn-success float-right">Post <i class="fas fa-paper-plane"></i></button>
                    
                            </div>

                            <!-- END MOBILE VIEW -->

                        </div>
                    </div>
                    <input type="hidden" id="type" name="type" value="none"/>
                    <input type="hidden" id="cover" name="cover" />
                    <input type="file" accept=".jpg,.jpeg,.png,.gif" id="file" name="files[]" style="display:none;outline:none;" multiple/>

                    <div class="row" id="previewMedia" style="display:none"></div>

                </form>

        </div>
    </div>
