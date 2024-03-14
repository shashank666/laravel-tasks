<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Jobs\MailNotification\MailNotificationJob;

use App\Model\User;
use App\Model\Post;
use App\Model\Thread;
use App\Model\ThreadOpinion;
use App\Model\ShortOpinion;
use App\Model\NotificationMail;
use Carbon\Carbon;
use DB;
use App\Http\Helpers\MailJetHelper;


class DailyNotificationMailTask extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notification:daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Task for sending daily notification to users';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $from=Carbon::now()->subDays(30);
        $to=Carbon::now();

        $opinions=ShortOpinion::where(['is_active'=>1,'type'=>'opinion'])->whereNotNull('plain_body')->orderBy('created_at','desc')->with('user','likesCount')->distinct('plain_body')->take(6)->get();
        
        $failed_emails=DB::table('failed_emails')->get()->pluck('email')->toArray();
        User::select('id','name','email')->whereNotIn('email',$failed_emails)->where(['is_active'=>1,'is_notified'=>1])->orderBy('id')->chunk(20, function ($users) use($opinions){
            dispatch((new MailNotificationJob($opinions,$users))->onQueue('notification'));
            
            usleep(2000000);
        });

        

    }
}
