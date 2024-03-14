<?php

namespace App\Http\Controllers\Api\Gamification;

use Illuminate\Http\Request;
use App\Model\GamificationReward;
use App\Model\ShortOpinion;
use App\Model\User;
use DB;

use App\Http\Controllers\Controller;

class RewardsController extends Controller
{
    public function agreeEvent(Request $request)
    {
        // Retrieve the user ID from the request or any authentication mechanism
        $userId = $request->user_id;

        // Determine the reward amount for the agree event
        $rewardAmount = 10; // Set the reward amount based on your logic
        
        // Create a new gamification reward record
        try {
            $reward = new GamificationReward();
            $reward->user_id = $userId;
            $reward->reward_type = 'agree_event';
            $reward->reward_amount = $rewardAmount;
            $reward->save();
        } catch (\Exception $e) {
            $response=array('status'=>'error','result'=>0,'message'=>'Something went wrong'.$e);
            return response()->json(($response), 500);
        }
        // Return a response or perform any other desired actions
        $response=array('status'=>'success','result'=>1,'message'=>'Agree event reward created');
        return response()->json(($response), 200);
        
    }

    public function commentEvent(Request $request)
{
    // Retrieve the user ID from the request or any authentication mechanism
    $userId = $request->user_id; // Assuming the user_id is provided in the request

    // Determine the reward amount for the comment event
    $rewardAmount = 5; // Set the reward amount based on your logic

    // Create a new gamification reward record
    try {
        $reward = new GamificationReward();
        $reward->user_id = $userId;
        $reward->reward_type = 'comment_event';
        $reward->reward_amount = $rewardAmount;
        $reward->save();
    } catch (\Exception $e) {
        $response=array('status'=>'error','result'=>0,'message'=>'Something went wrong');
        return response()->json(($response), 500);
    }
    $response=array('status'=>'success','result'=>1,'message'=>'Comment event reward created');
    return response()->json(($response), 200);
}

    public function opinionPostingEvent(Request $request)
    {
        // Retrieve the user ID from the request or any authentication mechanism
        $userId = $request->user_id;

        // Determine the reward amount for the opinion posting event
        $rewardAmount = 15; // Set the reward amount based on your logic

        // Create a new gamification reward record
        try {
            $reward = new GamificationReward();
            $reward->user_id = $userId;
            $reward->reward_type = 'opinion_posting_event';
            $reward->reward_amount = $rewardAmount;
            $reward->save();
        } catch (\Exception $e) {
            $response=array('status'=>'error','result'=>0,'message'=>'Something went wrong');
            return response()->json(($response), 500);
        }

        $response=array('status'=>'success','result'=>1,'message'=>'Opinion Post event reward created');
        return response()->json(($response), 200);
    }

    public function getTotalRewardAmount(Request $request, $user_id)
    {

        //TODO: Modify it to retutn User Level Details too
    // Retrieve the total reward_amount for the given user_id
    try {
        $totalRewardAmount = GamificationReward::where('user_id', $user_id)->sum('reward_amount');
    } catch (\Exception $e) {
        $response=array('status'=>'error','result'=>0,'message'=>'Something went wrong');
        return response()->json(($response), 500);
    }
    $response=array('status'=>'success','result'=>1,'total'=>$totalRewardAmount);
    return response()->json(($response), 200);
    }

    public function get_rewards_profile(Request $request, $user_id)
    {
        try{
            $totalRewardAmount = GamificationReward::where('user_id', $user_id)->sum('reward_amount');
            $user = User::where('id', $user_id)->first();
            $name = $user->name;
            $profile_pic = $user->image;

            $unlockedAchievements = DB::table('achievements')
            ->join('user_achievements', 'achievements.achievement_id', '=', 'user_achievements.achievements_id')
            ->where('user_achievements.user_id', $user_id)
            ->get();

            $response=array('status'=>'success','result'=>1,'toal'=>$totalRewardAmount,'name'=>$name,'image'=>$profile_pic,'achievements'=>$unlockedAchievements);
            return response()->json(($response), 200);

        }catch(\Exception $e){
            $response=array('status'=>'error','result'=>0,'message'=>'Something went wrong'.$e);
            return response()->json(($response), 500);
        }
    }

    public function all_achievements()
    {
        $achievements = DB::table('achievements')->get();

        $response=array('status'=>'success','result'=>1,'achievements'=>$achievements);
        return response()->json(($response), 200);

    }

    


    //follow event
}
