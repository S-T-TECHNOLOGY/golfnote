<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OldThingResource extends JsonResource
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
            'name' => $this->name,
            'image' => json_decode($this->image),
            'status' => !$this->quantity_remain ? 0 : 1,
            'price' => $this->price,
            'sale_off' => $this->sale_off,
            'quantity' => $this->quantity,
            'quantity_remain' => $this->quantity_remain,
            'description' => $this->description,
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'avatar' => $this->user->avatar,
                'phone' => $this->user->phone
            ]
        ];
    }
}
