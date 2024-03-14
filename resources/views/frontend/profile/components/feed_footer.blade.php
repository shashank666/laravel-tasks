
<footer class="page-footer">
    	<div class="thread_card card mb-3 p-2 text-center shadow-sm" style="border:0px; ">
    		<div class="row">
                        <div class="col-12">
      © {{ Carbon\Carbon::now()->format('Y') }} Copyright  <a href="https://www.weopined.com" style="color:#244363;text-decoration:none"><strong>Opined</strong></a>
     					</div>
     		</div>
     		<hr>
     <div class="font-weight-dark">
     	<div class="row">
     		<div class="col-12" style="padding-bottom: 5px">
          <a href="{{route('privacy_policy')}}" class="footer-links my-md-0 my-2 mr-md-2 " style="color:#244363">Privacy Policy</a>
          
          <a href="{{route('terms_of_service')}}" class="footer-links my-md-0 my-2 mx-md-2" style="color:#244363">Terms of Service</a>
</div>
         
     		<div class="col-12">
          <a href="{{route('contactus')}}" class="footer-links my-md-0 my-2 mr-md-2" style="color:#244363">Contact Us</a>
          
          <a href="https://play.google.com/store/apps/details?id=com.app.weopined" target="_blank" class="footer-links my-md-0 my-2 mx-md-2" style="color:#244363">Install Opined App</a>
</div>
      </div>
      <hr>
     <div class="footer-social-div center-on-small-only">
      <a href="{{ $company->facebook_url }}" target="_blank" title="Like Opined On Facebook" data-toggle="tooltip" data-placement="top"><i class="fab fa-facebook facebook-icon-grey mr-3"></i></a>
      <a href="{{ $company->twitter_url }}" target="_blank" title="Follow Opined On Twitter" data-toggle="tooltip" data-placement="top"><i class="fab fa-twitter  twitter-icon-grey mr-3"></i></a>
      <a href="{{ $company->linkedin_url }}" target="_blank" title="Follow Opined On Linkedin" data-toggle="tooltip" data-placement="top"><i class="fab fa-linkedin linkedin-icon-grey"></i></a>
      </div>
    <a class="scrollup" title="Back to Top" href="javascript:void(0);" style="text-decoration:none;color:#fff"><i class="fas fa-chevron-up" style="font-size:20px;margin-top:8px;"></i></a>
  </div>
</footer>