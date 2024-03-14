@extends('admin.layouts.app')

@push('scripts')
<script src="/public_admin/assets/libs/autosize/autosize.js"></script>
<script>
$(document).ready(function(){
    autosize($('#reply-message'));
    $('[data-toggle="tooltip"]').tooltip({
        container: 'body'
    });
});

$(document).on('click','.btn-reply',function(){
    var message_id=$(this).attr('data-messageid');
    var name=$(this).attr('data-name');
    var email=$(this).attr('data-email');
    var subject=$(this).attr('data-subject');

    $('#reply-message-id').val(message_id);
    $('#reply-name').val(name);
    $('#reply-modal-label').text('Send Reply to '+name);
    $('#reply-email').val(email);
    $('#reply-subject').val('Re: '+subject);
    $('#reply-modal').modal('show');
});

$(document).on('click','.btn-star',function(){
    var message_id=$(this).attr('data-messageid');
    $.ajax({
        url:"{{ route('admin.star_message') }}",
        type:'POST',
        data:{'message_id':message_id},
        dataType:'json',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success:function(response){
            if(response.star=='added')
            {
                $('.star-on-'+message_id).css('display','block');
                $('.star-off-'+message_id).css('display','none');
            }else{
                $('.star-off-'+message_id).css('display','block');
                $('.star-on-'+message_id).css('display','none');
            }
        },
        error:function(err){
            alert('Failed to Starred :(');
        }
    });
});

</script>
@endpush

@section('content')
    <div class="header">
            <div class="container">
            <div class="header-body">
                <div class="row align-items-center">
                    <div class="col">
                        <h1 class="header-title">
                        Messages From Contact Us
                        </h1>
                    </div>
                    <div class="col-auto">
                            <form id="delete_all_messages" method="POST" action="{{route('admin.delete_all_messages')}}" style="display:none;">{{csrf_field()}}</form>
                            <button class="btn btn-danger" onclick="event.preventDefault();document.getElementById('delete_all_messages').submit();" data-toggle="tooltip" data-placement="top" title="Delete All Unread and Read Messages"><i class="fe fe-trash-2 mr-2"></i><span>DELETE ALL MESSAGES</span></button>
                        </div>
                    </div>
                </div>
                <div class="row align-items-center">
                        <div class="col">
                          <ul class="nav nav-tabs nav-overflow header-tabs">
                            <li class="nav-item">
                              <a href="{{route('admin.unread_messages')}}"  class="{{$section=='unread'?'nav-link active':'nav-link'}}">
                                <i class="fe fe-mail mr-2"></i>Unread Messages
                              </a>
                            </li>
                            <li class="nav-item">
                              <a  href="{{route('admin.read_messages')}}"  class="{{$section=='read'?'nav-link active':'nav-link'}}">
                                <i class="fe fe-check-circle mr-2"></i> Read Messages
                              </a>
                            </li>
                            <li class="nav-item">
                              <a  href="{{route('admin.starred_messages')}}"  class="{{$section=='starred'?'nav-link active':'nav-link'}}">
                                    <i class="fe fe-star mr-2"></i>Starred Messages
                              </a>
                            </li>
                          </ul>

                        </div>
                      </div>
            </div>
    </div>

    <div class="container">
        <div class="card">

                <div class="card-body">

                    @if(count($messages)>0)

                    <input id="path" type="hidden" value="{{Request::fullUrl()}}" />
                    <input id="totalpage" type="hidden" value="{{ceil($messages->total()/$messages->perPage())}}" />

                    <div class="table-responsive">
                                <table class="table">
                                    <tbody id="append-div">
                                       @include('admin.dashboard.messages.message_row')
                                    </tbody>
                                </table>
                    </div>
                    @include('admin.partials.spinner')
                    @else
                    <p class="p-2">No messages available at this time.</p>
                    @endif
                </div>
        </div>
    </div>

    @if(count($messages)>0)
    @include('admin.dashboard.messages.modal_reply')
    @include('admin.partials.loadmorescript')
    @endif

@endsection
