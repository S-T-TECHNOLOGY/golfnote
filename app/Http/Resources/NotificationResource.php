<?php

namespace App\Http\Resources;

use App\Constants\NotificationType;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $timeCreated = Carbon::parse($this->created_at)->timestamp;
        return [
            'id' => $this->id,
            'type' => $this->type,
            'time_created' => $timeCreated,
            'is_read' => $this->is_read,
            'golf' => $this->type === NotificationType::REGISTER_GOLF_SUCCESS ? new GolfResource($this->golf) : new \stdClass(),
            'event' => $this->type === NotificationType::REGISTER_EVENT_SUCCESS ? new EventResource($this->event) : new \stdClass()
        ];
    }
}
