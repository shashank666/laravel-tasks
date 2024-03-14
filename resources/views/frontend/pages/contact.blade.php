@extends('frontend.layouts.app')
@section('title','Contact Us - Opined')
@section('description','Contact Us - Opined , We are happy to answer any questions you have or provide you with an estimate. Just send us a message with any question you may have.')
@section('keywords','Contact Us,Reach Us,Email Us')

@push('meta')
<link rel="canonical" href="https://www.weopined.com/contactus" />
<link href="https://www.weopined.com/contactus" rel="alternate" reflang="en" />

<!-- Twitter Card data -->
<meta name="twitter:card" content="summary">
<meta name="twitter:site" content="@weopined">
<meta name="twitter:title" content="Contact Us - Opined">
<meta name="twitter:description" content="Contact Us - Opined , We are happy to answer any questions you have or provide you with an estimate. Just send us a message with any question you may have or email us on reach-us@weopined.com">
<meta name="twitter:creator" content="@weopined">
<meta name="twitter:image" content="https://www.weopined.com/favicon.png">

<!-- Open Graph data -->
<meta property="og:title" content="Contact Us - Opined" />
<meta property="og:type" content="website" />
<meta property="og:url" content="https://www.weopined.com/contactus" />
<meta property="og:image" content="https://www.weopined.com/favicon.png" />
<meta property="og:description" content="Contact Us - Opined , We are happy to answer any questions you have or provide you with an estimate. Just send us a message with any question you may have or email us on reach-us@weopined.com"/> 
<meta property="og:site_name" content="Opined" />
<meta property="og:locale" content="en_US" />
<meta property="fb:app_id" content="1766000746745688" />
@endpush

@push('styles')
<style>
body{
    background-image: url('/img/bg-pattern.png'),linear-gradient(to right, #051937, #532a60, #a83267, #ea534c, #ff9800);
    background-size: auto;
}
</style>
@endpush

@push('scripts')
<script src='https://www.google.com/recaptcha/api.js'></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.17.0/jquery.validate.min.js"></script>
<script>
        function enableBtn(){
            $("#contactus_submit").removeAttr('disabled');
        }

        $(document).ready(function(){
            $('#contactus_submit').attr('disabled','disabled');
           
                $('#contact-form').validate({
                        rules: {
                            'name': {
                                required: true
                            },
                            'email': {
                                required: true
                            },
                            'subject':{
                                required: true
                            },
                            'message':{
                                required: true
                            }
                        },
                        highlight: function (input) {
                            $(input).addClass('is-invalid');
                        },
                        unhighlight: function (input) {
                            $(input).removeClass('is-invalid');
                        },
                        errorPlacement: function (error, element) {
                            $(element).next().append(error);
                        },
                        submitHandler: function(form) {
                            $("#contactus_submit").attr('disabled','disabled');
                            form.submit();
                        }
                });
                $('#contact_message').bind('copy paste cut',function(e) {
                      e.preventDefault();
                    });
        });

</script>
@endpush

@section('content')
        <div class="row"> 
            <div class="offset-md-3 col-md-6 col-12">
                  
                    <div class="card shadow">
                            <div class="card-header bg-white">
                                <h4 class="text-center font-weight-light">Contact Us</h4>
                                <p class="text-justify text-secondary font-weight-light">We are happy to answer any questions you have or provide you with an estimate. Just send us a message in the form below with any question you may have.</p>
                               
                            </div>
                            <div class="card-body">
                                 @include('frontend.partials.message')
                               
                                <br/>
                                <form id="contact-form" method="POST" action="{{route('send_message')}}">
                                                {{csrf_field()}}
                                        <div class="form-group">
                                        <input type="text" name="name" id="contact_name" placeholder="Your Name" class="form-control" required/>
                                        <div class="invalid-feedback"></div>
                                        </div>
                        
                                        <div class="form-group">
                                                <input type="email" name="email" id="contact_email" placeholder="Your Email Address" class="form-control" required/>
                                                <div class="invalid-feedback"></div>
                                        </div>
                        
                                        <div class="form-group">
                                                <input type="text" name="subject" id="contact_subject" placeholder="Subject" class="form-control" required/>
                                                <div class="invalid-feedback"></div>
                                        </div>
                                        <div class="form-group">
                                                <input type="hidden" name="device" id="contact_device" placeholder="device" class="form-control" value=""/>
                                                <div class="invalid-feedback"></div>
                                        </div>
                                        <div class="form-group">
                                                <textarea type="text" name="message" id="contact_message" placeholder="Message" class="form-control" rows="6" maxlength="520" required></textarea>
                                                <div class="invalid-feedback"></div>
                                        </div>

                                        <div class="form-group">
                                            <div class="g-recaptcha" data-sitekey="6Ld3WmwUAAAAAKqBCXMWe_tk4jyBDKgoTSA0mmBH" data-callback="enableBtn"></div>
                                        </div>
                        
                                        <button id="contactus_submit" type="submit" class="btn btn-success btn-block">Send Message</button>
                                </form>
                            </div>
                    </div>
                   
            </div>
        </div>    
@endsection                    