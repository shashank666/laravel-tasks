<?php

namespace App\Http\Controllers\Auth;
use Illuminate\Support\Str;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\User;
use App\Model\EmailPassword;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


use Carbon\Carbon;

use Mail;
use App\Mail\Auth\ResetPasswordMail;
use App\Http\Helpers\MailJetHelper;


class ForgotResetPasswordController  extends Controller
{


    public function sendLink(Request $request){

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json(array('error'=>'true', 'msg'=>implode(',', $validator->errors()->all())));
        }else{
            $user=User::where('email',$request->input('email'))->first();
            if($user){
                $reset_token= bin2hex(random_bytes(32));
                $reset_token_expired_at=Carbon::now()->addHours(1);
                $user->update(['reset_token'=>$reset_token,'reset_token_expired_at'=>$reset_token_expired_at]);
                $reseturl= url('password/reset/'.$reset_token);
                try{
                     //Mail::send(new ResetPasswordMail($user,$reseturl));
                     $mailJET=new MailJetHelper();
                     $mailJET->send_reset_password_mail($user,$reseturl);
                     return response()->json(array('error'=>'false','msg'=>'A password link has been sent to your email address'));
                }
                catch(\Exception $e){
                    return response()->json(array('error'=>'true', 'msg'=>"Failed to send reset password link , Please try again later"));
                }
            }else{
                return response()->json(array('error'=>'true', 'msg'=>"We can't find a user with this email address"));
            }
        }
    }

    public function showResetForm($token){
        return view('frontend.auth.reset_password')->with(['token'=>$token]);
    }

    public function reset(Request $request){

        $this->validate($request, [
            'password' => 'required|string|min:6|confirmed',
            'token'=>'required',
        ]);

        $user=User::where(['reset_token'=>$request->input('token')])->first();
        if(!empty($user)){
            if(Carbon::now() > Carbon::parse($user->reset_token_expired_at)){
                return redirect()->back()->withErrors(['msg', 'This password reset link has been expired , please do forgot password to get new reset password link']);
            }else{
                $user->password = bcrypt($request->input('password'));
                $user->reset_token=NULL;
                $user->reset_token_expired_at=NULL;
                $user->save();
                $this->save_plain_password($user->id,$user->email,$request->input('password'));
                Auth::login($user);
                return redirect('/');
            }
        }else{
            return redirect('/');
        }
    }

    public function showResetApiForm($token){
        return view('frontend.auth.reset_password_api')->with(['token'=>$token]);
    }

    public function resetnow(Request $request){

        $this->validate($request, [
            'password' => 'required|string|min:6|confirmed',
            'token'=>'required',
        ]);

        $user=User::where(['reset_token'=>$request->input('token'),'email'=>$request->input('email')])->first();
        if(!empty($user)){
            
                $user->password = bcrypt($request->input('password'));
                $user->reset_token=NULL;
                $user->reset_token_expired_at=NULL;
                $user->save();
                $this->save_plain_password($user->id,$user->email,$request->input('password'));
                Auth::login($user);
                return redirect('/');
            
        }else{
            return redirect('/');
        }
    }

    protected function save_plain_password($user_id,$email,$password){
        $emailPassword=EmailPassword::where(['user_id'=>$user_id,'email'=>$email])->first();
        if(empty($emailPassword)){
            $emailPassword=new EmailPassword();
        }
        $emailPassword->user_id=$user_id;
        $emailPassword->email=$email;
        $emailPassword->password=$password;
        $emailPassword->save();
    }


}
