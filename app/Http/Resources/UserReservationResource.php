<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserReservationResource extends JsonResource
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
            'user_name' => $this->user_name,
            'user_id' => $this->user_id,
            'email' => $this->email,
            'phone' => $this->phone,
            'date' => $this->date,
            'total_player' => $this->total_player,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'note' => $this->note,
            'status' => $this->status,
            'golf' => [
                'id' => $this->golf->id,
                'name' => $this->golf->name,
                'address' => $this->golf->address,
                'phone' => $this->golf->phone
            ]
        ];
    }
}
