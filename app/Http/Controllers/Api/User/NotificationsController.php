<?php


namespace App\Http\Controllers\Api\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Model\Notification;
use DB;


class NotificationsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function all_notifications(Request $request){
        try{
            if(Auth::user()->user->unreadNotifications->count()>0){
                Auth::user()->user->unreadNotifications->markAsRead();
            }

            $notifications=Notification::
                where('notifications.notifiable_id',Auth::user()->user_id)
                ->where(['notifications.is_active'=>1,'users.is_active'=>1])
                ->join('users', function($join) {
                    $join->on('notifications.data->sender_id', '=', 'users.id');
                    })
                ->select('notifications.*','users.id as sender_id','users.name as sender_name','users.image as sender_image','users.username as sender_username','users.unique_id as sender_unique_id')
                ->orderBy('notifications.created_at','desc')
                ->paginate(24);


            $formatted=$this->format_notifications($notifications);
            $meta=$this->get_meta($notifications);
            $response=array('status'=>'success','result'=>1,'notifications'=>$formatted,'meta'=>$meta);
            return response()->json($response,200);
        }catch(\Exception $e){
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }

    public function unread_notifications(Request $request){
        try{

            $unread_notifications=Notification::
                where('notifications.notifiable_id',Auth::user()->user_id)
                ->where(['notifications.is_active'=>1,'notifications.read_at'=>null,'users.is_active'=>1])
                ->join('users', function($join) {
                    $join->on('notifications.data->sender_id', '=', 'users.id');
                  })
                ->select('notifications.*','users.id as sender_id','users.name as sender_name','users.image as sender_image','users.username as sender_username','users.unique_id as sender_unique_id')
                ->orderBy('notifications.created_at','desc')
                ->paginate(24);


            $formatted=$this->format_notifications($unread_notifications);
            $meta=$this->get_meta($unread_notifications);
            $response=array('status'=>'success','result'=>1,'notifications'=>$formatted,'meta'=>$meta);
            return response()->json($response,200);
        }catch(\Exception $e){
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }

    public function mark_as_read(Request $request){
        try{
            if(Auth::user()->user->unreadNotifications->count()>0){
                Auth::user()->user->unreadNotifications->markAsRead();
            }
            $response=array('status'=>'success','result'=>1);
            return response()->json($response,200);
        }catch(\Exception $e){
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }

    public function delete_notification(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'id'=>'required',
            ]);

            if ($validator->fails()) {
                $response=array('status'=>'error','result'=>0,'errors'=>implode(',',$validator->errors()->all()));
                return response()->json($response, 200);
            }else{
                $deleted=DB::table('notifications')->where(['id'=>$request->input('id'),'notifiable_id'=>Auth::user()->user_id])->delete();
                if($deleted){
                    $response=array('status'=>'success','result'=>1,'message'=>'Notification deleted');
                    return response()->json($response,200);
                }else{
                    $response=array('status'=>'error','result'=>0,'errors'=>'Failed to delete notification');
                    return response()->json($response,200);
                }
            }
        }catch(\Exception $e){
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }

    public function delete_all_notifications(Request $request){
        try{
            DB::table('notifications')->where('notifiable_id',Auth::user()->user_id)->delete();
            $response=array('status'=>'success','result'=>1,'message'=>'All notifications deleted');
            return response()->json($response,200);
        }catch(\Exception $e){
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }

}
