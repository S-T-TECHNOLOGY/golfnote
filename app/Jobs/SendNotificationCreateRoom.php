<?php

namespace App\Jobs;

use App\Constants\NotificationType;
use App\Traists\PushNotificationTraist;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendNotificationCreateRoom implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    protected $ownerUser;
    protected $players;
    protected $room;
    use PushNotificationTraist;

    public function __construct($ownerUser, $players, $room)
    {
        $this->ownerUser = $ownerUser;
        $this->players = $players;
        $this->room = $room;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        foreach ($this->players as $player) {
            $token = $player->fcm_token;
            $device = $player->device;
            $data = [
                'type' => NotificationType::INVITED_ROOM,
                'room_id' => $this->room->id,
                'owner' => [
                    'user_id' => $this->ownerUser->id,
                    'name' => $this->ownerUser->name,
                    'phone' => $this->ownerUser->phone
                ]
            ];
            $this->pushMessage($token, $data, $device);
        }
    }
}
