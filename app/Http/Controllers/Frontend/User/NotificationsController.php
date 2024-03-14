<?php

namespace App\Http\Controllers\Frontend\User;
use Illuminate\Support\Str;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use App\Model\User;
use App\Model\Notification;
use DB;
use Session;

class NotificationsController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

     // function for display all notifications for user with pagination in notifications page
     public function get_all_notifications_with_pagination(Request $request){

        if(Auth::user()->unreadNotifications->count()>0){
            Auth::user()->unreadNotifications->markAsRead();
        }
        $google_ad=DB::table('google_ads')->where(['id'=>1,'is_active'=>1])->first();

        $notifications=Notification::
            where('notifications.notifiable_id',Auth::user()->id)
            ->where('notifications.is_active',1)
            ->leftJoin('users', function($join) {
                $join->on('notifications.data->sender_id', '=', 'users.id');
              })
            ->select('notifications.*','users.name as sender_name','users.image as sender_image','users.username as sender_username','users.unique_id as sender_unique_id')
            ->orderBy('notifications.created_at','desc')
            ->paginate(24);

        return view('frontend.profile.notifications',compact('google_ad','notifications'));
    }


    // function for get all unread notifications for user
    public function get_all_unread_notifications(Request $request){

        $unread_notifications=Notification::
            where('notifications.notifiable_id',Auth::user()->id)
            ->where(['notifications.is_active'=>1,'notifications.read_at'=>null])
            ->leftJoin('users', function($join) {
                $join->on('notifications.data->sender_id', '=', 'users.id');
              })
            ->select('notifications.*','users.name as sender_name','users.image as sender_image','users.username as sender_username','users.unique_id as sender_unique_id')
            ->orderBy('notifications.created_at','desc')
            ->get();

        if($request->ajax()){
           return response()->json(array('status'=>'success','unread_notifications'=>$unread_notifications));
        }else{
            return redirect('/');
        }
    }


    // function for mark user's unread notifications as read
    public function mark_as_read(Request $request){
        if(Auth::user()->unreadNotifications->count()>0){
            Auth::user()->unreadNotifications->markAsRead();
            return response()->json(array('status'=>'success'));
        }else{
            return response()->json(array('status'=>'error'));
        }
    }


    // function for delete all notifications for user
    public function delete_all_notifications(){
        DB::table('notifications')->where('notifiable_id',Auth::user()->id)->delete();
        return redirect('/me/notifications')->with('message','All Notifications Deleted');
    }

}
