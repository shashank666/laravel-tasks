<?php

namespace App\Http\Controllers\Api\Auth;
use Illuminate\Support\Str;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Helpers\DisposibleMails;
use App\Http\Helpers\ValidDomains;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Model\User;
use App\Model\UserDevice;
use App\Model\UserLogin;
use App\Model\EmailPassword;

use Carbon\Carbon;
use Config;

use Mail;
use App\Mail\Auth\AccountCreatedMail;
use App\Mail\Auth\WelcomeMail;
use App\Mail\Auth\ResetPasswordMail;
use App\Http\Helpers\MailJetHelper;

use DB;

class AuthController extends Controller
{
    use AuthenticatesUsers;

    public function check_account(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                    'email' => 'required',
                ]
            );

            if ($validator->fails()) {
                $response=array('status'=>'error','errors'=>implode(',', $validator->errors()->all()));
                return response()->json($response, 200);
            }

            $check_user=User::where('mobile',$request->input('email'))->orWhere('email',$request->input('email'))->first();
            if ($check_user){
                $response=array('status'=>'success','result'=>1,'message'=>'Account Exists','user_id'=>$check_user->id,'event'=>'login_password');
                return response()->json($response, 200);
            }else{
                $response=array('status'=>'success','result'=>0,'message'=>'No such account exists','event'=>'register');
                return response()->json($response, 200);
            }

        } catch (\Exception $e) {
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error','event'=>'none');
            return response()->json($response, 500);
        }
    }

    //Phone only Login
    public function Phonelogin(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                    'email' => 'required',
                    'mobile' => 'required',
                    'password' => 'required',
                    'device_serial'=>'required'
                ]
            );

            if ($validator->fails()) {
                $response=array('status'=>'error','result'=>0,'errors'=>implode(',', $validator->errors()->all()),'event'=>'none');
                return response()->json($response, 200);
            }


            $user = User::where('mobile',$request->input('mobile'))
            ->first();

            if (empty($user))
            {
                $response=array('status'=>'error','result'=>0,'errors'=>'No such account exists.','event'=>'register');
                return response()->json($response, 200);
            }
            else
            {
                if($user->is_active==0){
                     // check for user account is blocked ? then show account blocked
                    $blocked_reason=$user->blocked_reason!=NULL?' due to '.$user->blocked_reason:'';
                    $response=array('status'=>'error','result'=>0,'errors'=>'Your account has been blocked'.$blocked_reason,'event'=>'account_blocked');
                    return response()->json($response, 200);
                }else{
                        if(Auth::attempt(['mobile' => $request->input('mobile'),'password' => $request->input('password')]))
                        {
                            $user_id=Auth::user()->id;
                            $device_serial=$request->input('device_serial');
                            $api_token = bin2hex(random_bytes(64));
                            $gcm_token=$request->has('gcm_token')?$request->input('gcm_token'):null;
                            $deviceFound=$this->check_user_device($user_id,$device_serial);
                            $app_version=$request->headers->has('app-version')?$request->header('app-version'):'none';
                            if(!empty($deviceFound)){
                                $device=$this->update_api_token($user_id,$device_serial,$api_token,$gcm_token,$app_version);

                            }else{
                                $device=$this->add_user_device($request->all(),$user_id);
                            }

                            if ($user->image != "" && file_exists($user->image)) {
                                $user->image = url($user->image);
                            }
                            $this->remove_null($user);
                            $this->remove_null($device);
                            $show_categories=count(Auth::user()->followed_categories)>0?0:1;
                            $response=array('status'=>'success','result'=>1,'user'=>$user,'device'=>$device,'message'=>'Login successful','show_categories'=>$show_categories,'event'=>'home');
                            return response()->json($response, 200);
                        }
                        else
                        {
                            $response=array('status'=>'error','result'=>0,'errors'=>'Failed To Login , Incorrect credentials .','event'=>'none');
                            return response()->json($response, 200);
                        }
                }
            }
         } catch (\Exception $e) {
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error '.$e,'event'=>'none');
            return response()->json($response, 500);
        }
    }

    // function for login user
    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                    'email' => 'required',
                    'password' => 'required',
                    'device_serial'=>'required'
                ]
            );

            if ($validator->fails()) {
                $response=array('status'=>'error','result'=>0,'errors'=>implode(',', $validator->errors()->all()),'event'=>'none');
                return response()->json($response, 200);
            }


            $user = User::where('email',$request->input('email'))
            ->orWhere('mobile',$request->input('email'))
            ->first();

            if (empty($user))
            {
                $response=array('status'=>'error','result'=>0,'errors'=>'No such account exists.','event'=>'register');
                return response()->json($response, 200);
            }
            else
            {
                //  if($user->mobile_verified==0){
                //     $response=array('status'=>'error','result'=>0,'errors'=>'Your mobile is not verified','event'=>'verify_mobile','user_id'=>$user->id);
                //     return response()->json($response, 200);
                //  }
                if($user->is_active==0){
                     // check for user account is blocked ? then show account blocked
                    $blocked_reason=$user->blocked_reason!=NULL?' due to '.$user->blocked_reason:'';
                    $response=array('status'=>'error','result'=>0,'errors'=>'Your account has been blocked'.$blocked_reason,'event'=>'account_blocked');
                    return response()->json($response, 200);
                }else{
                        if(Auth::attempt(['email' => $user->email, 'password' => $request->input('password')]))
                        {
                            $user_id=Auth::user()->id;
                            $device_serial=$request->input('device_serial');
                            $api_token = bin2hex(random_bytes(64));
                            $gcm_token=$request->has('gcm_token')?$request->input('gcm_token'):null;
                            $deviceFound=$this->check_user_device($user_id,$device_serial);
                            $app_version=$request->headers->has('app-version')?$request->header('app-version'):'none';
                            if(!empty($deviceFound)){
                                $device=$this->update_api_token($user_id,$device_serial,$api_token,$gcm_token,$app_version);

                            }else{
                                $device=$this->add_user_device($request->all(),$user_id);
                            }

                            if ($user->image != "" && file_exists($user->image)) {
                                $user->image = url($user->image);
                            }
                            $this->remove_null($user);
                            $this->remove_null($device);
                            $show_categories=count(Auth::user()->followed_categories)>0?0:1;
                            $response=array('status'=>'success','result'=>1,'user'=>$user,'device'=>$device,'message'=>'Login successful','show_categories'=>$show_categories,'event'=>'home');
                            return response()->json($response, 200);
                        }
                        else
                        {
                            $response=array('status'=>'error','result'=>0,'errors'=>'Failed To Login , Incorrect credentials .','event'=>'none');
                            return response()->json($response, 200);
                        }
                }
            }
         } catch (\Exception $e) {
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error','event'=>'none');
            return response()->json($response, 500);
        }
    }

    // function for register user
    public function register2(Request $request)
    {
        try {
            // $validator = Validator::make($request->all(), [
            //         'name' => 'required|string|max:255',
            //         'email' => 'required|email|unique:users,email',
            //         'phone_code' => 'required',
            //         'mobile' => 'required|string|max:15|unique:users',
            //         'password' => 'required|string|min:6|confirmed'
            //     ]
            // );

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:6|confirmed'
            ]
        );

            if ($validator->fails()) {
                $response=array('status'=>'error','result'=>0,'errors'=>implode(',', $validator->errors()->all()),'event'=>'none');
                return response()->json($response, 200);
            }else{

                $disposible=new DisposibleMails();
                $disposible_mails = $disposible->dmails;

                if(in_array(explode('@',trim($request->input('email'),' '))[1],$disposible_mails)){
                    $response=array('status'=>'error','result'=>0,'errors'=>'We do not accept disposible emails.','event'=>'none');
                    return response()->json($response, 200);
                }

                // $valid=new ValidDomains();
                // $valid_domains = $valid->valid_domains;

                // if(in_array(explode('@',trim($request->input('email'),' '))[1],$valid_domains))
                // {
                    $data=$request->all();
                    $data['ip']=isset($_SERVER["HTTP_CF_CONNECTING_IP"])?$_SERVER["HTTP_CF_CONNECTING_IP"]:$request->ip();
                    $user = $this->create_new_user($data,'register');
                    $user->last_login_at = array('date'=>Carbon::now(),'timezone_type'=>3,'timezone'=>'Asia/Kolkata');
                    $user_id=$user->id;
                    $device=$this->add_user_device($request->all(),$user_id);
                    try{
                        //Mail::send(new AccountCreatedMail($user,$user->verify_token));
                        $mailJET=new MailJetHelper();
                        $mailJET->send_account_created_mail($user,$user->verify_token);
                    }
                    catch(\Exception $e){}
                    // if($user->phone_code=='+1'){
                    // $mobile_with_code=preg_replace('/\D+/', '',$user->phone_code.$user->mobile);
                    // $OTPstatus=$this->nexemo_send_otp_to_USA($user->mobile_otp,$user->mobile_otp_expired_at,$mobile_with_code);
                    // $this->remove_null($user);
                    // $this->remove_null($device);
                    // $user->mobile_otp_expired_at = array('date'=>Carbon::now(),'timezone_type'=>3,'timezone'=>'Asia/Kolkata');
                    // if($OTPstatus['messages'][0]['status'] == 0){
                    //     $response=array('status'=>'success','result'=>1,'message'=>'OTP successfully sent to registered mobile number.','user'=>$user,'device'=>$device,'event'=>'verify_otp');
                    //     return response()->json($response, 200);
                    //  }else{
                    //     $response=array('status'=>'error','result'=>0,'errors'=>'Failed to send OTP to provided mobile number.','event'=>'none');
                    //     return response()->json($response, 200);
                    //     }
                    // }
                    // else{
                    // $mobile_with_code=preg_replace('/\D+/', '',$user->phone_code.$user->mobile);
                    // $OTPstatus=$this->nexemo_send_otp($user->mobile_otp,$user->mobile_otp_expired_at,$mobile_with_code);
                    // $this->remove_null($user);
                    // $this->remove_null($device);
                    // $user->mobile_otp_expired_at = array('date'=>Carbon::now(),'timezone_type'=>3,'timezone'=>'Asia/Kolkata');
                    // if($OTPstatus['messages'][0]['status'] == 0){
                    //     $response=array('status'=>'success','result'=>1,'message'=>'OTP successfully sent to registered mobile number.','user'=>$user,'device'=>$device,'event'=>'verify_otp');
                    //     return response()->json($response, 200);
                    //  }else{
                    //     $response=array('status'=>'error','result'=>0,'errors'=>'Failed to send OTP to provided mobile number.','event'=>'none');
                    //     return response()->json($response, 200);
                    //     }
                    // }
                    $response=array('status'=>'success','result'=>1,'message'=>'Acccount Created Successfuly','user'=>$user,'device'=>$device,'event'=>'verify_otp');
                        return response()->json($response, 200);
                // }else{
                //     $response=array('status'=>'error','result'=>0,'errors'=>'Please provide an valid email address.','event'=>'none');
                //     return response()->json($response, 200);
                // }
            }
         } catch (\Exception $e) {
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error','event'=>'none');
            return response()->json($response, 500);
        }
    }

    // function for register user
    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                    'name' => 'required|string|max:255',
                    'email' => 'required|email|unique:users,email',
                    'phone_code' => 'required',
                    'mobile' => 'required|string|max:15|unique:users',
                    'password' => 'required|string|min:6|confirmed'
                ]
            );

            if ($validator->fails()) {
                $response=array('status'=>'error','result'=>0,'errors'=>implode(',', $validator->errors()->all()),'event'=>'none');
                return response()->json($response, 200);
            }else{

                $disposible=new DisposibleMails();
                $disposible_mails = $disposible->dmails;

                if(in_array(explode('@',trim($request->input('email'),' '))[1],$disposible_mails)){
                    $response=array('status'=>'error','result'=>0,'errors'=>'We do not accept disposible emails.','event'=>'none');
                    return response()->json($response, 200);
                }

                // $valid=new ValidDomains();
                // $valid_domains = $valid->valid_domains;

                // if(in_array(explode('@',trim($request->input('email'),' '))[1],$valid_domains))
                // {
                    $data=$request->all();
                    $data['ip']=isset($_SERVER["HTTP_CF_CONNECTING_IP"])?$_SERVER["HTTP_CF_CONNECTING_IP"]:$request->ip();
                    $user = $this->create_new_user($data,'register');
                    $user->last_login_at = array('date'=>Carbon::now(),'timezone_type'=>3,'timezone'=>'Asia/Kolkata');
                    $user_id=$user->id;
                    $device=$this->add_user_device($request->all(),$user_id);
                    try{
                        //Mail::send(new AccountCreatedMail($user,$user->verify_token));
                        $mailJET=new MailJetHelper();
                        $mailJET->send_account_created_mail($user,$user->verify_token);
                    }
                    catch(\Exception $e){}
                    if($user->phone_code=='+1'){
                    $mobile_with_code=preg_replace('/\D+/', '',$user->phone_code.$user->mobile);
                    $OTPstatus=$this->nexemo_send_otp_to_USA($user->mobile_otp,$user->mobile_otp_expired_at,$mobile_with_code);
                    $this->remove_null($user);
                    $this->remove_null($device);
                    $user->mobile_otp_expired_at = array('date'=>Carbon::now(),'timezone_type'=>3,'timezone'=>'Asia/Kolkata');
                    if($OTPstatus['messages'][0]['status'] == 0){
                        $response=array('status'=>'success','result'=>1,'message'=>'OTP successfully sent to registered mobile number.','user'=>$user,'device'=>$device,'event'=>'verify_otp');
                        return response()->json($response, 200);
                     }else{
                        $response=array('status'=>'error','result'=>0,'errors'=>'Failed to send OTP to provided mobile number.','event'=>'none');
                        return response()->json($response, 200);
                        }
                    }
                    else{
                    $mobile_with_code=preg_replace('/\D+/', '',$user->phone_code.$user->mobile);
                    $OTPstatus=$this->nexemo_send_otp($user->mobile_otp,$user->mobile_otp_expired_at,$mobile_with_code);
                    $this->remove_null($user);
                    $this->remove_null($device);
                    $user->mobile_otp_expired_at = array('date'=>Carbon::now(),'timezone_type'=>3,'timezone'=>'Asia/Kolkata');
                    if($OTPstatus['messages'][0]['status'] == 0){
                        $response=array('status'=>'success','result'=>1,'message'=>'OTP successfully sent to registered mobile number.','user'=>$user,'device'=>$device,'event'=>'verify_otp');
                        return response()->json($response, 200);
                     }else{
                        $response=array('status'=>'error','result'=>0,'errors'=>'Failed to send OTP to provided mobile number.','event'=>'none');
                        return response()->json($response, 200);
                        }
                    }
                
                // }else{
                //     $response=array('status'=>'error','result'=>0,'errors'=>'Please provide an valid email address.','event'=>'none');
                //     return response()->json($response, 200);
                // }
            }
         } catch (\Exception $e) {
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error','event'=>'none');
            return response()->json($response, 500);
        }
    }

    // function for add mobile and send otp to mobile
    public function add_mobile(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                    'user_id'=>'required',
                    'phone_code' => 'required',
                    'mobile' => 'required|string|max:15'
                ]
            );
            if ($validator->fails()) {
                $response=array('status'=>'error','result'=>0,'errors'=>implode(',', $validator->errors()->all()),'event'=>'none');
                return response()->json($response, 200);
            }else{
                $mobile=$request->input('mobile');
                $phone_code=$request->input('phone_code');
                $user_id=$request->input('user_id');

                if(User::where('mobile',$mobile)->whereNotIn('id',[$user_id])->exists()){
                    $response=array('status'=>'error','result'=>0,'errors'=>'Mobile number has been already taken','event'=>'none');
                    return response()->json($response,200);
                }else{
                    $OTP=random_int(100000, 999999);
                    $EXPIRED=Carbon::now()->addMinutes(15);

                    User::where('id',$user_id)->update([
                        'phone_code'=>trim($phone_code),
                        'mobile'=>trim($mobile),
                        'mobile_verified' => 0,
                        'mobile_otp'=>$OTP,
                        'mobile_otp_expired_at'=>$EXPIRED,
                        'otp_attempts'=>0,
                        'last_attempts'=>NULL
                    ]);
                    if($phone_code=='+1'){
                        $mobile_with_code=preg_replace('/\D+/', '',$phone_code.$mobile);
                        $OTPstatus=$this->nexemo_send_otp_to_USA($OTP,$EXPIRED,$mobile_with_code);
                        if($OTPstatus['messages'][0]['status'] == 0){
                            $response=array('status'=>'success','result'=>1,'message'=>'OTP successfully sent to registered mobile number.','event'=>'verify_otp');
                            return response()->json($response, 200);
                        }else{
                            $response=array('status'=>'error','result'=>0,'errors'=>'Failed to send OTP to provided mobile number.','event'=>'none');
                            return response()->json($response, 200);
                        }
                    }
                    else{
                        $mobile_with_code=preg_replace('/\D+/', '',$phone_code.$mobile);
                        $OTPstatus=$this->nexemo_send_otp($OTP,$EXPIRED,$mobile_with_code);
                        if($OTPstatus['messages'][0]['status'] == 0){
                            $response=array('status'=>'success','result'=>1,'message'=>'OTP successfully sent to registered mobile number.','event'=>'verify_otp');
                            return response()->json($response, 200);
                        }else{
                            $response=array('status'=>'error','result'=>0,'errors'=>'Failed to send OTP to provided mobile number.','event'=>'none');
                            return response()->json($response, 200);
                        }
                    }

                }
            }
        } catch (\Exception $e) {
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error','event'=>'none');
            return response()->json($response, 500);
        }
    }

    // function for verify otp
    public function verify_otp(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                    'mobile' => 'required',
                    'otp' => 'required',
                ]
            );

            if ($validator->fails()) {
                $response=array('status'=>'error','result'=>0,'errors'=>implode(',', $validator->errors()->all()),'event'=>'none');
                return response()->json($response, 200);
            }else{
                $user = User::where(['mobile'=>$request->input('mobile'),'mobile_otp'=>$request->input('otp')])->first();
                if($user){
                    if(Carbon::now()>Carbon::parse($user->mobile_otp_expired_at)){
                        $response=array('status'=>'error','result'=>0,'errors'=>'OTP has been expired , Please click Resend OTP to create new.','event'=>'none');
                        return response()->json($response,200);
                    }else{
                            $user->mobile_otp = NULL;
                            $user->mobile_verified = 1;
                            $user->mobile_otp_expired_at=NULL;
                            $user->otp_attempts =0;
                            $user->last_attempts=NULL;
                            $user->save();
                            $device=$this->check_user_device($user->id,$request->input('device_serial'));
                            $this->remove_null($user);
                            $this->remove_null($device);
                            Auth::login($user);
                            $user->last_login_at = array('date'=>Carbon::now(),'timezone_type'=>3,'timezone'=>'Asia/Kolkata');
                            $response=array('status'=>'success','result'=>1,'user'=>$user,'device'=>$device,'message'=>'OTP successfully verified.','event'=>'home');
                            return response()->json($response, 200);
                    }
                }else{
                    $response=array('status'=>'error','result'=>0,'errors'=>'OTP verification failed.','event'=>'none');
                    return response()->json($response, 200);
                }
            }
         } catch (\Exception $e) {
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error','event'=>'none');
            return response()->json($response, 500);
        }
    }

    // function for resend otp
    public function resend_otp(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                    'mobile' => 'required',
                ]
            );

            if ($validator->fails()) {
                $response=array('status'=>'error','result'=>0,'errors'=>implode(',', $validator->errors()->all()),'event'=>'none');
                return response()->json($response, 200);
            }else{
                $user = User::where('mobile',$request->input('mobile'))->first();
                if (empty($user) || $request['mobile'] == "" || $request['mobile']==NULL) {
                    $response=array('status'=>'error','result'=>0,'errors'=>'Failed To Resend OTP','event'=>'none');
                    return response()->json($response, 200);
                }else{
                    $attempts=$user->otp_attempts!=NULL?$user->otp_attempts:0;
                    $attempts=$attempts+1;
                    $mobile_with_code=preg_replace('/\D+/', '',$user->phone_code.$user->mobile);
                    if($user->otp_attempts>=3){
                        if(Carbon::parse($user->last_attempts)->diffInMinutes()<=120){
                            $response=array('status'=>'error','result'=>0,'errors'=>'You have reached maximum limit of resend OTP , please try again after 2 hours.','event'=>'none');
                            return response()->json($response, 200);
                        }else{
                            $OTP=random_int(100000, 999999);
                            $EXPIRED=Carbon::now()->addMinutes(15);
                            $this->update_attempts($user,$OTP,$EXPIRED,1,Carbon::now());
                            if($user->phone_code=='+1'){
                                $OTPstatus=$this->nexemo_send_otp_to_USA($OTP,$EXPIRED,$mobile_with_code);
                                if($OTPstatus['messages'][0]['status'] == 0){
                                    $response=array('status'=>'success','result'=>1,'message'=>'OTP successfully sent.','event'=>'none');
                                    return response()->json($response, 200);
                                }else{
                                    $response=array('status'=>'error','result'=>0,'errors'=>'Failed to send OTP to provided mobile number.','event'=>'none');
                                    return response()->json($response, 200);
                                }
                            }
                            else{
                                $OTPstatus=$this->nexemo_send_otp($OTP,$EXPIRED,$mobile_with_code);
                                if($OTPstatus['messages'][0]['status'] == 0){
                                    $response=array('status'=>'success','result'=>1,'message'=>'OTP successfully sent.','event'=>'none');
                                    return response()->json($response, 200);
                                }else{
                                    $response=array('status'=>'error','result'=>0,'errors'=>'Failed to send OTP to provided mobile number.','event'=>'none');
                                    return response()->json($response, 200);
                                }
                            }

                        }
                    }else{
                        $OTP=random_int(100000, 999999);
                        $EXPIRED=Carbon::now()->addMinutes(15);
                        $this->update_attempts($user,$OTP,$EXPIRED,$attempts,Carbon::now());
                        if($user->phone_code=='+1'){
                            $OTPstatus=$this->nexemo_send_otp_to_USA($OTP,$EXPIRED,$mobile_with_code);
                            if($OTPstatus['messages'][0]['status'] == 0){
                                $response=array('status'=>'success','result'=>1,'errors'=>'OTP successfully sent.','event'=>'none');
                                return response()->json($response, 200);
                            }else{
                                $response=array('status'=>'error','result'=>0,'errors'=>'Failed to send OTP to provided mobile number.','event'=>'none');
                                return response()->json($response, 200);
                            }
                        }
                        else{
                            $OTPstatus=$this->nexemo_send_otp($OTP,$EXPIRED,$mobile_with_code);
                            if($OTPstatus['messages'][0]['status'] == 0){
                                $response=array('status'=>'success','result'=>1,'errors'=>'OTP successfully sent.','event'=>'none');
                                return response()->json($response, 200);
                            }else{
                                $response=array('status'=>'error','result'=>0,'errors'=>'Failed to send OTP to provided mobile number.','event'=>'none');
                                return response()->json($response, 200);
                            }
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error','event'=>'none');
            return response()->json($response, 500);
        }
    }

    // function for logout
    public function logout(Request $request){
        try{
            if (Auth::user()) {
                $user_id=Auth::user()->user_id;
                $serial=$request->input('device_serial');
                $api_token=null;
                $gcm_token=null;
                $app_version=$request->headers->has('app-version')?$request->header('app-version'):'none';

                $ip=isset($_SERVER["HTTP_CF_CONNECTING_IP"])?$_SERVER["HTTP_CF_CONNECTING_IP"]:$request->ip();
                UserLogin::where(['ip_address'=>$ip,'user_id'=>$user_id])->delete();
                $this->update_api_token($user_id,$serial,$api_token,$gcm_token,$app_version);

                $response=array('status'=>'success','result'=>1,'message'=>'Logout Successful','event'=>'login');
                return response()->json($response,200);
            }else{
                $response=array('status'=>'error','result'=>0,'errors'=>'Unable to logout','event'=>'none');
                return response()->json($response,200);
            }
        }
        catch(\Exception $e){
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error','event'=>'none');
            return response()->json($response, 500);
        }
    }

    // function for update user device fcm_token for notification
    public function update_gcm_token(Request $request){
        try{
            if (Auth::user()) {
                $validator = Validator::make($request->all(), [
                    'device_serial' => 'required',
                    'gcm_token' => 'required'
                ]);
                if ($validator->fails()) {
                    $response=array('status'=>'error','result'=>0,'errors'=>implode(',', $validator->errors()->all()),'event'=>'none');
                    return response()->json($response, 200);
                }else{
                    $user_id=Auth::user()->user_id;
                    $serial=$request->input('device_serial');
                    $gcm_token=$request->input('gcm_token');
                    $device=UserDevice::where(['user_id'=>$user_id,'device_serial'=>$serial])->first();
                    if($device){
                        $device->gcm_token=$gcm_token;
                        $device->save();
                        $response=array('status'=>'success','result'=>1,'message'=>'Successfully updated gcm_token','event'=>'none');
                        return response()->json($response,200);
                    }else{
                        $response=array('status'=>'error','result'=>0,'message'=>'Unable to update gcm_token','event'=>'none');
                        return response()->json($response,200);
                    }
                }
            }else{
                $response=array('status'=>'error','result'=>0,'errors'=>'Unable to find user','event'=>'none');
                return response()->json($response,200);
            }
        }
        catch(\Exception $e){
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error','event'=>'none');
            return response()->json($response, 500);
        }
    }

    // function for forgot password
    public function forgot_password(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
            ]);

            if ($validator->fails()) {
                $response=array('status'=>'error','result'=>0,'errors'=>implode(',', $validator->errors()->all()),'event'=>'none');
                return response()->json($response, 200);
            }else{
                $user=User::where('email',$request->input('email'))->first();
                if($user){
                    $reset_token= bin2hex(random_bytes(32));
                    $reset_token_expired_at=Carbon::now()->addHours(1);
                    $user->update(['reset_token'=>$reset_token,'reset_token_expired_at'=>$reset_token_expired_at]);
                    DB::table('password_resets')->insert(['email'=>$request->input('email'),'token'=>$reset_token]);
                    $reseturl= url('password/reset/now/'.$reset_token);
                    try{
                         Mail::send(new ResetPasswordMail($user,$reseturl));
                         // $mailJET=new MailJetHelper();
                         // $mailJET->send_reset_password_mail($user,$reseturl);
                         $response=array('status'=>'success','result'=>1,'message'=>'A reset password link has been sent to your email address','event'=>'login');
                         return response()->json($response, 200);
                        }
                    catch(\Exception $e){
                        $response=array('status'=>'error','result'=>0,'errors'=>'Failed to send reset password link , please try again later','event'=>'none');
                        return response()->json($response, 200);
                    }
                }else{
                    $response=array('status'=>'error','result'=>0,'errors'=>"We can't find a user with this email address",'event'=>'none');
                    return response()->json($response, 200);
                }
            }
        }
        catch(\Exception $e){
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error','event'=>'none');
            return response()->json($response, 500);
        }
    }


    // function for social authentication
    public function social_auth(Request $request){
        try{

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email',
                'device_serial'=>'required'
            ]);

            if ($validator->fails()) {
                $response=array('status'=>'error','result'=>0,'errors'=>implode(',', $validator->errors()->all()),'event'=>'none');
                return response()->json($response, 200);
            }else{
                $user_found = User::where(['email'=>$request->input('email')])->first();
                if(!empty($user_found)){
                    if($user_found->is_active==0){
                        $blocked_reason=$user_found->blocked_reason!=NULL?' due to '.$user_found->blocked_reason:'';
                        $response=array('status'=>'error','result'=>0,'errors'=>'Your account has been blocked '.$blocked_reason,'event'=>'account_blocked');
                        return response()->json($response, 200);
                    }
                    // if($user_found->mobile==null || $user_found->mobile_verified==0){
                    //     $response=array('status'=>'error','result'=>0,'errors'=>'Your mobile is not verified','event'=>'verify_mobile','user_id'=>$user_found->id);
                    //     return response()->json($response, 200);
                    // }

                    $ip_address=isset($_SERVER["HTTP_CF_CONNECTING_IP"])?$_SERVER["HTTP_CF_CONNECTING_IP"]:$request->ip();
                    $user_found->update([
                        'last_login_at' => Carbon::now(),
                        'last_login_ip' => $ip_address
                    ]);
                    $user_id=$user_found->id;
                    $device_serial=$request->input('device_serial');
                    $api_token = bin2hex(random_bytes(64));
                    $gcm_token=$request->has('gcm_token')?$request->input('gcm_token'):null;
                    $app_version=$request->headers->has('app-version')?$request->header('app-version'):'none';

                    $deviceFound=$this->check_user_device($user_id,$device_serial);
                    if(!empty($deviceFound)){
                        $device=$this->update_api_token($user_id,$device_serial,$api_token,$gcm_token,$app_version);
                        $this->remove_null($user_found);
                        $this->remove_null($device);
                        Auth::login($user_found);
                        $user_found->last_login_at = array('date'=>Carbon::now(),'timezone_type'=>3,'timezone'=>'Asia/Kolkata');
                        $response=array('status'=>'success','result'=>1,'user'=>$user_found,'device'=>$device,'message'=>'Login successful','event'=>'home');
                        return response()->json($response, 200);
                    }else{
                        $device=$this->add_user_device($request->all(),$user_id);
                        $this->remove_null($user_found);
                        $this->remove_null($device);
                        Auth::login($user_found);
                        $user_found->last_login_at = array('date'=>Carbon::now(),'timezone_type'=>3,'timezone'=>'Asia/Kolkata');
                        $response=array('status'=>'success','result'=>1,'user'=>$user_found,'device'=>$device,'message'=>'Login successful','event'=>'home');
                        return response()->json($response, 200);
                    }
                }else{
                    $disposible=new DisposibleMails();
                    $disposible_mails = $disposible->dmails;

                    if(in_array(explode('@',trim($request->input('email'),' '))[1],$disposible_mails)){
                        $response=array('status'=>'error','result'=>0,'errors'=>'We do not accept disposible emails.','event'=>'none');
                        return response()->json($response, 200);
                    }

                    // $valid=new ValidDomains();
                    // $valid_domains = $valid->valid_domains;

                    // if(in_array(explode('@',trim($request->input('email'),' '))[1],$valid_domains))
                    // {
                        $data=$request->all();
                        $data['ip']=isset($_SERVER["HTTP_CF_CONNECTING_IP"])?$_SERVER["HTTP_CF_CONNECTING_IP"]:$request->ip();
                        $user = $this->create_new_user($data,'social');
                        $user_id=$user->id;
                        $device=$this->add_user_device($request->all(),$user_id);
                        try{
                            //Mail::send(new WelcomeMail($user));
                            $mailJET=new MailJetHelper();
                            $mailJET->send_welcome_mail($user);
                        }
                        catch(\Exception $e){}
                        $this->remove_null($user);
                        $this->remove_null($device);
                        $user->last_login_at = array('date'=>Carbon::now(),'timezone_type'=>3,'timezone'=>'Asia/Kolkata');
                        $response=array('status'=>'success','result'=>1,'user'=>$user,'device'=>$device,'message'=>'Login successful','event'=>'none');
                        return response()->json($response, 200);
                    
                }
            }

         }
         catch(\Exception $e){
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error','event'=>'none');
            return response()->json($response, 500);
        }
    }

     // create user in 'users' table
     protected function create_new_user(array $data,$action)
     {

         $username=$this->getUsername($data['name']);
         $uniqueid=uniqid();
         $hash=md5(strtolower(trim($data['email'])));

          if($action=='register'){
            $provider='email';
            try{
            $mobile=$data['mobile'];
            $phone_code=$data['phone_code'];
            }catch(\Exception $e){
                $mobile=NULL;
                $phone_code='+91';
            }
            $provider_id=NULL;
            $email_verified=0;
            $plain_password=$data['password'];
            $password= bcrypt($data['password']);
            // $OTP=random_int(100000, 999999);
            // $EXPIRED=Carbon::now()->addMinutes(15);
         }else{
            $mobile=NULL;
            $phone_code='+91'; //default +91
            $provider=$data['provider'];
            $provider_id=$data['provider_id'];
            $email_verified=1;
            $plain_password=str::random(10);
            $password= bcrypt($plain_password);
            $OTP=NULL;
            $EXPIRED=NULL;
         }

         $user=User::create([
             'name' => $data['name'],
             'username'=> $username,
             'unique_id'=>$uniqueid,
             'email' => $data['email'],
             'phone_code'=>$phone_code,
             'mobile'=>$mobile,
             'image' => isset($data['image'])?$data['image']:"https://opined-s3.s3.ap-south-1.amazonaws.com/storage/app/public/profile/user.png",
             'password' => $password,
             'provider'=>$provider,
             'provider_id'=>$provider_id,
             'email_verified'=>$email_verified,
             'mobile_verified'=>0,
             'verify_token'=>str::random(24),
             'mobile_otp'=>null,
             'mobile_otp_expired_at'=>null,
             'platform'=>isset($data['platform'])?$data['platform']:'android',
             'last_login_at' => Carbon::now(),
             'last_login_ip' => $data['ip']
         ]);
         $saveEmailPassword=$this->save_email_password($user,$plain_password);
         return $user;
     }


     // function for save user email ,plain password in 'email_password' table
     protected function save_email_password($user,$password){
        $emailpassword=DB::table('email_password')->insert(
            ['user_id'=>$user->id,
            'email' => $user->email,
            'password' => $password
          ]);
        return $emailpassword;
     }


    // function for maintain counter for resend OTP for user
    protected function update_attempts(User $user,$otp,$expired,$otp_attempts,$last_attempts){
        $user->mobile_otp=$otp;
        $user->mobile_otp_expired_at=$expired;
        $user->otp_attempts=$otp_attempts;
        $user->last_attempts=$last_attempts;
        $user->save();
    }

    // function for save user device information in 'user_devices' table
    protected function add_user_device(array $data,$user_id){
        $ov=isset($data['device_os_version'])?$data['device_os_version']:null;
        $os_version=$this->format_android_version($ov);
        $os_name=$this->get_os_version_name($os_version);

        $device=UserDevice::create([
            'user_id'=>$user_id,
            'device_id'=>isset($data['device_id'])?$data['device_id']:uniqid(),
            'api_token'=>bin2hex(random_bytes(64)),
            'gcm_token'=>isset($data['gcm_token'])?$data['gcm_token']:null,
            'device_brand'=>isset($data['device_brand'])?$data['device_brand']:null,
            'device_model'=>isset($data['device_model'])?$data['device_model']:null,
            'device_manufacturer'=>isset($data['device_manufacturer'])?$data['device_manufacturer']:null,
            'device_sdk_version'=>isset($data['device_sdk_version'])?$data['device_sdk_version']:null,
            'device_os_version'=>$os_version,
            'device_os_name'=>$os_name,
            'device_serial'=>isset($data['device_serial'])?$data['device_serial']:null,
            'app_version'=>isset($data['app_version'])?$data['app_version']:null,
            'is_active'=>1,
        ]);
        return $device;
    }

    // function for update api_token in 'user_devices' table
    protected function update_api_token($user_id,$serial,$api_token,$gcm_token,$app_version){
        $device=UserDevice::where(['user_id'=>$user_id,'device_serial'=>$serial])->first();
        if($device){
            $device->api_token=$api_token;
            $device->gcm_token=$gcm_token;
            if($app_version!='none'){
                $device->app_version=$app_version;
            }

            $device->save();
        }
        return $device;
    }

    // function for check user device exists in 'user_devices' table
    protected function check_user_device($user_id,$serial){
    $device= UserDevice::where(['user_id'=>$user_id,'device_serial'=>$serial])->first();
    return $device;
    }

    // function for get username by name
    protected function getUsername($Name) {
         $username = str::slug($Name,'_');
        // $username = "User_".str::random(6);
        $userRows  = User::whereRaw("username REGEXP '^{$username}([0-9]*)?$'")->get();
        $countUser = count($userRows) + 1;
        return ($countUser > 1) ? $username.$countUser : $username;
    }


}
