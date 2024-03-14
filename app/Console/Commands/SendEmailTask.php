<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\Admin\SendEmailJob;
use Carbon\Carbon;
use DB;
use App\Model\User;
use App\Model\EmailManager;
use Illuminate\Contracts\Bus\Dispatcher;

class SendEmailTask extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:sendemail {email_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Email to All Users';

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
        $email_id = $this->argument('email_id');
        $emailFound=EmailManager::where(['id'=>$email_id,'is_active'=>1])->first();
        if($emailFound){
            $job = (new SendEmailJob($email_id))->onQueue('email')->delay(Carbon::now()->addSeconds(60));
            $job_id  = app(Dispatcher::class)->dispatch($job);
            $emailFound->scheduled_at=Carbon::now();
            $emailFound->status='scheduled';
            $emailFound->job_id=$job_id;
            $emailFound->save();
        }
    }
}
