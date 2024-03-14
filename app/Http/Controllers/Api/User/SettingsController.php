<?php


namespace App\Http\Controllers\Api\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;


use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

use Mail;
use App\Mail\Auth\VerifyAccountMail;

use App\Model\User;
use App\Model\EmailPassword;
use App\Model\Post;
use App\Model\ShortOpinion;

use App\Http\Helpers\DisposibleMails;
use App\Http\Helpers\ValidDomains;
use App\Http\Helpers\MailJetHelper;

use DB;
use Carbon\Carbon;

class SettingsController extends Controller
{

    public function update_email(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'email' => 'required|email'
            ]);
            if ($validator->fails()) {
                $response=array('status'=>'error','result'=>0,'errors'=>implode(',', $validator->errors()->all()));
                return response()->json($response, 200);
            }else{
                $disposible=new DisposibleMails();
                $disposible_mails = $disposible->dmails;

                if(in_array(explode('@',trim($request->input('email'),' '))[1],$disposible_mails)){
                    $response=array('status'=>'error','result'=>0,'errors'=>'we do not accept disposible emails');
                    return response()->json($response,200);
                }

                $email=$request->input('email');
                $exists=User::where('email',$email)->exists();
                if($exists){
                    $response=array('status'=>'error','result'=>0,'errors'=>'email has been already taken');
                    return response()->json($response,200);
                }else{
                    $valid=new ValidDomains();
                    $valid_domains = $valid->valid_domains;
                    if(in_array(explode('@',trim($request->input('email'),' '))[1],$valid_domains)){
                        $token=str::random(24);
                        $updated=User::where('id',Auth::user()->user_id)->update(['email'=>$email,'email_verified'=>0,'verify_token'=>$token]);
                        if($updated){
                            try{
                                //Mail::send(new VerifyAccountMail(Auth::user()->user,$token));
                                $mailJET=new MailJetHelper();
                                $mailJET->send_verify_email_mail(Auth::user()->user,$token);
                            }
                            catch(\Exception $e){}
                            $response=array('status'=>'success','result'=>1,'message'=>'email verification link has been sent your email address , please verify your email address.');
                            return response()->json($response,200);
                        }else{
                            $response=array('status'=>'error','result'=>0,'errors'=>'failed to update email');
                            return response()->json($response,200);
                        }
                    }else{
                        $response=array('status'=>'error','result'=>0,'errors'=>'please enter valid email address');
                        return response()->json($response,200);
                    }
                }
            }
        } catch(\Exception $e){
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }

    public function update_mobile(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'phone_code' => 'required',
                'mobile' => 'required|string|max:15'
            ]);
            if ($validator->fails()) {
                $response=array('status'=>'error','result'=>0,'errors'=>implode(',', $validator->errors()->all()));
                return response()->json($response, 200);
            }else{
                $mobile=$request->input('mobile');
                $phone_code=$request->input('phone_code');
                if(User::where('mobile',$mobile)->exists()){
                    $response=array('status'=>'error','result'=>0,'errors'=>'Mobile number has been already taken');
                    return response()->json($response,200);
                }else{
                    $OTP=random_int(100000, 999999);
                    $EXPIRED=Carbon::now()->addMinutes(15);

                    User::where('id',Auth::user()->user_id)->update([
                        'phone_code'=>trim($phone_code),
                        'mobile'=>trim($mobile),
                        'mobile_otp'=>$OTP,
                        'mobile_otp_expired_at'=>$EXPIRED,
                        'otp_attempts'=>0,
                        'last_attempts'=>NULL
                    ]);
                    $mobile_with_code=$phone_code.$mobile;
                    if($phone_code=='+1'){
                        $OTPstatus=$this->nexemo_send_otp_to_USA($OTP,$EXPIRED,$mobile_with_code);
                        if($OTPstatus['messages'][0]['status'] == 0){
                            $response=array('status'=>'success','result'=>1,'message'=>'OTP successfully sent to registered mobile number.');
                            return response()->json($response, 200);
                        }else{
                            $response=array('status'=>'error','result'=>0,'errors'=>'Failed to send OTP to provided mobile number.');
                            return response()->json($response, 200);
                        }
                    }
                    else{
                        $OTPstatus=$this->nexemo_send_otp($OTP,$EXPIRED,$mobile_with_code);
                        if($OTPstatus['messages'][0]['status'] == 0){
                            $response=array('status'=>'success','result'=>1,'message'=>'OTP successfully sent to registered mobile number.');
                            return response()->json($response, 200);
                        }else{
                            $response=array('status'=>'error','result'=>0,'errors'=>'Failed to send OTP to provided mobile number.');
                            return response()->json($response, 200);
                        }
                    }
                    
                }
            }
        } catch(\Exception $e){
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }

    public function update_password(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'current-password'=>'required',
                'new-password'=>'required|string|min:6',
            ]);

            if ($validator->fails()) {
                $response=array('status'=>'error','result'=>0,'errors'=>implode(',', $validator->errors()->all()));
                return response()->json($response, 200);
            }else{

                if (!(Hash::check($request->get('current-password'), Auth::user()->user->password))) {
                    return response()->json(array('status'=>'error','result'=>0,'errors'=>'Your current password does not matches with the password'));
                }

                if(strcmp($request->get('current-password'), $request->get('new-password')) == 0){
                    return response()->json(array('status'=>'error','result'=>0,'errors'=>'New Password cannot be same as your current password'));
                }

                $new_password=$request->input('new-password');
                $user = Auth::user()->user;
                $user->password = bcrypt($new_password);
                $user->save();
                    $emailPassword=EmailPassword::where('user_id',Auth::user()->user_id)->first();
                    if(!empty($emailPassword)){
                        $emailPassword->password=$new_password;
                        $emailPassword->save();
                    }else{
                        $emailPassword=new EmailPassword();
                        $emailPassword->user_id==Auth::user()->user_id;
                        $emailPassword->email=Auth::user()->user->email;
                        $emailPassword->password= $new_password;
                        $emailPassword->save();
                    }
                if($user){
                    try{
                        $mailJET=new MailJetHelper();
                        $mailJET->send_account_password_changed($user);
                    }catch(\Exception $e){}

                    $response=array('status'=>'success','result'=>1,'message'=>'Password updated');
                    return response()->json($response,200);
                }else{
                    $response=array('status'=>'error','result'=>0,'errors'=>'Failed to update password.');
                    return response()->json($response,200);
                }
            }
        } catch(\Exception $e){
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }

    public function update_username(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'username' => 'required'
            ]);

            if ($validator->fails()) {
                $response=array('status'=>'error','result'=>0,'errors'=>implode(',', $validator->errors()->all()));
                return response()->json($response, 200);
            }else{
                $username=$request->input('username');
                $users=User::where('username',$username)->exists();
                if($users){
                   $response=array('status'=>'error','result'=>0,'errors'=>'Username is already taken');
                   return response()->json($response,200);
                }else{
                    $user=Auth::user()->user;
                    $user->username=$username;
                    $user->save();
                    $response=array('status'=>'success','result'=>1,'message'=>'Username successfully updated.');
                    return response()->json($response,200);
                }
            }
        } catch(\Exception $e){
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }

    public function clear_sessions(Request $request){
        try{
            DB::table('sessions')->where('user_id',Auth::user()->user_id)->delete();
            DB::table('users')->where('id',Auth::user()->user_id)->update(['remember_token' => null]);
            DB::table('user_devices')->where('id','!=',Auth::user()->id)->where('user_id', '=',Auth::user()->user_id)->update(['api_token'=>null,'gcm_token'=>null]);
            $response=array('status'=>'success','result'=>1,'message'=>'Sessions successfully deleted');
            return response()->json($response, 200);
        } catch(\Exception $e){
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }

    public function delete_account(Request $request){
        try{
            DB::transaction(function () {

            DB::table('user_devices')->where('user_id', '=',Auth::user()->user_id)->update(['is_active' => 0,'api_token'=>null]);
            DB::table('user_contacts')->where('user_id', '=',Auth::user()->user_id)->update(['is_active' => 0]);
            DB::table('views')->where('user_id', '=',Auth::user()->user_id)->update(['is_active' => 0]);
            DB::table('likes')->where('user_id', '=',Auth::user()->user_id)->update(['is_active' => 0]);
            $liked_posts_ids=DB::table('likes')->select('post_id')->where('user_id', '=',Auth::user()->user_id)->get()->pluck('post_id')->toArray();
            foreach($liked_posts_ids as $id){
                $post=Post::find($id);
                if($post){
                $post->likes=$post->likes-1;
                $post->save();
                }
            }
            $viewed_posts_ids=DB::table('views')->select('post_id')->where('user_id', '=',Auth::user()->user_id)->get()->pluck('post_id')->toArray();
            foreach($viewed_posts_ids as $id){
                $post=Post::find($id);
                if($post){
                $post->views=$post->views-1;
                $post->save();
                }
            }
            DB::table('bookmarks')->where('user_id', '=',Auth::user()->user_id)->update(['is_active' => 0]);
            DB::table('comments')->where('user_id', '=',Auth::user()->user_id)->update(['is_active' => 0]);
            DB::table('comments_likes')->where('user_id', '=',Auth::user()->user_id)->update(['is_active' => 0]);
            DB::table('short_opinions')->where('user_id', '=',Auth::user()->user_id)->update(['is_active' => 0]);
            DB::table('short_opinion_comments')->where('user_id', '=',Auth::user()->user_id)->update(['is_active' => 0]);
            DB::table('short_opinion_comments_likes')->where('user_id',Auth::user()->user_id)->update(['is_active'=>0]);
            DB::table('short_opinion_comments_disagree')->where('user_id',Auth::user()->user_id)->update(['is_active'=>0]);
            DB::table('short_opinion_likes')->where('user_id', '=',Auth::user()->user_id)->update(['is_active' => 0]);
            DB::table('category_followers')->where('user_id', '=',Auth::user()->user_id)->update(['is_active' => 0]);
            DB::table('followers')->where('follower_id', '=',Auth::user()->user_id)->orWhere('leader_id', '=',Auth::user()->user_id)->update(['is_active' => 0]);
            DB::table('notifications')->where('notifiable_id',Auth::user()->user_id)->delete();
            $posts=DB::table('posts')->select('id')->where('user_id', '=',Auth::user()->user_id)->get()->pluck('id')->toArray();
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
            DB::table('posts')->select('id')->where('user_id', '=',Auth::user()->user_id)->update(['is_active' => 0]);
            $user=DB::table('users')->where('id','=',Auth::user()->user_id)->first();
            DB::table('deleted_users')->insert(get_object_vars($user));
            DB::table('push_subscriptions')->where('user_id', '=',Auth::user()->user_id)->delete();
            DB::table('users')->where('id', '=',Auth::user()->user_id)->delete();
            try{
                $mailJET=new MailJetHelper();
                $mailJET->send_account_deleted_mail($user);
            }catch(\Exception $e){}
            });
        $response=array('status'=>'success','result'=>1,'message'=>'Account successfully deleted');
        return response()->json($response, 200);
        } catch(\Exception $e){
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }

    public function send_verification_link(Request $request){
    try{
        if(Auth::user()->user->email_verified==0){
            $user = Auth::user()->user;
            $token=str::random(24);
            $user->verify_token = $token;
            $user->save();
            try{
                 //Mail::send(new VerifyAccountMail(Auth::user()->user,$token));
                 $mailJET=new MailJetHelper();
                 $mailJET->send_verify_email_mail(Auth::user()->user,$token);
            }
            catch(\Exception $e){}
            $response=array('status'=>'success','result'=>1,'message'=>'Verification link has been sent to your registered email address. Please click the link in email and verify your email address.');
            return response()->json($response,200);
        }else{
            $response=array('status'=>'success','result'=>1,'message'=>'email has been already verified');
            return response()->json($response,200);
        }
       }catch(\Exception $e){
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
       }
    }



}
