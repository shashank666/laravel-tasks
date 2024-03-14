<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class MessageReply extends Model
{
    protected $table='messages_reply';
    public $primaryKey='id';
    public $timestamps = true;
    protected $fillable = ['message_id','subject','message','email_sent'];

}
