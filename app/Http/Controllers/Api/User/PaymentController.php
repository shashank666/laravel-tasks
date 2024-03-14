<?php

namespace App\Http\Controllers\Api\User;
use Illuminate\Support\Str;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\User;
use App\Model\UserAccount;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class PaymentController extends Controller
{
    

    public function get_user_payment(Request $request){
            try{
                if(Auth::user()->user->registered_as_writer==0){
                    return response()->json(array('status'=>'error','result'=>0,'errors'=>'No payment details found , please register as writer'),200);
                }else{
                    $account=UserAccount::where('user_id',Auth::user()->user_id)->first();
                    if(!empty($account)){
                        $this->remove_null($account);
                        return response()->json(array('status'=>'success','result'=>1,'user_payment_details'=>$account),200);
                    }else{
                        return response()->json(array('status'=>'error','result'=>0,'errors'=>'No payment details found'),200);
                    }
                }
            } catch (\Exception $e) {
                $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
                return response()->json($response, 500);
            }
    }

    public function save_user_payment(Request $request){
        try{
            $validator = Validator::make($request->all(), [ 
                'mobile'=>'required',
                'account_no'=>'required',
                'account_holdername'=>'required',
                'account_type'=>'required',
                'bank_name'=>'required',
                'bank_ifsc_code'=>'required',
                'address'=>'required',
                'zip_code'=>'required',
                'city'=>'required',
                'state'=>'required',
                'action'=>'required'
            ]);

            if ($validator->fails()) {
                $response=array('status'=>'error','result'=>0,'errors'=>implode(',',$validator->errors()->all()));
                return response()->json($response, 200);
           }else{
                $user=[
                    'user_id'=>Auth::user()->user_id,
                    'user_email'=>Auth::user()->user->email,
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
    
                    if($request->input('action')=='create'){
                        $account=UserAccount::create($user); 
                        if($account){
                            User::where('id', '=',Auth::user()->user_id)->update(['registered_as_writer' => 1]);
                            $this->remove_null($account);
                            return response()->json(array('status'=>'success','result'=>1,'user_payment_details'=>$account,'message'=>'Payment details has been successfully saved .'),200);
                        }
                    }else{
                        $account_updated=UserAccount::where('user_id',Auth::user()->user_id)->update($user); 
                        if($account_updated){
                            $account=UserAccount::where('user_id', '=',Auth::user()->user_id)->first();
                            $this->remove_null($account);
                            return response()->json(array('status'=>'success','result'=>1,'user_payment_details'=>$account,'message'=>'Payment details has been successfully saved .'),200);
                        }else{
                            return response()->json(array('status'=>'error','result'=>0,'errors'=>'Payment details failed to save.'),200);
                        }
                    }
           }
         } catch (\Exception $e) {
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        } 
    }
}
