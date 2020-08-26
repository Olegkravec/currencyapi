<?php

namespace App\Http\Controllers\API\v1;

use App\Events\MessageFiredEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\FireMessageRequest;
use App\Models\MessagesModel;
use App\Models\RoomsModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    /**
     * Actually thats method registers chat message event
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
}
