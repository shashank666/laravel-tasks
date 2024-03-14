@extends('admin.layouts.app') 
@section('title','Company Policies')

@push('scripts')
<script src="/public_admin/assets/libs/ckeditor/ckeditor.js"></script>
<script>
    $(document).ready(function () {

        CKEDITOR.replace('ckeditor_policy', {
            extraPlugins: 'autogrow',
        });
        CKEDITOR.replace('ckeditor_tos', {
            extraPlugins: 'autogrow',
        });
        CKEDITOR.replace('ckeditor_copy', {
            extraPlugins: 'autogrow',
        });
        CKEDITOR.replace('ckeditor_accepatable', {
            extraPlugins: 'autogrow',
        });
        CKEDITOR.replace('ckeditor_trademark', {
            extraPlugins: 'autogrow',
        });
        CKEDITOR.replace('ckeditor_full', {
            extraPlugins: 'autogrow',
        });
        CKEDITOR.replace('ckeditor_writer', {
            extraPlugins: 'autogrow',
        });
        CKEDITOR.replace('ckeditor_article_guideline', {
            extraPlugins: 'autogrow',
        });
        CKEDITOR.replace('ckeditor_payment_terms', {
            extraPlugins: 'autogrow',
        });
        CKEDITOR.replace('ckeditor_do_dont', {
            extraPlugins: 'autogrow',
        });
        CKEDITOR.replace('ckeditor_eligibility_of_rsm', {
            extraPlugins: 'autogrow',
        });
        CKEDITOR.replace('ckeditor_offer', {
            extraPlugins: 'autogrow',
        });
        CKEDITOR.replace('ckeditor_lockdown_offer', {
            extraPlugins: 'autogrow',
        });
    });
</script>
@endpush 

