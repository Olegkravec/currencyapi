<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class RoomsModel extends Model
{
    protected $table = 'rooms';

    public static function buildConversationModelFor($user_id){
        $response = [
            "rooms" => [],
            "members" => [],
        ];
        $rooms = RoomsModel::limit(10)->get();
        foreach ($rooms as $room){
            $members = $room->getMembersExceptMe();
            $conversation = [
                "room" => $room,
            ];
            foreach ($members as $member){
                $user = $member->getMember();
                if(empty($response['members'][$user->id]))
                    $response['members'][$user->id] = $user;
            }
            $conversation['room']['messages'] = $room->getMessages();
            $response['rooms'][$room->id] = $conversation;
        }
        return $response;
    }

    public function getMembers(){
        return $this->hasMany('App\Models\RoomsMembersModel', 'room_id', 'id')->get();
    }
    public function getMembersExceptMe(){
        return $this->hasMany('App\Models\RoomsMembersModel', 'room_id', 'id')->where("user_id", "!=" ,Auth::id())->get();
    }
    public function getMessages(){
        return $this->hasMany('App\Models\MessagesModel', 'room_id', 'id')->orderBy('created_at', "DESC")->get();
    }


}
