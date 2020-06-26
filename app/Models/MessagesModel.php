<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class MessagesModel extends Model
{
    protected $table = 'messages';

    public function getSender(){
        return $this->hasOne('App\User', 'id', 'user_id')->first();
    }

    public function isMine(){
        if($this->user_id === Auth::id())
            return true;

        return false;
    }
}
