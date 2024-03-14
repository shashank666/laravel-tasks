<?php

namespace App\Http\Controllers\Admin\Dashboard;
use Illuminate\Support\Str;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;

use Illuminate\Support\Facades\Auth;
use App\Model\Admin;
use Illuminate\Support\Facades\Hash;
use DB;

use Carbon\Carbon;

class SettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
        View::share('menu','settings');
    }

    public function showSettingsPage(){
        return view('admin.dashboard.settings.index');
    }

    public function showCompanySettings(){
        return view('admin.dashboard.settings.company');
    }

    public function showEmailSettings(){
        return view('admin.dashboard.settings.email');
    }

    public function showUISettings(){
        return view('admin.dashboard.settings.ui');
    }


    public function showAndroidSettings(){
        return view('admin.dashboard.settings.android');
    }

    public function showPersonalSettings(){
        return view('admin.dashboard.settings.personal');
    }


    public function showCompanyPolicySettings(){
        return view('admin.dashboard.settings.policy');
    }
    public function showExpirePassword(){
        return view('admin.dashboard.settings.expiredpassword')->with(['message'=>'Seems like you did not change your password in past 90 days, Kindly reset your password']);;
    }



    public function updateCompanyPolicy(Request $request,$policy_type){
        $updated=DB::table('company')->where('id', '=', 1)->update([$policy_type => $request->input('policy')]);
        return redirect()->back();
    }

    public function manageAdblocker(Request $request){
        $updated=DB::table('company_ui_settings')->where('id', '=', 1)->update(['adblocker' => $request->input('adblocker')]);
        if($updated){
            return response()->json(array('status'=>'success','message'=>'Adblocker Setting Updated'));
        }else{
            return response()->json(array('status'=>'error','message'=>'Failed To Update Adblocker Setting.'));
        }
    }

    public function manageWebpushNotification(Request $request){
        $updated=DB::table('company_ui_settings')->where('id', '=', 1)->update(['webpush_notification' => $request->input('webpush_notification')]);
        if($updated){
            return response()->json(array('status'=>'success','message'=>'Webpush Notification Setting Updated'));
        }else{
            return response()->json(array('status'=>'error','message'=>'Failed To Update Webpush Notification Setting.'));
        }
    }

    public function managerInviteButton(Request $request){
        $updated=DB::table('company_ui_settings')->where('id', '=', 1)->update(['invite_btn' => $request->input('invite_btn')]);
        if($updated){
            return response()->json(array('status'=>'success','message'=>'Invite-Btn Setting Updated'));
        }else{
            return response()->json(array('status'=>'error','message'=>'Failed To Update Invite-Btn Setting.'));
        }
    }

    public function managePagination(Request $request,$field){
        $updated=DB::table('company_ui_settings')->where('id', '=', 1)->update([$field => $request->input('pagination')]);
        if($updated){
            return response()->json(array('status'=>'success','message'=>strtoupper($field).' Setting Updated'));
        }else{
            return response()->json(array('status'=>'error','message'=>'Failed To Update '.strtoupper($field).' Setting.'));
        }
    }

    public function manageVerification(Request $request,$field){
        $updated=DB::table('company_ui_settings')->where('id', '=', 1)->update([$field => $request->input('switch')]);
        if($updated){
            return response()->json(array('status'=>'success','message'=>strtoupper($field).' Setting Updated'));
        }else{
            return response()->json(array('status'=>'error','message'=>'Failed To Update '.strtoupper($field).' Setting.'));
        }
    }

    public function manageGoogleAd(Request $request){
        $updated=DB::table('company_ui_settings')->where('id', '=', 1)->update(['show_google_ad' => $request->input('show_google_ad')]);
        if($updated){
            return response()->json(array('status'=>'success','message'=>'GoogleAd Setting Updated'));
        }else{
            return response()->json(array('status'=>'error','message'=>'Failed To Update GoogleAd Setting.'));
        }
    }

    public function manageGoogleAdCode(Request $request){
        $updated=DB::table('company_ui_settings')->where('id', '=', 1)->update(['google_adcode' => trim($request->input('google_adcode'))]);
        if($updated){
            return response()->json(array('status'=>'success','message'=>'Google AdCode Updated'));
        }else{
            return response()->json(array('status'=>'error','message'=>'Failed To Update GoogleAdCode.'));
        }
    }

    public function changePassword(Request $request){
        $user = Auth::user();
        //var_dump($user->password);
        if(Hash::check($request->input('old-password'), $user->password)){
            if(Hash::check($request->input('new-password'), $user->password)){
            return response()->json(array('status'=>'error','message'=>'New Password should be different from current'));
            }
            else{
            $user->password = bcrypt($request->input('new-password'));
            $user->password_changed_at = Carbon::now();
            $user->save();
            return response()->json(array('status'=>'success','message'=>'Password Updated'));
            }
            
        }
         else{
            return response()->json(array('status'=>'error','message'=>'Current Password did not match'));
        }
       /* $user->password = bcrypt($request->input('new-password'));
        $user->save();
        if($user){
            return response()->json(array('status'=>'success','message'=>'Password Updated'));
        }else{
            return response()->json(array('status'=>'error','message'=>'Failed To Update Password.'));
        }*/
    }

   /* public function changePassword(Request $request){
        $user = Auth::user();
        //$user_old = bcrypt($request->input('old-password'));
        var_dump(Hash::check($request->old_password, $request->user()->password));
        if(!Hash::check($request->old_password, $user->password)){
            
            return response()->json(array('status'=>'error','message'=>'Wrong Current password.'));
        }
        else {
            $user->password = bcrypt($request->input('new-password'));
            $user->save();
            if($user){
                return response()->json(array('status'=>'success','message'=>'Password Updated'));
            }else{
                return response()->json(array('status'=>'error','message'=>'Failed To Update Password.'));
            }
        }
    }
    */
    public function forceLogoutAllUsers(){
        $updated=DB::table('user_devices')->update(['api_token'=>NULL]);
        return redirect()->back();
    }

    public function manageAppPagination(Request $request,$field){
        $updated=DB::table('company_app_settings')->where('id', '=', 1)->update([$field => $request->input('pagination')]);
        if($updated){
            return response()->json(array('status'=>'success','message'=>strtoupper($field).' Setting Updated'));
        }else{
            return response()->json(array('status'=>'error','message'=>'Failed To Update '.strtoupper($field).' Setting.'));
        }
    }
    public function expirePassword(Request $request){
        $user = Auth::user();
        //var_dump($user->password);
        if(Hash::check($request->input('old-password'), $user->password)){
            if(Hash::check($request->input('new-password'), $user->password)){
            return response()->json(array('status'=>'error','message'=>'New Password should be different from current'));
            }
            else{
            $user->password = bcrypt($request->input('new-password'));
            $user->password_changed_at = Carbon::now();
            $user->save();
            return response()->json(array('status'=>'success','message'=>'Password Updated'));
            }
            
        }
         else{
            return response()->json(array('status'=>'error','message'=>'Current Password did not match'));
        }
    }
}
