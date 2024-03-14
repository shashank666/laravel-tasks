<?php

namespace App\Http\Controllers\Frontend\User;
use Illuminate\Support\Str;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use App\Model\User;
use App\Model\UserAccount;
use App\Model\UserEarning;
use DB;

class PaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // function for display create payment details page
    public function payment_page(Request $request){
        $states=DB::table('banks')->orderBy('STATE','asc')->distinct()->get(['STATE']);
        $states=array_unique($states->pluck('STATE')->toArray());
         if(Auth::user()->registered_as_writer==0){
            return view('frontend.payment.create',compact('states'));
        }else{
            $account=UserAccount::where('user_id',Auth::user()->id)->first();
            $user_earning=UserEarning::where(['user_id'=>Auth::user()->id])->first();
            if(Auth::user()->phone_code=='+91'){
                        return view('frontend.payment.edit',compact('account','states','user_earning'));
                    }
            else{
                return view('frontend.payment.edit_other',compact('account','states','user_earning'));
            }
        }
    }
    public function payment_page_show(Request $request){
        $states=DB::table('banks')->orderBy('STATE','asc')->distinct()->get(['STATE']);
        $user_id=auth()->user()->id;
        $user_earning=UserEarning::where(['user_id'=>$user_id])->first();
        
        $states=array_unique($states->pluck('STATE')->toArray());
         if(Auth::user()->registered_as_writer==0){
            return view('frontend.payment.create',compact('states'));
        }else{
            $account=UserAccount::where('user_id',Auth::user()->id)->first();
            if(Auth::user()->phone_code=='+91'){
            return view('frontend.payment.show',compact('account','states','user_earning'));
            }
            else{
                return view('frontend.payment.show_other',compact('account','states','user_earning'));
            }

        }
    }
    // function for create and update user's payment details with action ('create','update')
    public function save_payment(Request $request,$action){
        
        $this->validate($request,[
            /*'mobile'=>'required',
            'account_no'=>'required',
            'account_holdername'=>'required',
            'account_type'=>'required',
            'bank_name'=>'required',
            'bank_ifsc_code'=>'required',*/
            'address'=>'required',
            'zip_code'=>'required',
            'city'=>'required'/*,
            'state'=>'required'*/
        ]);
        if(Auth::user()->phone_code=='+91'){
            $user=[
                'user_id'=>auth()->user()->id,
                'user_email'=>auth()->user()->email,
                'mobile'=>$request->input('mobile'),
                'account_no'=>$request->input('account_no'),
                'account_holdername'=>$request->input('account_holdername'),
                'account_type'=>$request->input('account_type'),
                'bank_name'=>$request->input('bank_name'),
                'bank_ifsc_code'=>$request->input('bank_ifsc_code'),
                'address'=>$request->input('address'),
                'zip_code'=>$request->input('zip_code'),
                'city'=>$request->input('city'),
                'state'=>$request->input('state')
            ];
        }
        else{
            $user=[
                'user_id'=>auth()->user()->id,
                'user_email'=>auth()->user()->email,
                'mobile'=>$request->input('mobile'),
                'address'=>$request->input('address'),
                'zip_code'=>$request->input('zip_code'),
                'city'=>$request->input('city'),
                'country'=>strtoupper($request->input('country')),
                ];
        }

        if($action=='create'){
            $account=UserAccount::create($user); 
            if($account){
                User::where('id', '=',Auth::user()->id)->update(['registered_as_writer' => 1]);
            }
            return redirect('/me/writer_corner')->with(['message'=>'Your payment details has been successfully saved .']);
        }else{
            $account_updated=UserAccount::where('user_id',auth()->user()->id)->update($user); 
            if($account_updated){
                return redirect()->back()->with(['message'=>'Your details has been successfully saved .']);
            }
        }
    }

    /*---------------------- HELPER FUNCTIONS --------------------*/

    // function for display city results based on autocomplete text input
    public function search_cities(Request $request){
        $cities=DB::table('banks')->where('CITY','LIKE',strtoupper($request->query('q')).'%')->orderBy('CITY','asc')->distinct('CITY')->take(100)->get(['CITY']);
        if($cities && count($cities)>0){
            $cities=array_unique($cities->pluck('CITY')->toArray());
            $response['status']='success';
            $response['cities']=$cities;
        }else{
            $response['status']='error';
            $response['message']='no cities found';
        }
        if($request->ajax()){
            return response()->json($response);
        }else{
          return redirect('/');
        }
    }

    // function for display bank results based on autocomplete text input
    public function search_banks(Request $request){

        $banks=DB::table('banks')
        ->where('BANK','LIKE',strtoupper($request->query('q')).'%')
        //->where('CITY',$request->query('city'))
        ->distinct('BANK')
        ->orderBy('BANK','asc')
        ->take(100)->get(['BANK']);

        if($banks && count($banks)>0){
            $banks=array_unique($banks->pluck('BANK')->toArray());
            $response['status']='success';
            $response['banks']=$banks;
        }else{
            $response['status']='error';
            $response['message']='no banks found';
        }
        if($request->ajax()){
            return response()->json($response);
        }else{
            return redirect('/');
        }
    }

}
