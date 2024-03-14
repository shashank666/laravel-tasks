<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Notifications\ChannelManager;
use App\Channels\FCMChannel;
/**
 * Class FcmNotificationServiceProvider.
 */
class FcmNotificationServiceProvider extends ServiceProvider
{
    /**
     * Register.
     */
    public function register()
    {       
        $this->app->make(ChannelManager::class)->extend('fcm', function () {
            return new FCMChannel(config('services.fcm.server_api_key'));
        });
    }
}