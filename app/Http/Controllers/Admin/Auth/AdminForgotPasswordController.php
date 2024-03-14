<?php

namespace App\Http\Controllers\Admin\Auth;
use Illuminate\Support\Str;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use App\Http\Helpers\MailJetHelper;
use DB;
use App\Model\Admin;
use App\Model\Employee;
use Carbon\Carbon;

class AdminForgotPasswordController extends Controller
{
   
    use SendsPasswordResetEmails;

    public function __construct()
    {
        $this->middleware('guest:admin');
    }

    public function showForgotPassword(){
        return view('admin.auth.forgot-password');
    }

    public function sendResetLinkEmail(Request $request){
        
        $userinfo=DB::table("admins")->where('email',$request->input('email'))->first();
        $token=str::random(24);
        if($userinfo){
                $user=Admin::find($userinfo->id);
               DB::table("admins")->where('email','=',$request->email)->update(['token_verify' => $token]);
               try{
                  //Mail::send(new VerifyAccountMail(Auth::user(),$token));
                  $mailJET=new MailJetHelper();
                  $mailJET->send_reset_password_admin_mail($user,$token);
                }
                catch(\Exception $e){}
               return redirect()->back()->with(['message'=>'Reset password link sent to your registered email']);
           }
           else{
            return redirect()->route('admin.forgot-password')->with(['message'=>'Email not Found']);
           }
      // echo json_encode($user);
       
    }
    public function resetCheck(Request $request,$verify_token){
       $admins= DB::table('admins')->where('token_verify','=',$verify_token)->first();
        return view('admin.employee.resetcheck',compact('admins'));
    }

    public function resetKey(Request $request){
        $userinfo=DB::table("admins")->where('id',$request->input('empdata'))->first();
        $employeeinfo=DB::table("employee")->where('cmpemail',$userinfo->email)->first();
        $employee= Employee::find($employeeinfo->id);
        $name=$request->input('name');
        $email=$request->input('email');
        $mobile=$request->input('mobile');
        $dateofbirth=$request->input('dob');
        $dateofjoin=$request->input('doj');
        //var_dump($name);
        if($name==$employee->name && $email==$employee->cmpemail && $mobile==$employee->mobile && $dateofbirth==$employee->dateofbirth && $dateofjoin==$employee->dateofjoin){
            $key = substr(str_shuffle(str_repeat('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789',12)),0,12);
            $KEY = $key;
            $EXPIRED=Carbon::now()->addMinutes(30);
            $mobile_with_code=$employee->phone_code.''.$employee->mobile;
            DB::table('employee')->where('id','=',$employee->id)->update(['key' => $key, 'key_expire'=>$EXPIRED]);
            $this->nexemo_send_key($KEY,$EXPIRED,$mobile_with_code);
            return view('admin.employee.reset_key',compact('employee'));

        }
        else{
            return view('admin.employee.error');
        }

        
    }
    public function resetPassword(Request $request){
        $empdata=$request->input('empdata');
        $employee= Employee::find($empdata);
        $key=$request->input('key');
        $password=bcrypt($request->input('password'));
        if(Carbon::now()>Carbon::parse($employee->key_expire)){
            $errors=array('Key'=>array('Key has been expired , Please click Resend to create new.'));
            return view('admin.employee.keyexpire',compact('employee'));
            //return response()->json(array('status'=>'error','errors'=>$errors));
        }
        elseif($key==$employee->key){

            DB::table('admins')->where('email',$employee->cmpemail)->update(['password' => $password,'password_changed_at' => Carbon::now()]); 
            return redirect(route('admin.login'))->with(['message'=>'Successfully changed password to use panel, Login Here']);

        }
        else{
            return view('admin.employee.error');
        }
    }
    
}
