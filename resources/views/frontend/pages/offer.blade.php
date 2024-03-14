@extends('frontend.layouts.app')
@section('title','Opined Introductory Offer')
@section('description','Opined Introductory Offer - A chance to earn Rs. 2000 per Article , write an article on opined and you can earn flat Rs. 2000 per opinion article.')
@section('keywords','Introductory,Offer,Opined,Win2000')

@push('meta')
<link rel="canonical" href="https://www.weopined.com/offer" />
<link href="https://www.weopined.com/offer" rel="alternate" reflang="en" />
<meta name="robots" content="noindex"/>
<!-- Twitter Card data -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:site" content="@weopined">
<meta name="twitter:title" content="Opined Introductory Offer">
<meta name="twitter:description" content="Opined Introductory Offer - A chance to earn Rs. 2000 per Article , write an article on opined and you can earn flat Rs. 2000 per opinion article.">
<meta name="twitter:creator" content="@weopined">
<meta name="twitter:image:src" content="https://www.weopined.com/img/offer_lg.png">

<!-- Open Graph data -->
<meta property="og:title" content="Opined Introductory Offer" />
<meta property="og:type" content="article" />
<meta property="og:url" content="https://www.weopined.com/offer" />
<meta property="og:image" content="https://www.weopined.com/img/offer_lg.png" />
<meta property="og:description" content="Opined Introductory Offer - A chance to earn Rs. 2000 per Article , write an article on opined and you can earn flat Rs. 2000 per opinion article."/> 
<meta property="og:site_name" content="Opined" />
<meta property="og:locale" content="en_US" />
<meta property="fb:app_id" content="1766000746745688" />
@endpush


