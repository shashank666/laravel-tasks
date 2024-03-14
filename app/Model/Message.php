<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $table='messages';
    public $primaryKey='id';
    public $timestamps = true;

    protected $fillable = ['name','email', 'subject','message','starred','mark_read'];

    public function reply(){
        return $this->hasMany('App\Model\MessageReply','message_id');
    }
}
