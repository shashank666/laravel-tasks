<?php

namespace App\Http\Controllers\Admin\Employee;
use Illuminate\Support\Str;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;
use App\Model\User;
use App\Model\ShortOpinion;
use App\Model\ShortOpinionComment;
use App\Model\ShortOpinionLike;
use App\Model\UserAccount;
use App\Model\UserDevice;
use App\Model\UserContact;
use App\Model\Post;
use App\Model\Like;
use App\Model\Bookmark;
use App\Model\Comment;
use App\Model\Follower;
use App\Model\Category;
use App\Model\CategoryFollower;
use App\Model\Thread;
use App\Model\Employee;
use App\Model\Admin;
use DB;
use Carbon\Carbon;
use App\Http\Helpers\MailJetHelper;



class EmployeeController extends Controller
{

    public function __construct()
    {
       
    }

    
    public function activationPanel(Request $request,$verify_token){
       $employee= DB::table('employee')->where('verify_token','=',$verify_token)->first();
        return view('admin.employee.activation',compact('employee'));
    }
    

    public function checkInfoPanel(Request $request){
        $empdata=$request->input('empdata');
        $employee= Employee::find($empdata);
        $name=$request->input('name');
        $email=$request->input('email');
        $mobile=$request->input('mobile');
        $dateofbirth=$request->input('dob');
        $dateofjoin=$request->input('doj');
        
        if($name==$employee->name && $email==$employee->cmpemail && $mobile==$employee->mobile && $dateofbirth==$employee->dateofbirth && $dateofjoin==$employee->dateofjoin){
            $key = substr(str_shuffle(str_repeat('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789',12)),0,12);
            $KEY = $key;
            $EXPIRED=Carbon::now()->addMinutes(30);
            $mobile_with_code=$employee->phone_code.''.$employee->mobile;
            DB::table('employee')->where('id','=',$employee->id)->update(['key' => $key, 'key_expire'=>$EXPIRED]);
            $this->nexemo_send_key($KEY,$EXPIRED,$mobile_with_code);
            return view('admin.employee.checkinfopanel',compact('employee'));

        }
        else{
            return view('admin.employee.error');
        }

        
    }


    public function createPanel(Request $request){
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

            $admin=new Admin();
            $this->saveAdmin($admin,$employee->name,$employee->cmpemail,$password,1);
            DB::table('employee')->where('id',$empdata)->update(['cpanel' => 1]); 
            return redirect(route('admin.login'))->with(['message'=>'Successfully granted to use panel, Login Here']);

        }
        else{
            return view('admin.employee.error');
        }

        
    }
    
   public function resendKey(Request $request){
        $empdata=$request->input('empdata');
        $employee= Employee::find($empdata);
        $key = substr(str_shuffle(str_repeat('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789',12)),0,12);
        $KEY = $key;
        $EXPIRED=Carbon::now()->addMinutes(30);
        $mobile_with_code=$employee->phone_code.''.$employee->mobile;
        DB::table('employee')->where('id','=',$employee->id)->update(['key' => $key, 'key_expire'=>$EXPIRED]);
        $this->nexemo_send_key($KEY,$EXPIRED,$mobile_with_code);
        return view('admin.employee.checkinfopanel',compact('employee'))->with(['message'=>'Key has been resent to your number']);

        
    }

    protected function saveAdmin(Admin $admin,$name,$email,$password,$is_active){
        $admin->name=$name;
        $admin->email=$email;
        $admin->password= $password;
        $admin->is_active=$is_active;
        $admin->save();
    }

    

}
