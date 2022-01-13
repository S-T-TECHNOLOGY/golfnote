<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class UserReservationEventResource extends JsonResource
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
            'status' => $this->status,
            'created_at' => Carbon::parse($this->created_at)->format('H:i d/m/Y'),
            'event' => [
                'id' => $this->event_id,
                'name' => $this->name,
                'address' => $this->address
            ]
        ];
    }
}
