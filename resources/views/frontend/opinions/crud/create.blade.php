
    <div class="card box-shadow mb-3">
        <div class="card-body ">
            <div class="media align-items-center mb-3">
                    @if(Auth::user())
                    <img class="rounded-circle" src="{{Auth::user()->image}}" height="40" width="40" alt="{{ucfirst(Auth::user()->name)}}" onerror="this.onerror=null;this.src='/img/avatar.png';"/>
                     <div class="media-body">
                        <div class="d-flex justify-content-between align-items-bottom w-100">
                             <span class="ml-2">{{ucfirst(Auth::user()->name)}}</span>
                        </div>
                     </div>
                     @endif
                </div>
            <form id="opinion_form" role="form"  action="{{route('write_short_opinion')}}" method="POST" enctype="multipart/form-data">
                {{csrf_field()}}
                <div class="form-group">
                    <textarea name="body" class="form-control create_opinion" id="write_opinion" rows="3" placeholder="Write Your Opinion About {{$thread->name}} ?" maxlength="240" required></textarea>
                </div>
                <input type="hidden" id="type" name="type" value="none"/>
                <input type="hidden" id="thread" name="thread" value="{{$thread->id}}"/>
                <input type="hidden" id="cover" name="cover" />
                <input type="file" accept=".jpg,.jpeg,.png,.gif" id="file" name="files[]" style="display:none;outline:none;" multiple/>


                <div class="mb-1">
                    <span style="font-size:24px;" id="btn_add_image" class="mr-2 text-primary" data-toggle="tooltip" title="Add Photoes" data-placement="top" onclick="openFileChooser();"><i class="far fa-image"></i></span>
                    <span style="font-size:22px;" id="btn_add_gif" class="mr-2 text-success" data-toggle="tooltip" title="Add GIF" data-placement="top" onclick="openAddGIFModal('writeopinion');"><i class="far fa-sticky-note"></i></span>
                    <span style="font-size:24px;" id="btn_add_youtube" class="mr-2 text-danger" data-toggle="tooltip" title="Add YouTube Video" data-placement="top" onclick="openAddYoutubeVideoModal();"><i class="fab fa-youtube"></i></span>
                    <span style="font-size:16px;" id="btn_add_embed" class="mr-2 text-dark" data-toggle="tooltip" title="Add Embed Code" data-placement="top" onclick="openAddEmbedCodeModal();"><i class="fas fa-code"></i></span>
                    <button type="button" id="btn_post" class="btn btn-sm btn-success float-right">Post <i class="fas fa-paper-plane"></i></button>
                </div>

                <div class="row" id="previewMedia" style="display:none"></div>

            </form>

        </div>
    </div>
