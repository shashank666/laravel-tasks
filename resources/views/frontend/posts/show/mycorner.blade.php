@extends('frontend.layouts.app')
@section('title',"Articles Writer's Corner - Opined")
@section('description','This is the Corner Page for articles that you have published on Opined.')
@section('keywords','My Articles,Published Articles')

@push('meta')
<link rel="canonical" href="https://www.weopined.com/me/opinions" />
<link href="https://www.weopined.com/me/opinions" rel="alternate" reflang="en" />

<!-- Twitter Card data -->
<meta name="twitter:card" content="summary">
<meta name="twitter:site" content="@weopined">
<meta name="twitter:title" content="My Articles - Opined">
<meta name="twitter:description" content="This is the list of articles that you have published on Opined.">
<meta name="twitter:creator" content="@weopined">
<meta name="twitter:image" content="https://www.weopined.com/favicon.png">

<!-- Open Graph data -->
<meta property="og:title" content="My Articles - Opined" />
<meta property="og:type" content="website" />
<meta property="og:url" content="https://www.weopined.com/me/opinions" />
<meta property="og:image" content="https://www.weopined.com/favicon.png" />
<meta property="og:description" content="This is the list of articles that you have published on Opined." />
<meta property="og:site_name" content="Opined" />
<meta property="og:locale" content="en_US" />
<meta property="fb:app_id" content="1766000746745688" />
@endpush

@push('styles')
<style type="text/css">
.thumb{
  height:150px;
  width:100%;
}

.footer {
  position: fixed;
  left: 0;
  bottom: 0;
  width: 100%;
  /*background: linear-gradient( 33deg, rgba(246, 151, 33, 1) 10%, rgba(98, 112, 129, 1) 90%);*/
  color: #000;
  text-align: center;
}

div [class^="col-"]{
  padding-left:30px;
  padding-right:30px;
  margin-bottom:25px;
}
.card{
  transition:0.5s;
  cursor:pointer;
}
.card-title{  
  font-size:15px;
  transition:1s;
  cursor:pointer;
}
.card-title i{  
  font-size:15px;
  transition:1s;
  cursor:pointer;
  color:#ffa710
}
.card-title i:hover{
  transform: scale(1.25) rotate(100deg); 
  color:#18d4ca;
  
}
.card:hover{
  transform: scale(1.05);
  box-shadow: 10px 10px 15px rgba(0,0,0,0.3);
}
.card-text{
  height:80px;  
}

.card::before, .card::after {
  position: absolute;
  top: 0;
  right: 0;
  bottom: 0;
  left: 0;
  transform: scale3d(0, 0, 1);
  transition: transform .3s ease-out 0s;
  background: rgba(255, 255, 255, 0.1);
  content: '';
  pointer-events: none;
}
.card::before {
  transform-origin: left top;
}
.card::after {
  transform-origin: right bottom;
}
.card:hover::before, .card:hover::after, .card:focus::before, .card:focus::after {
  transform: scale3d(1, 1, 1);
}
</style>

<link href="https://fonts.googleapis.com/css?family=Crimson+Text&display=swap" rel="stylesheet">
@endpush


@push('scripts')
 <script type='text/javascript' src='/js/custom/delete.js'></script>
 @if($company_ui_settings->show_google_ad=='1')
 {!!  $company_ui_settings->google_adcode!!}
 @endif
@endpush



@section('content')

<div class="container mt-2 ">
  <h4 class="pb-3 mb-4 font-weight-normal" style="color:#244363;border-bottom:1px solid #495057;"><a href="/opinion/write" class="btn waves-effect waves-float float-right btn-article">Write Article</a><a href="/legal/offer-rsm" class="btn waves-effect waves-float float-right btn-article">Offer</a></h4>
<!--   <div class="card card-block mb-2">
    <h4 class="card-title">Card 1</h4>
    <p class="card-text">Welcom to bootstrap card styles</p>
    <a href="#" class="btn btn-primary">Submit</a>
  </div>   -->
  <h2 style="font-family: 'Crimson Text', serif;">Keep Track Of Your Articles</h2>
  <div class="row pt-3">
    <div class="col-md-4 col-sm-12">
        <a href="myallarticles">
      <div class="card card-block" style="border:none">
      <h4 class="card-title text-right"></h4>
      <div class="card-body">
        <img class="thumb" src="/img/myarticle.png" alt="My Articles">
          <h5 class="card-title mt-3 mb-3" style="text-align: center;">My Articles</h5>
              <p class="card-text" style="color: #000; text-align: justify;">Check this space to check all your articles and it's status. You can alse edit or delete them here.</p>
    </div> 
  </div>
