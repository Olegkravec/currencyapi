<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * Class RoomsModel
 * @package App\Models
 */
class RoomsModel extends Model
{
    protected $table = 'rooms';


    /**
     * Here I build object that contain all active rooms and his messages with relationships.
     * Thats provide for me easy-accessed data about all chats by user id
     * @param $user_id
     * @return array
     */
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

    /**
     * Get all members of the room
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getMembers(){
        return $this->hasMany('App\Models\RoomsMembersModel', 'room_id', 'id')->get();
    }

    /**
     * Get all members of the room except me
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getMembersExceptMe(){
        return $this->hasMany('App\Models\RoomsMembersModel', 'room_id', 'id')->where("user_id", "!=" ,Auth::id())->get();
    }

    /**
     * Get all messages typed in the chat
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getMessages(){
        return $this->hasMany('App\Models\MessagesModel', 'room_id', 'id')->orderBy('created_at', "DESC")->get();
    }
    /**
     * Get all messages typed in the chat
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUnsortedMessages(){
        return $this->hasMany('App\Models\MessagesModel', 'room_id', 'id')->get();
    }


}
