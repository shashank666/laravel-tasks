<?php

namespace App\Http\Controllers\Frontend;
use App\Http\Controllers\Controller;
use App\Model\User;
use App\Notifications\Frontend\CommentedOnShortOpinion;
use App\Jobs\AndroidPush\CustomMessageJob;
use App\Model\UserDevice;

use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function showForm()
    {
        return view('message.form');
    }

    public function store(Request $request)
    {
     $title = $request->input('title');
    $message = $request->input('message');
    $startRange = $request->input('start_range');
    $endRange = $request->input('end_range');

    // You can perform any further processing or validation here

    $follower_ids = range($startRange, $endRange);
        $fcm_tokens=UserDevice::whereIn('user_id',$follower_ids)->whereNotNull('api_token')->whereNotNull('gcm_token')->where('is_active',1)->pluck('gcm_token')->toArray();

        foreach(array_chunk($fcm_tokens,100) as $chunk){
            dispatch(new CustomMessageJob($title,$message,$chunk));
        }

        return "Title: " . $title . "<br>Message: " . $message;
    }
}
