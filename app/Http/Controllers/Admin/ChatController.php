<?php

namespace App\Http\Controllers\Admin;

use App\Events\MessageFiredEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\FireMessageRequest;
use App\Models\RoomsMembersModel;
use App\Models\RoomsModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    /**
     * Draw the conversation table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(){
        $my_rooms = RoomsModel::buildConversationModelFor(Auth::id());

        return view('chats.index')->with([
            "conversations" => $my_rooms
        ]);
    }

    /**
     * Direct or Group chat handler.
     * @param $room_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function chats_conversion($room_id){
        $room = RoomsModel::find($room_id);
        $messages = $room->getMessages();
        $members = $room->getMembersExceptMe();
        $room_name = (count($members) > 1) ? $room->name : $members[0]->name; // Chat name or first member name if not group chat
        return view('chats.conversation')->with([
            'room' => $room,
            'room_name' => $room_name,
            'messages' => $messages,
            'members' => $members,
        ]);
    }


    /**
     * Actually thats mathod that registers chat message event
     * @param FireMessageRequest $request
     * @param $room_id
     */
    public function fireMessage(FireMessageRequest $request, $room_id){
        $room = RoomsModel::find($room_id);

        broadcast(new MessageFiredEvent($room, Auth::user(), $request->validated()));
    }
}
