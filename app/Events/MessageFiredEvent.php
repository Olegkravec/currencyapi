<?php

namespace App\Events;

use App\Models\RoomsModel;
use App\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageFiredEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private $room;
    private $message;
    private $user;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(RoomsModel $room, User $user, $message)
    {
        $this->room = $room;
        $this->message = $message;
        $this->user = $user;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PresenceChannel("room.{$this->room->id}");
    }

    public function broadcastAs()
    {
        return 'messageFired';
    }
    public function broadcastWith()
    {
        return [
            'room' => $this->room,
            'message' => $this->message,
            'user' => $this->user,
        ];
    }
}
