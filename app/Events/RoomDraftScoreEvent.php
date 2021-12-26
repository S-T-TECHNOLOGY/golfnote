<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RoomDraftScoreEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    protected $data;
    protected $roomId;
    protected $holeCurent;
    public function __construct($data, $roomId, $holeCurent)
    {
        $this->data = $data;
        $this->roomId = $roomId;
        $this->holeCurent = $holeCurent;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('room-score.' .$this->roomId);
    }

    public function broadcastWith()
    {
        return [
            'data' => $this->data,
            'hole_current' => $this->holeCurent
        ];
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'room-score.' .$this->roomId;
    }
}
