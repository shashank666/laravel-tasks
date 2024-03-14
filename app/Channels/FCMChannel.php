<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;

class FCMChannel{

    protected $apikey;
    public function __construct($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    public function send($notifiable, Notification $notification)
    {
        $data = $notification->toFCM($notifiable);
        //$header = array("authorization: key=" .  $this->apiKey . "","content-type: application/json");
        $header = array("Authorization:key=" .  $this->apiKey . "","Content-Type:application/json");
        $formatted=$data->formatData();
        $body=json_encode($formatted);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
       /*  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST"); */
        curl_setopt($ch, CURLOPT_POSTFIELDS,$body);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;

    }

}
