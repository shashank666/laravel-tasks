<?php

namespace App\Jobs\Admin;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Http\Helpers\SendAndroidPush;
use App\Model\UserDevice;

class SendAppPush implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $title,$message;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($title,$message)
    {
        $this->title=$title;
        $this->message=$message;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $data['priority']='high';
        $data['notification']=[
            'title'        => $this->title,
            'body'         => $this->message,
            'image'        => '',
            'click_action' => 'APP'
        ];
        $all_devices=UserDevice::select('gcm_token')->where('is_active',1)->whereNotNull('gcm_token')->get()->pluck('gcm_token')->toArray();
        $unique_all_devices=array_unique($all_devices);

        foreach(array_chunk($unique_all_devices,100) as $chunk) {
            $data['registration_ids']=$chunk;
            $androidPush=new SendAndroidPush();
            $androidPush->send($data);
            usleep(500000);
        }
    }
}
