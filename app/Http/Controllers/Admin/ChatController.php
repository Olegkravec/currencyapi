<?php

namespace App\Http\Controllers\Admin;

use App\Events\MessageFiredEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\FireMessageRequest;
use App\Http\Requests\InviteMembersToRoomRequest;
use App\Models\MessagesModel;
use App\Models\RoomsMembersModel;
use App\Models\RoomsModel;
use App\User;
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
        if(empty($room)){
            flash("Room not found")->error();
            return redirect()->back();
        }
        $messages = $room->getUnsortedMessages();
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
     * Start new conversation with selected user
     * @param $user_id
     */
    public function new_conversion($user_id){
        $user = User::find($user_id);
        if(empty($user)){
            flash("User not found")->error();
            return redirect()->back();
        }

        if(!$user->can("can chatting with others")){
            flash("User cannot receive your conversation request. First you should add him permission")->error();
            return redirect()->back();
        }

        $room = RoomsMembersModel::isConverstationExistWithUsersPair(Auth::id(), $user_id);

        if(!empty($room)){
            return redirect()->route("chats_conversion", ["chat_id" => $room[0]->room_id]);
        }
        // Create room for conversation
        $room = new RoomsModel();
        $room->name = $user->name;
        $room->save();

        {
            // Add myself to the room
            $member = new RoomsMembersModel();
            $member->room_id = $room->id;
            $member->user_id = Auth::id();
            $member->save();
        }
        {
            // Add selected user to the room
            $member = new \App\Models\RoomsMembersModel();
            $member->room_id = $room->id;
            $member->user_id = $user->id;
            $member->save();
        }

        return redirect()->route("chats_conversion", ["chat_id" => $room->id]);
    }


    /**
     * Actually thats mathod that registers chat message event
     * @param FireMessageRequest $request
     * @param $room_id
     */
    public function fireMessage(FireMessageRequest $request, $room_id){
        $room = RoomsModel::find($room_id);

        broadcast(new MessageFiredEvent($room, Auth::user(), $request->validated()));

        {
            $message = new MessagesModel();
            $message->room_id = $room_id;
            $message->user_id = Auth::id();
            $message->message = $request->validated()['message'];
            $message->save();
        }
    }

    /**
     * Prepare tables with available admins for inviting
     *
     * @param $room_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function chats_invite($room_id){
        $room = RoomsModel::find($room_id);


        return view("chats.invite")->with([
            'room' => $room,
            'users' => User::permission('can chatting with others')->get()
        ]);
    }

    public function saveInvites(InviteMembersToRoomRequest $request, $room_id){
        $room = RoomsModel::find($room_id);

        $users = $request->validated()['id'];

        foreach ($users as $user){
            $room->inveteMember($user);
        }

        flash("Members invited successfully!")->success();
        return redirect()->route("chats_conversion", $room_id);
    }
}