@section('content')

    <div class="offer_cover d-flex flex-column align-items-center" style="background:#244363;">
        <div class="container py-5">
                <div class="row"> 
                        <div class="offset-md-2 col-md-8 col-12"> 
                        <h1 class="text-white text-center">Opined Introductory Offer</h1>
                        <p class="mt-4 text-white font-weight-light text-justify" style="font-size:18px;">Do you have an opinion on the current trending topic? Are you determined to make a difference with your opinion? Would you also like to get paid for your efforts? We value your quality work and believe in rewarding the same. Post your opinion article on our platform and increase your reach. Plus, the article might earn you flat Rs 2000.</p>
                        </div>
                </div>
        </div>
    </div>

        <div style="background:#ff9800;" class="pb-5">
                <h2 class="my-auto text-center text-white py-5">Why Opined?</h2>
                <div class="row"> 
                    <div class="offset-md-2 col-md-8 col-12 bg-white">
                        
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">
                                    <div class="media d-flex flex-row align-items-center">
                                        <i class="fas fa-bullhorn mx-3 text-primary" style="font-size:56px;"></i>
                                        <div class="media-body d-flex flex-column my-auto px-2 py-2">
                                            <h5 class="text-primary mb-0">Attract More Readers</h5>
                                            <p class="font-weight-light text-left mb-0">Opined is fast growing opinion platform. Gain early movers advantage and expand your reader base.</p>
                                        </div>
                                    </div>
                                </li>
                        
                                <li class="list-group-item">
                                    <div class="media d-flex flex-row align-items-center">
                                            <i class="fas fa-users mx-3 text-success" style="font-size:56px;"></i>
                                            <div class="media-body d-flex flex-column my-auto px-2 py-2">
                                            <h5 class="text-success mb-0">Increase Your Reach</h5>
                                            <p  class="font-weight-light text-left mb-0">Opined is not limited to Indian readers only. Increase your reach to national and international readers on Opined.</p>
                                        </div>
                                    </div>
                                </li>
                        
                                <li class="list-group-item">
                                    <div class="media d-flex flex-row align-items-center">
                                            <i class="fas fa-users-cog mx-3 text-danger" style="font-size:56px;"></i>
                                            <div class="media-body d-flex flex-column my-auto px-2 py-2">
                                            <h5 class="text-danger mb-0">Broaden your network</h5>
                                            <p  class="font-weight-light text-left mb-0">Opined includes a wide range of topics and categories. Take benefit of our diverse reader base.</p>
                                        </div>
                                    </div>
                                </li>
                        
                                <li class="list-group-item">
                                        <div class="media d-flex flex-row align-items-center">
                                            <i class="far fa-handshake mx-3 text-warning" style="font-size:56px;"></i>
                                            <div class="media-body d-flex flex-column my-auto px-2 py-2">
                                            <h5 class="text-warning mb-0">Long-Term Relationship</h5>
                                            <p class="font-weight-light text-left mb-0">We are working on implementing our revenue sharing model. Once implemented, apart from flat introductory offer money, you can earn more from your article.</p>
                                            </div>
                                        </div>
                                </li>
                            
                                <li class="list-group-item">
                                    <div class="media d-flex flex-row align-items-center">
                                            <i class="fas fa-user-clock mx-3 text-dark" style="font-size:56px;"></i>
                                            <div class="media-body d-flex flex-column my-auto px-2 py-2">
                                            <h5 class="text-dark mb-0">If you need us, We’re here</h5>
                                            <p class="font-weight-light text-left mb-0">We will be pleased to help you at any stage. Your feedback is equally valuable to us. You can reach out to us <a href="{{ route('contactus') }}">Here</a>.</p>
                                            </div>
                                    </div>
                                </li>
                            </ul>  
                    </div>
                </div>
        </div>

    <div style="background:#37474f;" class="py-5">
        <div class="row"> 
            <div class="offset-md-2 col-md-8 col-12"> 
                <p class="text-white font-weight-light text-justify">So here’s your chance to earn Rs 2000. We, at Opined, are pleased to announce our introductory offer where we are paying flat Rs 2000 for each eligible opinion article. Please read the detailed article selection criteria and conditions.  </p>
                <h5 class="text-center" style="color:#f44336">So what are you waiting for? Write an opinion article and submit it on the Opined.</h5>
            </div>
        </div>
    </div>
            
            <div style="background:#8bc34a;" class="pb-5">
                <h3 class="my-auto text-center text-white py-5">Article Eligibility Criteria and Conditions :</h3>
           
                <div class="row"> 
                    <div class="offset-md-2 col-md-8 col-12">        
                            <ul class="list-group list-group-flush">
                                    <li class="list-group-item font-weight-light">The introductory offer is been provided by Opined to its new writing partners from India</li>
                                    <li class="list-group-item font-weight-light">The user must have registered under <a href="{{ route('writer_terms') }}">Register as Writer</a></li>
                                    <li class="list-group-item font-weight-light">Must have updated mode of payment and relevant details</li>
                                    <li class="list-group-item font-weight-light">Offer is valid only in India</li>
                                    <li class="list-group-item font-weight-light">The offer is valid for first 100 eligible articles based on first-come-first criteria</li>
                                    <li class="list-group-item font-weight-light">The article must follow Opined’s <a href="{{ route('terms_of_service') }}">Terms of Services </a>,<a href="{{ route('acceptable_use_policy') }}"> Acceptable Use Policy </a> and <a href="{{ route('copyright_policy') }}">Copyright Policy</a></li>
                                    <li class="list-group-item font-weight-light">The article should reflect your personal opinion on the latest and trending topics</li>
                                    <li class="list-group-item font-weight-light">The article should be fresh work of writing and its fully or in parts should not have been published anywhere else</li>
                                    <li class="list-group-item font-weight-light">The writer must have appropriate rights or ownership to publish the content</li>
                                    <li class="list-group-item font-weight-light">The article must reach at least 50 likes on Opined platform</li>
                                    <li class="list-group-item font-weight-light">The article should be comprised of at least 400 words</li>
                                    <li class="list-group-item font-weight-light">Maximum 2 articles will be eligible per user</li>
                                    <li class="list-group-item font-weight-light">This is a limited time offer and Opined in its sole discretion reserves the right to add, alter modify, change, terminate or vary all or any part of this offer</li>
                            </ul>

                           
                            <a class="p-2 btn btn-block text-white" style="text-decoration:none;background:#244363" href="{{ route('writer_terms') }}">Register as Writer</a>

                    </div>
                </div>

            </div>
@endsection