@extends('admin.layouts.app')
@section('title','Offer Eligible Posts')

@push('styles')
<link href="/public_admin/assets/libs/noty/noty.min.css" rel="stylesheet" type="text/css"/>
@endpush

@push('scripts')
<script src="/public_admin/assets/libs/noty/noty.min.js"></script>
<script>
    $(document).on('click','.btn_sendmail',function(){
        $('#post_id').val($(this).attr('data-postid'));
        $('#user_id').val($(this).attr('data-userid'));
        $('#modal_email').modal('show');
    });


    $(document).on('click','#sendEmail',function(){
        $('.email_status').html('Sending ....');
        $.ajax({
            url:"{{  route('admin.send_payment_mail') }}",
            type:'POST',
            data:{'user_id':$('#user_id').val(),'post_id': $('#post_id').val()},
            dataType:'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            contentType:'application/json',
            success:function(response){
                    if(response.status=='success'){
                        $('.email_status').html(response.message);
                        setTimeout(function(){
                            $('#modal_email').modal('hide')
                        }, 5000);
                        location.reload();
                    }
            },
            error:function(){
                alert('Error :(');
                $('.email_status').html('Error Sending Email , Please Try again Later');
            }
        });
    });


    $(document).on('click','.delete-offerpost',function(){
        var offerpost_id=$(this).attr('data-id');
        $.ajax({
            url:'{{ route("admin.delete_offer_posts") }}',
            method:'POST',
            data:{'offerpost_id':offerpost_id},
            dataType:'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success:function(response){
                if(response.status=='success'){
                    $('#'+offerpost_id).remove();
                }
                    new Noty({
                        theme:'sunset',
                        type:response.status,
                        text: response.message,
                        timeout:3500,
                    }).show();
            },error:function(err){
                new Noty({
                    theme:'sunset',
                    type:'error',
                    text: 'FAILED TO DELETE OFFERPOST',
                    timeout:3500,
                }).show();
            }
        });
    });
</script>
@endpush

@section('content')
@include('admin.partials.header_title',['header_title'=>'Offer Eligible Posts'])
<div class="container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                   
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Post</th>
                                        <th>User</th>
                                        <th>Payment Status</th>
                                        <th>Eligible Date</th>
                                        <th>Send Mail</th>
                                        <th>Delete</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($offerposts as $offerpost)
                                    <tr id="{{ $offerpost->id }}">
                                        <td>
                                            <a href="{{ route('admin.blog_post',['id'=>$offerpost->post['id']]) }}">
                                                <p>{{ '#'. $offerpost->post['id'] .' - '.$offerpost->post['title'] }}</p>
                                                <img src="{{ $offerpost->post['coverimage']  }}" width="180" height="120" class="rounded"/>
                                            </a>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.user_details',['id'=>$offerpost->user['id']]) }}">
                                            <b> {{ $offerpost->user['name'] }}</b>
                                            <p>{{  $offerpost->user['email'] }}</p>
                                            <img src="{{ $offerpost->user['image'] }}" height="90" width="90" class="rounded-circle"/>
                                            </a>
                                        </td>
                                        <td>
                                            <span class="badge {{  $offerpost->payment_status==0?'badge-primary':'badge-success' }}">
                                                    {{  $offerpost->payment_status==0?'Payment Due':'Payment Paid' }}
                                            </span>
                                        </td>
                                        <td>{{ $offerpost->created_at }}</td>
                                        <td>
                                            @if($offerpost->payment_status==0)
                                            <button class="btn btn-primary btn_sendmail" data-postid="{{ $offerpost->post['id'] }}" data-userid="{{ $offerpost->user['id'] }}" data-offerpost_id="{{ $offerpost->id }}">Send Mail</button>
                                            @else
                                            <p>Payment Email Sent</p>
                                            @endif
                                        </td>
                                        <td>
                                            <button data-id="{{ $offerpost->id }}" type="button" class="delete-offerpost btn btn-danger btn-rounded-circle">
                                                <i class="fe fe-trash-2"></i>
                                            </button>
                                        <td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                  
                    @if($offerposts->total()>0)
                    <div class="card-footer d-flex justify-content-center align-items-center">
                       {{ $offerposts->links('admin.partials.pagination') }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
</div>

<div class="modal fade" id="modal_email" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="defaultModalLabel">
                        Send Payment Email
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                        </button>
                    </h4>
                </div>
                <div class="modal-body">
                    <form id="email_form" method="POST" action="{{  route('admin.send_payment_mail') }}">
                        <input type="hidden" id="offerpost_id" name="offerpost_id" />
                        <input type="hidden" id="post_id" name="post_id" />
                        <input type="hidden" id="user_id" name="user_id"/>
                        {{csrf_field()}}
                    <p class="email_status"></p>
                    <button id="sendEmail1" type="submit" class="btn btn-success">SEND EMAIL</button>
                    <button type="button" class="btn btn-light" data-dismiss="modal">CLOSE</button>
                </form>
                </div>
            </div>
        </div>
</div>
@endsection
