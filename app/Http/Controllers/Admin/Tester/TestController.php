<?php

namespace App\Http\Controllers\Admin\Tester;
use Illuminate\Support\Str;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Config;
use Carbon\Carbon;
use DB;
use Mail;
use App\Mail\Auth\AccountCreatedMail;
use App\Model\User;

class TestController extends Controller
{

    public function testemail(Request $request){
        $user=User::find(48);
        $bbb=Mail::send(new AccountCreatedMail($user,str::random(20)));
        return $bbb;
    }

    public function testOTP(Request $request){
        $OTP=random_int(10000000, 99999999);
        $EXPIRED=Carbon::now()->addMinutes(5);
        $OTPstatus=$this->sendOTP($OTP,$EXPIRED,'9724922772');
        $OTPstatus=json_decode($OTPstatus,true);
        if($OTPstatus['status']=='success'){
            return response()->json(array('status'=>'success','message'=>'OTP has been sent to registered mobile number.'));
        }else{
            $errors=array('otp'=>array('Failed to send OTP to provided mobile number.'));
            return response()->json(array('status'=>'error','errors'=>$errors));
        }
    }

      // function for send OTP SMS
      protected function sendOTP($OTP,$OTP_EXPIRED,$mobile){
        $EXPIRED=Carbon::parse($OTP_EXPIRED)->format('H:i:s');
        $msg1 = "Thank you for registering with Opined . Your OTP is ";
        $msg2 = $OTP." valid till ".$EXPIRED;
        $msg3=".Do not share OTP for security reasons.";
        $message =  rawurlencode($msg1.$msg2.$msg3);
        $test = "0";
        $sender = urlencode("Opined");

        $data = array('apikey' =>Config::get('app.company')->sms_apikey, 
        'numbers' => $mobile,
        'username'=>Config::get('app.company')->sms_username,
        'password'=>Config::get('app.company')->sms_password,
         'sender' => $sender, 
         'message' => $message);

        $ch = curl_init(Config::get('app.company')->sms_apiurl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }
}
