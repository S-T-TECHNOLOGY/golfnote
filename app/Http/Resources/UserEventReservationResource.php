<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserEventReservationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'user_name' => $this->user_name,
            'handicap_score' => empty($this->user->userSummary) ? 0 : $this->user->userSummary->handicap_score,
            'email' => $this->email,
            'phone' => $this->phone,
            'note' => $this->note,
            'status' => $this->status,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'event' => [
                'id' => $this->event->id,
                'name' => $this->event->name,
                'address' => $this->event->address
            ]
        ];
    }
}
