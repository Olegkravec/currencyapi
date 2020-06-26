<?php

use Illuminate\Database\Seeder;

class FirstChatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $room = new \App\Models\RoomsModel();
        $room->name = "laravel api bot";
        $room->save();

        {
            /**
             * Adding super admin to the room(chat)
             */
            $member = new \App\Models\RoomsMembersModel();
            $member->room_id = $room->id;
            $member->user_id = 1; // Super Admin
            $member->save();
        }

        {
            /**
             * Adding test user to the room(chat)
             */
            $member = new \App\Models\RoomsMembersModel();
            $member->room_id = $room->id;
            $member->user_id = 2; // Some user
            $member->save();
        }

        $message = new \App\Models\MessagesModel();
        $message->room_id = $room->id;
        $message->user_id = 2;
        $message->message = "Hello supper admin, I am hepy to see you there :)";
        $message->save();
    }
}
