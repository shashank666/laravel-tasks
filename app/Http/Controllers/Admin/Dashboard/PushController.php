<?php

namespace App\Http\Controllers\Admin\Dashboard;
use Illuminate\Support\Str;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;
use App\Model\UserDevice;
use App\Jobs\Admin\SendAppPush;
use Illuminate\Contracts\Bus\Dispatcher;

class PushController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
        View::share('menu','pushmanager');
    }

    public function index(){
        $devices['total']=UserDevice::where('is_active',1)->whereNotNull('gcm_token')->count();
        return view('admin.dashboard.pushmanager.index')->with(['devices'=>$devices]);
    }

    public function send(Request $request){
        $this->validate($request,[
            'title'=>'required',
            'message'=>'required'
        ]);
        $title=$request->input('title');
        $message=$request->input('message');
        $job = (new SendAppPush($title,$message))->onQueue('default');
        $job_id  = app(Dispatcher::class)->dispatch($job);
        return redirect()->route('admin.push.index')->with(['message'=>'Notification sending started ...']);
    }


}
