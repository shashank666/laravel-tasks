<?php

namespace App\Http\Controllers\Frontend\Legal;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;
use App\Model\RsmOffer;
use DB;

class LegalController extends Controller
{

    public function __construct()
    {
        $google_ad=DB::table('google_ads')->where(['id'=>1,'is_active'=>1])->first();
        View::share('google_ad',$google_ad);
    }


    public function privacy_policy(){
        return view('frontend.legal.privacy_policy');
    }

    public function copyright_policy(){
        return view('frontend.legal.copyright_policy');
    }


    public function trademark_policy(){
        return view('frontend.legal.trademark_policy');
    }

    public function acceptable_use_policy(){
        return view('frontend.legal.acceptable_use_policy');
    }

    public function writer_terms(){
        return view('frontend.legal.writer_terms');
    }

    public function terms_of_service(){
        return view('frontend.legal.terms_of_service');
    }

    public function full_terms(){
        return view('frontend.legal.full_terms');
    }
    public function article_guideline(){
        return view('frontend.legal.article_guideline');
    }
    public function payment_terms(){
        return view('frontend.legal.payment_terms');
    }
    public function do_dont(){
        return view('frontend.legal.do_dont');
    }
    public function eligibility_of_rsm(){
        return view('frontend.legal.eligibility_of_rsm');
    }
    public function offer(){
        $offer_data = RsmOffer::count();
        $offer_remain = 100-$offer_data;
        return view('frontend.legal.offer',compact('offer_remain'));
    }
    public function lockdown_offer(){
        return view('frontend.legal.lockdown_offer');
    }
}
