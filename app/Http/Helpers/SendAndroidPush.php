<?php
namespace App\Http\Helpers;

class SendAndroidPush {

    public $api_key;

    public function __construct()
    {
        $this->api_key=env('FCM_SERVER_KEY');
    }

    public function send($data)
    {
        $header = array("Authorization:key=" .  $this->api_key . "","Content-Type:application/json");
        $body=json_encode($data);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS,$body);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch);
        curl_close($ch);
        //dd($result);
        return $result;

    }

}
