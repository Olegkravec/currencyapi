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
        $my_rooms_first = RoomsModel::buildConversationModelFor(Auth::id());

        // I will remap collection for building rooms model with members content
        $my_rooms_remaped = $my_rooms_first->map(function (RoomsModel $room) {
            $room->members = $room->getMembersAsUser();
            return $room;
        });
        return view('chats.index')->with([
            "conversations" => $my_rooms_remaped
        ]);
    }

    /**
     * Direct or Group chat handler.
     * @param $room_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function chatsConversion($room_id){
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
     * @return \Illuminate\Http\RedirectResponse
     */
    public function newConversion($user_id){
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

        if(!empty($room) and !empty($room[0])){
            return redirect()->route("chats_conversion", ["chat_id" => $room[0]->room_id]);
        }

        // Create room for conversation
        $room = RoomsModel::createRoomWithMembers($user->name, Auth::id(), $user->id);

        return redirect()->route("chats_conversion", ["chat_id" => $room->id]);
    }


    /**
     * Actually thats mathod that registers chat message event
     * @param FireMessageRequest $request
     * @param $room_id
     */
    public function fireMessage(FireMessageRequest $request, $room_id){
        $room = RoomsModel::find($room_id);

        // Send web-socket broadcast to selected room
        broadcast(new MessageFiredEvent($room, Auth::user(), $request->validated()));

        {
            // Store message in DB
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
    public function chatsInvite($room_id){
        $room = RoomsModel::find($room_id);


        return view("chats.invite")->with([
            'room' => $room,
            'users' => User::permission('can chatting with others')->get()
        ]);
    }

    /**
     * @param InviteMembersToRoomRequest $request
     * @param $room_id
     * @return \Illuminate\Http\RedirectResponse
     */
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
