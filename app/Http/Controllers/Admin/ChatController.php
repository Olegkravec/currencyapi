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
    public function index(){
        $my_rooms = RoomsModel::buildConversationModelFor(Auth::id());

        return view('chats.index')->with([
            "conversations" => $my_rooms
        ]);
    }

    public function chats_conversion($room_id){
        $room = RoomsModel::find($room_id);
        $messages = $room->getMessages();
        $members = $room->getMembersExceptMe();
        $room_name = (count($members) > 1) ? $room->name : $members[0]->name;
        return view('chats.conversation')->with([
            'room' => $room,
            'room_name' => $room_name,
            'messages' => $messages,
            'members' => $members,
        ]);
    }

    public function fireMessage(FireMessageRequest $request, $room_id){
        $room = RoomsModel::find($room_id);

        broadcast(new MessageFiredEvent($room, $request->validated()));
    }
}