@section('content')
@include('admin.partials.header_title',['header_title'=>'Company Policies'])
<div class="container">
    <div class="card">
        <div class="card-body">
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="nav-item active">
                    <a  class="nav-link active" href="#privacy_policy" data-toggle="tab">PRIVACY POLICY</a>
                </li>
                <li role="presentation" class="nav-item">
                    <a  class="nav-link" href="#tos" data-toggle="tab">TOS</a>
                </li>
                <li role="presentation" class="nav-item">
                    <a  class="nav-link" href="#copyright" data-toggle="tab">COPYRIGHT POLICY</a>
                </li>
                <li role="presentation" class="nav-item">
                    <a  class="nav-link" href="#accepatable" data-toggle="tab">ACCEPTABLE_USE POLICY</a>
                </li>
                <li role="presentation" class="nav-item">
                    <a  class="nav-link" href="#trademark" data-toggle="tab">TRADEMARK POLICY</a>
                </li>
                <li role="presentation" class="nav-item">
                    <a  class="nav-link" href="#full_tos" data-toggle="tab">FULL TOS</a>
                </li>
                <li role="presentation" class="nav-item">
                        <a  class="nav-link" href="#writer" data-toggle="tab">WRITERS TERMS</a>
                </li>
                <li role="presentation" class="nav-item">
                        <a  class="nav-link" href="#article_guideline" data-toggle="tab">ARTICLE GUIDELINE</a>
                </li>
                <li role="presentation" class="nav-item">
                        <a  class="nav-link" href="#payment_terms" data-toggle="tab">PAYMENT TERMS</a>
                </li>
                <li role="presentation" class="nav-item">
                        <a  class="nav-link" href="#do_dont" data-toggle="tab">DOs & DON'ts</a>
                </li>
                <li role="presentation" class="nav-item">
                        <a  class="nav-link" href="#eligibility_of_rsm" data-toggle="tab">ELIGIBILITY OF RSM</a>
                </li>
                <li role="presentation" class="nav-item">
                        <a  class="nav-link" href="#offer" data-toggle="tab">OFFER</a>
                </li>
                <li role="presentation" class="nav-item">
                        <a  class="nav-link" href="#lockdown_offer" data-toggle="tab">LOCKDOWN OFFER</a>
                </li>
                
            </ul>
            <div class="tab-content mt-5">
                <div role="tabpanel" class="tab-pane in active" id="privacy_policy">
                    <form method="POST" action="{{route('admin.update_policy',['policy_type'=>'privacy_policy'])}}">
                        {{ csrf_field() }}
                        <textarea id="ckeditor_policy" name="policy">
                            {!!$company->privacy_policy!!}
                        </textarea>
                        <br/>
                        <button class="btn btn-block btn-success" type="submit">UPDATE PRIVACY POLICY</button>
                    </form>
                </div>

                <div role="tabpanel" class="tab-pane fade" id="tos">
                    <form method="POST" action="{{route('admin.update_policy',['policy_type'=>'terms_of_service'])}}">
                        {{ csrf_field() }}
                        <textarea id="ckeditor_tos" name="policy">
                            {!!$company->terms_of_service!!}
                        </textarea>
                        <br/>
                        <button class="btn btn-block btn-success" type="submit">UPDATE TERMS OF SERVICE</button>
                    </form>
                </div>
                <div role="tabpanel" class="tab-pane fade " id="copyright">
                    <form method="POST" action="{{route('admin.update_policy',['policy_type'=>'copyright_policy'])}}">
                        {{ csrf_field() }}
                        <textarea id="ckeditor_copy" name="policy">
                            {!!$company->copyright_policy!!}
                        </textarea>
                        <br/>
                        <button class="btn btn-block btn-success" type="submit">UPDATE COPYRIGHT POLICY</button>
                    </form>
                </div>
                <div role="tabpanel" class="tab-pane fade" id="accepatable">
                    <form method="POST" action="{{route('admin.update_policy',['policy_type'=>'acceptable_use_policy'])}}">
                        {{ csrf_field() }}
                        <textarea id="ckeditor_accepatable" name="policy">
                            {!!$company->acceptable_use_policy!!}
                        </textarea>
                        <br/>
                        <button class="btn btn-block btn-success" type="submit">UPDATE ACCEPTABLE_USE_POLICY</button>
                    </form>
                </div>
                <div role="tabpanel" class="tab-pane fade" id="trademark">
                    <form method="POST" action="{{route('admin.update_policy',['policy_type'=>'trademark_policy'])}}">
                        {{ csrf_field() }}
                        <textarea id="ckeditor_trademark" name="policy">
                            {!!$company->trademark_policy!!}
                        </textarea>
                        <br/>
                        <button class="btn btn-block btn-success" type="submit">UPDATE TRADEMARK POLICY</button>
                    </form>
                </div>
                <div role="tabpanel" class="tab-pane fade" id="full_tos">
                    <form method="POST" action="{{route('admin.update_policy',['policy_type'=>'full_terms_of_service'])}}">
                        {{ csrf_field() }}
                        <textarea id="ckeditor_full" name="policy">
                            {!!$company->full_terms_of_service!!}
                        </textarea>
                        <br/>
                        <button class="btn btn-block btn-success" type="submit">UPDATE FULL TERMS OF SERVICE</button>
                    </form>
                </div>
                <div role="tabpanel" class="tab-pane fade" id="writer">
                        <form method="POST" action="{{route('admin.update_policy',['policy_type'=>'writers_terms'])}}">
                            {{ csrf_field() }}
                            <textarea id="ckeditor_writer" name="policy">
                                {!!$company->writers_terms!!}
                            </textarea>
                            <br/>
                            <button class="btn btn-block btn-success" type="submit">UPDATE WRITER&apos;S TERMS</button>
                        </form>
                </div>
                <div role="tabpanel" class="tab-pane fade" id="article_guideline">
                        <form method="POST" action="{{route('admin.update_policy',['policy_type'=>'article_guideline'])}}">
                            {{ csrf_field() }}
                            <textarea id="ckeditor_article_guideline" name="policy">
                                {!!$company->article_guideline!!}
                            </textarea>
                            <br/>
                            <button class="btn btn-block btn-success" type="submit">UPDATE ARTICLE GUIDELINE</button>
                        </form>
                </div>

                <div role="tabpanel" class="tab-pane fade" id="payment_terms">
                        <form method="POST" action="{{route('admin.update_policy',['policy_type'=>'payment_terms'])}}">
                            {{ csrf_field() }}
                            <textarea id="ckeditor_payment_terms" name="policy">
                                {!!$company->payment_terms!!}
                            </textarea>
                            <br/>
                            <button class="btn btn-block btn-success" type="submit">UPDATE PAYMENT TERMS</button>
                        </form>
                </div>

                <div role="tabpanel" class="tab-pane fade" id="do_dont">
                        <form method="POST" action="{{route('admin.update_policy',['policy_type'=>'do_dont'])}}">
                            {{ csrf_field() }}
                            <textarea id="ckeditor_do_dont" name="policy">
                                {!!$company->do_dont!!}
                            </textarea>
                            <br/>
                            <button class="btn btn-block btn-success" type="submit">UPDATE DOs & DON'ts</button>
                        </form>
                </div> 

                <div role="tabpanel" class="tab-pane fade" id="eligibility_of_rsm">
                        <form method="POST" action="{{route('admin.update_policy',['policy_type'=>'eligibility_of_rsm'])}}">
                            {{ csrf_field() }}
                            <textarea id="ckeditor_eligibility_of_rsm" name="policy">
                                {!!$company->eligibility_of_rsm!!}
                            </textarea>
                            <br/>
                            <button class="btn btn-block btn-success" type="submit">UPDATE ELIGIBILITY OF RSM</button>
                        </form>
                </div>

                <div role="tabpanel" class="tab-pane fade" id="offer">
                        <form method="POST" action="{{route('admin.update_policy',['policy_type'=>'offer'])}}">
                            {{ csrf_field() }}
                            <textarea id="ckeditor_offer" name="policy">
                                {!!$company->offer!!}
                            </textarea>
                            <br/>
                            <button class="btn btn-block btn-success" type="submit">UPDATE OFFER</button>
                        </form>
                </div>

                
                <div role="tabpanel" class="tab-pane fade" id="lockdown_offer">
                        <form method="POST" action="{{route('admin.update_policy',['policy_type'=>'lockdown_offer'])}}">
                            {{ csrf_field() }}
                            <textarea id="ckeditor_lockdown_offer" name="policy">
                                {!!$company->lockdown_offer!!}
                            </textarea>
                            <br/>
                            <button class="btn btn-block btn-success" type="submit">UPDATE LOCKDOWN OFFER</button>
                        </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection