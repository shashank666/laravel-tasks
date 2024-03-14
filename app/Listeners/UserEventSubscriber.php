<?php

namespace App\Listeners;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Model\UserLogin;
use App\Model\UserLocation;
use Session;
use Jenssegers\Agent\Agent;
use DB;

class UserEventSubscriber
{
    public function __construct(Request $request)
    {
        $this->request = $request;
        if(!$this->request->is('cpanel/*')){
            if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
                $this->ip  = $_SERVER["HTTP_CF_CONNECTING_IP"];
            }else{
                $this->ip = $this->request->ip();
            }
            $this->ua=$this->request->header("user-agent");
        }
    }

    /**
     * Handle user login events.
     */
    public function handleUserLogin($event) {

       if(!$this->request->is('cpanel/*')){

                $user = $event->user;
                $user->last_login_at = Carbon::now();
                $user->last_login_ip =$this->ip;
                $user->save();

                    $agent = new Agent();
                    $browser = $agent->browser();
                    $browser_version=$agent->version($browser);
                    if($agent->isMobile() || $agent->isPhone()){
                        $device_type='mobile';
                    }else if($agent->isTablet()){
                        $device_type='tablet';
                    }else if($agent->isDesktop()){
                        $device_type='desktop';
                    }else{
                        $device_type='mobile';
                    };

                if($this->request->is('api/*')){
                    $platform='android';
                    $device_os_name='Android '.$this->request->device_os_version;
                    $device_name=ucfirst($this->request->device_brand).'  '.$this->request->device_model;
                    $is_robot=0;
                }else{
                    $platform='website';
                    $device_os_name=$agent->platform();
                    $device_name=$agent->device();
                    $is_robot=$agent->isRobot();
                }


                $ip_url='http://www.geoplugin.net/json.gp?ip='.$this->ip;
                $json = file_get_contents($ip_url);
                $data = json_decode($json);
                $location_id=null;
                if($data && $data->geoplugin_status==200){
                    $loc_found=UserLocation::where(['user_id'=>$event->user->id,
                    'country_code'=>$data->geoplugin_countryCode,
                    'city'=>$data->geoplugin_city,
                    'state'=>$data->geoplugin_regionName])->first();
                    if($loc_found){
                        $location_id=$this->saveUserLocation($event->user->id,$loc_found,$data);
                    }else{
                        $location_id=$this->saveUserLocation($event->user->id,new UserLocation(),$data);
                    }
                }

                $login_found=UserLogin::where(['ip_address'=>$this->ip,'user_agent'=>$this->ua,'user_id'=>$event->user->id])->first();
                if(!$login_found){
                    UserLogin::create([
                        'session_id'=>Session::getId(),
                        'user_id'=> $event->user->id,
                        'ip_address'=>$this->ip,
                        'user_agent'=>$this->ua,
                        'browser_name'=> $browser,
                        'browser_version'=>$browser_version,
                        'device_os_name'=>$device_os_name,
                        'device_name'=>$device_name,
                        'device_type'=>$device_type,
                        'is_robot'=>$is_robot,
                        'location_id'=>$location_id,
                        'platform'=>$platform,
                        'login_at'=>Carbon::now(),
                        'is_active'=>1
                    ]);

                    /* $login_counts=UserLogin::where(['user_id'=>$event->user->id])->count();
                    if($login_counts>1){
                        // notify user
                    } */
                }
           //}
       }
    }

    public function saveUserLocation($userid,$location,$data){
        $location->user_id=$userid;
        $location->city=$data->geoplugin_city;
        $location->state=$data->geoplugin_regionName;
        $location->country_name=$data->geoplugin_countryName;
        $location->country_code=$data->geoplugin_countryCode;
        $location->latitude=$data->geoplugin_latitude;
        $location->longitude=$data->geoplugin_longitude;
        $location->save();
        return $location->id;
    }



    /**
     * Handle user logout events.
     */
    public function handleUserLogout($event){
        if(!$this->request->is('cpanel/*')){
            //if ($this->request->is('api/*')){

            //}else{
                $userid=$event->user['id'];
                UserLogin::where(['ip_address'=>$this->ip,'user_agent'=>$this->ua,'user_id'=>$userid])->delete();
           // }
        }
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param  \Illuminate\Events\Dispatcher  $events
     */
    public function subscribe($events)
    {
        $events->listen(
            'Illuminate\Auth\Events\Login',
            'App\Listeners\UserEventSubscriber@handleUserLogin'
        );

        $events->listen(
            'Illuminate\Auth\Events\Logout',
            'App\Listeners\UserEventSubscriber@handleUserLogout'
        );
    }
}
