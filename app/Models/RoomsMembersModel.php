<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class RoomsMembersModel extends Model
{
    protected $table = 'room_members';
    public $timestamps = false;

    public function getMember(){
        return $this->hasOne('App\User', 'id', 'user_id')->first();
    }
    public static function getMyRooms(){
        return self::hasMany('App\Models\RoomsModel', 'room_id', 'id')->get();
    }
}
