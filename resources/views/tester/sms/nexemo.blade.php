@extends('tester.testerapp')
@section('title',"Nexemo SMS")

@push('scripts')
<script>
    $(document).on('click','#nexemo-send-btn',function(){
        $(this).attr('disabled','disabled');
        $.ajax({
            url:'/cpanel/tester/nexemo',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type:'POST',
            dataType:'json',
            data:$('#nexemo-form').serialize(),
            success:function(response){
                console.log(response);
                $('#nexemo-send-btn').removeAttr('disabled');
                $('#nexemo-response').text(JSON.stringify(response.data));
            },
            error:function(err){
                $('#nexemo-send-btn').removeAttr('disabled');
            }
        })
    });
</script>
@endpush

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row clearfix">
                <div class="col-lg-4">
                        <div class="card">
                                <div class="header">
                                    <h2>Nexemo SMS</h2>
                                </div>
                                <div class="body">
                                    <form method="POST" id="nexemo-form">
                                        <div class="form-group form-float">
                                                <div class="form-line">
                                                    <input type="text" class="form-control" autocomplete="off" name="api_key" value="31b3a44f" readonly>
                                                    <label class="form-label">Api Key</label>
                                                </div>
                                        </div>

                                        <div class="form-group form-float">
                                                <div class="form-line">
                                                    <input type="text" class="form-control" autocomplete="off" name="api_secret" value="cZDqd7Nqi6Lpcs8K" readonly>
                                                    <label class="form-label">Api Secret</label>
                                                </div>
                                        </div>

                                        <div class="form-group form-float">
                                                <div class="form-line">
                                                    <input type="text" class="form-control" autocomplete="off" name="from" value="OPINED">
                                                    <label class="form-label">From</label>
                                                </div>
                                        </div>

                                        <div class="form-group form-float">
                                                <div class="form-line">
                                                    <input type="tel" class="form-control"  name="to" value="" required>
                                                    <label class="form-label">To</label>
                                                </div>
                                        </div>

                                        <div class="form-group form-float">
                                                <div class="form-line">
                                                    <input type="text" class="form-control" autocomplete="off" name="text" value="" required>
                                                    <label class="form-label">SMS Text</label>
                                                </div>
                                        </div>

                                        <button type="button" class="btn btn-primary waves-effect btn-block" id="nexemo-send-btn">Send SMS</button>
                                    </form>
                                </div>
                        </div>
                </div>
                <div class="col-lg-4">
                    <div class="card">
                        <div class="header"><h2>API Response</h2></div>
                        <div class="body">
                            <p id="nexemo-response">

                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
