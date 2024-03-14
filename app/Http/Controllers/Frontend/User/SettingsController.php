<?php

namespace App\Http\Controllers\Frontend\User;
use Illuminate\Support\Str;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Validator;

use App\Model\User;
use App\Model\Post;
use App\Model\UserAccount;
use App\Model\ShortOpinion;
use App\Model\EmailPassword;

use DB;
use Session;
use Hash;

use Mail;
use App\Mail\Auth\AccountVerifiedMail;
use App\Mail\Auth\VerifyAccountMail;
use App\Mail\Auth\VerifyEmailAccountMail;
use Carbon\Carbon;

use Config;

use App\Http\Helpers\DisposibleMails;
use App\Http\Helpers\ValidDomains;
use App\Http\Helpers\MailJetHelper;

class SettingsController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth',['except'=>['activate_account','verify_email','sendOtpActivate','activateAccountCheck']]);
    }

      // function for display settings page
      public function settings(){
        $google_ad=DB::table('google_ads')->where(['id'=>1,'is_active'=>1])->first();
        $user_account=UserAccount::where('user_id',Auth::user()->id)->exists();
        if($user_account){
            $user_account=UserAccount::where('user_id',Auth::user()->id)->first();
        }else{
            $user_account=null;
        }
        return view('frontend.profile.settings',compact('google_ad','user_account'));
    }




    // public function to search username
    public function search_username(Request $request){
        $username=$request->input('username');
        $users=User::where('username',$username)->exists();
        if($users){
            return response()->json(array('status'=>'error','message'=>'Username has been Already Taken.'));
        }else{
            return response()->json(array('status'=>'success','message'=>'Username is Available.'));
        }
    }



    // function for send verification link via email to user
    public function send_me_verification_email(Request $request){
        if(Auth::user()->email_verified==0){
            if(Auth::user()->verify_token!==NULL){
                $token=Auth::user()->verify_token;
            }else{
                $user = Auth::user();
                $token=str::random(24);
                $user->verify_token = $token;
                $user->save();
            }

            try{
                  //Mail::send(new VerifyAccountMail(Auth::user(),$token));
                  $mailJET=new MailJetHelper();
                  $mailJET->send_verify_email_mail(Auth::user(),$token);
                }
            catch(\Exception $e){}

            return redirect()->back()->with(['account'=>'Verification link has been sent to your registered email address '.Auth::user()->email.' Please click the link in email and verify your email address.','alert-class'=>'alert-info']);
        }else{
            return redirect()->back();
        }
    }


    // function for verify user email address
    public function verify_email($token){
         $user=User::where('verify_token',$token)->first();
         if($user){
            $user->email_verified=true;
            $user->verify_token = NULL;
            $user->save();
            try{
                //Mail::send(new AccountVerifiedMail($user));
                $mailJET=new MailJetHelper();
                $mailJET->send_account_verified_mail($user);
            }
            catch(\Exception $e){}
             if(Auth::guest()){
                Auth::login($user);
             }else{
                Auth::logout();
                Auth::login($user);
             }
            return redirect('/')->with(['account'=>'Your Account has been successfully verified with registered email :)','alert-class'=>'alert-success']);
         }else{
             return redirect('/');
         }
    }



    // function for resend otp to user
    public function resendOTP(Request $request){
        $attempts=Auth::user()->otp_attempts!=NULL?(Auth::user()->otp_attempts):0;
        $attempts=$attempts+1;
        if(Auth::user()->otp_attempts>=3){
            if(Carbon::parse(Auth::user()->last_attempts)->diffInMinutes()<=120){
                $errors=array('otp'=>array('You have reached maximum limit of resend OTP , please try again after 2 hours.'));
                return response()->json(array('status'=>'error','errors'=>$errors));
            }else{
                $OTP=random_int(100000, 999999);
                $EXPIRED=Carbon::now()->addMinutes(15);
                $this->update_attempts(Auth::user(),$OTP,$EXPIRED,1,Carbon::now());
                if(Auth::user()->phone_code=='+1'){
                    $mobile_with_code=Auth::user()->phone_code.''.Auth::user()->mobile;
                    $OTPstatus=$this->nexemo_send_otp_to_USA($OTP,$EXPIRED,$mobile_with_code);
                    if($OTPstatus['messages'][0]['status'] == 0){
                        return response()->json(array('status'=>'success','user_id'=>Auth::user()->id,'message'=>'OTP has been sent to registered mobile number.'));
                    }else{
                        $errors=array('otp'=>array('Failed to send OTP to provided mobile number.'));
                        return response()->json(array('status'=>'error','errors'=>$errors));
                    }
                }
                else{
                    $mobile_with_code=Auth::user()->phone_code.''.Auth::user()->mobile;
                    $OTPstatus=$this->nexemo_send_otp($OTP,$EXPIRED,$mobile_with_code);
                    if($OTPstatus['messages'][0]['status'] == 0){
                        return response()->json(array('status'=>'success','user_id'=>Auth::user()->id,'message'=>'OTP has been sent to registered mobile number.'));
                    }else{
                        $errors=array('otp'=>array('Failed to send OTP to provided mobile number.'));
                        return response()->json(array('status'=>'error','errors'=>$errors));
                    }
                }
                
            }
        }else{
            $OTP=random_int(100000, 999999);
            $EXPIRED=Carbon::now()->addMinutes(15);
            $this->update_attempts(Auth::user(),$OTP,$EXPIRED,$attempts,Carbon::now());
            $mobile_with_code=Auth::user()->phone_code.''.Auth::user()->mobile;
            if(Auth::user()->phone_code=='+1'){
                $OTPstatus=$this->nexemo_send_otp_to_USA($OTP,$EXPIRED,$mobile_with_code);
                if($OTPstatus['messages'][0]['status'] == 0){
                    return response()->json(array('status'=>'success','user_id'=>Auth::user()->id,'message'=>'OTP has been sent to registered mobile number.'));
                }else{
                    $errors=array('otp'=>array('Failed to send OTP to provided mobile number.'));
                    return response()->json(array('status'=>'error','errors'=>$errors));
                }
            }
            else{
                $OTPstatus=$this->nexemo_send_otp($OTP,$EXPIRED,$mobile_with_code);
                if($OTPstatus['messages'][0]['status'] == 0){
                    return response()->json(array('status'=>'success','user_id'=>Auth::user()->id,'message'=>'OTP has been sent to registered mobile number.'));
                }else{
                    $errors=array('otp'=>array('Failed to send OTP to provided mobile number.'));
                    return response()->json(array('status'=>'error','errors'=>$errors));
                }
            }
            
        }

    }

    protected function update_attempts($user,$otp,$expired,$otp_attempts,$last_attempts){
        $user=Auth::user();
        $user->mobile_otp=$otp;
        $user->mobile_otp_expired_at=$expired;
        $user->otp_attempts=$otp_attempts;
        $user->last_attempts=$last_attempts;
        $user->save();
    }

    // function for add user mobile and send otp for verification
    public function add_mobile(Request $request){
        if($request->input('mobile')=='' || strlen($request->input('mobile'))==0){
            return response()->json(array('status'=>'error','errors'=>array('mobile'=>array('Mobile number is required.'))));
        }

        if($request->has('mobile') && (strlen($request->input('mobile'))<6 || strlen($request->input('mobile'))>15)){
            return response()->json(array('status'=>'error','errors'=>array('mobile'=>array('Invalid mobile number.'))));
        }

        $mobile=$request->input('mobile');
        $phone_code=$request->input('phone_code');

        if(User::where('mobile',$mobile)->where('id',Auth::user()->id)->where('mobile_verified',1)->exists()){
            return response()->json(array('status'=>'error','errors'=>array('mobile'=>array('Mobile number is already verified , No need to verify again.'))));
        }

        if(User::where('mobile',$mobile)->where('id','!=',Auth::user()->id)->exists()){
            return response()->json(array('status'=>'error','errors'=>array('mobile'=>array('Mobile number is already taken.'))));
        }

        $OTP=random_int(100000, 999999);
        $EXPIRED=Carbon::now()->addMinutes(15);

        User::where('id',Auth::user()->id)->update([
            'phone_code'=>$phone_code,
            'mobile'=>$mobile,
            'mobile_otp'=>$OTP,
            'mobile_otp_expired_at'=>$EXPIRED
        ]);
        if($phone_code=='+1'){

            $mobile_with_code=$phone_code.''.$mobile;
            $OTPstatus=$this->nexemo_send_otp_to_USA($OTP,$EXPIRED,$mobile_with_code);
            if($OTPstatus['messages'][0]['status'] == 0){
                return response()->json(array('status'=>'success','user_id'=>Auth::user()->id,'mobileno'=>$mobile_with_code,'message'=>'OTP has been sent to registered mobile number.'));
            }else{
                return response()->json(array('status'=>'error','errors'=>array('otp'=>array('Failed to send OTP to provided mobile number.'))));
            }
        }

        else{
            $mobile_with_code=$phone_code.''.$mobile;
            $OTPstatus=$this->nexemo_send_otp($OTP,$EXPIRED,$mobile_with_code);
            if($OTPstatus['messages'][0]['status'] == 0){
                return response()->json(array('status'=>'success','user_id'=>Auth::user()->id,'mobileno'=>$mobile_with_code,'message'=>'OTP has been sent to registered mobile number.'));
            }else{
                return response()->json(array('status'=>'error','errors'=>array('otp'=>array('Failed to send OTP to provided mobile number.'))));
            }
        }
        
    }

    // function for verify user mobile by otp
    public function verify_mobile(Request $request){

        if(Auth::user()->mobile==NULL || Auth::user()->mobile==''){
            return response()->json(array('status'=>'error','errors'=>array('mobile'=>array('Mobile number is required.'))));
        }

        if(!$request->has('otp') || $request->input('otp')==''){
            return response()->json(array('status'=>'error','errors'=>array('otp'=>array('OTP is required.'))));
        }


        if(Carbon::now()>Carbon::parse(Auth::user()->mobile_otp_expired_at)){
            $errors=array('otp'=>array('OTP has been expired , Please click Resend OTP to create new.'));
            return response()->json(array('status'=>'error','errors'=>$errors));
        }

        $user=User::where('id',Auth::user()->id)->first();

        if($user->mobile_otp==$request->input('otp')){
            $user=Auth::user();
            $user->mobile_verified=true;
            $user->mobile_otp=NULL;
            $user->mobile_otp_expired_at=NULL;
            $user->otp_attempts=0;
            $user->last_attempts=NULL;
            $user->save();
            return response()->json(array('status'=>'success','message'=>'Mobile number successfully verified.'));
        }else{
            $errors=array('otp'=>array('Invalid OTP , please enter valid OTP number.'));
            return response()->json(array('status'=>'error','errors'=>$errors));
        }

    }


     // function for update username
     public function update_username(Request $request){
        if(!$request->has('username')){
            return response()->json(array('status'=>'error','errors'=>array('username'=>array('Username is required'))));
        }

        if(trim($request->input('username'))==''){
            return response()->json(array('status'=>'error','errors'=>array('username'=>array('Username is required'))));
        }

        $username=$request->input('username');
        $users=User::where('username',$username)->exists();
        if($users){
           return response()->json(array('status'=>'error','errors'=>array('username'=>array('Username is Already Taken'))));
        }else{
            $user=Auth::user();
            $user->username=$username;
            $user->save();
            return response()->json(array('status'=>'success','message'=>'Username Successfully Updated.'));
        }
    }

    //function to save the User Keywords desciption

    public function update_keywords(Request $request){
        if(!$request->has('keywords')){
            return response()->json(array('status'=>'error','errors'=>array('keywords'=>array('Keywords are required'))));
        }

        if(trim($request->input('keywords'))==''){
            return response()->json(array('status'=>'error','errors'=>array('keywords'=>array('Keywords are required'))));
        }

        $keywords=$request->input('keywords');


            $user=Auth::user();
            $user->keywords=$keywords;
            $user->save();
            return response()->json(array('status'=>'success','message'=>'Keywords Successfully Updated.'));

    }

    // Function to update Name of User
    public function update_name(Request $request){
        if(!$request->has('name')){
            return response()->json(array('status'=>'error','errors'=>array('name'=>array('Name is required'))));
        }

        if(trim($request->input('name'))==''){
            return response()->json(array('status'=>'error','errors'=>array('name'=>array('name are required'))));
        }

        $name=$request->input('name');


            $user=Auth::user();
            $user->name=$name;
            $user->save();
            return response()->json(array('status'=>'success','message'=>'Name Has Been Successfully Updated.'));

    }


    // function for update user email
    public function update_email(Request $request){
        if(!$request->has('email')){
            return response()->json(array('status'=>'error','errors'=>array('email'=>array('email is required'))));
        }

        if(trim($request->input('email'))==''){
            return response()->json(array('status'=>'error','errors'=>array('email'=>array('email is required'))));
        }

        $disposible=new DisposibleMails();
        $disposible_mails = $disposible->dmails;

        if(in_array(explode('@',trim($request->input('email'),' '))[1],$disposible_mails)){
            return response()->json(array('status'=>'error','errors'=>array('email'=>array('we do not accept disposible emails'))));
        }

        $email=$request->input('email');
        $exists=User::where('email',$email)->exists();
        if($exists){
            return response()->json(array('status'=>'error','errors'=>array('email'=>array('email is already taken'))));
        }else{
            $valid=new ValidDomains();
            $valid_domains = $valid->valid_domains;
            if(in_array(explode('@',trim($request->input('email'),' '))[1],$valid_domains)){
                $token=str::random(24);
                $updated=User::where('id',Auth::user()->id)->update(['email'=>$email,'email_verified'=>0,'verify_token'=>$token]);
                if($updated){
                    try{
                        //Mail::send(new VerifyAccountMail(Auth::user(),$token));
                        $mailJET=new MailJetHelper();
                        $mailJET->send_verify_email_mail(Auth::user(),$token);
                    }
                    catch(\Exception $e){}
                    return response()->json(array('status'=>'success','message'=>'email verification link has been sent your email address , please verifiy your email address.'));
                }else{
                    return response()->json(array('status'=>'error','errors'=>array('email'=>array('failed to update email'))));
                }
            }else{
                return response()->json(array('status'=>'error','errors'=>array('email'=>array('please enter valid email address'))));
            }
        }
    }

    // function for update user password
    public function update_password(Request $request){
        $validator = Validator::make($request->all(), [
            'current-password'=>'required',
            'new-password'=>'required|string|min:6',
        ]);

        if ($validator->fails()) {
            $response=array('status'=>'error','result'=>0,'errors'=>implode(',',$validator->errors()->all()));
            return response()->json($response, 200);
        }else{

                if (!(Hash::check($request->get('current-password'), Auth::user()->password))) {
                    return response()->json(array('status'=>'error','errors'=>array('current-password'=>array('Current Password you entered is not correct'))));
                }

                if(strcmp($request->get('current-password'), $request->get('new-password')) == 0){
                    return response()->json(array('status'=>'error','errors'=>array('current-password'=>array('New Password cannot be same as your current password'))));
                }
                if(strcmp($request->get('confirm-password'), $request->get('new-password')) != 0){
                    return response()->json(array('status'=>'error','errors'=>array('current-password'=>array('Verify Password did not match with New Password'))));
                }

                $user = Auth::user();
                $user->password = bcrypt($request->input('new-password'));
                $user->save();
                $this->save_plain_password(Auth::user()->id,Auth::user()->email,$request->input('new-password'));

                //Auth::logoutOtherDevices($request->input('new-password'));
                try{
                    $mailJET=new MailJetHelper();
                    $mailJET->send_account_password_changed($user);
                }catch(\Exception $e){}
                $this->clear_other_sessions();
               return response()->json(array('status'=>'success','message'=>'Password changed successfully !'));
                //return redirect(route('logout'))->with(['message'=>'Successfully Changed password, Login Again']);
                
        }
       // return redirect(Auth::logout());
    }

    // Function to check auth to show payment details

    public function checkPass(Request $request){
        
         if ((Hash::check($request->get('password'), Auth::user()->password))) {
                   return redirect('/me/payment_show');
                }
        else{
            return redirect()->back()->with('message','You have entered wrong password'); 
        }

                
    }

    // function for save plain password
    protected function save_plain_password($user_id,$email,$password){
        $found=EmailPassword::where(['user_id'=>$user_id,'email'=>$email])->first();
        if(empty($found)){
            EmailPassword::create(['user_id'=>$user_id,'email'=>$email,'password'=>$password]);
        }else{
            EmailPassword::where(['user_id'=>$user_id,'email'=>$email])->update(['password'=>$password]);
        }
    }


    // function to delete logged in user's all other login sessions except current sessions
    public function clear_other_sessions(){
        $sessionID=Session::getId();
        if($sessionID!=null){
            DB::table('sessions')->where([['user_id', '=', Auth::user()->id],['id','!=',$sessionID]])->delete();
            DB::table('user_devices')->where('user_id', '=',Auth::user()->id)->update(['api_token'=>null,'gcm_token'=>null]);
            DB::table('users')->where('id',Auth::user()->id)->update(['remember_token' => null]);
            return redirect('/me/settings')->with('message','You have successfully signed out from all other sessions');
        }else{
            return redirect()->back();
        }
    }

    // function to Subscribe Unsubscribe to Digest Email
    public function subscribe(Request $request){
        
        $status=$request->input('status');
        $user=Auth::user();
        $user->is_subscribed=$status;
        $user->save();
        return redirect()->back();
    }

     // function for setting deactivate logged in user all contents and account
     public function delete_account(Request $request){
        DB::table('users')->where('id', '=',Auth::user()->id)->update(['delete_reason' => $request->input('flag')]);
        DB::transaction(function () {


            DB::table('views')->where('user_id', '=',Auth::user()->id)->update(['is_active' => 0]);
            DB::table('likes')->where('user_id', '=',Auth::user()->id)->update(['is_active' => 0]);
            DB::table('disagree')->where('user_id', '=',Auth::user()->id)->update(['is_active' => 0]);
            $liked_posts_ids=DB::table('likes')->select('post_id')->where('user_id', '=',Auth::user()->id)->get()->pluck('post_id')->toArray();
            foreach($liked_posts_ids as $id){
                $post=Post::find($id);
                if($post){
                $post->likes=$post->likes-1;
                $post->save();
                }
            }
            $disagreed_posts_ids=DB::table('disagree')->select('post_id')->where('user_id', '=',Auth::user()->id)->get()->pluck('post_id')->toArray();
            foreach($disagreed_posts_ids as $id){
                $post=Post::find($id);
                if($post){
                $post->disagree=$post->disagree-1;
                $post->save();
                }
            }
            $viewed_posts_ids=DB::table('views')->select('post_id')->where('user_id', '=',Auth::user()->id)->get()->pluck('post_id')->toArray();
            foreach($viewed_posts_ids as $id){
                $post=Post::find($id);
                if($post){
                $post->views=$post->views-1;
                $post->save();
                }
            }
            DB::table('user_devices')->where('user_id', '=',Auth::user()->id)->update(['is_active' => 0,'api_token'=>null]);
            DB::table('push_subscriptions')->where('user_id', '=',Auth::user()->id)->delete();
            DB::table('user_contacts')->where('user_id', '=',Auth::user()->id)->update(['is_active' => 0]);
            DB::table('bookmarks')->where('user_id', '=',Auth::user()->id)->update(['is_active' => 0]);
            DB::table('comments')->where('user_id', '=',Auth::user()->id)->update(['is_active' => 0]);
            DB::table('comments_likes')->where('user_id', '=',Auth::user()->id)->update(['is_active' => 0]);
            DB::table('comments_disagree')->where('user_id', '=',Auth::user()->id)->update(['is_active' => 0]);
            DB::table('short_opinions')->where('user_id', '=',Auth::user()->id)->update(['is_active' => 0]);
            DB::table('short_opinion_comments')->where('user_id', '=',Auth::user()->id)->update(['is_active' => 0]);
            DB::table('short_opinion_likes')->where('user_id', '=',Auth::user()->id)->update(['is_active' => 0]);
            DB::table('short_opinion_disagree')->where('user_id', '=',Auth::user()->id)->update(['is_active' => 0]);
            DB::table('short_opinion_comments_likes')->where('user_id',Auth::user()->id)->update(['is_active'=>0]);
            DB::table('short_opinion_comments_disagree')->where('user_id',Auth::user()->id)->update(['is_active'=>0]);
            DB::table('poll_results')->where('user_id','=',Auth::user()->id)->update(['is_active' => 0]);
            DB::table('category_followers')->where('user_id', '=',Auth::user()->id)->update(['is_active' => 0]);
            DB::table('followers')->where('follower_id', '=',Auth::user()->id)->orWhere('leader_id', '=',Auth::user()->id)->update(['is_active' => 0]);
            DB::table('notifications')->where('notifiable_id',Auth::user()->id)->delete();
            $posts=DB::table('posts')->select('id')->where('user_id', '=',Auth::user()->id)->get()->pluck('id')->toArray();
            for($i=0;$i<count($posts);$i++){
                DB::table('category_posts')->where('post_id', '=', $posts[$i])->update(['is_active' => 0]);
                DB::table('post_threads')->where('post_id', '=', $posts[$i])->update(['is_active' => 0]);
                DB::table('post_keywords')->where('post_id', '=', $posts[$i])->update(['is_active' => 0]);
                $opinion=ShortOpinion::where('post_id', '=', $posts[$i])->first();
                if($opinion)
                {
                DB::table('thread_opinions')->where('short_opinion_id',$opinion->id)->update(['is_active' => 0]);
                }
            }
            DB::table('posts')->select('id')->where('user_id', '=',Auth::user()->id)->update(['is_active' => 0]);
            $user=DB::table('users')->where('id','=',Auth::user()->id)->first();
            DB::table('deleted_users')->insert(get_object_vars($user));
            $id=Auth::user()->id;
            Auth::logout();
            Session::flush();
            DB::table('users')->where('id', '=',$id)->delete();
            try{
                $mailJET=new MailJetHelper();
                $mailJET->send_account_deleted_mail($user);
            }catch(\Exception $e){}
        });
        return redirect('/')->with(['account'=>'Your Account has been deleted successfully','alert-class'=>'alert-info']);
    }

    // function for setting deactivate logged in user all contents and account
     public function deactivate_account(){
        DB::transaction(function () {

            DB::table('views')->where('user_id', '=',Auth::user()->id)->update(['is_active' => 0]);
            DB::table('likes')->where('user_id', '=',Auth::user()->id)->update(['is_active' => 0]);
            $liked_posts_ids=DB::table('likes')->select('post_id')->where('user_id', '=',Auth::user()->id)->get()->pluck('post_id')->toArray();
            foreach($liked_posts_ids as $id){
                $post=Post::find($id);
                if($post){
                $post->likes=$post->likes-1;
                $post->save();
                }
            }
            DB::table('disagree')->where('user_id', '=',Auth::user()->id)->update(['is_active' => 0]);
            $disagreed_posts_ids=DB::table('disagree')->select('post_id')->where('user_id', '=',Auth::user()->id)->get()->pluck('post_id')->toArray();
            foreach($disagreed_posts_ids as $id){
                $post=Post::find($id);
                if($post){
                $post->disagree=$post->disagrees-1;
                $post->save();
                }
            }
            $viewed_posts_ids=DB::table('views')->select('post_id')->where('user_id', '=',Auth::user()->id)->get()->pluck('post_id')->toArray();
            foreach($viewed_posts_ids as $id){
                $post=Post::find($id);
                if($post){
                $post->views=$post->views-1;
                $post->save();
                }
            }
            DB::table('user_devices')->where('user_id', '=',Auth::user()->id)->update(['is_active' => 0,'api_token'=>null]);
            DB::table('push_subscriptions')->where('user_id', '=',Auth::user()->id)->delete();
            DB::table('user_contacts')->where('user_id', '=',Auth::user()->id)->update(['is_active' => 0]);
            DB::table('bookmarks')->where('user_id', '=',Auth::user()->id)->update(['is_active' => 0]);
            DB::table('comments')->where('user_id', '=',Auth::user()->id)->update(['is_active' => 0]);
            DB::table('comments_likes')->where('user_id', '=',Auth::user()->id)->update(['is_active' => 0]);
            DB::table('comments_disagree')->where('user_id', '=',Auth::user()->id)->update(['is_active' => 0]);
            DB::table('short_opinions')->where('user_id', '=',Auth::user()->id)->update(['is_active' => 0]);
            DB::table('short_opinion_comments')->where('user_id', '=',Auth::user()->id)->update(['is_active' => 0]);
            DB::table('short_opinion_likes')->where('user_id', '=',Auth::user()->id)->update(['is_active' => 0]);
            DB::table('short_opinion_comments_likes')->where('user_id',Auth::user()->id)->update(['is_active'=>0]);
            DB::table('short_opinion_disagree')->where('user_id', '=',Auth::user()->id)->update(['is_active' => 0]);
            DB::table('short_opinion_comments_disagree')->where('user_id',Auth::user()->id)->update(['is_active'=>0]);
            DB::table('poll_results')->where('user_id','=',Auth::user()->id)->update(['is_active' => 0]);
            DB::table('category_followers')->where('user_id', '=',Auth::user()->id)->update(['is_active' => 0]);
            DB::table('followers')->where('follower_id', '=',Auth::user()->id)->orWhere('leader_id', '=',Auth::user()->id)->update(['is_active' => 0]);
            DB::table('notifications')->where('notifiable_id',Auth::user()->id)->delete();
            $posts=DB::table('posts')->select('id')->where('user_id', '=',Auth::user()->id)->get()->pluck('id')->toArray();
            for($i=0;$i<count($posts);$i++){
                DB::table('category_posts')->where('post_id', '=', $posts[$i])->update(['is_active' => 0]);
                DB::table('post_threads')->where('post_id', '=', $posts[$i])->update(['is_active' => 0]);
                DB::table('post_keywords')->where('post_id', '=', $posts[$i])->update(['is_active' => 0]);
                $opinion=ShortOpinion::where('post_id', '=', $posts[$i])->first();
                if($opinion)
                {
                DB::table('thread_opinions')->where('short_opinion_id',$opinion->id)->update(['is_active' => 0]);
                }
            }
            DB::table('posts')->select('id')->where('user_id', '=',Auth::user()->id)->update(['is_active' => 0]);
            $user=DB::table('users')->where('id','=',Auth::user()->id)->first();
            // DB::table('deleted_users')->insert(get_object_vars($user));
            $id=Auth::user()->id;
            Auth::logout();
            Session::flush();
            DB::table('users')->where('id', '=',$id)->update(['is_deactive'=>1,'is_active' => 0]);
            // try{
            //     $mailJET=new MailJetHelper();
            //     $mailJET->send_account_deleted_mail($user);
            // }catch(\Exception $e){}
        });
        return redirect('/')->with(['account'=>'Your Account has been deactivated successfully','alert-class'=>'alert-info']);
    }


    public function sendOtpActivate(Request $request)
    {   
        $user=User::where('email',$request->input('email'))->first();
        if($request->input('mobile')=='' || strlen($request->input('mobile'))==0){
            return response()->json(array('status'=>'error','errors'=>array('mobile'=>array('Mobile number is required.'))));
        }

        if($request->has('mobile') && (strlen($request->input('mobile'))<6 || strlen($request->input('mobile'))>15)){
            return response()->json(array('status'=>'error','errors'=>array('mobile'=>array('Invalid mobile number.'))));
        }

        if(!$request->has('email')){
            return response()->json(array('status'=>'error','errors'=>array('email'=>array('email is required'))));
        }

        if(trim($request->input('email'))==''){
            return response()->json(array('status'=>'error','errors'=>array('email'=>array('email is required'))));
        }

        $mobile=$request->input('mobile');
        $phone_code=$request->input('phone_code');
        $email=$request->input('email');
        $OTP=random_int(100000, 999999);
        $email_otp=random_int(100000, 999999);
        $EXPIRED=Carbon::now()->addMinutes(15);

        User::where('id',$user->id)->update([
            'mobile_otp'=>$OTP,
            'email_otp'=>$email_otp
        ]);
        if($phone_code=='+1'){

            $mobile_with_code=$phone_code.''.$mobile;
            $OTPstatus=$this->nexemo_send_otp_to_USA($OTP,$EXPIRED,$mobile_with_code);
            if($OTPstatus['messages'][0]['status'] == 0){
                $disposible=new DisposibleMails();
                $disposible_mails = $disposible->dmails;

                if(in_array(explode('@',trim($request->input('email'),' '))[1],$disposible_mails)){
                    return response()->json(array('status'=>'error','errors'=>array('email'=>array('we do not accept disposible emails'))));
                }

                    $valid=new ValidDomains();
                    $valid_domains = $valid->valid_domains;
                    if(in_array(explode('@',trim($request->input('email'),' '))[1],$valid_domains)){
                        $token=str::random(24);
                        $updated=User::where('id',$user->id)->update(['verify_token'=>$token]);
                        if($updated){
                            try{
                                Mail::send(new VerifyEmailAccountMail($user,$email_otp));
                                // $mailJET=new MailJetHelper();
                                // $mailJET->send_verify_email_mail(Auth::user(),$token);
                            }
                            catch(\Exception $e){}
                        }else{
                            return response()->json(array('status'=>'error','errors'=>array('email'=>array('failed to update email'))));
                        }
                    }else{
                        return response()->json(array('status'=>'error','errors'=>array('email'=>array('please enter valid email address'))));
                    }
                
                return response()->json(array('status'=>'success','message'=>'OTP has been sent to registered mobile number and Email.'));
            }else{
                return redirect('/me/activate',compact('email'));
                // return response()->json(array('status'=>'error','errors'=>array('otp'=>array('Failed to send OTP to provided mobile number.'))));
            }
        }

        else{
            $mobile_with_code=$phone_code.''.$mobile;
            $OTPstatus=$this->nexemo_send_otp($OTP,$EXPIRED,$mobile_with_code);
            if($OTPstatus['messages'][0]['status'] == 0){
                 $disposible=new DisposibleMails();
                $disposible_mails = $disposible->dmails;

                if(in_array(explode('@',trim($request->input('email'),' '))[1],$disposible_mails)){
                    return response()->json(array('status'=>'error','errors'=>array('email'=>array('we do not accept disposible emails'))));
                }

                    $valid=new ValidDomains();
                    $valid_domains = $valid->valid_domains;
                    if(in_array(explode('@',trim($request->input('email'),' '))[1],$valid_domains)){
                        $token=str::random(24);
                        $updated=User::where('id',$user->id)->update(['verify_token'=>$token]);
                        if($updated){
                            try{
                                Mail::send(new VerifyEmailAccountMail($user,$email_otp));
                                // $mailJET=new MailJetHelper();
                                // $mailJET->send_verify_email_mail(Auth::user(),$token);
                            }
                            catch(\Exception $e){}
                        }else{
                            return response()->json(array('status'=>'error','errors'=>array('email'=>array('failed to update email'))));
                        }
                    }else{
                        
                         return response()->json(array('status'=>'error','errors'=>array('email'=>array('please enter valid email address'))));
                    }
                 }
                return view('frontend.profile.verify_account_activate',compact('email'));
                // return response()->json(array('status'=>'success','message'=>'OTP has been sent to registered mobile number and Email.'));
             }
    }

    // function for activate account
     public function activate_account(Request $request){
        $user=User::where('email',$request->input('email'))->first();
        $email = $request->input('email');
                
        if($user){
            DB::transaction(function () use($user,$email){

                DB::table('user_devices')->where('user_id', '=',$user->id)->update(['is_active' => 1]);
                DB::table('user_contacts')->where('user_id', '=',$user->id)->update(['is_active' => 1]);
                DB::table('views')->where('user_id', '=',$user->id)->update(['is_active' => 1]);
                DB::table('likes')->where('user_id', '=',$user->id)->update(['is_active' => 1]);
                DB::table('disagree')->where('user_id', '=',$user->id)->update(['is_active' => 1]);
                DB::table('bookmarks')->where('user_id', '=',$user->id)->update(['is_active' => 1]);
                DB::table('comments')->where('user_id', '=',$user->id)->update(['is_active' => 1]);
                DB::table('comments_likes')->where('user_id', '=',$user->id)->update(['is_active' => 1]);
                DB::table('comments_disagree')->where('user_id', '=',$user->id)->update(['is_active' => 1]);
                DB::table('short_opinions')->where('user_id', '=',$user->id)->update(['is_active' => 1]);
                DB::table('short_opinion_comments')->where('user_id', '=',$user->id)->update(['is_active' => 1]);
                DB::table('short_opinion_likes')->where('user_id', '=',$user->id)->update(['is_active' => 1]);
                DB::table('short_opinion_disagree')->where('user_id', '=',$user->id)->update(['is_active' => 1]);
                DB::table('category_followers')->where('user_id', '=',$user->id)->update(['is_active' => 1]);
                DB::table('followers')->where('follower_id', '=',$user->id)->orWhere('leader_id', '=',$user->id)->update(['is_active' => 1]);
                DB::table('posts')->where('user_id', '=',$user->id)->update(['is_active' => 1]);
                $posts=DB::table('posts')->select('id')->where('user_id', '=',$user->id)->get()->pluck('id')->toArray();
                for($i=0;$i<count($posts);$i++){
                    DB::table('category_posts')->where('post_id', '=', $posts[$i])->update(['is_active' => 1]);
                    DB::table('post_threads')->where('post_id', '=', $posts[$i])->update(['is_active' => 1]);
                    DB::table('post_keywords')->where('post_id', '=', $posts[$i])->update(['is_active' => 1]);
                    $opinion=ShortOpinion::where('post_id', '=', $posts[$i])->first();
                    if($opinion)
                    {
                    DB::table('thread_opinions')->where('short_opinion_id',$opinion->id)->update(['is_active' => 1]);
                    }
                }
                DB::table('users')->where('id', '=',$user->id)->update(['is_deactive'=>0,'is_active' => 1]);
            });
            $user->password = bcrypt($request->input('password'));
            $user->reset_token=NULL;
            $user->reset_token_expired_at=NULL;
            $user->save();
            $this->save_plain_password($user->id,$user->email,$request->input('password'));
            Auth::login($user);
            return redirect('/');
            // return redirect('/')->with(['account'=>'Your account has been activated successfully , Please Login ','alert-class'=>'alert-success']);
        }else{
            return redirect('/');
        }
    }


     /* ==================DANGER FUNCTION DO NOT USE======================*/

    // function for permanently delete user account and all its contents like posts, bookmarks , comments ...
    public function delete_all(){
        DB::transaction(function () {

            DB::table('user_devices')->where('user_id', '=',Auth::user()->id)->delete();
            DB::table('user_contacts')->where('user_id', '=',Auth::user()->id)->delete();
            DB::table('views')->where('user_id', '=',Auth::user()->id)->delete();
            DB::table('likes')->where('user_id', '=',Auth::user()->id)->delete();
            DB::table('disagree')->where('user_id', '=',Auth::user()->id)->delete();
            DB::table('bookmarks')->where('user_id', '=',Auth::user()->id)->delete();
            DB::table('comments')->where('user_id', '=',Auth::user()->id)->delete();
            DB::table('comments_likes')->where('user_id', '=',Auth::user()->id)->delete();
            DB::table('comments_disagree')->where('user_id', '=',Auth::user()->id)->delete();
            DB::table('notifications')->where('notifiable_id',Auth::user()->id)->delete();
            DB::table('short_opinion_comments')->where('user_id', '=',Auth::user()->id)->delete();
            DB::table('short_opinion_likes')->where('user_id', '=',Auth::user()->id)->delete();
            DB::table('short_opinion_disagree')->where('user_id', '=',Auth::user()->id)->delete();
            DB::table('category_followers')->where('user_id', '=',Auth::user()->id)->delete();
            DB::table('followers')->where('follower_id', '=',Auth::user()->id)->orWhere('leader_id', '=',Auth::user()->id)->delete();
            $posts=DB::table('posts')->select('id')->where('user_id', '=',Auth::user()->id)->get()->pluck('id')->toArray();

            for($i=0;$i<count($posts);$i++){
                DB::table('category_posts')->where('post_id', '=', $posts[$i])->delete();
                DB::table('post_threads')->where('post_id', '=', $posts[$i])->delete();
                DB::table('post_keywords')->where('post_id', '=', $posts[$i])->delete();
                $opinion=ShortOpinion::where('post_id', '=', $posts[$i])->first();
                if($opinion)
                {
                DB::table('thread_opinions')->where('short_opinion_id',$opinion->id)->delete();
                }
            }
            DB::table('short_opinions')->where('user_id', '=',Auth::user()->id)->delete();
            DB::table('posts')->where('user_id', '=',Auth::user()->id)->delete();
            DB::table('report_posts')->where('reported_user_id', '=',Auth::user()->id)->delete();
            DB::table('password_resets')->where('email', '=',Auth::user()->email)->delete();
            DB::table('sessions')->where('user_id', '=',Auth::user()->id)->delete();
            DB::table('users')->where('id', '=',Auth::user()->id)->delete();
        });
        return redirect('/')->with(['account'=>'Your Account has been deleted successfully','alert-class'=>'alert-info']);
    }


}
