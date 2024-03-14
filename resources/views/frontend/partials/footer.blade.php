
<footer class="page-footer bg-opined-dark-blue">
    <div class="py-3 container d-flex flex-md-row flex-column justify-content-between align-items-center">
     <div class="text-center font-weight-light text-white mb-md-0 mb-2"> 
      © {{ Carbon\Carbon::now()->format('Y') }} Copyright  <a href="https://www.weopined.com" style="color:#244363;text-decoration:none"><strong>Opined</strong></a>
     </div>
     <div class="d-flex flex-md-row flex-column font-weight-light text-center justify-content-around align-items-center mb-md-0 mb-2">
          <a href="{{route('privacy_policy')}}" class="footer-links my-md-0 my-2 mr-md-2"style="color:#244363;">Privacy Policy</a>
          <span class="footer-dot d-md-inline d-none mx-md-2 mx-0"></span>
          <a href="{{route('terms_of_service')}}" class="footer-links my-md-0 my-2 mx-md-2"style="color:#244363;">Terms of Service</a>
          <span class="footer-dot d-md-inline d-none mx-md-2 mx-0"></span>
          <a href="{{route('contactus')}}" class="footer-links my-md-0 my-2 ml-md-2"style="color:#244363;">Contact Us</a>
      </div>
     <div class="footer-social-div center-on-small-only">
      <a href="{{ $company->facebook_url }}" target="_blank" title="Like Opined On Facebook" data-toggle="tooltip" data-placement="top"><i class="fab fa-facebook facebook-icon-white mr-3"></i></a>
      <a href="{{ $company->twitter_url }}" target="_blank" title="Follow Opined On Twitter" data-toggle="tooltip" data-placement="top"><i class="fab fa-twitter  twitter-icon-white mr-3"></i></a>
      <a href="{{ $company->linkedin_url }}" target="_blank" title="Follow Opined On Linkedin" data-toggle="tooltip" data-placement="top"><i class="fab fa-linkedin linkedin-icon-white"></i></a>
      </div>
    <a class="scrollup" title="Back to Top" href="javascript:void(0);" style="text-decoration:none;color:#fff"><i class="fas fa-chevron-up" style="font-size:20px;margin-top:8px;"></i></a>
  </div>
</footer>