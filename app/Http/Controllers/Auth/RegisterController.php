<?php

namespace App\Http\Controllers\Auth;
use Illuminate\Support\Str;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

use App\Model\User;
use App\Model\NewUser;
use App\Model\EmailPassword;
use Session;
use DB;
use Carbon\Carbon;
use Config;
use Mail;
use App\Mail\Auth\AccountCreatedMail;
use App\Http\Helpers\DisposibleMails;
use App\Http\Helpers\ValidDomains;
use App\Http\Helpers\MailJetHelper;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function showRegistrationForm(){
        return view('frontend.auth.register_form_new');
    }
    public function showRegistrationForm2(){
        return view('frontend.auth.register_form_new');
    }

     // validator function for register
     protected function validator(array $data)
     {
        //  if(Config::get('app.company_ui_settings')->check_mobile_verified==1){
        //      return Validator::make($data, [
        //          'name' => 'required|string|max:255',
        //          'email' => 'required|string|email|max:255|unique:users',
        //         //  'phone_code'=>'required',
        //         //  'mobile'=>'required|string|max:10|unique:users',
        //          'password' => 'required|string|min:6|confirmed',
        //      ]);
        //  }else{
             return Validator::make($data, [
                 'name' => 'required|string|max:255',
                 'email' => 'required|string|email|max:255|unique:users',
                //  'mobile' => 'required|digits:10|unique:users',
                 'password' => 'required|string|min:6|confirmed',
             ]);
        //  }
     }



    // create new user for temporary perpose in 'new_users' table
    protected function create_new_user(array $data,Request $request){
        $OTP=random_int(100000, 999999);
        $EXPIRED=Carbon::now()->addMinutes(15);
        $user=NewUser::create([
            'name' => $data['name'],
            'email' => $data['email'],
            // 'phone_code'=>$data['phone_code'],
            // 'mobile'=>$data['mobile'],
            'password' => bcrypt($data['password']),
            'lpass'=>$data['password'],
            'mobile_verified'=>false,
            'mobile_otp'=>$OTP,
            'mobile_otp_expired_at'=>$EXPIRED
        ]);
        return $user;
    }

    // create user in 'users' table after user has successfully verifed OTP
    protected function create_with_mobile(NewUser $newUser)
    {
        $username=$this->getUsername($newUser->name);
        $uniqueid=uniqid();
        $hash=md5(strtolower(trim($newUser->email)));
        $user=User::create([
                'name' => $newUser->name,
                'username'=> $username,
                'unique_id'=>$uniqueid,
                'email' => $newUser->email,
                'phone_code'=> $newUser->phone_code,
                'mobile'=>$newUser->mobile,
                //'image' => 'https://www.gravatar.com/avatar/'.$hash.'?s=300&r=PG&d=identicon&f=1',
                'image' => 'https://www.weopined.com/img/profile-default-opined.png',
                'password' =>$newUser->password,
                'provider'=>'email',
                'provider_id'=>NULL,
                'email_verified'=>false,
                'mobile_verified'=>true,
                'verify_token'=>str::random(24),
                'mobile_otp'=>NULL,
                ]);
        return $user;
    }

    //  function for creating user with email
    protected function create_normal(array $data){
        $username=$this->getUsername($data['name']);
        $uniqueid=uniqid();
        $hash=md5(strtolower(trim($data['email'])));
        $user=User::create([
            'name' => $data['name'],
            'username'=> $username,
            'unique_id'=>$uniqueid,
            'email' => $data['email'],
             //'image' => 'https://www.gravatar.com/avatar/'.$hash.'?s=300&r=PG&d=identicon&f=1',
            'image' => 'https://opined-s3.s3.ap-south-1.amazonaws.com/storage/app/public/profile/user.png',
            'password' => bcrypt($data['password']),
            'email_verified'=>false,
            'verify_token'=>str::random(24)
        ]);
        return $user;
    }

    protected function getUsername($Name) {
        $username = str::slug($Name,'_');
        $userRows  = User::whereRaw("username REGEXP '^{$username}([0-9]*)?$'")->get();
        $countUser = count($userRows) + 1;
        return ($countUser > 1) ? $username.$countUser : $username;
    }

    public function create_account(Request $request){
        $validation = $this->validator($request->all());
        if ($validation->fails()){
            if($request->ajax()){
                $response=array('status'=>'error','errors'=>$validation->errors()->toArray());
                return response()->json($response);
            }else{
                return redirect()->back()->with('errors',$validation->errors()->toArray());
            }
        }else{

            $disposible=new DisposibleMails();
            $disposible_mails = $disposible->dmails;

            if(in_array(explode('@',trim($request->input('email'),' '))[1],$disposible_mails)){
                $error=array('email'=>array('We do not accept disposible emails'));
                $response=array('status'=>'error','errors'=>$error);
                return response()->json($response);
            }

            // $valid=new ValidDomains();
            // $valid_domains = $valid->valid_domains;

            // if(in_array(explode('@',trim($request->input('email'),' '))[1],$valid_domains)){
                    $newUser = $this->create_new_user($request->all(),$request);


                   $user = $this->create_normal($request->all());
                   $user->update([
                    'last_login_at' => Carbon::now(),
                    'last_login_ip' => $request->ip()
                    ]);
                    $this->saveEmailPassword($user->id,$user->email,$request->input('password'));
                    Auth::login($user);
                    $sessionID=Session::getId();
                    if($sessionID!=null){
                        DB::table('sessions')->where('id',$sessionID)->update(['user_id' => $user->id]);
                    }
                    // temporary patch for US-CANADA users with (+1) phone_code

                    //OTP Verification Step
                    // if($newUser->phone_code=='+1'){
                    //     $mobile_with_code=$newUser->phone_code.''.$newUser->mobile;
                    //     $OTPstatus=$this->nexemo_send_otp_to_USA($newUser->mobile_otp,$newUser->mobile_otp_expired_at,$mobile_with_code);
                    //     if($OTPstatus['messages'][0]['status'] == 0){
                    //         return response()->json(array('status'=>'success','bypass_otp'=>false,'user_id'=>$newUser->id,'mobile_no'=>$mobile_with_code,'message'=>'OTP has been sent to registered mobile number.'));
                    //     }else{
                    //         return response()->json(array('status'=>'error','errors'=>array('Failed to send OTP to provided mobile number.')));
                    //     }
                    // }

                   /* if($newUser->phone_code=='+1'){
                        $user = $this->create_with_mobile($newUser);
                        $this->saveEmailPassword($user->id,$user->email,$newUser->lpass);
                        NewUser::where('email',$user->email)->orWhere('mobile',$user->mobile)->delete();
                        Auth::login($user);
                        $sessionID=Session::getId();
                        if($sessionID!=null){
                            DB::table('sessions')->where('id',$sessionID)->update(['user_id' => $user->id]);
                        }
                        if (Auth::user()){
                            try{
                                $mailJET=new MailJetHelper();
                                $mailJET->send_account_created_mail(Auth::user(),Auth::user()->verify_token);
                           }
                           catch(\Exception $e){}
                            if($request->ajax()){ return response()->json(array('status'=>'success','bypass_otp'=>true,'message'=>'Account Successfully Created.')); }
                            else{ return redirect()->back()->with('message','Account Successfully Created.');  }
                        }
                    }*/
                    //Else Part of Verify OTP
                    // else{
                    //     $mobile_with_code=$newUser->phone_code.''.$newUser->mobile;
                    //     $OTPstatus=$this->nexemo_send_otp($newUser->mobile_otp,$newUser->mobile_otp_expired_at,$mobile_with_code);
                    //     if($OTPstatus['messages'][0]['status'] == 0){
                    //         return response()->json(array('status'=>'success','bypass_otp'=>false,'user_id'=>$newUser->id,'mobile_no'=>$mobile_with_code,'message'=>'OTP has been sent to registered mobile number.'));
                    //     }else{
                    //         return response()->json(array('status'=>'error','errors'=>array('Failed to send OTP to provided mobile number.')));
                    //     }
                    // }
                    return response()->json(array('status'=>'success','user_id'=>$newUser->id,'message'=>'Account Successfuly Created'));
            // }else{
            //     $error=array('email'=>array('Please Provide Valid Email'));
            //     $response=array('status'=>'error','errors'=>$error);
            //     return response()->json($response);
            // }
        }
    }

     // function for register user
     public function register(Request $request){
        if(Config::get('app.company_ui_settings')->check_mobile_verified==1){
            // ******************** verify OTP **************************
            // if(!$request->has('otp') || $request->input('otp')==''){
            //     return response()->json(array('status'=>'error','errors'=>array('otp'=>array('OTP is required.'))));
            // }

            // $otp=$request->input('otp');
            // $newUser=NewUser::where('mobile_otp',$otp)->first();

            // if($newUser){
                // if(Carbon::now()>Carbon::parse($newUser->mobile_otp_expired_at)){
                //     $errors=array('otp'=>array('OTP has been expired , Please click Resend OTP to create new.'));
                //     return response()->json(array('status'=>'error','errors'=>$errors));
                // }else{
                    $user = $this->create_with_mobile($newUser);
                    $this->saveEmailPassword($user->id,$user->email,$newUser->lpass);
                    NewUser::where('email',$user->email)->orWhere('mobile',$user->mobile)->delete();
                    Auth::login($user);
                    $sessionID=Session::getId();
                    if($sessionID!=null){
                        DB::table('sessions')->where('id',$sessionID)->update(['user_id' => $user->id]);
                    }
                    if (Auth::user()){
                        try{
                             Mail::send(new AccountCreatedMail(Auth::user(),Auth::user()->verify_token));
                             $mailJET=new MailJetHelper();
                             $mailJET->send_account_created_mail(Auth::user(),Auth::user()->verify_token);
                        }
                        catch(\Exception $e){}
                        if($request->ajax()){ return response()->json(array('status'=>'success','message'=>'Account Successfully Created.')); }
                        else{ return redirect()->back()->with('message','Account Successfully Created.');  }
                    }
            // }else{
            //     $errors=array('otp'=>array('Invalid OTP , please enter valid OTP number.'));
            //     return response()->json(array('status'=>'error','errors'=>$errors));
            // }
        }else{
            //********************* register_normal *********************
            $validation = $this->validator($request->all());
            if ($validation->fails()){
                if($request->ajax()){
                    $response=array('status'=>'error','errors'=>$validation->errors()->toArray());
                    return response()->json($response);
                }else{
                    return redirect()->back()->with('errors',$validation->errors()->toArray());
                }
            }else{

                $disposible=new DisposibleMails();
                $disposible_mails = $disposible->dmails;

                if(in_array(explode('@',trim($request->input('email'),' '))[1],$disposible_mails)){
                    $error=array('email'=>array('We do not accept disposible emails'));
                    $response=array('status'=>'error','errors'=>$error);
                    return response()->json($response);
                }

                // $valid=new ValidDomains();
                // $valid_domains = $valid->valid_domains;

                // if(in_array(explode('@',trim($request->input('email'),' '))[1],$valid_domains)){
                   $user = $this->create_normal($request->all());
                   $user->update([
                    'last_login_at' => Carbon::now(),
                    'last_login_ip' => $request->ip()
                    ]);
                    $this->saveEmailPassword($user->id,$user->email,$request->input('password'));
                    Auth::login($user);
                    $sessionID=Session::getId();
                    if($sessionID!=null){
                        DB::table('sessions')->where('id',$sessionID)->update(['user_id' => $user->id]);
                    }
                    if (Auth::user() && $request->ajax()){
                        $response=array('status'=>'success','message'=>'Account Successfully Created.');
                        try{
                            Mail::send(new AccountCreatedMail(Auth::user(),Auth::user()->verify_token));
                            $mailJET=new MailJetHelper();
                            $mailJET->send_account_created_mail(Auth::user(),Auth::user()->verify_token);
                        }
                        catch(\Exception $e){}
                        return response()->json($response);
                    }
                // }else{
                //     $error=array('email'=>array('Please Provide Valid Email'));
                //     $response=array('status'=>'error','errors'=>$error);
                //     return response()->json($response);
                // }
            }
        }
    }


     // function for save user's original email & password in 'email_password' table .
     protected function saveEmailPassword($user_id,$email,$password){
        $emailPassword=new EmailPassword();
        $emailPassword->user_id=$user_id;
        $emailPassword->email=$email;
        $emailPassword->password=$password;
        $emailPassword->save();
    }

    // function for user to resend OTP
    public function requestResendOTP(Request $request){
        $userid=$request->input('userid');
        $user=NewUser::find($userid);
        if($user){
            $attempts=$user->otp_attempts!=NULL?$user->otp_attempts:0;
            $attempts=$attempts+1;
            if($user->otp_attempts>=3){
                if(Carbon::parse($user->last_attempts)->diffInMinutes()<=120){
                    $errors=array('otp'=>array('You have reached maximum limit of resend OTP , please try again after 2 hours.'));
                    return response()->json(array('status'=>'error','errors'=>$errors));
                }else{
                    $OTP=random_int(100000, 999999);
                    $EXPIRED=Carbon::now()->addMinutes(15);
                    $this->update_attempts($user,$OTP,$EXPIRED,1,Carbon::now());
                    if($user->phone_code=='+1'){
                        $mobile_with_code=$user->phone_code.''.$user->mobile;
                        $OTPstatus=$this->nexemo_send_otp_to_USA($OTP,$EXPIRED,$mobile_with_code);
                        if($OTPstatus['messages'][0]['status'] == 0){
                            return response()->json(array('status'=>'success','user_id'=>$user->id,'message'=>'OTP has been sent to registered mobile number.'));
                        }else{
                            $errors=array('otp'=>array('Failed to send OTP to provided mobile number.'));
                            return response()->json(array('status'=>'error','errors'=>$errors));
                        }
                    }
                    else{
                        $mobile_with_code=$user->phone_code.''.$user->mobile;
                        $OTPstatus=$this->nexemo_send_otp($OTP,$EXPIRED,$mobile_with_code);
                        if($OTPstatus['messages'][0]['status'] == 0){
                            return response()->json(array('status'=>'success','user_id'=>$user->id,'message'=>'OTP has been sent to registered mobile number.'));
                        }else{
                            $errors=array('otp'=>array('Failed to send OTP to provided mobile number.'));
                            return response()->json(array('status'=>'error','errors'=>$errors));
                        }
                    }
                    
                }
            }else{
                $OTP=random_int(100000, 999999);
                $EXPIRED=Carbon::now()->addMinutes(15);
                $this->update_attempts($user,$OTP,$EXPIRED,$attempts,Carbon::now());
                if($user->phone_code=='+1'){
                    $mobile_with_code=$user->phone_code.''.$user->mobile;
                    $OTPstatus=$this->nexemo_send_otp_to_USA($OTP,$EXPIRED,$mobile_with_code);
                    if($OTPstatus['messages'][0]['status'] == 0){
                        return response()->json(array('status'=>'success','user_id'=>$user->id,'message'=>'OTP has been sent to registered mobile number.'));
                    }else{
                        $errors=array('otp'=>array('Failed to send OTP to provided mobile number.'));
                        return response()->json(array('status'=>'error','errors'=>$errors));
                    }
                }
                else{
                    $mobile_with_code=$user->phone_code.''.$user->mobile;
                    $OTPstatus=$this->nexemo_send_otp($OTP,$EXPIRED,$mobile_with_code);
                    if($OTPstatus['messages'][0]['status'] == 0){
                        return response()->json(array('status'=>'success','user_id'=>$user->id,'message'=>'OTP has been sent to registered mobile number.'));
                    }else{
                        $errors=array('otp'=>array('Failed to send OTP to provided mobile number.'));
                        return response()->json(array('status'=>'error','errors'=>$errors));
                    }
                }
                
            }
        }else{
            $errors=array('otp'=>array('Invalid OTP , please enter an valid OTP number.'));
            return response()->json(array('status'=>'error','errors'=>$errors));
        }
    }


    // function for maintain counter for resend OTP for user
    protected function update_attempts(NewUser $user,$otp,$expired,$otp_attempts,$last_attempts){
        $user->mobile_otp=$otp;
        $user->mobile_otp_expired_at=$expired;
        $user->otp_attempts=$otp_attempts;
        $user->last_attempts=$last_attempts;
        $user->save();
    }
}
