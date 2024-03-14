@extends('admin.layouts.app')
@section('title','Company Settings')

@push('scripts')
<script src="/public_admin/assets/libs/autosize/autosize.js"></script>
<script>
        $(document).ready(function(){
            autosize($('textarea.auto-growth'));
        });
</script>
@endpush

@section('content')
@include('admin.partials.header_title',['header_title'=>'Company Settings'])

<div class="container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                            <form class="form-horizontal" method="POST">
                                    {{ csrf_field() }}
                                    <div class="row clearfix">
                                        <div class="col-lg-2 col-md-2 col-sm-4 col-12 form-control-label">
                                            <label for="name">Company Name</label>
                                        </div>
                                        <div class="col-lg-10 col-md-10 col-sm-8 col-12">
                                            <div class="form-group">
                                                <div class="form-line">
                                                    <input type="text" id="name" class="form-control" placeholder="Company Name" value="{{$company->name}}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row clearfix">
                                            <div class="col-lg-2 col-md-2 col-sm-4 col-12 form-control-label">
                                                <label for="tagline">Tagline</label>
                                            </div>
                                            <div class="col-lg-10 col-md-10 col-sm-8 col-12">
                                                <div class="form-group">
                                                    <div class="form-line">
                                                        <input type="text" id="tagline" class="form-control" placeholder="Company Tagline" value="{{$company->tagline}}">
                                                    </div>
                                                </div>
                                            </div>
                                    </div>
                                    <div class="row clearfix">
                                            <div class="col-lg-2 col-md-2 col-sm-4 col-12 form-control-label">
                                                <label for="keywords">Keywords</label>
                                            </div>
                                            <div class="col-lg-10 col-md-10 col-sm-8 col-12">
                                                <div class="form-group">
                                                    <div class="form-line">
                                                        <input type="text" id="keywords" class="form-control" placeholder="Keywords" value="{{$company->keywords}}">
                                                    </div>
                                                </div>
                                            </div>
                                    </div>
                                    <div class="row clearfix">
                                            <div class="col-lg-2 col-md-2 col-sm-4 col-12 form-control-label">
                                                <label for="short_description">Short Description</label>
                                            </div>
                                            <div class="col-lg-10 col-md-10 col-sm-8 col-12">
                                                <div class="form-group">
                                                    <div class="form-line">
                                                            <textarea rows="2" id="short_description" class="form-control no-resize auto-growth" placeholder="Short Description"  >
                                                                    {{trim($company->short_description)}}
                                                            </textarea>
                                                    </div>
                                                </div>
                                            </div>
                                    </div>
                                    <div class="row clearfix">
                                            <div class="col-lg-2 col-md-2 col-sm-4 col-12 form-control-label">
                                                <label for="description">Description</label>
                                            </div>
                                            <div class="col-lg-10 col-md-10 col-sm-8 col-12">
                                                <div class="form-group">
                                                    <div class="form-line">
                                                            <textarea rows="6" id="description" class="form-control no-resize auto-growth" placeholder="Short Description"  >
                                                                    {{trim($company->description)}}
                                                            </textarea>
                                                    </div>
                                                </div>
                                            </div>
                                    </div>
                                    <div class="row clearfix">
                                            <div class="col-lg-2 col-md-2 col-sm-4 col-12 form-control-label">
                                                <label for="aboutus">About Us</label>
                                            </div>
                                            <div class="col-lg-10 col-md-10 col-sm-8 col-12">
                                                <div class="form-group">
                                                    <div class="form-line">
                                                            <textarea rows="2" id="aboutus" class="form-control no-resize auto-growth" placeholder="Short Description"  >
                                                                    {{trim($company->aboutus)}}
                                                            </textarea>
                                                    </div>
                                                </div>
                                            </div>
                                    </div>
                                    <div class="row clearfix">
                                            <div class="col-lg-2 col-md-2 col-sm-4 col-12 form-control-label">
                                                <label for="domain">Domain</label>
                                            </div>
                                            <div class="col-lg-10 col-md-10 col-sm-8 col-12">
                                                <div class="form-group">
                                                    <div class="form-line">
                                                            <input type="text" id="domain" class="form-control" placeholder="Domain" value="{{$company->domain}}">
                                                    </div>
                                                </div>
                                            </div>
                                    </div>
                                    <div class="row clearfix">
                                            <div class="col-lg-2 col-md-2 col-sm-4 col-12 form-control-label">
                                                <label for="ceo">CEO</label>
                                            </div>
                                            <div class="col-lg-10 col-md-10 col-sm-8 col-12">
                                                <div class="form-group">
                                                    <div class="form-line">
                                                            <input type="text" id="ceo" class="form-control" placeholder="CEO" value="{{$company->CEO}}">
                                                    </div>
                                                </div>
                                            </div>
                                    </div>
                                    <div class="row clearfix">
                                            <div class="col-lg-2 col-md-2 col-sm-4 col-12 form-control-label">
                                                <label for="address">Address</label>
                                            </div>
                                            <div class="col-lg-10 col-md-10 col-sm-8 col-12">
                                                <div class="form-group">
                                                    <div class="form-line">
                                                            <textarea rows="2" id="address" class="form-control no-resize auto-growth" placeholder="Company Address">
                                                                    {{trim($company->address)}}
                                                            </textarea>
                                                    </div>
                                                </div>
                                            </div>
                                    </div>
                                    <div class="row clearfix">
                                            <div class="col-lg-2 col-md-2 col-sm-4 col-12 form-control-label">
                                                <label for="facebook_page">Facebook Page</label>
                                            </div>
                                            <div class="col-lg-10 col-md-10 col-sm-8 col-12">
                                                <div class="form-group">
                                                    <div class="form-line">
                                                            <input type="url" id="facebook_page" class="form-control" placeholder="Facebook Page" value="{{$company->facebook_url}}">
                                                    </div>
                                                </div>
                                            </div>
                                    </div>
                                    <div class="row clearfix">
                                            <div class="col-lg-2 col-md-2 col-sm-4 col-12 form-control-label">
                                                <label for="twitter_page">Twitter Page</label>
                                            </div>
                                            <div class="col-lg-10 col-md-10 col-sm-8 col-12">
                                                <div class="form-group">
                                                    <div class="form-line">
                                                            <input type="url" id="twitter_page" class="form-control" placeholder="Twitter Page" value="{{$company->twitter_url}}">
                                                    </div>
                                                </div>
                                            </div>
                                    </div>
                                    <div class="row clearfix">
                                            <div class="col-lg-2 col-md-2 col-sm-4 col-12 form-control-label">
                                                <label for="linkedin_page">Linkedin Page</label>
                                            </div>
                                            <div class="col-lg-10 col-md-10 col-sm-8 col-12">
                                                <div class="form-group">
                                                    <div class="form-line">
                                                            <input type="url" id="linkedin_page" class="form-control" placeholder="Linkedin Page" value="{{$company->linkedin_url}}">
                                                    </div>
                                                </div>
                                            </div>
                                    </div>
                                    <div class="row clearfix">
                                            <div class="col-lg-2 col-md-2 col-sm-4 col-12 form-control-label">
                                                <label for="google_page">GooglePlus Page</label>
                                            </div>
                                            <div class="col-lg-10 col-md-10 col-sm-8 col-12">
                                                <div class="form-group">
                                                    <div class="form-line">
                                                            <input type="url" id="google_page" class="form-control" placeholder="GooglePlus Page" value="{{$company->googleplus_url}}">
                                                    </div>
                                                </div>
                                            </div>
                                    </div>
                                    <div class="row clearfix">
                                            <div class="col-lg-2 col-md-2 col-sm-4 col-12 form-control-label">
                                                <label for="youtube_page">Youtube Page</label>
                                            </div>
                                            <div class="col-lg-10 col-md-10 col-sm-8 col-12">
                                                <div class="form-group">
                                                    <div class="form-line">
                                                            <input type="url" id="youtube_page" class="form-control" placeholder="Youtube Page" value="{{$company->youtube_url}}">
                                                    </div>
                                                </div>
                                            </div>
                                    </div>



                            </form>
                    </div>
                </div>
            </div>
        </div>
</div>
@endsection
