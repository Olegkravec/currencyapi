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
     * Method that check if two users already joined in the same room
     *
     * @param $first_user
     * @param $second_user
     * @param bool $alsoInGroup
     * @return mixed
     */
    public static function isConverstationExistWithUsersPair($first_user, $second_user, $alsoInGroup = false){
        $room = RoomsModel::from("room_members as RM")->select("RM.room_id")->leftJoin('rooms as R', 'RM.room_id', '=', 'R.id')
            ->where("R.isGroup", $alsoInGroup)
            ->where("RM.user_id", $first_user)
            ->whereExists(function ($query) use ($second_user) {
                $query->select(\DB::raw("*"))
                    ->from('room_members AS SECOND_RM')
                    ->whereRaw('SECOND_RM.room_id = RM.room_id')
                    ->where('SECOND_RM.user_id', $second_user);
            })->get();

        return $room;
    }
}
