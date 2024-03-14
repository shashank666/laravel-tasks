<?php

namespace App\Http\Controllers\Admin\Dashboard;
use Illuminate\Support\Str;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Model\User;
use App\Model\EmailManager;
use Illuminate\Support\Facades\Redis;
use Carbon\Carbon;
use Artisan;


class EmailController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:admin');
        View::share('menu','email');
    }

    public function index(){
        $emails=EmailManager::orderBy('created_at','desc')->paginate(12);
        return view('admin.dashboard.emailmanager.index',compact('emails'));
    }

    public function create_form(){
        return view('admin.dashboard.emailmanager.create');
    }


    public function create(Request $request){
        $this->validate($request,[
            'email_to_type'=>'required',
            'to'=>'required',
            'subject'=>'required',
            'message'=>'required'
        ]);
        $email_to_type=$request->input('email_to_type');
        $subject=$request->input('subject');
        $message=$request->input('message');
        if($email_to_type=='specific'){
            $to=implode(",",explode(" ",$request->input('hidden-custom')));
        }else{
            $to=$request->input('to');
        }
        $emailManager=new EmailManager();
        $emailManager->email_to_type=$email_to_type;
        $emailManager->email_to=$to;
        $emailManager->email_subject=$subject;
        $emailManager->email_content=$message;
        $emailManager->save();
        return redirect()->route('admin.email.preview',['id'=>$emailManager->id]);
    }

    public function preview($id){
        $emailManager=EmailManager::findOrFail($id);
        return view('admin.dashboard.emailmanager.preview')->with(['email'=>$emailManager]);
    }

    public function schedule_and_send(Request $request){
        $email_id=$request->input('email_id');
        Artisan::call('admin:sendemail',['email_id' => $email_id]);
        return redirect()->route('admin.email.index')->with(['message'=>'Email Job '.$email_id.' has been scheduled after 1 minute']);
    }

    public function stop(Request $request){
        $email_id=$request->input('email_id');
        $emailManager=EmailManager::find($email_id);
        $emailManager->is_active=0;
        $emailManager->status='stopped';
        $emailManager->save();
        $key='horizon:'.$emailManager->job_id;
        Redis::del($key);
        //Redis::del('queues:email:reserved');
        Artisan::call('queue:clear',['connection' => 'redis','queue'=>'email']);
        return redirect()->route('admin.email.index')->with(['message'=>'Email Job '.$email_id.'has been stopped']);
    }

    public function delete(Request $request){
        $email_id=$request->input('email_id');
        $key='horizon:'.$email_id;
        Redis::del($key);
        EmailManager::where('id',$email_id)->delete();
        return redirect()->route('admin.email.index')->with(['message'=>'Email Job '.$email_id.' has been deleted']);
    }

    public function edit(Request $request,$id){
        $emailManager=EmailManager::findOrFail($id);
        return view('admin.dashboard.emailmanager.edit')->with(['email'=>$emailManager]);
    }

    public function update(Request $request){
        $this->validate($request,[
            'email_id'=>'required',
            'subject'=>'required',
            'message'=>'required',
        ]);
        $email_id=$request->input('email_id');
        $subject=$request->input('subject');
        $message=$request->input('message');
        $emailManager=EmailManager::findOrFail($email_id);
        $emailManager->email_subject=$subject;
        $emailManager->email_content=$message;
        $emailManager->save();
        return redirect()->route('admin.email.preview',['id'=>$email_id]);
    }


    public function find_users(Request $request){
        if($request->has('q') && strlen($request->input('q')) > 0){
            $query=$request->input('q');
            if($request->ajax()){
                    $users=User::where(['is_active'=>1])
                    ->where('name', 'LIKE', $query.'%')
                    ->orWhere('email', 'LIKE', $query.'%')
                    ->orWhere('username', 'LIKE', $query.'%')
                    ->take(100)->get();
                    return response()->json(array('status' => 'success','users'=>$users));
            }
        }else{
            return response()->json(array('status' => 'error'));
        }
    }

    public function upload_image(Request $request){
        $inputName='image';
        $folder='mails';
        $validator = Validator::make($request->all(),
        [
            'image'=>'required|mimes:jpeg,jpg,png,gif|max:3072'
        ]);
        if ($validator->fails()){
            $response=array('status'=>'error','result'=>0,'errors'=>implode(',',$validator->errors()->all()));
            return response()->json($response, 200);
        }
        if($request->hasFile($inputName)){
            $uniqueid=uniqid();
            $original_name=$request->file($inputName)->getClientOriginalName();
            $original_size=$request->file($inputName)->getSize();
            $extension=$request->file($inputName)->getClientOriginalExtension();
            $filename=Carbon::now()->format('Ymd').'_'.$uniqueid.'.'.$extension;
            $imagepath=url('/storage/'.$folder.'/'.$filename);
            $path=$request->file($inputName)->storeAs('public/'.$folder,$filename);
            $size=$this->optimize_image($extension,$folder,$filename,$original_size);
            $response=array('status'=>'success','result'=>1,'image'=>$imagepath);
            return response()->json($response, 200);
        }
    }

}
