@extends('frontend.layouts.app')
@section('title', 'Your Articles Performance - Opined')
@section('description','View the statistics for all opinons you write on Opined.')
@section('keywords','Stats')

@push('meta')
<link rel="canonical" href="https://www.weopined.com/me/performance" />
<link href="https://www.weopined.com/me/performance" rel="alternate" reflang="en" />


<!-- Twitter Card data -->
<meta name="twitter:card" content="summary">
<meta name="twitter:site" content="@weopined">
<meta name="twitter:title" content="Your Article's Performance  - Opined">
<meta name="twitter:description" content="View the statistics for all opinons you write on Opined.">
<meta name="twitter:creator" content="@weopined">
<meta name="twitter:image" content="https://www.weopined.com/favicon.png">

<!-- Open Graph data -->
<meta property="og:title" content="Your Article's Performance  - Opined"/>
<meta property="og:type" content="website" />
<meta property="og:url" content="https://www.weopined.com/me/performance" />
<meta property="og:image" content="https://www.weopined.com/favicon.png" />
<meta property="og:description" content="View the statistics for all opinons you write on Opined." />
<meta property="og:site_name" content="Opined" />
<meta property="og:locale" content="en_US" />
<meta property="fb:app_id" content="1766000746745688" />
@endpush

@push('styles')
@endpush

@push('scripts')
@endpush
@section('content')
@include('frontend.profile.modals.auth_test')
@include('frontend.partials.message')
<h1 class="mt-2">Article Performance</h1>
<span style="color: red;font-weight: 900;">*</span> Below article performance data may lag and are not real time data.
<div class="container">

    @if($posts->total()>0)
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="card">
                
                <div class="card-header">
                    <div class="row mt-3" style="text-align: center;">
                            <div class="col-lg-3">
                                    <div class="form-group">
                                            <a href="invoices" class="btn btn-primary">View Transaction</a>
                                    </div>
                            </div>
                            <div class="col-lg-3">
                                    <div class="form-group">
                                            
                                            <button class="btn btn-success" data-toggle="modal" data-target="#authTestModal">Payment Details</button>
                                            
                                    </div>
                            </div>
                            <div class="col-lg-3">
                                    <div class="form-group">
                                       <span  class=" badge badge-primary" style="padding: 10px; font-size: 1.0rem;font-weight: 400;">Total Earning: $ {{number_format($user_earning->total_earning, 2)}}</span>
                    
                                    </div>
                            </div>
                            <div class="col-lg-3">
                                    <div class="form-group">
                                      <span  class="badge badge-success" style="padding: 10px; font-size: 1.0rem;font-weight: 400;">Total Dues: $ {{number_format($user_earning->total_earning - $user_earning->total_paid, 2)}}</span>
                    
                                    </div>
                            </div>
                        </div>
                    </div>
        

                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Article</th>
                                <th>Views</th>
                                <th>Likes</th>
                                <th>Comments</th>
                                <th>Earning</th>
                            </tr>
                        </thead>
                        <tbody id="append-div">

                          @include('frontend.profile.components.article_performance_row')
                        
                        </tbody>
                    </table>
                </div>
                <div class="row">
                  <div class="col align-self-center">
                      {{ $posts->links('frontend.posts.components.pagination') }}
                  </div>
              </div>
            </div>
        </div>
    </div>
    <!--<div class="row mt-5">
         <div class="offset-xl-1 col-xl-10 offset-lg-1 col-lg-10 offset-md-1 col-md-10 col-12">
            @foreach($posts as $post)
              @include('frontend.posts.components.stats-post-card')
            @endforeach
        </div>
    </div>-->
    @else
        <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="card">
                
                <div class="card-header">
                    <div class="row mt-3" style="text-align: center;">
                            <div class="col-lg-3">
                                    <div class="form-group">
                                            <a href="invoices" class="btn btn-primary">View Transaction</a>
                                    </div>
                            </div>
                            <div class="col-lg-3">
                                    <div class="form-group">
                                            
                                            <button class="btn btn-success" data-toggle="modal" data-target="#authTestModal">Payment Details</button>
                                            
                                    </div>
                            </div>
                            <div class="col-lg-3">
                                    <div class="form-group">
                                       <span  class=" badge badge-primary" style="padding: 10px; font-size: 1.0rem;font-weight: 400;">Total Earning: $ {{number_format(0, 2)}}</span>
                    
                                    </div>
                            </div>
                            <div class="col-lg-3">
                                    <div class="form-group">
                                      <span  class="badge badge-success" style="padding: 10px; font-size: 1.0rem;font-weight: 400;">Total Dues: $ {{number_format(0, 2)}}</span>
                    
                                    </div>
                            </div>
                        </div>
                    </div>
        

                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Article</th>
                                <th>Views</th>
                                <th>Likes</th>
                                <th>Comments</th>
                                <th>Earning</th>
                            </tr>
                        </thead>
                        <tbody id="append-div">

                          <tr class="post-row" id="{{$monetisation->id}}" data-name="{{ $monetisation->title }}" data-isactive="{{ $monetisation->is_active }}">

                                <td><a href="{{ route('blog_post',['slug'=>$monetisation->post->slug]) }}" target="_blank">{{ str::limit($monetisation->post->title,45,'...')}}</a></td>
                                <td>
                                 {{$monetisation->post->ViewsCount}}
                                </td>
                                <td>
                                   {{$monetisation->post->LikesCount}}
                                </td>
                                <td>
                                    {{$monetisation->post->CommentsCount}}
                                </td>
                                <td>
                                    {{number_format(0,2)}}
                                </td>
                                </tr>
                        
                        </tbody>
                    </table>
                </div>
                <div class="row">
                  <div class="col align-self-center">
                      {{ $posts->links('frontend.posts.components.pagination') }}
                  </div>
              </div>
            </div>
        </div>
    </div>
    @endif
{{--
    <input type="hidden" value="{{  implode(',',array_pluck($dates,'date')) }}" id="dates"/>
    <input type="hidden" value="{{  implode(',',array_pluck($dates,'likes')) }}" id="likes"/>
    <input type="hidden" value="{{  implode(',',array_pluck($dates,'views')) }}" id="views"/>
--}}
</div>

@endsection
