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



class AdminEmployeeController extends Controller
{

    public function __construct()
    {
       $this->middleware('auth:admin');
        View::share('menu','index');
    }

    

    public function showEmployees(Request $request){

        $employees=DB::table("employee")->where('status','=',1)->get();
        $from=$request->has('from')?$request->query('from'):Carbon::create(2018, 1, 01, 0, 0, 0);
        $to=$request->has('to')?$request->query('to'):Carbon::now()->endOfDay();

        
        
        if($request->ajax()){
            $view = (String) view('admin.employee.employee_row',compact('users'));
            return response()->json(['html'=>$view]);
        }else{

            return view('admin.employee.employees',compact('from','to','employees'));

        }

    }

   public function showAddEmployeeForm(){
        return view('admin.employee.add_employee');
    }

    public function addEmployee(Request $request){
        $this->validate($request,[
            'name'=>'required',
            
        ]);

        $name=$request->input('name');
        $email=$request->input('email');
        $cmpemail=$request->input('cmpemail');
        $phone_code=$request->input('phone_code');
        $mobile=$request->input('mobile');
        $dateofbirth=$request->input('dob');
        $position=$request->input('position');
        $dateofjoin=$request->input('joindate');
        $employee=new Employee();
        $this->saveEmployee($employee,$name,$email,$cmpemail,$phone_code,$mobile,$dateofbirth,$position,$dateofjoin,0);
        return redirect()->route('admin.administration')->with(['message'=>'Employee has been successfully Created.']);
    }


    public function showEditEmployeeForm(Request $request,$employeeid){
        $employee=Employee::find($employeeid);
        if($employee){
            if($request->has('json') && $request->query('json')==1){
                return response()->json(array('employee'=>$employee));
            }
            return view('admin.employee.edit_employee',compact('employee'));
        }else{
            return view('admin.error.404');
        }
    }

    public function updateEmployee(Request $request){
        $this->validate($request,[
            'name'=>'required',
            
        ]);

        $name=$request->input('name');
        $email=$request->input('email');
        $cmpemail=$request->input('cmpemail');
        $phone_code=$request->input('phone_code');
        $mobile=$request->input('mobile');
        $dateofbirth=$request->input('dob');
        $position=$request->input('position');
        $dateofjoin=$request->input('joindate');
        $employee= Employee::find($request->input('empid'));
        $this->saveEmployee($employee,$name,$email,$cmpemail,$phone_code,$mobile,$dateofbirth,$position,$dateofjoin,0);
        return redirect()->route('admin.administration')->with(['message'=>'Employee has been successfully Updated.']);
    }

    public function deleteEmployee(Request $request,$employeeid){
        

        $employee= Employee::find($employeeid);
        DB::table('employee')->where('id','=',$employee->id)->delete();
        DB::table('admins')->where('email','=',$employee->cmpemail)->delete();
        return redirect()->route('admin.administration')->with(['message'=>'Employee has been successfully Deleted.']);
    }
    
    public function desablePanel(Request $request,$employeeid){
       
        $employee= Employee::find($employeeid);
        DB::table('admins')->where('email','=',$employee->cmpemail)->delete();
        DB::table('employee')->where('id','=',$employee->id)->update(['cpanel' => 0]);
        return redirect()->route('admin.administration')->with(['message'=>'Cpanel Access removed for the Employee']);
    }
    
    protected function saveEmployee(Employee $employee,$name,$email,$cmpemail,$phone_code,$mobile,$dateofbirth,$position,$dateofjoin,$is_active){
        $employee->name=$name;
        $employee->email=$email;
        $employee->cmpemail=$cmpemail;
        $employee->phone_code= $phone_code;
        $employee->mobile=$mobile;
        $employee->dateofbirth=$dateofbirth;
        $employee->dateofjoin=$dateofjoin;
        //$category->slug= $slug;
        $employee->position=$position;
        //$category->category_group=$group;
        //$employee->image=$imageurl;

        $employee->is_active=$is_active;
        $employee->save();
    }

    

    // function for send Invite link via email to Employee
    public function send_invitation(Request $request,$employeeid){

        $employee= Employee::find($employeeid);
                $token=str::random(24);
                $employee->verify_token = $token;
                $employee->save();
            

            try{
                  Mail::send(new VerifyAccountMail(Auth::user(),$token));
                  $mailJET=new MailJetHelper();
                  $mailJET->send_invite_email_mail($employee,$token);
                }
            catch(\Exception $e){}

            return redirect()->back()->with(['message'=>'Link has been sent to Employees registered email address '.$employee->cmpemail.'','alert-class'=>'alert-info']);
        }
    


}
