<?php

namespace App\Http\Controllers\Admin\Dashboard;
use Illuminate\Support\Str;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\UserDevice;
use App\Model\UserContact;
use Illuminate\Support\Facades\View;
use Carbon\Carbon;
use DB;

class AndroidController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
        View::share('menu','android');
    }

    public function index(){

        $count['users_installed_app']=UserDevice::select('user_id')->distinct()->where('is_active',1)->count();
        $count['total_devices']=UserDevice::count();
        $count['total_active_devices']=UserDevice::where('is_active',1)->count();
        $count['total_disabled_devices']=UserDevice::where('is_active',0)->count();
        $count['total_contacts']=UserContact::count();
        $count['user_logins_30days']=UserDevice::where('is_active',1)->whereBetween('updated_at', [Carbon::now()->subDays(30), Carbon::now()])->count();

        $top_brands=UserDevice::select('device_brand',DB::raw('count(*) as total'))->where('is_active',1)->groupBy('device_brand')->orderBy('total','desc')->get();
        $app_version_installs=UserDevice::select('app_version',DB::raw('count(*) as total'))->where('is_active',1)->groupBy('app_version')->orderBy('app_version','desc')->get();
        $os_version_installs=UserDevice::select('device_os_version','device_os_name',DB::raw('count(*) as total'))->where('is_active',1)->groupBy('device_os_version')->orderBy('device_os_version','desc')->get();
        return view('admin.dashboard.android.index',compact('count','top_brands','app_version_installs','os_version_installs'));
    }
    public function devices(Request $request,$brand){

        $count['users_installed_app']=UserDevice::select('user_id')->distinct()->where(['is_active'=>1,'device_brand'=>$brand])->count();
        $count['total_devices']=UserDevice::where(['device_brand'=>$brand])->count();
        $count['total_active_devices']=UserDevice::where(['is_active'=>1,'device_brand'=>$brand])->count();
        $count['total_disabled_devices']=UserDevice::where(['is_active'=>0,'device_brand'=>$brand])->count();
        $count['total_contacts']=UserContact::count();
        $count['users_installed_app_30days']=UserDevice::select('user_id')->distinct()->where(['is_active'=>1,'device_brand'=>$brand])->whereBetween('created_at', [Carbon::now()->subDays(30), Carbon::now()])->count();
        $count['user_logins_30days']=UserDevice::where(['is_active'=>1,'device_brand'=>$brand])->whereBetween('updated_at', [Carbon::now()->subDays(30), Carbon::now()])->count();
        $current_brand = $brand;
        $top_models=UserDevice::select('device_model',DB::raw('count(*) as total'))->where(['is_active'=>1,'device_brand'=>$brand])->groupBy('device_model')->orderBy('total','desc')->get();
        $app_version_installs=UserDevice::select('app_version',DB::raw('count(*) as total'))->where(['is_active'=>1,'device_brand'=>$brand])->groupBy('app_version')->orderBy('app_version','desc')->get();
        $os_version_installs=UserDevice::select('device_os_version','device_os_name',DB::raw('count(*) as total'))->where(['is_active'=>1,'device_brand'=>$brand])->groupBy('device_os_version')->orderBy('device_os_version','desc')->get();
        return view('admin.dashboard.android.bydevice',compact('count','top_models','app_version_installs','os_version_installs','current_brand'));
    }

    public function all(Request $request){

        $model_users=UserDevice::where(['is_active'=>1])->distinct()->with('user','locations')->orderBy('created_at','desc')->paginate(20);
        return view('admin.dashboard.android.all',compact('model_users'));
    }

    public function byModel(Request $request,$brand,$model){

        $current_brand = $brand;
        $current_model = $model;
        
        $model_users=UserDevice::where(['is_active'=>1,'device_brand'=>$brand,'device_model'=>$model])->distinct()->with('user','locations')->orderBy('created_at','desc')->paginate(20);
        return view('admin.dashboard.android.by_model',compact('model_users','current_brand','current_model'));
    }
}
