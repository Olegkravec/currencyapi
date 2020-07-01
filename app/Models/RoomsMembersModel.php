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

    /**
     * There is two functions that checks if the room already was created for selected user pair and returns room_id
     * @param $first_user
     * @param $second_user
     * @return mixed
     */
    public static function isGroupConverstationExistWithUsersPair($first_user, $second_user){
        return \DB::select(\DB::raw("SELECT RM.room_id FROM room_members as RM LEFT JOIN rooms r on RM.room_id = r.id WHERE r.isGroup = false AND RM.user_id = $first_user AND Exists (select * from room_members AS SRM where SRM.room_id = RM.room_id AND SRM.user_id = $second_user)"));
    }
    public static function isConverstationExistWithUsersPair($first_user, $second_user){
        return \DB::select(\DB::raw("SELECT RM.room_id FROM room_members as RM LEFT JOIN rooms r on RM.room_id = r.id WHERE r.isGroup = false AND RM.user_id = $first_user AND Exists (select * from room_members AS SRM where SRM.room_id = RM.room_id AND SRM.user_id = $second_user)"));
    }
}