</a>
    </div>

@if(count($monetisation)>0)
    <div class="col-md-4 col-sm-12 ">
        <a href="article_performance">
      <div class="card card-block" style="border:none">
      <h4 class="card-title text-right"></h4>
      <div class="card-body">
    <img class="thumb" src="/img/performance.png" alt="Performance">
        <h5 class="card-title mt-3 mb-3" style="text-align: center;">Article Performance</h5>
        <p class="card-text" style="color: #000; text-align: justify;">This space is for monetised articles and related functions. Track your earnings.</p>
        </div> 
  </div>
</a>
    </div>
@else
    <div class="col-md-4 col-sm-12 " style="opacity: 0.4;">
      <div class="card card-block" style="border:none">
      <h4 class="card-title text-right"></h4>
      <div class="card-body">
    <img class="thumb" src="/img/performance.png" alt="Performance">
        <h5 class="card-title mt-3 mb-3" style="text-align: center;">Article Performance</h5>
        <p class="card-text" style="color: #000; text-align: justify;">This space is for monetised articles and related functions. Track your earnings.</p>
        </div> 
  </div>
</a>
    </div>
@endif
    <div class="col-md-4 col-sm-12">
        <a href="/legal/dos_donts">
      <div class="card card-block" style="border:none">
      <h4 class="card-title text-right"></h4>
      <div class="card-body">
    <img class="thumb" src="/img/do&dont.png" alt="Do & Don't">
        <h5 class="card-title mt-3 mb-3" style="text-align: center;">Do's & Don't</h5>
        <p class="card-text" style="color: #000; text-align: justify;">A guideline that may help you increase your earnings and things that you should avoid.</p>
        </div> 
  </div>
</a>
    </div>
<!--
    <div class="col-md-3 col-sm-6">
      <div class="card card-block" style="border:none">
      <h4 class="card-title text-right"></h4>
      <div class="card-body">
        <img class="thumb" src="/img/payment.png" alt="My Articles">
          <h5 class="card-title mt-3 mb-3" style="text-align: center;">Payment Terms</h5>
              <p class="card-text">This is a company that builds websites, web apps and e-commerce solutions.</p>
    </div> 
  </div>
    </div>-->
   <!-- <div class="col-md-3 col-sm-6">
      <div class="card card-block">
      <h4 class="card-title text-right"></h4>
      <div class="card card-body">
    <img class="thumb" src="https://static.pexels.com/photos/7096/people-woman-coffee-meeting.jpg" alt="Photo of sunset">
        <h5 class="card-title mt-3 mb-3">Sierra Web Development • Owner</h5>
        <p class="card-text">This is a company that builds websites, web apps and e-commerce solutions.</p>
        </div> 
  </div>
    </div>

    <div class="col-md-3 col-sm-6">
      <div class="card card-block">
      <h4 class="card-title text-right"></h4>
    <img class="thumb" src="https://static.pexels.com/photos/7357/startup-photos.jpg" alt="Photo of sunset">
        <h5 class="card-title  mt-3 mb-3">ProVyuh</h5>
        <p class="card-text">This is a company that builds websites, web .</p> 
  </div>
    </div>
    <div class="col-md-3 col-sm-6">
      <div class="card card-block">
      <h4 class="card-title text-right"></h4>
    <img class="thumb" src="https://static.pexels.com/photos/262550/pexels-photo-262550.jpeg" alt="Photo of sunset">
        <h5 class="card-title  mt-3 mb-3">ProVyuh</h5>
        <p class="card-text">This is a company that builds websites, web apps and e-commerce solutions.</p> 
  </div>
    </div>
    <div class="col-md-3 col-sm-6">
      <div class="card card-block">
      <h4 class="card-title text-right"><i class="material-icons">settings</i></h4>
    <img class="thumb" src="https://static.pexels.com/photos/326424/pexels-photo-326424.jpeg" alt="Photo of sunset">
        <h5 class="card-title  mt-3 mb-3">ProVyuh</h5>
        <p class="card-text">This is a company that builds websites, web apps and e-commerce solutions.</p> 
  </div>
    </div>   --> 
  </div>
  
</div>



<div class="footer">
    <p> <span class="mr-3">&#8226;<a href="/legal/writer_terms">Writer's term</a></span>
        <span class="mr-3">&#8226;<a href="/legal/payment_terms">Payment term</a></span>
        <span class="mr-3">&#8226;<a href="/legal/eligibility_of_rsm">Eligibility for RSM</a></span>
        <span class="mr-3">&#8226;<a href="/legal/article_guideline">Opinion Article Guideline</a></span>
    </p>
</div>



@endsection

