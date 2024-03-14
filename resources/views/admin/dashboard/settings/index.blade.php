@extends('admin.layouts.app')
@section('title','Settings')

@section('content')
<div class="header">
        <div class="container">
          <div class="header-body">
            <div class="row">
              <div class="col">
                <h6 class="header-pretitle">
                  Overview
                </h6>
                <h1 class="header-title">
                  Settings
                </h1>
              </div>
            </div> 
          </div> 
        </div>
</div>
<div class="container">
    <div class="row clearfix">
        <div class="col-lg-4 col-md-4 col-sm-6 col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-header-title">COMPANY SETTINGS <i class="fe fe-briefcase ml-2"></i></h4>
                </div>
                <div class="card-body">
                    <p>Conpany Information like name , tagline, logo - icon , address.. </p>
                            <a  href="{{route('admin.company_settings')}}"  class="btn btn-sm btn-block btn-primary">
                            <span>View Company Settings</span>
                            <i class="fe fe-arrow-right"></i>
                        </a>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-6 col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-header-title">COMPANY POLICIES  <i class="fe fe-shield ml-2"></i></h4>
                </div>
                <div class="card-body">
                        <p>See Privacy policy , terms of service , acceptable useof policy etc...</p>
                    
                            <a  href="{{route('admin.company_policy')}}"  class="btn btn-sm btn-block btn-primary">
                            <span>View Company Policies</span>
                            <i class="fe fe-arrow-right"></i>
                        </a>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-6 col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-header-title">EMAIL SETTINGS <i class="fe fe-mail ml-2"></i></h4>
                    </div>
                    <div class="card-body">
                        <p>SMTP Email credentials of company emails</p>
                    
                            <a  href="{{route('admin.email_settings')}}"  class="btn btn-sm btn-block btn-primary">
                            <span>View Email Settings</span>
                            <i class="fe fe-arrow-right"></i>
                        </a>
                    </div>
                </div>
        </div>
    </div>
    <div class="row clearfix">
            <div class="col-lg-4 col-md-4 col-sm-6 col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-header-title">UI SETTINGS <i class="fe fe-airplay ml-2"></i></h4>
                    </div>
                    <div class="card-body">
                        <p>UI settings for customize experience</p>
                    
                            <a  href="{{route('admin.ui_settings')}}"  class="btn btn-sm btn-block btn-primary">
                            <span>View UI Settings</span>
                            <i class="fe fe-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-6 col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-header-title">ANDROID APP SETTINGS <i class="fe fe-smartphone ml-2"></i></h4>
                    </div>
                    <div class="card-body">
                        <p>Pagination & etc stuff</p>
                            <a  href="{{route('admin.android_settings')}}"  class="btn btn-sm   btn-block btn-primary">
                            <span>View Andriod App Settings</span>
                            <i class="fe fe-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-6 col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-header-title">PERSONAL SETTINGS <i class="fe fe-user ml-2"></i></h4>
                    </div>
                    <div class="card-body">
                        <p>Personal settings for admin</p>
                    
                            <a  href="{{route('admin.personal_settings')}}"  class="btn btn-sm btn-block btn-primary">
                            <span>View Personal Settings</span>
                            <i class="fe fe-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
    </div> 
</div>
@endsection
