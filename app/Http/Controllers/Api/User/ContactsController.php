<?php

namespace App\Http\Controllers\Api\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\UserContact;
use App\Model\User;
use App\Model\Follower;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class ContactsController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function store(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'contacts'=>'required',
            ]);

            if ($validator->fails()) {
                $response=array('status'=>'error','result'=>0,'errors'=>implode(',',$validator->errors()->all()));
                return response()->json($response, 200);
            }else{
                $contacts=$request->input('contacts');
                $decoded=json_decode($contacts);
                foreach ($decoded as $index => $contact) {
                    $this->save_user_contact($contact);
                }
                $user=User::where('id',Auth::user()->user_id)->first();
                $user->contacts_saved=1;
                $user->contacts_saved_at=Carbon::now();
                $user->save();
                $response=array('status'=>'success','result'=>1,'message'=>'data saved');
                return response()->json($response, 200);
           }
           }catch (\Exception $e) {
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }


    public function follow(Request $request){
        try{
			
            $contacts=UserContact::select('normalized_number')->where(['user_id'=>Auth::user()->user_id,'is_active'=>1,'follow_hidden'=>0])->get()->pluck('normalized_number')->toArray();
            if(Auth::user()->user->contacts_saved==0 || count($contacts)==0){
                $response=array('status'=>'error','result'=>0,'message'=>'No Contacts available');
                return response()->json($response, 200);
            }else{
                $all_users=User::select('id','mobile')->where('is_active',1)->get();
                $auth_users_follwings=Auth::user()->user->active_followings->pluck('id')->toArray();
                $user_to_follow=[];
                foreach($contacts as $contact){
                    foreach($all_users as $user){
                        if(substr($contact,-10)==$user->mobile &&  !in_array($user->id,$auth_users_follwings) && $user->id!=Auth::user()->user_id){
                            array_push($user_to_follow,$user->id);							 
                        }
                    }
                }
			 
               $users=User::select('id','name','username','unique_id','image')->whereIn('id',$user_to_follow)->paginate(20);


                $formatted=$users->getCollection()->transform(function($user,$key){
					//New changes as per requirement
					$FollowerID= $user->id;
					$cnt= Follower:: select('id')-> where(['follower_id'=>$FollowerID, 'is_active'=>1])->count();
					$Followercnt= Follower:: select('id')-> where(['leader_id'=>$FollowerID, 'is_active'=>1])->count();			
					 $user->Follower_no= $Followercnt;
					 $user->Following_no= $cnt;
                    return $user;
                });
                $meta=$this->get_meta($users);
                $response=array('status'=>'success','result'=>1,'users'=>$formatted,'meta'=>$meta);
                return response()->json($response, 200);
            }
         }catch (\Exception $e) {
                $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
                return response()->json($response, 500);
        }
		
		
    }
	/*
	  public function followerCount(Request $request){
        try{
		 		
			$FollowerID= $request->input('Id');
			$cnt= Follower:: select('id')-> where(['Follower_ID'=>$FollowerID, 'is_active'=>1])->count();
			$Followercnt= Follower:: select('id')-> where(['leader_id'=>$FollowerID, 'is_active'=>1])->count();
			 
            $response=array('status'=>'success','result'=>1,'Follower_no'=> $Followercnt,'Following_no'=> $cnt);
                return response()->json($response, 200);
            
         }catch (\Exception $e) {
                $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error'.$e->getMessage());
                return response()->json($response, 500);
        }
	  }
	  */


    public function invite(Request $request){
        try{
            $users=UserContact::where(['user_id'=>Auth::user()->user_id,'is_active'=>1,'app_installed'=>0,'invite_hidden'=>0])->orderBy('times_contacted','desc')->paginate(10);
            $formatted=$users->getCollection()->transform(function($user,$key){
                $this->remove_null($user);
                return $user;
           });
           $meta=$this->get_meta($users);
           $response=array('status'=>'success','result'=>1,'users'=>$formatted,'meta'=>$meta);
            return response()->json($response, 200);
        }catch (\Exception $e) {
                $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
                return response()->json($response, 500);
        }
    }

    public function reject_invite(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'id'=>'required',
            ]);

            if ($validator->fails()) {
                $response=array('status'=>'error','result'=>0,'errors'=>implode(',',$validator->errors()->all()));
                return response()->json($response, 200);
            }else{
                $updated=UserContact::where(['id'=>$request->input('id'),'user_id'=>Auth::user()->user_id])->update(['invite_hidden'=>1]);
                $response=array('status'=>'success','result'=>1,'message'=>'Invitation Hidden');
                return response()->json($response, 200);
            }
        }catch (\Exception $e) {
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }

    public function reject_follow(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'id'=>'required',
            ]);

            if ($validator->fails()) {
                $response=array('status'=>'error','result'=>0,'errors'=>implode(',',$validator->errors()->all()));
                return response()->json($response, 200);
            }else{
                $user_to_reject=User::where('id',$request->input('id'))->whereNotNull('mobile')->first();
                if($user_to_reject){
                    $updated=UserContact::where(['user_id'=>Auth::user()->user_id])->where('normalized_number', 'LIKE','%'.$user_to_reject->mobile)->update(['follow_hidden'=>1]);
                    if($updated){
                        $response=array('status'=>'success','result'=>1,'message'=>'Follow Suggestion Hidden');
                        return response()->json($response, 200);
                    }else{
                        $response=array('status'=>'error','result'=>0,'message'=>'Failed to Hide Follow Suggestion');
                        return response()->json($response, 200);
                    }
                }else{
                    $response=array('status'=>'error','result'=>0,'message'=>'User not found');
                    return response()->json($response, 200);
                }
            }
        }catch (\Exception $e) {
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }

    protected function save_user_contact($contact){
        $formatted=preg_replace('/\D+/', '',$contact->number);
        $exists=UserContact::where(['normalized_number'=>$formatted,'user_id'=>auth()->user()->user_id])->exists();
        if(!$exists){
            $user_contact=new UserContact();
            $user_contact->name=isset($contact->name)?$contact->name:NULL;
            $user_contact->number=isset($contact->number)?$contact->number:NULL;
            $user_contact->normalized_number=isset($contact->number)?$formatted:NULL;
            $user_contact->email=isset($contact->email)?$contact->email:NULL;
            $user_contact->is_primary=isset($contact->is_primary)?$contact->is_primary:0;
            $user_contact->is_starred=isset($contact->is_starred)?$contact->is_starred:0;
            $user_contact->times_contacted=isset($contact->times_contacted)?$contact->times_contacted:NULL;
            $user_contact->last_time_contacted=isset($contact->last_time_contacted)?$contact->last_time_contacted:NULL;
            $user_contact->type=isset($contact->type)?$contact->type:NULL;
            $user_contact->label=isset($contact->label)?$contact->label:NULL;
            $user_contact->user_id=isset(auth()->user()->user_id)?auth()->user()->user_id:NULL;
            $user_contact->save();
        }
    }

}
