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
        ];

        $rooms = RoomsModel::join("room_members", "room_members.room_id", "=", "rooms.id")->where("room_members.user_id", $user_id)->get("rooms.*");
        foreach ($rooms as $room){
            $members = $room->getMembers();

            $conversation = [
                "room" => $room,
                "members" => [],
                "room_name" => "",
            ];

            foreach ($members as $member){
                $user = $member->getMember();
                if($user->id !== Auth::id())
                    $conversation['room_name'] = $user->name;

                if(empty($conversation['members'][$user->id]))
                    $conversation['members'][$user->id] = $user;
            }

            if(count($conversation['members']) > 2){
                $conversation['room_name'] = "Room #{$room->id}";
            }
            $conversation['room']['messages'] = $room->getLastMessage();
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


    public function inveteMember($member_id){
        $member = new RoomsMembersModel();
        $member->room_id = $this->id;
        $member->user_id = $member_id;
        $member->save();

        if(!$this->isGroup){
            $this->isGroup = true;
            $this->save();
        }
    }


    /**
     * Checks if room already have invited selected user
     *
     * @param $member_id
     * @return bool
     */
    public function containMember($member_id){
        $user = RoomsMembersModel::where("user_id", $member_id)->where("room_id", $this->id)->first();
        return !empty($user);
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
    public function getLastMessage(){
        return $this->hasMany('App\Models\MessagesModel', 'room_id', 'id')->orderBy('created_at', "DESC")->limit(1)->get();
    }
    /**
     * Get all messages typed in the chat
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUnsortedMessages(){
        return $this->hasMany('App\Models\MessagesModel', 'room_id', 'id')->get();
    }


}
