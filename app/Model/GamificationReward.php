<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GamificationReward extends Model
{
    use HasFactory;

    protected $table = 'gamification_rewards';

    protected $fillable = [
        'user_id',
        'reward_type',
        'reward_amount',
    ];
}
